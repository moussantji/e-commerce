-- Import des produits réels du catalogue Maden Baoubab.
-- La colonne `id` est volontairement OMISE : l'auto-incrément attribue de
-- NOUVELLES clés (aucune collision avec des id existants).
--
-- Prérequis :
--   - les category_id / brand_id référencés doivent exister dans la base cible ;
--   - les `sku` doivent être uniques (ils ont été nettoyés des espaces de fin).
--
-- Colonnes (sans id) :
--   name, description, price, sale_price, stock, image, images, sku,
--   weight, length, width, height, is_active, is_featured, category_id,
--   created_at, updated_at, brand_id

INSERT INTO `produits`
(`name`, `description`, `price`, `sale_price`, `stock`, `image`, `images`, `sku`, `weight`, `length`, `width`, `height`, `is_active`, `is_featured`, `category_id`, `created_at`, `updated_at`, `brand_id`)
VALUES
('Casque sans fil Bluetooth', 'Casque audio confortable avec réduction de bruit active.
son puissant & clair
✔️ Réduction de bruit
✔️ Très confortable
✔️ Boîte + pochette incluses
', 15000.00, 10000, 99, NULL, NULL, 'casque-sans-fil-bluetooth', NULL, NULL, NULL, NULL, 1, 0, 11, '2026-06-04 18:30:43', '2026-06-14 15:21:57', NULL),
('Tapettes', ' Confortables et légères
✅ Qualité durable
✅ Design tendance
 idéales pour un style casual.', 8000.00, 5300, 73, NULL, NULL, 'tapettes', NULL, NULL, NULL, NULL, 1, 0, 15, '2026-06-04 18:30:43', '2026-06-14 16:01:25', NULL),
('Jumbo', 'Épicerie', 850.00, 800, 10, NULL, NULL, 'jumbo', NULL, NULL, NULL, NULL, 1, 0, 36, '2026-06-08 15:07:49', '2026-06-08 15:08:30', NULL),
('Robe Hijab', 'Coupe longue et ample pour un maximum de confort
Tissu léger, doux et agréable à porter
Design élégant et intemporel
Convient aux occasions décontractées et habillées
Facile à assortir avec différents styles de hijabs et accessoires

📏 Tailles disponibles : S, M, L, XL, XXL
🎨 Coloris disponibles : Plusieurs couleurs selon disponibilité', 12000.00, 10000, 10, NULL, NULL, 'robe-hijabs', NULL, NULL, NULL, NULL, 1, 0, 14, '2026-06-14 14:41:05', '2026-06-14 14:48:59', NULL),
('Tablette galaxy A11', 'Galaxy Tab A11 64Go / RAM 4Go (Carton Original) 
✅ Écran large et confortable
✅ 64Go de stockage
✅ RAM 4Go fluide et rapide
✅ Garantie 24 mois', 100000.00, 93000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 9, '2026-06-14 16:14:11', '2026-06-14 17:15:09', NULL),
('Bazin super riche', '* 3 mètres 👉 à 12.500 Fcfa
* 4 mètres 👉 à 17.000 Fcfa
* 5 mètres 👉 à 20.000 Fcfa
* 6 mètres 👉 à 23.000 Fcfa
* 7 mètres 👉 à 26.000 Fcfa
* 8 mètres 👉 à 29.000 Fcfa
* 9 mètres 👉 à 32.000 Fcfa
* 10 mètres 👉 à 36.000 Fcfa', 3550.00, 3000, 60, NULL, NULL, 'bazin-super-riche', NULL, NULL, NULL, NULL, 1, 0, 2, '2026-06-14 16:38:06', '2026-06-14 16:38:58', NULL),
('Taser rechargeable+Torche', 'Puissant • Compact • Facile à transporter
💡 Idéal pour votre sécurité de nuit', 13500.00, 10000, 20, NULL, NULL, 'taser-rechargeable-torche', NULL, NULL, NULL, NULL, 1, 0, 1, '2026-06-14 16:51:23', '2026-06-14 17:13:57', NULL),
('Congélateur Roch', ' Congélateur ROCH 6 Étagères ❄️
✅ Donne deux fois de glace en 24h
✅ Grande capacité de stockage
✅ 6 étagères pratiques et bien espacées
✅ Refroidissement rapide et efficace
✅ Faible consommation d’énergie
✅ Idéal pour les maisons, boutiques et restaurants', 210000.00, 200000, 15, NULL, NULL, 'congelateur-roch', NULL, NULL, NULL, NULL, 1, 0, 1, '2026-06-14 17:00:25', '2026-06-14 17:13:42', NULL),
('Humidificateur Super Sugu', ' 2 batteries – Autonomie jusqu’à 12h
⚡ Puissance : 75W', 145000.00, 115000, 15, NULL, NULL, 'humidificateur-super-sugu', NULL, NULL, NULL, NULL, 1, 0, 1, '2026-06-14 17:07:25', '2026-06-14 17:13:26', NULL),
('Tecno Camon 50Pro', ' Carton Original 256 Go 📱
✅ Caméra professionnelle 50 MP avec zoom avancé
✅ Grand stockage 256 Go + 16 Go RAM étendue
✅ Écran incurvé élégant et fluide
✅ Résistant à l’eau et à la poussière (IP68/IP69)
✅ Performances puissantes avec l’intelligence artificielle TECNO AI', 200000.00, 197000, 10, NULL, NULL, 'tecno-camon', NULL, NULL, NULL, NULL, 1, 0, 9, '2026-06-14 17:23:05', '2026-06-14 17:34:27', NULL),
('Tecno Spark 50', 'Carton Original 📱
✅ Batterie géante 6700 mAh
✅ Caméra 50 MP FlashSnap
✅ Grande capacité de stockage
✅ Résistant à la poussière et aux éclaboussures
✅ Téléphone rapide et performant
📱 128 Go : à 110.000 FCFA
📱 256 Go : à 125.000 FCFA', 117000.00, 110000, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 9, '2026-06-14 17:30:24', '2026-06-14 17:30:40', NULL),
('Tecno Pop 20', 'Carton Original 📱 64giga & 128giga

✅ Grande autonomie de batterie
✅ Double caméra avec beau design
✅ Écran large et fluide
✅ Recharge rapide 15W
✅ Idéal pour un usage quotidien
📱 64 Go : à 85.000 FCFA
📱 128 Go : à 95.000 FCFA', 91000.00, 85000, 10, NULL, NULL, 'tecno-pop', NULL, NULL, NULL, NULL, 1, 0, 9, '2026-06-14 17:38:45', '2026-06-14 17:40:16', NULL),
('Tondeuse Rechargeable Dragon', '✅ Rechargeable USB
✅ Idéale pour barbe, cheveux et finitions
✅ Compacte et facile à transporter
✅ Livrée avec plusieurs sabots de coupe
✅ Design premium et élégant
', 10000.00, 8000, 10, NULL, NULL, 'tondeuse-rechargeable', NULL, NULL, NULL, NULL, 1, 0, 1, '2026-06-14 17:52:22', '2026-06-14 17:52:45', NULL),
('Bracelet Montre Lumineux', 'Bracelet Montre LED Lumineux', 7000.00, 5000, 10, NULL, NULL, 'montre-lumineux', NULL, NULL, NULL, NULL, 1, 0, 16, '2026-06-14 17:58:46', '2026-06-14 17:59:05', NULL),
('Robe', 'jolies robes 2 pièces avec dessous et corde 🥰🥰🥰🥰
', 13000.00, 10000, 10, NULL, NULL, 'robe', NULL, NULL, NULL, NULL, 1, 0, 14, '2026-06-14 18:06:42', '2026-06-14 18:07:08', NULL),
('Huile Peeling Extra Strong', ' (Soin réparateur intense)
✅ Élimine les peaux mortes en profondeur
✅ Rend la peau plus lisse et plus propre
✅ Aide à améliorer l’aspect des taches et cicatrices
✅ Soin efficace pour une peau renouvelée
🔥 Résultat visible après quelques jours', 12000.00, 7000, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 21, '2026-06-14 18:16:32', '2026-06-21 14:00:30', NULL),
('Play 5 Mini', 'Playe 5 Mini RS5 🔥🔥🔥
✅ Livré avec 2 manettes style PlayStation 5
✅ 🎮 manette Fonctionne avec pile 🔋
✅ Carte intégrée avec plus de 20.000 jeux 🎮
✅ Idéal pour enfants et adultes – amusement garanti à la maison 🥰', 40000.00, 30000, 10, NULL, NULL, 'play-5mini', NULL, NULL, NULL, NULL, 1, 0, 1, '2026-06-21 13:23:37', '2026-06-21 13:24:01', NULL),
('Congélateur Hisense', ' 
✅ Grande capacité de stockage
✅ 6 étagères pratiques et bien organisées
✅ Produit une excellente glace
✅ Peut faire de la glace jusqu''à 2 fois par jour (24h)
✅ Économique et performant
', 230000.00, 210000, 10, NULL, NULL, 'congelateur-hisense', NULL, NULL, NULL, NULL, 1, 0, 1, '2026-06-21 13:36:37', '2026-06-21 13:39:13', NULL),
('Sérum De Visages', 'Sérums Visage de très bonne qualité 
✅ Hydrate et nourrit la peau ✅ Aide à réduire les taches du visage 
✅ Rend la peau plus douce et éclatante 
✅ Convient à tous les types de peau 
✅ Format pratique de 30 ml', 10000.00, 7500, 10, NULL, NULL, 'serum-de-visage', NULL, NULL, NULL, NULL, 1, 0, 21, '2026-06-21 13:47:10', '2026-06-21 14:00:06', NULL),
('Air Force Nike', ' Air Force Nike 

✅ Design élégant et tendance
✅ Confortable pour un usage quotidien
✅ Semelle résistante et durable
✅ Convient aux hommes et aux femmes', 15000.00, 11500, 10, NULL, NULL, 'air-force', NULL, NULL, NULL, NULL, 1, 0, 15, '2026-06-21 13:52:11', '2026-06-21 13:52:57', NULL),
('Parfum Monark', ' Eau de Parfum 👑
✨ Un parfum élégant et puissant pour hommes
💎 Odeur intense qui attire l’attention
🕐 Longue tenue sur les habits et sur la peau', 15000.00, 12000, 10, NULL, NULL, 'parfum-monark', NULL, NULL, NULL, NULL, 1, 0, 23, '2026-06-21 13:58:32', '2026-06-21 13:59:26', NULL),
('Pistolet De Massage Corporel', 'Grand Pistolet de Massage Corporel Rechargeable
🔥 Double tête + Mini percussion musculaire
✅ Massage profond et puissant
✅ Soulage rapidement les douleurs du dos, des jambes et des épaules
✅ Double tête pour un massage plus efficace
✅ Rechargeable et facile à utiliser
✅ Plusieurs embouts inclus pour tout le corps
✅ Idéal après le sport, le travail ou la fatigue', 22000.00, 15000, 10, NULL, NULL, 'pistolet-de-massage', NULL, NULL, NULL, NULL, 1, 0, 4, '2026-06-21 14:06:17', '2026-06-21 14:06:35', NULL),
('Lampe Led', 'Lampe LED Design Champignon ✨
✅ Fonctionne avec des piles 
✅ Lumière douce et chaleureuse 
✅ Design moderne et élégant 
✅ Idéale pour chambre, salon ou bureau 
✅ Facile à déplacer et à utiliser', 10000.00, 7500, 10, NULL, NULL, 'lampe-led', NULL, NULL, NULL, NULL, 1, 0, 16, '2026-06-21 14:13:41', '2026-06-21 14:14:07', NULL),
('Robe Adja', ' jolies robes Adja-Bienvenue avec large foulard 
✅ Robe élégante et confortable 
✅ Large foulard assorti inclus 
✅ Tissu en coton industriel de qualité 
✅ Taille standard 
✅ Idéale pour sorties et événements', 12500.00, 10000, 10, NULL, NULL, 'robe-adja', NULL, NULL, NULL, NULL, 1, 0, 14, '2026-06-21 14:20:38', '2026-06-21 14:20:58', NULL),
('Tissus Velours', 'Très bonne qualité 💯

le complet (3 mètres)

', 15000.00, 11500, 10, NULL, NULL, 'tissus-velours', NULL, NULL, NULL, NULL, 1, 0, 14, '2026-06-21 14:26:20', '2026-06-21 14:28:45', NULL),
('Claquette Homme', 'Design moderne et élégant
Très confortable au quotidien
Semelle résistante et antidérapants
Pointures : 40, 41, 42, 43, 44, 45', 7500.00, 6000, 10, NULL, NULL, 'claquette-homme', NULL, NULL, NULL, NULL, 1, 0, 15, '2026-06-25 21:00:09', '2026-06-25 21:00:54', 22),
('Coffret Matelot', ' Parfum + Déodorant 🩵
✅ Parfum longue tenue
✅ Déodorant assorti inclus
✅ Élégant coffret cadeau
✅ Idéal pour un usage quotidien', 12000.00, 10000, 10, NULL, NULL, 'coffret-matelot', NULL, NULL, NULL, NULL, 1, 0, 23, '2026-06-25 21:05:48', '2026-06-25 21:06:05', NULL),
('Led RGB Multicolore', ' LED Rouleau 10 mètres RGB Multicolore 🌈✨
✅ 10 mètres de longueur 
✅ Colle adhésive intégrée 
✅ Facile à installer sur les murs et plafonds 
✅ Plusieurs couleurs et effets lumineux 
✅ Idéal pour chambres, salons, boutiques et décorations', 10500.00, 8000, 10, NULL, NULL, 'led-rgb-multicolore', NULL, NULL, NULL, NULL, 1, 0, 18, '2026-06-25 21:17:16', '2026-06-25 21:17:57', NULL),
('Robe Foulards', ' Robes👗 avec Foulard 🥰🥰🥰

✅ Foulard assorti inclus
✅ Tissu doux et brillant
✅ Taille standard
✅ Confortable et élégante
✅ Plusieurs coloris disponibles', 10000.00, 7500, 10, NULL, NULL, 'robe-foulards', NULL, NULL, NULL, NULL, 1, 0, 14, '2026-06-25 21:23:54', '2026-06-25 21:24:16', NULL),
('Robe Kashka Avec Foulard', 'robes Kashka avec foulard 👗🥰
✨ Foulard assorti inclus
✨ Tissu coton industriel de qualité
✨ Taille standard
✨ Modèle élégant et confortable', 14000.00, 10000, 10, NULL, NULL, 'robe-kashka-foulard', NULL, NULL, NULL, NULL, 1, 0, 14, '2026-06-25 21:33:19', '2026-06-25 21:33:37', NULL),
('Tapette Louis Vuitton', 'Tapettes Louis Vuitton ✨
✅ Design moderne et élégant
✅ Très confortable pour un usage quotidien
✅ Finition de qualité supérieure
✅ Léger, résistant et tendance
Pointures disponibles :
41 • 42 • 43 • 44 • 45', 13000.00, 8000, 10, NULL, NULL, 'tapette-louis-vuitton', NULL, NULL, NULL, NULL, 1, 0, 15, '2026-06-25 21:50:03', '2026-06-25 21:50:21', NULL),
('Brodé Homme', ' Brodés pour Homme ✨🥰🥰🥰
👔 Brodés élégants et de qualité supérieure
✅ Idéal pour les fêtes, mariages, baptêmes et événements spéciaux.
📏 Un complet = 4m50 (5 yards)', 17500.00, 14000, 0, NULL, NULL, 'brode-homme', NULL, NULL, NULL, NULL, 1, 0, 13, '2026-06-25 21:56:12', '2026-06-25 21:56:12', NULL),
('Tissus Chanel Multicolore', ' Tissus Chanelle Multicolore ✨🥰
🌈 Tissus élégants, doux et confortables
✅ Idéal pour tenues de sortie, cérémonies et uniformes.', 3500.00, 3000, 10, NULL, NULL, 'tissus-chanel-multicolore', NULL, NULL, NULL, NULL, 1, 0, 14, '2026-06-25 22:06:06', '2026-06-25 22:07:01', NULL),
('Tissus Homme', ' tissus brodés sans écriture ✨ 🥰🥰🥰
✅ Tissu de qualité avec belle broderie
 ✅ Disponible en plusieurs couleurs 
✅ Idéal pour hommes, femmes et associations
Le 3 mètres........', 11000.00, 9000, 10, NULL, NULL, 'tissus-homme', NULL, NULL, NULL, NULL, 1, 0, 13, '2026-06-25 22:14:32', '2026-06-25 22:14:49', NULL),
('Mini Climatisseur', 'Mini Climatiseur Portable Rechargeable
💦 Fonctionne avec eau pour un air frais et agréable
🔋 Rechargeable – pratique partout (maison, bureau, voyage)
⏱️ Autonomie : jusqu’à 4 heures
🌬️ Refroidit rapidement même en forte chaleur', 10000.00, 8500, 20, NULL, NULL, 'mini-climatisseur', NULL, NULL, NULL, NULL, 1, 0, 1, '2026-06-25 22:20:44', '2026-06-25 22:21:29', NULL),
('Wifi Portable', 'WiFi Portable Rechargeable (Mobile Router 5G)
🔋 Batterie rechargeable – utilisation partout
🌍 Compatible avec toutes les SIM (universel)
📱 Connecte plusieurs appareils en même temps', 18500.00, 15000, 10, NULL, NULL, 'wifi-portable', NULL, NULL, NULL, NULL, 1, 0, 12, '2026-06-26 11:24:04', '2026-06-26 11:24:22', NULL),
('Raquette Anti-moustique', 'Raquette Anti-Moustique Rechargeable 🦟⚡
✅ Rechargeable avec une autonomie jusqu''à 12h 
✅ Élimine efficacement les moustiques et insectes volants 
✅ Facile à utiliser à la maison ou en voyage 
✅ Léger, pratique et durable ✅ Dormez tranquillement sans nuisance', 10000.00, 8000, 10, NULL, NULL, 'raquette-anti-moustique', NULL, NULL, NULL, NULL, 1, 0, 1, '2026-06-26 11:39:55', '2026-06-26 11:40:14', NULL),
('Mini Caméra Imprimante', 'Mini Caméra Imprimante Rechargeable (Tout-en-1)
✅ Photo, vidéo et jeux intégrés
✅ Impression instantanée sans encre
✅ Double caméra (avant/arrière)
✅ Idéal pour enfants et cadeaux 🎁', 17000.00, 14500, 10, NULL, NULL, 'mini-camera', NULL, NULL, NULL, NULL, 1, 0, 16, '2026-06-26 11:49:48', '2026-06-26 11:50:10', NULL),
('Tensiomètre', ' Tensiomètre Électronique Rechargeable
✅ Mesure rapide de la tension artérielle & du rythme cardiaque
🔋 Rechargeable USB – plus besoin de piles
📟 Grand écran digital LCD facile à lire
⚡ Résultat rapide, précis et simple d’utilisation
🖐️ Format poignet pratique et confortable
🏠 Idéal pour surveiller votre santé à domicile ou pour vos proches
📌 Un appareil indispensable pour suivre votre tension au quotidien !', 20000.00, 16000, 10, NULL, NULL, 'tensiometre', NULL, NULL, NULL, NULL, 1, 0, 16, '2026-06-26 12:05:51', '2026-06-26 12:06:36', NULL),
('Reborned Face', ' Sérum Visage Vitaminé ✨

🌿 Sérum visage enrichi en Vitamine C, Acide Hyaluronique et Niacinamide
💧 Aide à hydrater, illuminer et rendre la peau plus douce et éclatante
✨ Texture légère et agréable pour le visage', 7500.00, 6000, 10, NULL, NULL, 'reborned-face', NULL, NULL, NULL, NULL, 1, 0, 21, '2026-06-26 12:17:56', '2026-06-26 12:18:51', NULL),
('Chargeur & Purificateur', 'Chargeur Voiture Rapide & Purificateur d’Air Aromatique
🧠 Charge ultra-rapide – Ports compatibles iPhone & Android
⚡ Diffuseur d’arômes intégré (parfum frais, anti-odeurs)
🌈 Lumière LED décorative – Ambiance luxueuse dans la voiture
🚗 Stabilisé et sécurisé – Idéal pour tous types de véhicules', 22500.00, 17500, 10, NULL, NULL, 'chargeur-purificateur', NULL, NULL, NULL, NULL, 1, 0, 16, '2026-06-26 12:26:25', '2026-06-26 12:27:01', NULL),
('Tecno Spark Slim', ' Tecno Spark Slim – Carton original (256 Go)
🧠 Mémoire : 256 Go
⚡ RAM : 16 Go
🚚 Livraison : Gratuite
🔥 Design ultra fin et élégant
✅ Produit neuf – dans carton original
✅ Très beau modèle, idéal pour revente
Cliquez ici pour commander', 170000.00, 160000, 10, NULL, NULL, 'tecno-spark-slim', NULL, NULL, NULL, NULL, 1, 0, 9, '2026-06-26 12:38:28', '2026-06-26 12:38:46', NULL),
('Samsung Galaxy A07', ' Carton original
🧠 Mémoire : 64 Go / 128 Go
⚡ RAM : Fluide et rapide', 70000.00, 65000, 10, NULL, NULL, 'samsung-galaxy-a07', NULL, NULL, NULL, NULL, 1, 0, 9, '2026-06-26 12:48:57', '2026-06-26 12:49:20', 4),
('Telephone Fixe', 'Téléphone Fixe Huawei (Avec SIM)
🧠 Fonctionne avec carte SIM – idéal pour maison, boutique ou bureau
⚡ Réseau stable, appels clairs même en zone difficile
🔋 Autonomie longue durée + antenne pour meilleure réception', 25000.00, 22000, 10, NULL, NULL, 'telephone-fixe', NULL, NULL, NULL, NULL, 1, 0, 1, '2026-06-26 12:55:07', '2026-06-26 12:57:47', NULL),
('Dentifrice Au Charbon', 'Dentifrice au Charbon Actif BECKON – Blancheur Intense ✨
✅ Élimine les taches jaunes et les taches de café ou de tabac
✅ Blanchit les dents progressivement
✅ Nettoie en profondeur grâce au charbon actif
✅ Laisse une haleine fraîche durable
✅ Aide à protéger les dents et les gencives

🔥 Produit du quotidien', 5000.00, 2500, 10, NULL, NULL, 'dentifrice-au-charbon', NULL, NULL, NULL, NULL, 1, 0, 4, '2026-06-26 22:58:13', '2026-06-26 22:58:32', NULL);
