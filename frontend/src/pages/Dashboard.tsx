import React, { useEffect, useMemo, useState } from "react";
import {
  Home,
  Calendar,
  Plus,
  LogOut,
  Menu,
  Clock,
  BookOpen,
  Target,
  Filter,
} from "lucide-react";
import {
  ResponsiveContainer,
  BarChart,
  Bar,
  XAxis,
  YAxis,
  Tooltip,
} from "recharts";
import { getMe, getTeacherDashboard, createPointage } from "../api/services";

/* ================= TYPES ================= */

type Matiere = { id: number; nom: string };

type Teacher = {
  nom?: string;
  prenom?: string;
  email?: string;
};

type Stats = {
  total_heures_effectuees?: number;
  nombre_sessions_realisees?: number;
  total_heures_annee?: number;
};

type PointageRow = {
  date: string;
  matiere: string;
  duree?: number;
};

type TabType = "dashboard" | "history" | "form";

/* ================= HELPERS ================= */

function safeToken(): string | null {
  const t = localStorage.getItem("token");
  if (!t || t === "undefined" || t === "null") return null;
  return t;
}

function diffHours(start: string, end: string): number {
  const [sh, sm] = start.split(":").map(Number);
  const [eh, em] = end.split(":").map(Number);
  const diff = eh * 60 + em - (sh * 60 + sm);
  return diff > 0 ? Math.round((diff / 60) * 100) / 100 : 0;
}

/* ================= COMPONENT ================= */

