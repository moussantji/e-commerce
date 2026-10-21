-- ============================================================================
-- Schéma manquant en production — 20/10/2026
--
-- Corrige notamment :
--   SQLSTATE[42S02] Table 'c2808726c_ecommerce.coupon_user' doesn't exist
--
-- Le script est idempotent : il peut être relancé sans casser les tables
-- ou colonnes déjà présentes.
--
-- Tables/colonnes couvertes :
--   - review_replies
--   - promo_codes.is_automatic
--   - coupon_user (+ last_sent_month)
--
-- Import : phpMyAdmin > base c2808726c_ecommerce > Importer > ce fichier.
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- 1. Réponses aux avis
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `review_replies` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `avis_client_id` bigint(20) unsigned NOT NULL,
    `user_id` bigint(20) unsigned NOT NULL,
    `parent_id` bigint(20) unsigned DEFAULT NULL,
    `message` text NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `review_replies_user_id_foreign` (`user_id`),
    KEY `review_replies_parent_id_foreign` (`parent_id`),
    KEY `review_replies_avis_client_id_parent_id_index` (`avis_client_id`, `parent_id`),
    CONSTRAINT `review_replies_avis_client_id_foreign`
        FOREIGN KEY (`avis_client_id`) REFERENCES `avis_clients` (`id`) ON DELETE CASCADE,
    CONSTRAINT `review_replies_parent_id_foreign`
        FOREIGN KEY (`parent_id`) REFERENCES `review_replies` (`id`) ON DELETE CASCADE,
    CONSTRAINT `review_replies_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. Coupons automatiques : promo_codes.is_automatic
-- ----------------------------------------------------------------------------
SET @has_is_automatic := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'promo_codes'
      AND COLUMN_NAME = 'is_automatic'
);

SET @sql := IF(
    @has_is_automatic = 0,
    'ALTER TABLE `promo_codes` ADD COLUMN `is_automatic` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_active`',
    'DO 0'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ----------------------------------------------------------------------------
-- 3. Table pivot coupon_user
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `coupon_user` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `promo_code_id` bigint(20) unsigned NOT NULL,
    `user_id` bigint(20) unsigned NOT NULL,
    `sent_at` timestamp NULL DEFAULT NULL,
    `used_at` timestamp NULL DEFAULT NULL,
    `source` varchar(30) NOT NULL DEFAULT 'manual',
    `last_sent_month` varchar(7) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `coupon_user_promo_code_id_user_id_unique` (`promo_code_id`, `user_id`),
    KEY `coupon_user_user_id_sent_at_index` (`user_id`, `sent_at`),
    CONSTRAINT `coupon_user_promo_code_id_foreign`
        FOREIGN KEY (`promo_code_id`) REFERENCES `promo_codes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `coupon_user_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. Si coupon_user existait déjà sans last_sent_month, on ajoute la colonne.
-- ----------------------------------------------------------------------------
SET @has_last_sent_month := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'coupon_user'
      AND COLUMN_NAME = 'last_sent_month'
);

SET @sql := IF(
    @has_last_sent_month = 0,
    'ALTER TABLE `coupon_user` ADD COLUMN `last_sent_month` varchar(7) DEFAULT NULL AFTER `source`',
    'DO 0'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 5. Enregistrer les migrations comme exécutées.
--    Évite que `php artisan migrate` tente de recréer ces tables/colonnes.
-- ----------------------------------------------------------------------------
INSERT INTO `migrations` (`migration`, `batch`)
SELECT m.migration, (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`)
FROM (
    SELECT '2026_10_20_000000_create_review_replies_table' AS migration
    UNION ALL
    SELECT '2026_10_20_000001_add_automatic_distribution_to_promo_codes_table'
    UNION ALL
    SELECT '2026_10_20_000002_create_coupon_user_table'
    UNION ALL
    SELECT '2026_10_20_000003_add_last_sent_month_to_coupon_user_table'
) AS m
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` AS existing
    WHERE existing.migration = m.migration
);
