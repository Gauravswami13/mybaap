-- Safe update for the live Hostinger database u668197116_mybaap_db.
-- Adds profile photo and category image. Does not delete existing rows.
-- In phpMyAdmin, select u668197116_mybaap_db, then import this file.
-- Do not import mybaap_db.sql over live data.

SET @db = DATABASE();

SET @has_photo = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'users' AND COLUMN_NAME = 'photo'
);
SET @sql = IF(@has_photo = 0, 'ALTER TABLE `users` ADD `photo` varchar(255) NULL AFTER `name`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_image = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'categories' AND COLUMN_NAME = 'image'
);
SET @sql = IF(@has_image = 0, 'ALTER TABLE `categories` ADD `image` varchar(255) NULL AFTER `slug`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_08_223000_add_profile_and_category_images', 7
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations` WHERE `migration` = '2026_10_08_223000_add_profile_and_category_images'
);
