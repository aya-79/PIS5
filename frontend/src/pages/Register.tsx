import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { registerRequest } from "../api/auth";

export default function Register() {
  const navigate = useNavigate();

  const [prenom, setPrenom] = useState("");
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [telephone, setTelephone] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (password !== confirmPassword) {
      alert("Les mots de passe ne correspondent pas.");
      return;
    }

    if (password.length < 8) {
      alert("Mot de passe trop court.");
      return;
    }

    setLoading(true);

    try {
      await registerRequest({
        name,
        prenom,
        email,
        telephone,
        password,
        password_confirmation: confirmPassword,
      });

      navigate("/login");
    } catch {
      alert("Erreur lors de l'inscription");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#F7F8FC] to-[#EEF1FF] p-4">
      <div className="w-full max-w-lg bg-white rounded-3xl p-8 shadow-sm">
        <h1 className="text-2xl font-bold text-[#2E2A78] mb-2 text-center">
          Inscription
        </h1>
        <p className="text-sm text-slate-500 text-center mb-6">
          Créez votre compte enseignant
        </p>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <input
              type="text"
              placeholder="Prénom"
              className="border rounded-xl px-4 py-3"
              value={prenom}
              onChange={(e) => setPrenom(e.target.value)}
              required
            />
            <input
              type="text"
              placeholder="Nom"
              className="border rounded-xl px-4 py-3"
              value={name}
              onChange={(e) => setName(e.target.value)}
              required
            />
          </div>

          <input
            type="email"
            placeholder="Email"
            className="border rounded-xl px-4 py-3 w-full"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
          />

          <input
            type="tel"
            placeholder="Téléphone (optionnel)"
            className="border rounded-xl px-4 py-3 w-full"
            value={telephone}
            onChange={(e) => setTelephone(e.target.value)}
          />

          <input
            type="password"
            placeholder="Mot de passe (min 8 caractères)"
            className="border rounded-xl px-4 py-3 w-full"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
          />

          <input
            type="password"
            placeholder="Confirmer le mot de passe"
            className="border rounded-xl px-4 py-3 w-full"
            value={confirmPassword}
            onChange={(e) => setConfirmPassword(e.target.value)}
            required
          />

          <button
            type="submit"
            disabled={loading}
            className="w-full bg-[#2E2A78] text-white py-3 rounded-xl font-medium hover:opacity-90"
          >
            {loading ? "Création..." : "Créer un compte"}
          </button>
        </form>

        <p className="mt-6 text-sm text-center text-slate-500">
          Déjà un compte ?{" "}
          <a href="/login" className="text-[#2E2A78] font-medium">
            Se connecter
          </a>
        </p>
      </div>
    </div>
  );
}
