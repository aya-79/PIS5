import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { loginRequest } from "../api/auth";

export default function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);

    try {
      const res = await loginRequest({ email, password });
      const token = res.data?.data?.access_token;
      const user = res.data?.data?.user;

      if (!token || !user) {
        alert("Réponse serveur inattendue.");
        return;
      }

      localStorage.setItem("token", token);
      localStorage.setItem("teacher", JSON.stringify(user));
      navigate("/dashboard", { replace: true });
    } catch {
      alert("Identifiants incorrects !");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#F7F8FC] to-[#EEF1FF] p-4">
      <div className="w-full max-w-md bg-white rounded-3xl p-8 shadow-sm">
        <h1 className="text-2xl font-bold text-[#2E2A78] mb-2 text-center">
          Connexion
        </h1>
        <p className="text-sm text-slate-500 text-center mb-6">
          Accédez à votre espace enseignant
        </p>

        <form onSubmit={handleSubmit} className="space-y-4">
          <input
            type="email"
            placeholder="Email"
            className="w-full border rounded-xl px-4 py-3"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
          />

          <input
            type="password"
            placeholder="Mot de passe"
            className="w-full border rounded-xl px-4 py-3"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
          />

          <button
            type="submit"
            disabled={loading}
            className="w-full bg-[#2E2A78] text-white py-3 rounded-xl font-medium hover:opacity-90"
          >
            {loading ? "Connexion..." : "Se connecter"}
          </button>
        </form>

        <p className="mt-6 text-sm text-center text-slate-500">
          Pas encore de compte ?{" "}
          <a href="/register" className="text-[#2E2A78] font-medium">
            Créer un compte
          </a>
        </p>
      </div>
    </div>
  );
}
