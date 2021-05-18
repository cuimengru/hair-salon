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

-- 导出  表 hair-salon.admin_menu 结构
CREATE TABLE IF NOT EXISTS `admin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 正在导出表  hair-salon.admin_menu 的数据：~33 rows (大约)
DELETE FROM `admin_menu`;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES
	(1, 0, 1, '首页', 'fa-bar-chart', '/', NULL, NULL, '2021-05-08 02:16:32'),
	(2, 0, 31, '系统管理', 'fa-tasks', NULL, NULL, NULL, '2021-05-15 16:43:04'),
	(3, 2, 32, '管理员', 'fa-users', 'auth/users', NULL, NULL, '2021-05-15 16:43:04'),
	(4, 2, 33, '角色', 'fa-user', 'auth/roles', NULL, NULL, '2021-05-15 16:43:04'),
	(5, 2, 34, '权限', 'fa-ban', 'auth/permissions', NULL, NULL, '2021-05-15 16:43:04'),
	(6, 2, 35, '菜单', 'fa-bars', 'auth/menu', NULL, NULL, '2021-05-15 16:43:04'),
	(7, 2, 36, '操作日志', 'fa-history', 'auth/logs', NULL, NULL, '2021-05-15 16:43:04'),
	(8, 36, 3, '用户管理', 'fa-users', '/users', NULL, '2021-05-08 03:33:59', '2021-05-15 10:46:34'),
	(9, 0, 12, '商城管理', 'fa-cubes', NULL, NULL, '2021-05-08 17:06:49', '2021-05-15 10:47:44'),
	(10, 9, 15, '集品类商品管理', 'fa-list-ul', '/products', NULL, '2021-05-08 17:16:19', '2021-05-15 16:43:04'),
	(11, 9, 16, '自营类商品管理', 'fa-list-ol', '/self_products', NULL, '2021-05-08 17:18:28', '2021-05-15 16:43:04'),
	(12, 9, 17, '闲置类商品管理', 'fa-align-justify', '/idle_products', NULL, '2021-05-08 17:19:16', '2021-05-15 16:43:04'),
	(13, 9, 13, '商品类目管理', 'fa-bars', '/categories', NULL, '2021-05-09 10:36:07', '2021-05-15 10:47:44'),
	(14, 0, 18, '订单管理', 'fa-rmb', '/orders', NULL, '2021-05-10 11:28:59', '2021-05-15 16:43:04'),
	(15, 0, 19, '文教娱乐管理', 'fa-life-bouy', NULL, NULL, '2021-05-11 10:02:00', '2021-05-15 16:43:04'),
	(16, 15, 20, '教育类管理', 'fa-leanpub', '/education_cultures', NULL, '2021-05-11 10:03:21', '2021-05-15 16:43:04'),
	(17, 15, 21, '培训类管理', 'fa-camera', '/train_cultures', NULL, '2021-05-11 10:05:56', '2021-05-15 16:43:04'),
	(18, 15, 22, '线下活动类管理', 'fa-anchor', '/offline_cultures', NULL, '2021-05-11 10:07:02', '2021-05-15 16:43:04'),
	(19, 0, 26, '广告管理', 'fa-picture-o', NULL, NULL, '2021-05-12 10:38:59', '2021-05-15 16:43:04'),
	(21, 0, 37, '系统设置', 'fa-toggle-on', 'configx/edit', NULL, '2021-05-12 11:49:27', '2021-05-15 16:43:04'),
	(22, 19, 27, '广告分类', 'fa-camera-retro', '/advert_categories', NULL, '2021-05-12 18:01:27', '2021-05-15 16:43:04'),
	(23, 19, 28, '广告列表', 'fa-bars', '/adverts', NULL, '2021-05-12 18:01:52', '2021-05-15 16:43:04'),
	(24, 0, 5, '美业管理', 'fa-crosshairs', NULL, NULL, '2021-05-13 09:24:52', '2021-05-15 10:47:44'),
	(25, 24, 8, '设计师管理', 'fa-slideshare', '/designers', NULL, '2021-05-13 09:26:24', '2021-05-15 10:47:44'),
	(26, 24, 7, '作品管理', 'fa-product-hunt', '/productions', NULL, '2021-05-13 10:24:24', '2021-05-15 10:47:44'),
	(27, 24, 11, '时尚资讯管理', 'fa-american-sign-language-interpreting', '/fashions', NULL, '2021-05-13 11:15:21', '2021-05-15 10:47:44'),
	(28, 0, 29, '帮助信息管理', 'fa-flag-checkered', '/help_centers', NULL, '2021-05-13 13:51:52', '2021-05-15 16:43:04'),
	(29, 0, 30, '问题反馈', 'fa-wechat', '/feedback', NULL, '2021-05-13 15:09:39', '2021-05-15 16:43:04'),
	(30, 24, 9, '预约信息管理', 'fa-commenting', '/reserve_informations', NULL, '2021-05-14 09:09:16', '2021-05-15 10:47:44'),
	(31, 24, 10, '预约订单管理', 'fa-first-order', '/reserve_orders', NULL, '2021-05-14 09:10:41', '2021-05-15 10:47:44'),
	(32, 0, 23, '评价管理', 'fa-comments', NULL, NULL, '2021-05-14 16:18:37', '2021-05-15 16:43:04'),
	(33, 32, 24, '设计师评价管理', 'fa-slideshare', '/designer_comments', NULL, '2021-05-14 16:19:45', '2021-05-15 16:43:04'),
	(34, 32, 25, '晒单管理', 'fa-product-hunt', '/product_comments', NULL, '2021-05-14 16:20:13', '2021-05-15 16:43:04'),
	(35, 24, 6, '服务项目管理', 'fa-server', '/service_projects', NULL, '2021-05-15 09:35:53', '2021-05-15 10:47:44'),
	(36, 0, 2, '用户基础信息管理', 'fa-user-md', NULL, NULL, '2021-05-15 10:46:13', '2021-05-15 10:46:20'),
	(37, 36, 4, '余额管理', 'fa-balance-scale', '/balances', NULL, '2021-05-15 10:47:25', '2021-05-15 10:47:44'),
	(38, 9, 14, '标签管理', 'fa-flag', '/product_labels', NULL, '2021-05-15 16:42:56', '2021-05-15 16:43:04');
/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
