/**
 * URL de base de l'API Laravel.
 *
 * ⚠️ À adapter selon votre environnement :
 *  - Émulateur Android  : http://sugu.mandenbaoubab.com/api   (10.0.2.2 = localhost du PC)
 *  - Simulateur iOS     : http://sugu.mandenbaoubab.com/api
 *  - Téléphone réel     : http://<IP-LAN-de-votre-PC>:8000/api  (ex: 192.168.1.20)
 *  - Production         : https://sugu.mandenbaoubab.com/api
 *
 * Démarrez le backend avec :  php artisan serve --host=0.0.0.0 --port=8000
 */
export const API_BASE_URL = "https://sugu.mandenbaoubab.com/api";

export const CURRENCY = "FCFA";

/**
 * Identifiants OAuth pour la connexion Google / Facebook.
 * Laissez vides tant que non configurés (les boutons afficheront un rappel).
 * Voir mobile/README.md (section "Connexion Google / Facebook").
 */
export const GOOGLE_CLIENT_IDS = {
    expo: "767565152255-6t15btstu5uej5u3k8vl1bcipfj25c0h.apps.googleusercontent.com", // Web client ID (utilisé dans Expo Go)
    android: "767565152255-6t15btstu5uej5u3k8vl1bcipfj25c0h.apps.googleusercontent.com", // Android client ID
    ios: "767565152255-6t15btstu5uej5u3k8vl1bcipfj25c0h.apps.googleusercontent.com", // iOS client ID
    web: "767565152255-6t15btstu5uej5u3k8vl1bcipfj25c0h.apps.googleusercontent.com", // Web client ID
};

export const FACEBOOK_APP_ID = "";

/**
 * Fond des écrans Connexion / Inscription.
 *  - URL distante :  "https://....jpg"
 *  - Image locale :  require("../../assets/login-bg.jpg")
 *  - null         :  garde le dégradé orange
 */
export const LOGIN_BG_IMAGE =
    "https://images.unsplash.com/photo-1607082349566-187342175e2f?auto=format&fit=crop&w=1080&q=80";
