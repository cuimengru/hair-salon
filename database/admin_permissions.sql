-- --------------------------------------------------------
-- 主机:                           127.0.0.1
-- 服务器版本:                        5.7.27-0ubuntu0.18.04.1 - (Ubuntu)
-- 服务器操作系统:                      Linux
-- HeidiSQL 版本:                  11.0.0.5919
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- 导出 hair-salon 的数据库结构
CREATE DATABASE IF NOT EXISTS `hair-salon` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `hair-salon`;

-- 导出  表 hair-salon.admin_permissions 结构
CREATE TABLE IF NOT EXISTS `admin_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `http_path` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_permissions_name_unique` (`name`),
  UNIQUE KEY `admin_permissions_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 正在导出表  hair-salon.admin_permissions 的数据：~12 rows (大约)
DELETE FROM `admin_permissions`;
/*!40000 ALTER TABLE `admin_permissions` DISABLE KEYS */;
INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES
	(1, 'All permission', '*', '', '*', NULL, NULL),
	(2, 'Dashboard', 'dashboard', 'GET', '/', NULL, NULL),
	(3, 'Login', 'auth.login', '', '/auth/login\r\n/auth/logout', NULL, NULL),
	(4, 'User setting', 'auth.setting', 'GET,PUT', '/auth/setting', NULL, NULL),
	(5, 'Auth management', 'auth.management', '', '/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs', NULL, NULL),
	(6, 'Admin Config', 'ext.config', '', '/config*', '2021-05-12 11:48:04', '2021-05-12 11:48:04'),
	(7, 'Admin Configx', 'ext.configx', '', '/configx/*', '2021-05-12 11:49:27', '2021-05-12 11:49:27'),
	(8, '网站设置-基本设置', 'confix.tab.website', NULL, '/configx/tab-website', '2021-05-12 13:41:10', '2021-05-12 13:50:33'),
	(9, '网站设置-编辑-config', 'confix.tab.new_config', NULL, '/configx/tab-new_config', '2021-05-12 13:41:10', '2021-05-12 13:50:33'),
	(10, '网站设置-编辑-tabs', 'confix.tab.tabs_config', NULL, '/configx/tab-tabs_config', '2021-05-12 13:41:10', '2021-05-12 13:50:33'),
	(11, '网站设置-文件上传', 'confix.tab.upload', NULL, '/configx/tab-upload', '2021-05-12 14:06:44', '2021-05-12 14:06:44'),
	(12, '网站设置-APP', 'confix.tab.app', NULL, '/configx/tab-app', '2021-05-12 14:11:26', '2021-05-12 14:11:26');
/*!40000 ALTER TABLE `admin_permissions` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
