<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).



---

# 🚀 Déploiement sur LWS (hébergement mutualisé) + configuration des emails

Ce guide explique comment déployer ce projet **Laravel** sur un hébergement **LWS**
(offre mutualisée avec cPanel), configurer la base de données, les emails
(y compris les **emails professionnels LWS**), et l'application mobile.

## 1. Prérequis côté LWS

- Un hébergement LWS avec **PHP 8.2+** (réglable dans cPanel → « Sélectionner la version de PHP »).
- Une **base de données MySQL** (cPanel → « Bases de données MySQL »).
- Un **nom de domaine** pointant vers l'hébergement.
- Accès **SSH** (recommandé) ou à défaut le **Gestionnaire de fichiers** cPanel + FTP.
- Extensions PHP activées : `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd`.

## 2. Base de données

1. cPanel → **Bases de données MySQL** → créez une base (ex : `monsite_db`).
2. Créez un utilisateur MySQL + mot de passe, puis **ajoutez-le à la base** avec tous les privilèges.
3. Notez : nom de la base, utilisateur, mot de passe, hôte (souvent `localhost`).

## 3. Envoi des fichiers

**Option A — Git (si SSH disponible) :**
```bash
cd ~
git clone https://github.com/moussantji/e-commerce.git monsite
cd monsite
composer install --no-dev --optimize-autoloader
```

**Option B — FTP / Gestionnaire de fichiers :**
- Envoyez tout le projet dans un dossier privé (ex : `~/monsite`), **hors** de `public_html`.
- Uploadez aussi le dossier `vendor/` (généré en local via `composer install --no-dev`).

> ⚠️ Sur mutualisé, ne mettez pas tout le projet dans `public_html`. On fait pointer
> le domaine vers le dossier `public/` uniquement (étape 5).

## 4. Fichier `.env`

Copiez `.env.example` en `.env` puis renseignez :

```dotenv
APP_NAME="Ma Boutique"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# Base de données (valeurs de l'étape 2)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=monsite_db
DB_USERNAME=monsite_user
DB_PASSWORD=motdepasse

# Sessions / cache / file d'attente
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database
```

Générez la clé d'application :
```bash
php artisan key:generate
```

## 5. Faire pointer le domaine vers `/public`

Deux méthodes :

- **Recommandé (cPanel → Domaines) :** définissez la **racine du document**
  (Document Root) du domaine sur `.../monsite/public`.
- **Sinon (mutualisé classique) :** placez le contenu de `public/` dans `public_html/`
  et éditez `public_html/index.php` pour corriger les chemins :
  ```php
  require __DIR__.'/../monsite/vendor/autoload.php';
  $app = require_once __DIR__.'/../monsite/bootstrap/app.php';
  ```

## 6. Migrations, seeders et optimisation

```bash
php artisan migrate --force
# Méthodes de paiement (Orange Money, Moov Money, Wave, Paiement à la livraison)
php artisan db:seed --class=PaymentMethodSeeder
# (Optionnel) données de démonstration
php artisan db:seed --force

# Lien de stockage (images uploadées)
php artisan storage:link

# Cache de production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Droits d'écriture :
```bash
chmod -R 775 storage bootstrap/cache
```

## 7. Tâches planifiées (cron)

cPanel → **Tâches Cron** → ajoutez (toutes les minutes) :
```
* * * * * cd /home/UTILISATEUR/monsite && php artisan schedule:run >> /dev/null 2>&1
```

---

# 📧 Configuration des emails (SMTP LWS)

Par défaut le projet écrit les emails dans les logs (`MAIL_MAILER=log`). En production,
utilisez le **SMTP de votre email professionnel LWS**.

## 1. Créer un email professionnel

cPanel → **Comptes de messagerie** → créez par ex. `contact@votre-domaine.com`
(ou `no-reply@votre-domaine.com`) avec un mot de passe fort.

## 2. Paramètres SMTP LWS dans `.env`

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=mail.votre-domaine.com      # ou le serveur SMTP indiqué par LWS
MAIL_PORT=465                          # 465 (SSL) recommandé, ou 587 (TLS)
MAIL_USERNAME=contact@votre-domaine.com
MAIL_PASSWORD=le_mot_de_passe_de_la_boite
MAIL_ENCRYPTION=ssl                    # ssl pour 465, tls pour 587
MAIL_FROM_ADDRESS="contact@votre-domaine.com"
MAIL_FROM_NAME="${APP_NAME}"
```

> 🔎 Les valeurs exactes (serveur entrant/sortant) sont visibles dans
> cPanel → Comptes de messagerie → **Connecter des appareils** / « Paramètres de messagerie ».

Rechargez la config après modification :
```bash
php artisan config:cache
php artisan queue:restart
```

## 3. File d'attente des emails (recommandé)

Les notifications (changement de statut de commande, paiement à vérifier, etc.)
partent par email. Pour ne pas ralentir les requêtes, utilisez la file d'attente :

```dotenv
QUEUE_CONNECTION=database
```
```bash
php artisan queue:table && php artisan migrate --force
```
Puis lancez un worker (Cron toutes les minutes, ou Supervisor si VPS) :
```
* * * * * cd /home/UTILISATEUR/monsite && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

## 4. Emails « pro » : bonnes pratiques de délivrabilité

Pour éviter le spam, ajoutez ces enregistrements DNS (cPanel → **Éditeur de zone DNS**) :

- **SPF** (TXT sur `@`) : `v=spf1 include:_spf.lws.fr ~all` (adaptez à l'include indiqué par LWS).
- **DKIM** : activez-le via cPanel → **Authentification de l'email** (génère la clé DKIM).
- **DMARC** (TXT sur `_dmarc`) : `v=DMARC1; p=none; rua=mailto:postmaster@votre-domaine.com`.
- Utilisez une adresse d'expéditeur **du même domaine** que le site (`@votre-domaine.com`).

## 5. Tester l'envoi

```bash
php artisan tinker
>>> Mail::raw('Test LWS', fn($m) => $m->to('votre@email.com')->subject('Test'));
```
Vérifiez la réception (et le dossier spam la première fois).

---

# 📱 Application mobile (Expo)

Le dossier `mobile/` contient l'app React Native (Expo SDK 52).

```bash
cd mobile
npm install
npx expo install expo-image-picker   # requis (avatar, photos d'avis)
```

Configurez l'URL de l'API dans `mobile/src/config.js` :
```js
// Production
export const API_BASE_URL = "https://votre-domaine.com/api";
```

Lancement en développement : `npx expo start`
Build de production : voir la documentation Expo (EAS Build) dans `mobile/README.md`.

---

# ✅ Récapitulatif post-déploiement

- [ ] `.env` renseigné (DB, `APP_URL`, `APP_KEY`, SMTP)
- [ ] `php artisan migrate --force` + `db:seed --class=PaymentMethodSeeder`
- [ ] `php artisan storage:link`
- [ ] `config:cache` / `route:cache` / `view:cache`
- [ ] Domaine pointé sur `/public`, HTTPS actif (LWS → Let's Encrypt)
- [ ] Email pro créé + SMTP configuré + SPF/DKIM/DMARC
- [ ] Cron `schedule:run` (+ `queue:work` si file d'attente)
- [ ] `mobile/src/config.js` → `API_BASE_URL` en HTTPS
- [ ] Un compte administrateur (`role = admin`) pour valider les paiements
