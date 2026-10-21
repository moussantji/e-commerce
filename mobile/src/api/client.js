import axios from "axios";
import { API_BASE_URL } from "../config";

const api = axios.create({
    baseURL: API_BASE_URL,
    // Pas de Content-Type global : axios choisit application/json pour les
    // objets et laisse React Native générer le boundary pour les FormData
    // (envoi des captures de paiement).
    headers: { Accept: "application/json" },
    timeout: 30000,
});

/** Définit (ou retire) le token Bearer pour toutes les requêtes. */
export function setAuthToken(token) {
    if (token) {
        api.defaults.headers.common.Authorization = `Bearer ${token}`;
    } else {
        delete api.defaults.headers.common.Authorization;
    }
}

/** Extrait un message d'erreur lisible d'une réponse API. */
export function apiError(error, fallback = "Une erreur est survenue") {
    const res = error?.response;
    if (res?.data?.message) return res.data.message;
    if (res?.data?.errors) {
        const first = Object.values(res.data.errors)[0];
        return Array.isArray(first) ? first[0] : String(first);
    }
    if (error?.message === "Network Error") {
        return "Impossible de joindre le serveur. Vérifiez l'URL de l'API (src/config.js) et que le backend tourne.";
    }
    return fallback;
}

export default api;
