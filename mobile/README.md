# 📱 E-Commerce — Application mobile (React Native / Expo)

Application mobile **Android & iOS** qui consomme l'API REST du backend Laravel.

## 🏗️ Architecture

```
┌────────────────────┐      HTTPS/JSON      ┌──────────────────────┐      SQL       ┌─────────────────┐
│  App React Native  │  ───────────────▶   │  API Laravel (Sanctum)│  ──────────▶  │  MySQL distant  │
│   (Android / iOS)  │   token Bearer      │   /api/...            │               │                 │
└────────────────────┘                     └──────────────────────┘               └─────────────────┘
```

> ⚠️ **Important** : une app mobile ne se connecte **jamais** directement à MySQL (identifiants exposés = faille de sécurité). Elle passe par l'API Laravel, qui est la seule à parler à la base.

---

## 1) Backend Laravel + MySQL distant

### Configurer la base MySQL distante (`.env` du projet Laravel)

```env
DB_CONNECTION=mysql
DB_HOST=123.45.67.89        # IP ou hôte de votre serveur MySQL distant
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=ecommerce_user
DB_PASSWORD=motdepasse_solide

APP_URL=http://192.168.1.20:8000   # URL publique du backend (sert aussi pour les images)
```

Côté serveur MySQL distant, autorisez la connexion distante :
- `bind-address = 0.0.0.0` dans la config MySQL,
- un utilisateur autorisé depuis l'extérieur : `CREATE USER 'ecommerce_user'@'%' ...; GRANT ALL ON ecommerce.* TO 'ecommerce_user'@'%';`
- ouvrez le port **3306** dans le pare-feu (idéalement restreint à l'IP du serveur applicatif).

Puis :
```bash
composer install
php artisan migrate
php artisan db:seed                 # données de démo (produits, etc.)
php artisan storage:link
php artisan serve --host=0.0.0.0 --port=8000   # --host=0.0.0.0 pour être joignable depuis le téléphone
```

### Endpoints exposés (`routes/api.php`)
| Méthode | URL | Auth | Description |
|---|---|---|---|
| POST | `/api/register` | – | Inscription (renvoie un token) |
| POST | `/api/login` | – | Connexion (renvoie un token) |
| GET | `/api/products` | – | Liste paginée (`?search=`, `?category_id=`, `?featured=1`, `?sort=price_asc`) |
| GET | `/api/products/{id}` | – | Détail produit |
| GET | `/api/categories` | – | Catégories |
| GET | `/api/me` | ✅ | Profil courant |
| POST | `/api/logout` | ✅ | Déconnexion |
| GET/POST/PUT/DELETE | `/api/cart` | ✅ | Panier (lister / ajouter / modifier / retirer) |

L'authentification se fait par **token Bearer** (Laravel Sanctum) : `Authorization: Bearer <token>`.

---

## 2) Lancer l'application mobile

### Prérequis
- Node.js 18+ et npm
- L'app **Expo Go** sur votre téléphone (Play Store / App Store), ou un émulateur Android / simulateur iOS

### Installation
```bash
cd mobile
npm install
# Si des versions natives ne s'alignent pas avec le SDK Expo :
npx expo install --fix
```

### Configurer l'URL de l'API
Éditez **`src/config.js`** :
```js
// Émulateur Android :
export const API_BASE_URL = 'http://10.0.2.2:8000/api';
// Simulateur iOS :        'http://localhost:8000/api'
// Téléphone réel (LAN) :  'http://192.168.1.20:8000/api'   // IP de votre PC
// Production :            'https://votre-domaine.com/api'
```

### Démarrer
```bash
npx expo start
```
Scannez le QR code avec **Expo Go** (téléphone sur le **même réseau Wi-Fi** que le PC), ou appuyez sur `a` (Android) / `i` (iOS).

---

## 3) Générer les applications Android (.aab/.apk) et iOS (.ipa)

On utilise **EAS Build** (service de build d'Expo, pas besoin de Mac pour iOS) :

```bash
npm install -g eas-cli
eas login
eas build:configure

# Android (APK de test) :
eas build -p android --profile preview

# Android (AAB pour le Play Store) :
eas build -p android --profile production

# iOS (nécessite un compte Apple Developer) :
eas build -p ios --profile production
```

Le build se fait dans le cloud Expo ; vous récupérez un lien de téléchargement à la fin.
Pour publier des mises à jour instantanées (OTA) sans repasser par les stores : `eas update`.

---

## 📂 Structure
```
mobile/
├── App.js                      # point d'entrée + providers
├── app.json                    # config Expo (nom, icônes, bundle ids)
└── src/
    ├── config.js               # URL de l'API + devise
    ├── api/client.js           # axios + token Bearer
    ├── context/
    │   ├── AuthContext.js       # session (login/register/logout, SecureStore)
    │   └── CartContext.js       # panier (API)
    ├── navigation/index.js      # tabs (Boutique/Panier/Profil) + stack détail
    └── screens/
        ├── LoginScreen.js
        ├── RegisterScreen.js
        ├── ProductsScreen.js    # grille + recherche + pagination
        ├── ProductDetailScreen.js
        ├── CartScreen.js
        └── ProfileScreen.js
```

## Fonctionnalités incluses
- 🔐 Inscription / connexion par token (stocké de façon sécurisée via `expo-secure-store`)
- 🛍️ Catalogue produits (recherche, pagination, promo, note)
- 📄 Fiche produit + ajout au panier (contrôle de stock côté serveur)
- 🛒 Panier (quantités, suppression, total) avec badge dans l'onglet
- 👤 Profil + déconnexion

## Pistes d'évolution
- Paiement (CinetPay / Stripe) à l'étape commande
- Catégories en écran dédié, favoris/wishlist, notifications push (`expo-notifications`)



---

## 🔑 Connexion Google / Facebook

L'app propose la connexion sociale (boutons sur Login/Register). Le flux :
**App (expo-auth-session) → token → `POST /api/auth/social` (Laravel Socialite vérifie le token) → token Sanctum.**

### A. Côté backend (`.env` du projet Laravel)
```env
GOOGLE_CLIENT_ID=xxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=xxxx
GOOGLE_REDIRECT_URI=${APP_URL}/auth/google/callback

FACEBOOK_CLIENT_ID=xxxx
FACEBOOK_CLIENT_SECRET=xxxx
FACEBOOK_REDIRECT_URI=${APP_URL}/auth/facebook/callback
```
(`config/services.php` contient déjà les entrées `google` et `facebook`.)

### B. Côté mobile (`mobile/src/config.js`)
```js
export const GOOGLE_CLIENT_IDS = {
  expo: 'XXX.apps.googleusercontent.com',     // Web client ID (Expo Go)
  android: 'XXX.apps.googleusercontent.com',  // Android client ID
  ios: 'XXX.apps.googleusercontent.com',      // iOS client ID
  web: 'XXX.apps.googleusercontent.com',      // Web client ID
};
export const FACEBOOK_APP_ID = '0000000000';
```

### Où obtenir les identifiants
- **Google** : [Google Cloud Console](https://console.cloud.google.com/) → APIs & Services → Credentials → « OAuth client ID ». Créez un client par plateforme (Web/Android/iOS). Pour Android, ajoutez l'empreinte SHA-1 (`eas credentials`).
- **Facebook** : [Meta for Developers](https://developers.facebook.com/) → créez une app → « Facebook Login » → récupérez l'**App ID**.

> Tant que ces identifiants ne sont pas renseignés, les boutons Google/Facebook
> affichent simplement un rappel — la connexion email/mot de passe fonctionne normalement.



---

## 🖼️ Fond des écrans Connexion / Inscription

Par défaut, un **fond image** (avec voile orange pour la lisibilité) est utilisé.
Réglable dans `mobile/src/config.js` :

```js
// Image distante :
export const LOGIN_BG_IMAGE = "https://…/photo.jpg";
// Image locale (placez le fichier dans mobile/assets/) :
export const LOGIN_BG_IMAGE = require("../../assets/login-bg.jpg");
// Revenir au dégradé orange :
export const LOGIN_BG_IMAGE = null;
```

### 🎬 Fond vidéo (optionnel)
La vidéo nécessite la librairie `expo-av` :
```bash
cd mobile
npx expo install expo-av
```
Puis dans `src/components/AuthBackground.js`, remplacez l'`ImageBackground` par :
```jsx
import { Video, ResizeMode } from "expo-av";

<Video
  source={{ uri: "https://votre-domaine.com/login-bg.mp4" }}  // ou require("../../assets/login.mp4")
  style={StyleSheet.absoluteFill}
  resizeMode={ResizeMode.COVER}
  shouldPlay
  isLooping
  isMuted
/>
```
(gardez le voile `LinearGradient` au‑dessus pour la lisibilité). ⚠️ Une vidéo en boucle consomme plus de batterie/données ; une image est recommandée pour un écran de connexion.
