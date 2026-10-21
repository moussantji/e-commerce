import axios from "axios";
import { API_BASE_URL } from "../config";
import { cacheKey, readCache, writeCache } from "./cache";

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

/**
 * Endpoints publics dont les réponses peuvent être réutilisées hors ligne.
 * Les endpoints du compte (commandes, panier, favoris, portefeuille, avis...)
 * ne sont volontairement jamais mis en cache.
 */
const CACHEABLE_PREFIXES = [
    "/products",
    "/categories",
    "/search/suggestions",
    "/payment-methods",
    "/shipping-methods",
];

function isCacheable(config) {
    if (!config) return false;
    if (String(config.method || "get").toLowerCase() !== "get") return false;

    const url = String(config.url || "");
    if (!url.startsWith("/")) return false;

    return CACHEABLE_PREFIXES.some(
        (prefix) =>
            url === prefix ||
            url.startsWith(`${prefix}/`) ||
            url.startsWith(`${prefix}?`),
    );
}

function isOfflineError(error) {
    if (!error || error.code === "ERR_CANCELED") return false;
    return (
        !error.response ||
        error.code === "ERR_NETWORK" ||
        error.code === "ECONNABORTED" ||
        error.message === "Network Error"
    );
}

api.interceptors.response.use(
    (response) => {
        const config = response.config;
        if (isCacheable(config) && response.status >= 200 && response.status < 300) {
            // Écriture non bloquante : l'écran ne doit pas attendre le disque.
            writeCache(
                cacheKey(config.method, config.url, config.params, config.baseURL),
                response.data,
            ).catch(() => {});
        }
        return response;
    },
    async (error) => {
        const config = error?.config;

        if (isCacheable(config) && isOfflineError(error)) {
            const cached = await readCache(
                cacheKey(config.method, config.url, config.params, config.baseURL),
            );
            if (cached) {
                return {
                    data: cached.data,
                    status: 200,
                    statusText: "OK (cache hors ligne)",
                    headers: {},
                    config,
                    request: {},
                    fromCache: true,
                    cachedAt: cached.savedAt,
                };
            }
        }

        return Promise.reject(error);
    },
);

/** Extrait un message d'erreur lisible d'une réponse API. */
export function apiError(error, fallback = "Une erreur est survenue") {
    const res = error?.response;
    if (res?.status >= 500) {
        return "Le serveur rencontre un problème. Réessayez dans quelques instants.";
    }
    if (res?.status === 401) {
        const message = res.data?.message;
        return !message || message === "Unauthenticated."
            ? "Veuillez vous connecter pour continuer."
            : message;
    }
    if (res?.data?.message) return res.data.message;
    if (res?.data?.errors) {
        const first = Object.values(res.data.errors)[0];
        return Array.isArray(first) ? first[0] : String(first);
    }
    if (isOfflineError(error)) {
        return "Pas de connexion internet. Vérifiez votre réseau et réessayez.";
    }
    return fallback;
}

export default api;
