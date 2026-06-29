/**
 * URL de base de l'API Laravel.
 *
 * ⚠️ À adapter selon votre environnement :
 *  - Émulateur Android  : http://10.0.2.2:8000/api   (10.0.2.2 = localhost du PC)
 *  - Simulateur iOS     : http://localhost:8000/api
 *  - Téléphone réel     : http://<IP-LAN-de-votre-PC>:8000/api  (ex: 192.168.1.20)
 *  - Production         : https://votre-domaine.com/api
 *
 * Démarrez le backend avec :  php artisan serve --host=0.0.0.0 --port=8000
 */
export const API_BASE_URL = "http://10.0.2.2:8000/api";

export const CURRENCY = "FCFA";

/**
 * Identifiants OAuth pour la connexion Google / Facebook.
 * Laissez vides tant que non configurés (les boutons afficheront un rappel).
 * Voir mobile/README.md (section "Connexion Google / Facebook").
 */
export const GOOGLE_CLIENT_IDS = {
    expo: "", // Web client ID (utilisé dans Expo Go)
    android: "", // Android client ID
    ios: "", // iOS client ID
    web: "", // Web client ID
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
