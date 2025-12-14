import api from "./axios";

/**
 * Auth / Profile
 * Backend: AuthController@me returns { success: true, data: { user, profile } }
 */
export const getMe = () => api.get("/me");

/**
 * Teacher dashboard data
 * Backend: DashboardEnseignantController@index returns:
 * { enseignant, stats, cours_du_jour, cours_semaine, historique_sessions, pointages }
 */
export const getTeacherDashboard = () => api.get("/dashboard-enseignant");

/**
 * Pointages CRUD (teacher)
 * Backend: PointageController
 */
export const listMyPointages = (params?: {
  statut_pointage?: string;
  date_debut?: string;
  date_fin?: string;
}) => api.get("/pointages", { params });

export const createPointage = (payload: {
  date: string; // YYYY-MM-DD
  heure_debut: string; // HH:mm
  heure_fin: string; // HH:mm
  type_seance: "CM" | "TD" | "TP";
  matiere_id: number;
}) => api.post("/pointages", payload);