export default function Dashboard() {
  const [teacher, setTeacher] = useState<Teacher>({});
  const [stats, setStats] = useState<Stats>({});
  const [matieres, setMatieres] = useState<Matiere[]>([]);
  const [pointages, setPointages] = useState<PointageRow[]>([]);
  const [activeTab, setActiveTab] = useState<TabType>("dashboard");
  const [sidebarOpen, setSidebarOpen] = useState(false);

  // History filters
  const [qDate, setQDate] = useState("");
  const [qMatiere, setQMatiere] = useState("");

  // Form
  const [matiereId, setMatiereId] = useState<number | "">("");
  const [date, setDate] = useState(() => new Date().toISOString().split("T")[0]);
  const [heureDebut, setHeureDebut] = useState("08:00");
  const [heureFin, setHeureFin] = useState("10:00");
  const [saving, setSaving] = useState(false);

  const computedDuree = useMemo(
    () => diffHours(heureDebut, heureFin),
    [heureDebut, heureFin]
  );

  const filteredPointages = useMemo(() => {
    return pointages.filter((p) => {
      const okDate = qDate ? p.date === qDate : true;
      const okMat = qMatiere
        ? p.matiere.toLowerCase().includes(qMatiere.toLowerCase())
        : true;
      return okDate && okMat;
    });
  }, [pointages, qDate, qMatiere]);

  useEffect(() => {
    if (!safeToken()) {
      window.location.href = "/login";
      return;
    }

    (async () => {
      const me = await getMe();
      setMatieres(me?.data?.data?.user?.enseignant?.matieres ?? []);

      const dash = await getTeacherDashboard();
      setTeacher(dash.data?.enseignant ?? {});
      setStats(dash.data?.stats ?? {});
      setPointages(dash.data?.pointages ?? []);
    })();
  }, []);

  const submitPointage = async () => {
    if (!matiereId || computedDuree <= 0) return;
    setSaving(true);
    await createPointage({
      matiere_id: Number(matiereId),
      date,
      heure_debut: heureDebut,
      heure_fin: heureFin,
    });
    window.location.reload();
  };

  const logout = () => {
    localStorage.clear();
    window.location.href = "/login";
  };

  const nav = [
    { id: "dashboard" as const, icon: Home },
    { id: "history" as const, icon: Calendar },
    { id: "form" as const, icon: Plus },
  ];

  /* ================= MOCK DATA (TEMPORAIRE & JUSTIFIABLE) ================= */

  const heuresParType = [
    { type: "CM", heures: 5 },
    { type: "TD", heures: 2 },
    { type: "TP", heures: 0 },
  ];

  /* ================= UI ================= */

  return (
    <div className="min-h-screen flex bg-[#F7F8FC]">
      {/* SIDEBAR */}
      <aside
        className={`fixed md:static inset-y-0 left-0 z-40 w-24 bg-gradient-to-b from-[#2E2A78] to-[#25215F]
        flex flex-col items-center py-6 transition-transform
        ${sidebarOpen ? "translate-x-0" : "-translate-x-full md:translate-x-0"}`}
      >
        <button
          className="md:hidden mb-6 text-white"
          onClick={() => setSidebarOpen(false)}
        >
          ✕
        </button>

        <nav className="flex flex-col gap-5 mt-4">
          {nav.map((n) => {
            const active = activeTab === n.id;
            return (
              <button
                key={n.id}
                onClick={() => {
                  setActiveTab(n.id);
                  setSidebarOpen(false);
                }}
                className={`w-14 h-14 rounded-xl flex items-center justify-center transition
                  ${
                    active
                      ? "bg-white text-[#2E2A78] shadow-md"
                      : "bg-white/20 text-white hover:bg-white/30"
                  }`}
              >
                <n.icon size={26} strokeWidth={2.2} />
              </button>
            );
          })}
        </nav>

        <button
          onClick={logout}
          className="mt-auto w-14 h-14 rounded-xl flex items-center justify-center
          bg-white/20 text-white hover:bg-white/30 transition"
        >
          <LogOut size={22} />
        </button>
      </aside>

      {/* MAIN */}
      <main className="flex-1 md:ml-24 p-6 space-y-8">
        {/* DASHBOARD */}
        {activeTab === "dashboard" && (
          <>
            {/* HERO */}
            <div className="bg-gradient-to-r from-[#FFBE76] to-[#FFD8B1] rounded-3xl p-6">
              <h1 className="text-2xl font-bold text-[#2E2A78]">
                Bonjour {teacher.prenom}
              </h1>
              <p className="text-[#2E2A78]/80 mt-1">
                Suivez vos séances et pointez vos heures simplement.
              </p>
            </div>

            {/* STATS */}
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <StatCard
                icon={<Clock />}
                label="Heures Équiv. CM"
                value={stats.total_heures_effectuees}
              />
              <StatCard
                icon={<BookOpen />}
                label="Cours Effectués"
                value={stats.nombre_sessions_realisees}
              />
              <StatCard
                icon={<Target />}
                label="Cours Total"
                value={stats.total_heures_annee}
              />
            </div>

            {/* CHART */}
            <div className="bg-white rounded-3xl p-6">
              <h3 className="font-semibold text-lg mb-4">
                Répartition des heures par type de séance
              </h3>
              <div className="w-full h-64">
                <ResponsiveContainer width="100%" height="100%">
                  <BarChart data={heuresParType}>
                    <XAxis dataKey="type" />
                    <YAxis />
                    <Tooltip />
                    <Bar dataKey="heures" fill="#2E2A78" radius={[6, 6, 0, 0]} />
                  </BarChart>
                </ResponsiveContainer>
              </div>
            </div>

            {/* EXTRA */}
           
          </>
        )}

        {/* HISTORY */}
        {activeTab === "history" && (
          <div className="bg-white rounded-3xl p-6 space-y-4">
            <div className="flex flex-col sm:flex-row gap-3">
              <input
                type="date"
                value={qDate}
                onChange={(e) => setQDate(e.target.value)}
                className="border rounded-xl px-3 py-2"
              />
              <div className="relative">
                <Filter className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                <input
                  type="text"
                  placeholder="Filtrer matière"
                  value={qMatiere}
                  onChange={(e) => setQMatiere(e.target.value)}
                  className="pl-9 border rounded-xl px-3 py-2"
                />
              </div>
            </div>

            <table className="min-w-full text-sm">
              <thead className="bg-slate-50">
                <tr>
                  <th className="px-4 py-3 text-left">Date</th>
                  <th className="px-4 py-3 text-left">Matière</th>
                  <th className="px-4 py-3 text-left">Durée</th>
                </tr>
              </thead>
              <tbody>
                {filteredPointages.map((p, i) => (
                  <tr key={i} className="border-t">
                    <td className="px-4 py-2">{p.date}</td>
                    <td className="px-4 py-2">{p.matiere}</td>
                    <td className="px-4 py-2">{p.duree} h</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        {/* FORM */}
        {activeTab === "form" && (
          <div className="flex justify-center mt-10">
            <div className="w-full max-w-xl bg-white rounded-3xl p-6 space-y-4">
              <h2 className="text-xl font-semibold text-center">
                Nouvelle séance
              </h2>

              <select
                value={matiereId}
                onChange={(e) => setMatiereId(Number(e.target.value))}
                className="w-full border rounded-xl px-3 py-2"
              >
                <option value="">Choisir une matière</option>
                {matieres.map((m) => (
                  <option key={m.id} value={m.id}>
                    {m.nom}
                  </option>
                ))}
              </select>

              <input
                type="date"
                value={date}
                onChange={(e) => setDate(e.target.value)}
                className="w-full border rounded-xl px-3 py-2"
              />

              <div className="grid grid-cols-2 gap-3">
                <input
                  type="time"
                  value={heureDebut}
                  onChange={(e) => setHeureDebut(e.target.value)}
                  className="border rounded-xl px-3 py-2"
                />
                <input
                  type="time"
                  value={heureFin}
                  onChange={(e) => setHeureFin(e.target.value)}
                  className="border rounded-xl px-3 py-2"
                />
              </div>

              <button
                onClick={submitPointage}
                disabled={saving}
                className="w-full bg-[#2E2A78] text-white py-3 rounded-xl font-medium"
              >
                Valider ({computedDuree.toFixed(2)} h)
              </button>
            </div>
          </div>
        )}
      </main>
    </div>
  );
}

/* ================= SMALL COMPONENT ================= */

function StatCard({
  icon,
  label,
  value,
}: {
  icon: React.ReactNode;
  label: string;
  value?: number;
}) {
  return (
    <div className="bg-white rounded-2xl p-4 flex items-center gap-4 shadow-sm">
      <div className="w-10 h-10 rounded-xl bg-[#EEF2FF] text-[#2E2A78] flex items-center justify-center">
        {icon}
      </div>
      <div>
        <p className="text-sm text-slate-500">{label}</p>
        <p className="text-xl font-semibold">{value ?? 0}</p>
      </div>
    </div>
  );
}
