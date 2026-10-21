/**
 * URL de base de l'API Laravel.
 *
 * L'application mobile utilise l'API publique de production.
 */
export const API_BASE_URL = "https://sugu.mandenbaoubab.com/api";

/** Racine du site web (utilisée pour le retour OAuth Google). */
export const SITE_URL = API_BASE_URL.replace(/\/api\/?$/, "");

export const CURRENCY = "FCFA";

/**
 * Identifiants OAuth pour la connexion Google.
 *
 * Le flux Expo Go / navigateur passe par le backend (client OAuth web) :
 * seul le Web client ID est nécessaire ici. Pour un build natif Android/iOS
 * avec @react-native-google-signin, créez aussi un client OAuth Android
 * (package + SHA-1) et/ou iOS, puis renseignez-les ci-dessous.
 */
export const GOOGLE_CLIENT_IDS = {
    expo: "362195699932-7d4f145lg0jtnqrugf27g7e5h1mgi847.apps.googleusercontent.com", // Web client ID (Expo Go)
    android: "", // À renseigner pour un build Android natif
    ios: "", // À renseigner pour un build iOS natif
    web: "362195699932-7d4f145lg0jtnqrugf27g7e5h1mgi847.apps.googleusercontent.com", // Web client ID
};

/**
 * Fond des écrans Connexion / Inscription.
 *  - URL distante :  "https://....jpg"
 *  - Image locale :  require("../../assets/login-bg.jpg")
 *  - null         :  garde le dégradé orange
 */
export const LOGIN_BG_IMAGE =
    "https://images.unsplash.com/photo-1607082349566-187342175e2f?auto=format&fit=crop&w=1080&q=80";
