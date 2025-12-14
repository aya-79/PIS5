import axios from "axios";

/**
 * Axios instance for your Laravel API.
 * - baseURL defaults to http://127.0.0.1:8000/api (Laravel routes/api.php is automatically prefixed with /api)
 * - Automatically attaches Bearer token from localStorage("token")
 */
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? "http://127.0.0.1:8000/api",
  headers: {
    Accept: "application/json",
  },
});

// Attach token on every request
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers = config.headers ?? {};
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;
