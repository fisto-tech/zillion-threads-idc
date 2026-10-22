-- MySQL Schema for Product Catalog
-- Database: product_catalog

-- CREATE DATABASE IF NOT EXISTS `product_catalog` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `product_catalog`;

-- Table for Catalog Pages metadata (Page 4, Page 5, etc.)
CREATE TABLE IF NOT EXISTS `catalog_pages` (
    `id` VARCHAR(50) NOT NULL PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `subtitle` VARCHAR(500) DEFAULT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for Catalog Items matching products.json
CREATE TABLE IF NOT EXISTS `catalog_items` (
    `id` VARCHAR(50) NOT NULL PRIMARY KEY,
    `page_key` VARCHAR(50) NOT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `name` VARCHAR(255) NOT NULL,
    `category` VARCHAR(255) DEFAULT NULL,
    `description` TEXT,
    `image` VARCHAR(500) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_page_key` (`page_key`),
    CONSTRAINT `fk_catalog_items_page` FOREIGN KEY (`page_key`) REFERENCES `catalog_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
