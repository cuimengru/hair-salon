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

-- 导出  表 hair-salon.admin_config 结构
CREATE TABLE IF NOT EXISTS `admin_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_config_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 正在导出表  hair-salon.admin_config 的数据：~11 rows (大约)
DELETE FROM `admin_config`;
/*!40000 ALTER TABLE `admin_config` DISABLE KEYS */;
INSERT INTO `admin_config` (`id`, `name`, `value`, `description`, `created_at`, `updated_at`) VALUES
	(1, '__configx__', 'do not delete', '{"website.content":{"options":[],"element":"editor","help":null,"name":"\\u54c1\\u724c\\u7406\\u5ff5\\u7ef4\\u62a4","order":20},"website.title":{"options":[],"element":"normal","help":null,"name":"\\u7f51\\u7ad9\\u6807\\u9898","order":5},"website.keyword":{"options":[],"element":"normal","help":null,"name":"\\u7f51\\u7ad9\\u5173\\u952e\\u5b57","order":10},"website.description":{"options":[],"element":"textarea","help":null,"name":"\\u7f51\\u7ad9\\u63cf\\u8ff0","order":15},"website.email":{"options":[],"element":"normal","help":null,"name":"\\u8054\\u7cfb\\u90ae\\u7bb1","order":25},"website.phone":{"options":[],"element":"normal","help":null,"name":"\\u8054\\u7cfb\\u7535\\u8bdd","order":30},"upload.image_ext":{"options":[],"element":"tags","help":"\\u5141\\u8bb8\\u4e0a\\u4f20\\u7684\\u56fe\\u7247\\u540e\\u7f00","name":"\\u5141\\u8bb8\\u56fe\\u7247\\u540e\\u7f00","order":5},"app.android_version":{"options":[],"element":"normal","help":"\\u5b89\\u5353\\u7248\\u672c\\u53f7","name":"\\u5b89\\u5353\\u7248\\u672c\\u53f7","order":5},"app.android_debug":{"options":[],"element":"yes_or_no","help":"\\u5b89\\u5353\\u5f00\\u53d1\\u6a21\\u5f0f","name":"\\u5b89\\u5353\\u5f00\\u53d1\\u6a21\\u5f0f","order":10},"app.ios_version":{"options":[],"element":"normal","help":"iOS \\u7248\\u672c\\u53f7","name":"iOS \\u7248\\u672c\\u53f7","order":15},"app.ios_debug":{"options":[],"element":"yes_or_no","help":"iOS \\u5f00\\u53d1\\u6a21\\u5f0f","name":"iOS \\u5f00\\u53d1\\u6a21\\u5f0f","order":20}}', '2021-05-12 13:36:59', '2021-05-12 14:15:47'),
	(2, 'website.content', '<p>美发</p>', '品牌理念维护', '2021-05-12 13:56:29', '2021-05-12 14:16:28'),
	(3, 'website.title', '美发', '网站标题', '2021-05-12 13:58:23', '2021-05-12 14:10:20'),
	(4, 'website.keyword', '美发', '网站关键字', '2021-05-12 14:02:22', '2021-05-12 14:16:28'),
	(5, 'website.description', '美发app', '网站描述', '2021-05-12 14:03:52', '2021-05-12 14:16:28'),
	(6, 'website.email', '123456@qq.com', '邮箱', '2021-05-12 14:04:35', '2021-05-12 14:16:28'),
	(7, 'website.phone', '123456', '联系电话', '2021-05-12 14:04:56', '2021-05-12 14:16:28'),
	(8, 'upload.image_ext', 'png,jpg,jpeg,gif,bmp', '允许图片后缀', '2021-05-12 14:07:57', '2021-05-12 14:09:54'),
	(9, 'app.android_version', '1.0.0', '安卓版本号', '2021-05-12 14:12:24', '2021-05-12 14:16:02'),
	(10, 'app.android_debug', '0', '安卓开发模式', '2021-05-12 14:14:17', '2021-05-12 14:16:02'),
	(11, 'app.ios_version', '1.0.0', 'iOS 版本号', '2021-05-12 14:14:55', '2021-05-12 14:16:02'),
	(12, 'app.ios_debug', '0', 'iOS 开发模式', '2021-05-12 14:15:47', '2021-05-12 14:16:02');
/*!40000 ALTER TABLE `admin_config` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
