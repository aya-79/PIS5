import api from "./axios";

export interface RegisterPayload {
  name: string;
  prenom: string;
  email: string;
  telephone?: string | null;
  password: string;
  password_confirmation: string;
  role?: "enseignant" | "agent-scolaire" | "admin";
}

export const registerRequest = (data: RegisterPayload) => {
  return api.post("/register", data);
};

export const loginRequest = (data: {
  email: string;
  password: string;
}) => {
  return api.post("/login", data);
};
