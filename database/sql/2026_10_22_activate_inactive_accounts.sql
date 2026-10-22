-- ============================================================
--  Mise à jour manuelle — 22/10/2026
--  Base concernée : c2808726c_ecommerce (LWS)
--
--  À importer dans phpMyAdmin si `php artisan migrate` n'est pas
--  exécuté après le déploiement du code.
--
--  Ce script :
--   1. réactive les clients inactifs (correctif inscription web) ;
--   2. réactive les admins inactifs (correctif connexion Google) ;
--   3. NE TOUCHE PAS aux vendeurs inactifs (en attente de validation) ;
--   4. enregistre les 2 migrations pour éviter qu'elles soient rejouées.
--
--  Aucune modification de structure (tables / colonnes).
--  Faire une sauvegarde avant l'import.
-- ============================================================

START TRANSACTION;

-- 1. Clients inactifs -> actifs
UPDATE `users`
SET `status` = 'active'
WHERE `role` = 'customer'
  AND `status` = 'inactive';

SET @clients_activated = ROW_COUNT();

-- 2. Admins inactifs -> actifs (comptes Google créés avant le correctif)
UPDATE `users`
SET `status` = 'active'
WHERE `role` = 'admin'
  AND `status` = 'inactive';

SET @admins_activated = ROW_COUNT();

-- 3. Enregistrer les migrations correspondantes (sans doublon)
SET @batch = (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_22_000000_activate_inactive_customers', @batch
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1
    FROM (SELECT `migration` FROM `migrations`) AS `m`
    WHERE `m`.`migration` = '2026_10_22_000000_activate_inactive_customers'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_22_000001_activate_inactive_admins', @batch
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1
    FROM (SELECT `migration` FROM `migrations`) AS `m`
    WHERE `m`.`migration` = '2026_10_22_000001_activate_inactive_admins'
);

COMMIT;

-- 4. Vérification
SELECT @clients_activated AS `clients_reactives`,
       @admins_activated AS `admins_reactives`;

SELECT `id`, `email`, `role`, `status`
FROM `users`
ORDER BY `id`;

SELECT `migration`, `batch`
FROM `migrations`
WHERE `migration` LIKE '2026_10_22_%'
ORDER BY `migration`;
