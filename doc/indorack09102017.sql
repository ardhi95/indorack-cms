-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2017 at 05:33 AM
-- Server version: 10.1.19-MariaDB
-- PHP Version: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `indorack`
--

-- --------------------------------------------------------

--
-- Table structure for table `acos`
--

DROP TABLE IF EXISTS `acos`;
CREATE TABLE `acos` (
  `id` int(10) NOT NULL,
  `parent_id` int(10) DEFAULT NULL,
  `acos_type_id` smallint(2) NOT NULL DEFAULT '2',
  `model` varchar(255) DEFAULT NULL,
  `controller` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `acos`
--

INSERT INTO `acos` (`id`, `parent_id`, `acos_type_id`, `model`, `controller`, `alias`, `description`, `lft`, `rght`, `status`, `created`, `modified`) VALUES
(1, NULL, 2, NULL, NULL, 'top', '', 1, 22, 1, '2016-11-21 00:00:00', '2016-11-21 00:00:00'),
(2, 1, 2, NULL, 'Dashboards', 'Dashboards', '', 2, 3, 1, '2016-11-21 22:41:45', '2016-11-22 16:50:47'),
(4, 1, 2, NULL, 'Admins', 'Admins', '', 4, 5, 1, '2016-11-21 22:42:07', '2016-11-22 16:51:00'),
(5, 1, 2, NULL, 'CmsMenus', 'CmsMenus', '', 6, 7, 1, '2016-11-21 22:47:10', '2016-11-22 16:57:18'),
(7, 1, 1, NULL, 'ModuleObjects', 'ModuleObjects', '', 8, 9, 1, '2016-11-22 16:27:57', '2016-11-22 16:58:48'),
(9, 1, 2, NULL, 'AdminGroups', 'UserGroups', '', 10, 11, 1, '2016-11-23 12:26:39', '2017-05-05 16:35:46'),
(48, 1, 2, NULL, 'Vehicles', 'Vehicles', '', 20, 21, 1, '2017-08-28 09:57:23', '2017-08-28 09:57:23'),
(46, 1, 2, NULL, 'Products', 'Products', '', 16, 17, 1, '2017-05-16 10:14:25', '2017-05-16 10:14:25'),
(47, 1, 2, NULL, 'ProductCategories', 'ProductCategories', '', 18, 19, 1, '2017-08-09 14:05:15', '2017-08-09 14:05:15'),
(45, 1, 2, NULL, 'Orders', 'Orders', '', 14, 15, 1, '2017-05-08 14:43:01', '2017-05-08 14:43:01');

-- --------------------------------------------------------

--
-- Table structure for table `aros`
--

DROP TABLE IF EXISTS `aros`;
CREATE TABLE `aros` (
  `id` int(10) NOT NULL,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  `total_admin` int(11) NOT NULL DEFAULT '0',
  `status` smallint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `aros`
--

INSERT INTO `aros` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `description`, `lft`, `rght`, `total_admin`, `status`, `created`, `modified`) VALUES
(1, NULL, NULL, NULL, 'Developer', '', 1, 14, 0, 1, '2016-11-23 00:00:00', '2016-11-28 05:36:38'),
(3, 2, NULL, NULL, 'Admin', '', 3, 4, 1, 1, '2016-11-26 20:28:05', '2017-03-11 14:58:38'),
(2, 1, NULL, NULL, 'Super Admin', '', 2, 13, 1, 1, '2016-11-23 20:52:43', '2016-11-28 05:36:16'),
(4, 2, NULL, NULL, 'Kepala Gudang', '', 5, 6, 0, 1, '2017-05-05 16:31:06', '2017-05-05 16:31:06'),
(5, 2, NULL, NULL, 'Teknisi', '', 7, 8, 0, 1, '2017-05-05 16:34:05', '2017-05-05 16:34:05'),
(6, 2, NULL, NULL, 'Driver', '', 9, 10, 0, 1, '2017-05-05 16:34:23', '2017-05-05 16:34:23'),
(7, 2, NULL, NULL, 'Customer', '', 11, 12, 0, 1, '2017-05-05 16:34:35', '2017-05-05 16:34:35');

-- --------------------------------------------------------

--
-- Table structure for table `aros_acos`
--

DROP TABLE IF EXISTS `aros_acos`;
CREATE TABLE `aros_acos` (
  `id` int(11) NOT NULL,
  `aro_id` int(10) NOT NULL,
  `aco_id` int(10) NOT NULL,
  `_create` varchar(2) NOT NULL DEFAULT '0',
  `_read` varchar(2) NOT NULL DEFAULT '0',
  `_update` varchar(2) NOT NULL DEFAULT '0',
  `_delete` varchar(2) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `aros_acos`
--

INSERT INTO `aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES
(17, 2, 4, '1', '1', '1', '1'),
(15, 2, 2, '1', '1', '1', '1'),
(5, 1, 9, '1', '1', '1', '1'),
(6, 1, 7, '1', '1', '1', '1'),
(7, 1, 5, '1', '1', '1', '1'),
(8, 1, 4, '1', '1', '1', '1'),
(10, 1, 2, '1', '1', '1', '1'),
(18, 2, 5, '1', '1', '1', '1'),
(19, 2, 7, '0', '0', '0', '0'),
(20, 2, 9, '1', '1', '1', '1'),
(98, 1, 47, '1', '1', '1', '1'),
(97, 3, 46, '0', '0', '0', '0'),
(96, 3, 45, '1', '1', '1', '1'),
(95, 3, 9, '0', '1', '0', '0'),
(94, 3, 7, '0', '0', '0', '0'),
(93, 3, 5, '0', '0', '0', '0'),
(91, 3, 2, '1', '1', '1', '1'),
(92, 3, 4, '0', '1', '0', '0'),
(44, 4, 2, '0', '0', '0', '0'),
(46, 4, 4, '0', '0', '0', '0'),
(47, 4, 5, '0', '0', '0', '0'),
(48, 4, 9, '0', '0', '0', '0'),
(100, 1, 48, '1', '1', '1', '1'),
(83, 2, 46, '1', '1', '1', '1'),
(99, 2, 47, '1', '1', '1', '1'),
(66, 5, 9, '0', '0', '0', '0'),
(65, 5, 5, '0', '0', '0', '0'),
(64, 5, 4, '0', '0', '0', '0'),
(62, 5, 2, '0', '0', '0', '0'),
(71, 7, 2, '0', '0', '0', '0'),
(73, 7, 4, '0', '0', '0', '0'),
(74, 7, 5, '0', '0', '0', '0'),
(75, 7, 9, '0', '0', '0', '0'),
(101, 2, 48, '1', '1', '1', '1'),
(82, 1, 46, '1', '1', '1', '1'),
(80, 1, 45, '1', '1', '1', '1'),
(81, 2, 45, '1', '1', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `cms_menus`
--

DROP TABLE IF EXISTS `cms_menus`;
CREATE TABLE `cms_menus` (
  `id` int(11) NOT NULL,
  `aco_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `lft` int(11) NOT NULL,
  `rght` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon_class` varchar(255) DEFAULT '',
  `is_group_separator` smallint(1) NOT NULL DEFAULT '0',
  `status` smallint(6) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cms_menus`
--

INSERT INTO `cms_menus` (`id`, `aco_id`, `parent_id`, `lft`, `rght`, `name`, `icon_class`, `is_group_separator`, `status`, `created`, `modified`) VALUES
(1, NULL, NULL, 1, 24, 'Top Level Menu', '', 0, 1, '2016-11-13 15:58:17', '2016-11-13 15:58:17'),
(2, NULL, 1, 2, 3, 'Menu Utama', '', 1, 1, '2016-11-13 15:58:17', '2017-01-05 09:58:42'),
(3, 2, 1, 4, 5, 'Dashboard', 'fa fa-desktop', 0, 1, '2016-11-13 15:58:17', '2016-11-13 20:46:05'),
(5, NULL, 1, 14, 15, 'Settings', '', 1, 1, '2016-11-13 15:58:17', '2017-05-05 13:21:29'),
(6, 4, 1, 16, 17, 'Admin List', 'fa fa-user', 0, 1, '2016-11-13 15:59:25', '2017-05-10 13:55:28'),
(9, 5, 1, 20, 21, 'Menu CMS', 'fa fa-bars', 0, 1, '2016-11-14 10:15:59', '2017-01-05 10:02:00'),
(26, 7, 1, 22, 23, 'Objek Modul', 'glyphicon glyphicon-wrench', 0, 1, '2016-11-21 20:25:25', '2017-01-05 11:44:14'),
(29, 9, 1, 18, 19, 'Admin Groups', 'fa fa-users', 0, 1, '2016-11-22 17:09:30', '2017-05-10 13:55:40'),
(64, 47, 1, 10, 11, 'Product Category', 'fa fa-bars', 0, 1, '2017-08-09 14:06:03', '2017-08-09 14:06:03'),
(63, 46, 1, 8, 9, 'Products', 'fa fa-laptop', 0, 1, '2017-05-16 10:15:31', '2017-05-16 10:24:22'),
(62, 45, 1, 6, 7, 'Orders', 'glyphicon glyphicon-edit', 0, 1, '2017-05-08 14:45:22', '2017-05-08 14:45:22'),
(65, 48, 1, 12, 13, 'Vehicles', 'fa fa-truck', 0, 1, '2017-08-28 09:58:03', '2017-08-28 09:58:03');

-- --------------------------------------------------------

--
-- Table structure for table `cms_menu_translations`
--

DROP TABLE IF EXISTS `cms_menu_translations`;
CREATE TABLE `cms_menu_translations` (
  `id` int(10) NOT NULL,
  `locale` varchar(6) NOT NULL,
  `model` varchar(255) NOT NULL,
  `foreign_key` int(10) NOT NULL,
  `field` varchar(255) NOT NULL,
  `content` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cms_menu_translations`
--

INSERT INTO `cms_menu_translations` (`id`, `locale`, `model`, `foreign_key`, `field`, `content`) VALUES
(1, 'idn', 'CmsMenu', 1, 'name', 'Top Level Menu'),
(2, 'eng', 'CmsMenu', 1, 'name', 'Top Level Menu'),
(3, 'idn', 'CmsMenu', 2, 'name', 'Menu Utama'),
(4, 'eng', 'CmsMenu', 2, 'name', 'Main Menu'),
(5, 'idn', 'CmsMenu', 3, 'name', 'Dashboard'),
(6, 'eng', 'CmsMenu', 3, 'name', 'Dashboard'),
(61, 'idn', 'CmsMenu', 65, 'name', 'Vehicles'),
(60, 'eng', 'CmsMenu', 64, 'name', 'Product Category'),
(9, 'idn', 'CmsMenu', 5, 'name', 'Menu Admin'),
(10, 'eng', 'CmsMenu', 5, 'name', 'Settings'),
(11, 'idn', 'CmsMenu', 6, 'name', 'Daftar Admin'),
(12, 'eng', 'CmsMenu', 6, 'name', 'Admin List'),
(13, 'idn', 'CmsMenu', 9, 'name', 'Menu CMS'),
(14, 'eng', 'CmsMenu', 9, 'name', 'CMS Menu'),
(15, 'idn', 'CmsMenu', 26, 'name', 'Objek Modul'),
(16, 'eng', 'CmsMenu', 26, 'name', 'Module Object'),
(17, 'idn', 'CmsMenu', 29, 'name', 'Grup Admin'),
(18, 'eng', 'CmsMenu', 29, 'name', 'Admin Groups'),
(59, 'idn', 'CmsMenu', 64, 'name', 'Product Category'),
(58, 'eng', 'CmsMenu', 63, 'name', 'Products'),
(57, 'idn', 'CmsMenu', 63, 'name', 'Product'),
(55, 'idn', 'CmsMenu', 62, 'name', 'Orders'),
(56, 'eng', 'CmsMenu', 62, 'name', 'Orders'),
(62, 'eng', 'CmsMenu', 65, 'name', 'Vehicles');

-- --------------------------------------------------------

--
-- Table structure for table `contents`
--

DROP TABLE IF EXISTS `contents`;
CREATE TABLE `contents` (
  `id` int(11) NOT NULL,
  `model` varchar(100) NOT NULL,
  `model_id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `host` varchar(255) NOT NULL,
  `url` varchar(100) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `contents`
--

INSERT INTO `contents` (`id`, `model`, `model_id`, `type`, `host`, `url`, `mime_type`, `path`, `width`, `height`, `created`, `modified`) VALUES
(1, 'User', 2, 'square', 'http://192.168.1.103/indorack/cms/', 'contents/User/2/2_square.jpg', 'image/jpg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/User/2/2_square.jpg', 200, 200, '2017-05-05 11:33:09', '2017-05-05 11:33:09'),
(2, 'User', 2, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/User/2/2_maxwidth.jpg', 'image/jpg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/User/2/2_maxwidth.jpg', 200, 200, '2017-05-05 11:33:09', '2017-05-05 11:33:09'),
(3, 'ProductImage', 1, 'square', 'http://192.168.1.103/indorack/cms/', 'contents/ProductImage/1/1_square.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductImage/1/1_square.jpg', 500, 500, '2017-05-16 10:35:41', '2017-05-16 10:53:54'),
(4, 'ProductImage', 1, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/ProductImage/1/1_maxwidth.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductImage/1/1_maxwidth.jpg', 800, 450, '2017-05-16 10:35:41', '2017-05-16 10:53:55'),
(5, 'ProductImage', 2, 'square', 'http://192.168.1.103/indorack/cms/', 'contents/ProductImage/2/2_square.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductImage/2/2_square.jpg', 300, 300, '2017-05-17 10:19:17', '2017-05-17 10:19:17'),
(6, 'ProductImage', 2, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/ProductImage/2/2_maxwidth.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductImage/2/2_maxwidth.jpg', 400, 400, '2017-05-17 10:19:17', '2017-05-17 10:19:17'),
(7, 'ProductImage', 3, 'square', 'http://192.168.1.103/indorack/cms/', 'contents/ProductImage/3/3_square.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductImage/3/3_square.jpg', 300, 300, '2017-05-17 10:22:55', '2017-08-07 13:40:29'),
(8, 'ProductImage', 3, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/ProductImage/3/3_maxwidth.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductImage/3/3_maxwidth.jpg', 400, 400, '2017-05-17 10:22:55', '2017-08-07 13:40:29'),
(10, 'User', 17, 'square', 'http://192.168.1.103/indorack/cms/', 'contents/User/17/17_square.jpg', 'image/jpg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/User/17/17_square.jpg', 160, 160, '2017-05-30 14:18:30', '2017-06-07 12:01:18'),
(11, 'User', 17, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/User/17/17_maxwidth.jpg', 'image/jpg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/User/17/17_maxwidth.jpg', 200, 200, '2017-05-30 14:18:30', '2017-06-07 12:01:18'),
(21, 'ProductCategory', 1, 'square', 'http://192.168.1.103/indorack/cms/', 'contents/ProductCategory/1/1_square.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductCategory/1/1_square.jpg', 400, 400, '2017-08-09 14:12:16', '2017-08-09 14:12:16'),
(13, 'Task', 85, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/Task/85/85_maxwidth.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/Task/85/85_maxwidth.jpg', 800, 1067, '2017-06-09 10:53:53', '2017-06-09 10:53:53'),
(14, 'ProductImage', 4, 'square', 'http://192.168.1.103/indorack/cms/', 'contents/ProductImage/4/4_square.png', 'image/png', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductImage/4/4_square.png', 300, 300, '2017-08-07 13:44:04', '2017-08-07 13:44:28'),
(15, 'ProductImage', 4, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/ProductImage/4/4_maxwidth.png', 'image/png', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductImage/4/4_maxwidth.png', 800, 1422, '2017-08-07 13:44:05', '2017-08-07 13:44:29'),
(17, 'Product', 4, 'pdf', 'http://192.168.1.103/indorack/cms/', 'contents/Product/4/4_pdf.pdf', 'application/pdf', 'contents/Product/4/4_pdf.pdf', NULL, NULL, '2017-08-08 14:45:03', '2017-08-08 14:48:27'),
(18, 'Product', 5, 'pdf', 'http://192.168.1.103/indorack/cms/', 'contents/Product/5/5_pdf.pdf', 'application/pdf', 'contents/Product/5/5_pdf.pdf', NULL, NULL, '2017-08-08 14:50:16', '2017-08-08 14:50:16'),
(19, 'ProductImage', 5, 'square', 'http://192.168.1.103/indorack/cms/', 'contents/ProductImage/5/5_square.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductImage/5/5_square.jpg', 300, 300, '2017-08-08 14:51:02', '2017-08-08 14:51:02'),
(20, 'ProductImage', 5, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/ProductImage/5/5_maxwidth.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductImage/5/5_maxwidth.jpg', 400, 400, '2017-08-08 14:51:02', '2017-08-08 14:51:02'),
(22, 'ProductCategory', 1, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/ProductCategory/1/1_maxwidth.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductCategory/1/1_maxwidth.jpg', 400, 400, '2017-08-09 14:12:16', '2017-08-09 14:12:16'),
(23, 'ProductCategory', 2, 'square', 'http://192.168.1.103/indorack/cms/', 'contents/ProductCategory/2/2_square.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductCategory/2/2_square.jpg', 400, 400, '2017-08-09 14:24:00', '2017-08-09 14:24:00'),
(24, 'ProductCategory', 2, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/ProductCategory/2/2_maxwidth.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductCategory/2/2_maxwidth.jpg', 400, 400, '2017-08-09 14:24:00', '2017-08-09 14:24:00'),
(25, 'ProductCategory', 3, 'square', 'http://192.168.1.103/indorack/cms/', 'contents/ProductCategory/3/3_square.png', 'image/png', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductCategory/3/3_square.png', 400, 400, '2017-08-09 16:51:45', '2017-08-23 15:15:22'),
(26, 'ProductCategory', 3, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/ProductCategory/3/3_maxwidth.png', 'image/png', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/ProductCategory/3/3_maxwidth.png', 800, 1422, '2017-08-09 16:51:45', '2017-08-23 15:15:22'),
(34, 'Task', 2, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/Task/2/2_maxwidth.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/Task/2/2_maxwidth.jpg', 800, 1067, '2017-09-20 10:58:57', '2017-09-20 10:58:57'),
(32, 'Order', 2, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/Order/2/2_maxwidth.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/Order/2/2_maxwidth.jpg', 800, 1067, '2017-09-18 21:21:16', '2017-09-18 21:21:16'),
(33, 'Task', 1, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/Task/1/1_maxwidth.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/Task/1/1_maxwidth.jpg', 800, 1067, '2017-09-18 22:00:33', '2017-09-20 10:55:07'),
(35, 'Task', 4, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/Task/4/4_maxwidth.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/Task/4/4_maxwidth.jpg', 800, 1067, '2017-09-20 11:02:49', '2017-09-20 11:02:49'),
(36, 'Task', 5, 'maxwidth', 'http://192.168.1.103/indorack/cms/', 'contents/Task/5/5_maxwidth.jpg', 'image/jpeg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/Task/5/5_maxwidth.jpg', 800, 1067, '2017-09-20 11:04:51', '2017-09-20 11:04:51');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_statuses`
--

DROP TABLE IF EXISTS `delivery_statuses`;
CREATE TABLE `delivery_statuses` (
  `id` int(11) NOT NULL,
  `name` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_types`
--

DROP TABLE IF EXISTS `delivery_types`;
CREATE TABLE `delivery_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `delivery_types`
--

INSERT INTO `delivery_types` (`id`, `name`) VALUES
(1, 'Delivery by driver'),
(2, 'Pickup on indorack office');

-- --------------------------------------------------------

--
-- Table structure for table `i18n`
--

DROP TABLE IF EXISTS `i18n`;
CREATE TABLE `i18n` (
  `id` int(10) NOT NULL,
  `locale` varchar(6) NOT NULL,
  `model` varchar(255) NOT NULL,
  `foreign_key` int(10) NOT NULL,
  `field` varchar(255) NOT NULL,
  `content` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `job_assigns`
--

DROP TABLE IF EXISTS `job_assigns`;
CREATE TABLE `job_assigns` (
  `id` bigint(20) NOT NULL,
  `task_id` varchar(255) DEFAULT NULL,
  `order_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `vehicle_no` varchar(7) NOT NULL,
  `created` datetime NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `job_assign_status`
--

DROP TABLE IF EXISTS `job_assign_status`;
CREATE TABLE `job_assign_status` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `color` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `job_assign_status`
--

INSERT INTO `job_assign_status` (`id`, `name`, `description`, `color`) VALUES
(1, 'Assign', 'Waktu kepala gudang menugaskan driver, status pertama kali yang akan dilihat driver adalah Waiting. Artinya sistem masih menunggu apakah driver akan melaksanakan tugasnya atau tidak.', '#0085b6'),
(2, 'Accepted', 'Jika driver menerima tugas, namun belum mengantarkannya ke tempat tujuan', '#0085b6'),
(3, 'Rejected', 'Jika driver menolak mengantarkan barangnya', '#ffd200'),
(4, 'On Progress', 'Jika driver sedang dalam proses pengantaran barang', '#7f7fff'),
(5, 'Completed', 'Jika barang sudah diserahkan driver ke Customer', '#05f6ff'),
(6, 'Failed', 'Jika barang sudah diantarkan driver, namun gagal diantarkan karena alasan tertentu, misalnya banjir, customer tidak berada di tempat, alamat tidak ditemukan dll', '#00d814');

-- --------------------------------------------------------

--
-- Table structure for table `langs`
--

DROP TABLE IF EXISTS `langs`;
CREATE TABLE `langs` (
  `id` int(11) NOT NULL,
  `code` varchar(3) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `langs`
--

INSERT INTO `langs` (`id`, `code`, `name`) VALUES
(1, 'idn', 'Indonesia'),
(2, 'eng', 'English');

-- --------------------------------------------------------

--
-- Table structure for table `navigation_menus`
--

DROP TABLE IF EXISTS `navigation_menus`;
CREATE TABLE `navigation_menus` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `android_class_name` varchar(255) DEFAULT NULL,
  `aro_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `navigation_menus`
--

INSERT INTO `navigation_menus` (`id`, `name`, `icon`, `android_class_name`, `aro_id`) VALUES
(1, 'History', '@drawable/icn_history', 'CustomerHistory', 7),
(2, 'Notification', '@drawable/icn_history', 'NotificationActivity', 7),
(3, 'Sign Out', '@drawable/icn_sign_out', 'SignOut', 7),
(4, 'Contact Us', '@drawable/icn_contact_us', NULL, 7),
(5, 'History', '@drawable/icn_history', 'DashboardKepalaGudang', 4),
(6, 'Notification', '@drawable/icn_history', 'NotificationActivity', 4),
(7, 'Sign Out', '@drawable/icn_sign_out', 'SignOut', 4),
(8, 'Contact Us', '@drawable/icn_contact_us', NULL, 4),
(9, 'History', '@drawable/icn_history', 'DashboardTeknisiActivity', 5),
(10, 'Notification', '@drawable/icn_history', 'NotificationActivity', 5),
(11, 'Sign Out', '@drawable/icn_sign_out', 'SignOut', 5),
(12, 'Contact Us', '@drawable/icn_contact_us', NULL, 5),
(13, 'History', '@drawable/icn_history', 'DashboardDriver', 6),
(14, 'Notification', '@drawable/icn_history', 'NotificationActivity', 6),
(15, 'Sign Out', '@drawable/icn_sign_out', 'SignOut', 6),
(16, 'Contact Us', '@drawable/icn_contact_us', NULL, 6),
(17, 'History', '@drawable/icn_history', 'DashboardSuperadmin', 2),
(18, 'Notification', '@drawable/icn_history', 'NotificationActivity', 2),
(19, 'Sign Out', '@drawable/icn_sign_out', 'SignOut', 2),
(20, 'Contact Us', '@drawable/icn_contact_us', NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `navigation_menus_2`
--

DROP TABLE IF EXISTS `navigation_menus_2`;
CREATE TABLE `navigation_menus_2` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `android_class_name` varchar(255) DEFAULT NULL,
  `aro_id` set('1','2','3','4','5','6','7') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `navigation_menus_2`
--

INSERT INTO `navigation_menus_2` (`id`, `name`, `icon`, `android_class_name`, `aro_id`) VALUES
(1, 'History', '@drawable/icn_history', 'CustomerHistory', '7'),
(2, 'Notification', '@drawable/icn_history', 'NotificationActivity', '4,7');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` bigint(20) NOT NULL,
  `notification_group_id` bigint(20) DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `gcm_id` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text,
  `description` text,
  `android_class_name` varchar(255) DEFAULT NULL,
  `params` text COMMENT 'in json',
  `created` datetime NOT NULL,
  `arrival_date` datetime DEFAULT NULL,
  `read_date` datetime DEFAULT NULL,
  `is_arrival` smallint(1) NOT NULL DEFAULT '0',
  `is_readed` smallint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `notification_group_id`, `order_id`, `user_id`, `gcm_id`, `title`, `message`, `description`, `android_class_name`, `params`, `created`, `arrival_date`, `read_date`, `is_arrival`, `is_readed`) VALUES
(1, 1, '1', 6, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO001', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-20 10:41:29', NULL, NULL, 0, 0),
(2, 1, '1', 24, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO001', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-20 10:41:29', NULL, NULL, 0, 0),
(3, 1, '1', 25, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO001', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-20 10:41:29', NULL, NULL, 0, 0),
(4, 1, '1', 28, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO001', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-20 10:41:29', NULL, NULL, 0, 0),
(5, 2, '1', 12, NULL, 'INDORACK', 'New job request DO001', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia<br/>Delivery Date : 20 Sep 2017 10:55', 'DashboardDriver', NULL, '2017-09-20 10:45:30', '2017-09-20 11:00:04', NULL, 1, 1),
(6, 3, '1', 6, NULL, 'INDORACK', 'Maman Selamet has accepted job DO001', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"1"},{"key":"order_id","val":"1"}]', '2017-09-20 10:46:25', NULL, NULL, 0, 0),
(7, 3, '1', 24, NULL, 'INDORACK', 'Maman Selamet has accepted job DO001', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"1"},{"key":"order_id","val":"1"}]', '2017-09-20 10:46:25', NULL, NULL, 0, 0),
(8, 3, '1', 25, NULL, 'INDORACK', 'Maman Selamet has accepted job DO001', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"1"},{"key":"order_id","val":"1"}]', '2017-09-20 10:46:25', NULL, NULL, 0, 0),
(9, 3, '1', 28, NULL, 'INDORACK', 'Maman Selamet has accepted job DO001', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"1"},{"key":"order_id","val":"1"}]', '2017-09-20 10:46:25', NULL, NULL, 0, 0),
(10, 4, '1', 6, NULL, 'INDORACK', 'DO001 is on deliver progress ', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"1"},{"key":"order_id","val":"1"}]', '2017-09-20 10:46:32', NULL, NULL, 0, 0),
(11, 4, '1', 24, NULL, 'INDORACK', 'DO001 is on deliver progress ', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"1"},{"key":"order_id","val":"1"}]', '2017-09-20 10:46:32', NULL, NULL, 0, 0),
(12, 4, '1', 25, NULL, 'INDORACK', 'DO001 is on deliver progress ', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"1"},{"key":"order_id","val":"1"}]', '2017-09-20 10:46:32', NULL, NULL, 0, 0),
(13, 4, '1', 28, NULL, 'INDORACK', 'DO001 is on deliver progress ', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"1"},{"key":"order_id","val":"1"}]', '2017-09-20 10:46:32', NULL, NULL, 0, 0),
(14, 5, '1', 11, 'cm_0DNYM5FM:APA91bGiVMOwIrjp7LczQO-xLxxcCFuf3W-mWggeJsBs9BAxFFb-Z_uAYxF_G--yr24JmJFNL3_mIzjMRevEF6YovyR9MqxBvGbdoH9XQX3TTIgvwbCoKjtS0_R7ZuqSBczp1I2mCGk8', 'INDORACK', 'Driver in on the way to deliver your item PO001', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'DetailOrder', '[{"key":"order_id","val":"1"}]', '2017-09-20 10:46:32', NULL, NULL, 0, 0),
(15, 6, '1', 6, NULL, 'INDORACK', 'DO001 has completely sent', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"1"},{"key":"order_id","val":"1"}]', '2017-09-20 10:55:05', NULL, NULL, 0, 0),
(16, 6, '1', 24, NULL, 'INDORACK', 'DO001 has completely sent', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"1"},{"key":"order_id","val":"1"}]', '2017-09-20 10:55:05', NULL, NULL, 0, 0),
(17, 6, '1', 25, NULL, 'INDORACK', 'DO001 has completely sent', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"1"},{"key":"order_id","val":"1"}]', '2017-09-20 10:55:05', NULL, NULL, 0, 0),
(18, 6, '1', 28, NULL, 'INDORACK', 'DO001 has completely sent', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"1"},{"key":"order_id","val":"1"}]', '2017-09-20 10:55:05', NULL, NULL, 0, 0),
(19, 7, '1', 11, 'cm_0DNYM5FM:APA91bGiVMOwIrjp7LczQO-xLxxcCFuf3W-mWggeJsBs9BAxFFb-Z_uAYxF_G--yr24JmJFNL3_mIzjMRevEF6YovyR9MqxBvGbdoH9XQX3TTIgvwbCoKjtS0_R7ZuqSBczp1I2mCGk8', 'INDORACK', 'Your item is successfully delivered PO001', 'PO No. : PO001<br/>Delivery No. : DO001<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'DetailOrder', '[{"key":"order_id","val":"1"}]', '2017-09-20 10:55:05', NULL, NULL, 0, 0),
(20, 8, '2', 6, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO002', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-20 10:56:46', NULL, NULL, 0, 0),
(21, 8, '2', 24, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO002', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-20 10:56:46', NULL, NULL, 0, 0),
(22, 8, '2', 25, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO002', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-20 10:56:46', NULL, NULL, 0, 0),
(23, 8, '2', 28, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO002', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-20 10:56:46', NULL, NULL, 0, 0),
(24, 9, '2', 12, NULL, 'INDORACK', 'New job request DO002', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia<br/>Delivery Date : 21 Sep 2017 10:00', 'DashboardDriver', NULL, '2017-09-20 10:57:27', '2017-09-20 11:00:04', NULL, 1, 0),
(25, 10, '2', 7, NULL, 'INDORACK', 'New job request DO002', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia<br/>Delivery Date : 21 Sep 2017 10:00', 'DashboardTeknisiActivity', NULL, '2017-09-20 10:57:43', NULL, NULL, 0, 0),
(26, 10, '2', 22, NULL, 'INDORACK', 'New job request DO002', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia<br/>Delivery Date : 21 Sep 2017 10:00', 'DashboardTeknisiActivity', NULL, '2017-09-20 10:57:43', '2017-09-20 12:03:28', NULL, 1, 1),
(27, 11, '2', 6, NULL, 'INDORACK', 'Maman Selamet has accepted job DO002', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"2"},{"key":"order_id","val":"2"}]', '2017-09-20 10:58:21', NULL, NULL, 0, 0),
(28, 11, '2', 24, NULL, 'INDORACK', 'Maman Selamet has accepted job DO002', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"2"},{"key":"order_id","val":"2"}]', '2017-09-20 10:58:21', NULL, NULL, 0, 0),
(29, 11, '2', 25, NULL, 'INDORACK', 'Maman Selamet has accepted job DO002', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"2"},{"key":"order_id","val":"2"}]', '2017-09-20 10:58:21', NULL, NULL, 0, 0),
(30, 11, '2', 28, NULL, 'INDORACK', 'Maman Selamet has accepted job DO002', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"2"},{"key":"order_id","val":"2"}]', '2017-09-20 10:58:21', NULL, NULL, 0, 0),
(31, 12, '2', 6, NULL, 'INDORACK', 'DO002 is on deliver progress ', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"2"},{"key":"order_id","val":"2"}]', '2017-09-20 10:58:26', NULL, NULL, 0, 0),
(32, 12, '2', 24, NULL, 'INDORACK', 'DO002 is on deliver progress ', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"2"},{"key":"order_id","val":"2"}]', '2017-09-20 10:58:26', NULL, NULL, 0, 0),
(33, 12, '2', 25, NULL, 'INDORACK', 'DO002 is on deliver progress ', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"2"},{"key":"order_id","val":"2"}]', '2017-09-20 10:58:26', NULL, NULL, 0, 0),
(34, 12, '2', 28, NULL, 'INDORACK', 'DO002 is on deliver progress ', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"2"},{"key":"order_id","val":"2"}]', '2017-09-20 10:58:26', NULL, NULL, 0, 0),
(35, 13, '2', 11, 'cm_0DNYM5FM:APA91bGiVMOwIrjp7LczQO-xLxxcCFuf3W-mWggeJsBs9BAxFFb-Z_uAYxF_G--yr24JmJFNL3_mIzjMRevEF6YovyR9MqxBvGbdoH9XQX3TTIgvwbCoKjtS0_R7ZuqSBczp1I2mCGk8', 'INDORACK', 'Driver in on the way to deliver your item PO002', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'DetailOrder', '[{"key":"order_id","val":"2"}]', '2017-09-20 10:58:26', NULL, NULL, 0, 0),
(36, 14, '2', 6, NULL, 'INDORACK', 'DO002 has completely sent', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"2"},{"key":"order_id","val":"2"}]', '2017-09-20 10:58:55', NULL, NULL, 0, 0),
(37, 14, '2', 24, NULL, 'INDORACK', 'DO002 has completely sent', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"2"},{"key":"order_id","val":"2"}]', '2017-09-20 10:58:55', NULL, NULL, 0, 0),
(38, 14, '2', 25, NULL, 'INDORACK', 'DO002 has completely sent', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"2"},{"key":"order_id","val":"2"}]', '2017-09-20 10:58:55', NULL, NULL, 0, 0),
(39, 14, '2', 28, NULL, 'INDORACK', 'DO002 has completely sent', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"2"},{"key":"order_id","val":"2"}]', '2017-09-20 10:58:55', NULL, NULL, 0, 0),
(40, 15, '2', 11, 'cm_0DNYM5FM:APA91bGiVMOwIrjp7LczQO-xLxxcCFuf3W-mWggeJsBs9BAxFFb-Z_uAYxF_G--yr24JmJFNL3_mIzjMRevEF6YovyR9MqxBvGbdoH9XQX3TTIgvwbCoKjtS0_R7ZuqSBczp1I2mCGk8', 'INDORACK', 'Your item is successfully delivered PO002', 'PO No. : PO002<br/>Delivery No. : DO002<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'DetailOrder', '[{"key":"order_id","val":"2"}]', '2017-09-20 10:58:55', NULL, NULL, 0, 0),
(41, 16, '3', 6, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-20 11:00:46', NULL, NULL, 0, 0),
(42, 16, '3', 24, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-20 11:00:46', NULL, NULL, 0, 0),
(43, 16, '3', 25, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-20 11:00:46', NULL, NULL, 0, 0),
(44, 16, '3', 28, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-20 11:00:46', NULL, NULL, 0, 0),
(45, 17, '3', 12, NULL, 'INDORACK', 'New job request DO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia<br/>Delivery Date : 21 Sep 2017 11:00', 'DashboardDriver', NULL, '2017-09-20 11:01:29', NULL, NULL, 0, 0),
(46, 18, '3', 7, NULL, 'INDORACK', 'New job request DO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia<br/>Delivery Date : 21 Sep 2017 11:00', 'DashboardTeknisiActivity', NULL, '2017-09-20 11:01:42', NULL, NULL, 0, 0),
(47, 18, '3', 22, NULL, 'INDORACK', 'New job request DO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia<br/>Delivery Date : 21 Sep 2017 11:00', 'DashboardTeknisiActivity', NULL, '2017-09-20 11:01:42', '2017-09-20 12:03:28', NULL, 1, 1),
(48, 19, '3', 6, NULL, 'INDORACK', 'Maman Selamet has accepted job DO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"4"},{"key":"order_id","val":"3"}]', '2017-09-20 11:02:22', NULL, NULL, 0, 0),
(49, 19, '3', 24, NULL, 'INDORACK', 'Maman Selamet has accepted job DO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"4"},{"key":"order_id","val":"3"}]', '2017-09-20 11:02:22', NULL, NULL, 0, 0),
(50, 19, '3', 25, NULL, 'INDORACK', 'Maman Selamet has accepted job DO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"4"},{"key":"order_id","val":"3"}]', '2017-09-20 11:02:22', NULL, NULL, 0, 0),
(51, 19, '3', 28, NULL, 'INDORACK', 'Maman Selamet has accepted job DO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"4"},{"key":"order_id","val":"3"}]', '2017-09-20 11:02:22', NULL, NULL, 0, 0),
(52, 20, '3', 6, NULL, 'INDORACK', 'DO003 is on deliver progress ', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"4"},{"key":"order_id","val":"3"}]', '2017-09-20 11:02:28', NULL, NULL, 0, 0),
(53, 20, '3', 24, NULL, 'INDORACK', 'DO003 is on deliver progress ', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"4"},{"key":"order_id","val":"3"}]', '2017-09-20 11:02:28', NULL, NULL, 0, 0),
(54, 20, '3', 25, NULL, 'INDORACK', 'DO003 is on deliver progress ', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"4"},{"key":"order_id","val":"3"}]', '2017-09-20 11:02:28', NULL, NULL, 0, 0),
(55, 20, '3', 28, NULL, 'INDORACK', 'DO003 is on deliver progress ', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"4"},{"key":"order_id","val":"3"}]', '2017-09-20 11:02:28', NULL, NULL, 0, 0),
(56, 21, '3', 11, 'cm_0DNYM5FM:APA91bGiVMOwIrjp7LczQO-xLxxcCFuf3W-mWggeJsBs9BAxFFb-Z_uAYxF_G--yr24JmJFNL3_mIzjMRevEF6YovyR9MqxBvGbdoH9XQX3TTIgvwbCoKjtS0_R7ZuqSBczp1I2mCGk8', 'INDORACK', 'Driver in on the way to deliver your item PO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'DetailOrder', '[{"key":"order_id","val":"3"}]', '2017-09-20 11:02:28', NULL, NULL, 0, 0),
(57, 22, '3', 6, NULL, 'INDORACK', 'DO003 has completely sent', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"4"},{"key":"order_id","val":"3"}]', '2017-09-20 11:02:48', NULL, NULL, 0, 0),
(58, 22, '3', 24, NULL, 'INDORACK', 'DO003 has completely sent', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"4"},{"key":"order_id","val":"3"}]', '2017-09-20 11:02:48', NULL, NULL, 0, 0),
(59, 22, '3', 25, NULL, 'INDORACK', 'DO003 has completely sent', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"4"},{"key":"order_id","val":"3"}]', '2017-09-20 11:02:48', NULL, NULL, 0, 0),
(60, 22, '3', 28, NULL, 'INDORACK', 'DO003 has completely sent', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignDriverNewActivity', '[{"key":"task_id","val":"4"},{"key":"order_id","val":"3"}]', '2017-09-20 11:02:48', NULL, NULL, 0, 0),
(61, 23, '3', 11, 'cm_0DNYM5FM:APA91bGiVMOwIrjp7LczQO-xLxxcCFuf3W-mWggeJsBs9BAxFFb-Z_uAYxF_G--yr24JmJFNL3_mIzjMRevEF6YovyR9MqxBvGbdoH9XQX3TTIgvwbCoKjtS0_R7ZuqSBczp1I2mCGk8', 'INDORACK', 'Your item is successfully delivered PO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'DetailOrder', '[{"key":"order_id","val":"3"}]', '2017-09-20 11:02:48', NULL, NULL, 0, 0),
(62, 24, '3', 6, NULL, 'INDORACK', 'Mahfud Azhary has accepted job DO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignTechnisian', '[{"key":"task_id","val":"5"},{"key":"order_id","val":"3"}]', '2017-09-20 11:04:23', NULL, NULL, 0, 0),
(63, 24, '3', 24, NULL, 'INDORACK', 'Mahfud Azhary has accepted job DO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignTechnisian', '[{"key":"task_id","val":"5"},{"key":"order_id","val":"3"}]', '2017-09-20 11:04:23', NULL, NULL, 0, 0),
(64, 24, '3', 25, NULL, 'INDORACK', 'Mahfud Azhary has accepted job DO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignTechnisian', '[{"key":"task_id","val":"5"},{"key":"order_id","val":"3"}]', '2017-09-20 11:04:23', NULL, NULL, 0, 0),
(65, 24, '3', 28, NULL, 'INDORACK', 'Mahfud Azhary has accepted job DO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignTechnisian', '[{"key":"task_id","val":"5"},{"key":"order_id","val":"3"}]', '2017-09-20 11:04:23', NULL, NULL, 0, 0),
(66, 25, '3', 6, NULL, 'INDORACK', 'DO003 is on assembly progress ', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignTechnisian', '[{"key":"task_id","val":"5"},{"key":"order_id","val":"3"}]', '2017-09-20 11:04:30', NULL, NULL, 0, 0),
(67, 25, '3', 24, NULL, 'INDORACK', 'DO003 is on assembly progress ', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignTechnisian', '[{"key":"task_id","val":"5"},{"key":"order_id","val":"3"}]', '2017-09-20 11:04:30', NULL, NULL, 0, 0),
(68, 25, '3', 25, NULL, 'INDORACK', 'DO003 is on assembly progress ', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignTechnisian', '[{"key":"task_id","val":"5"},{"key":"order_id","val":"3"}]', '2017-09-20 11:04:30', NULL, NULL, 0, 0),
(69, 25, '3', 28, NULL, 'INDORACK', 'DO003 is on assembly progress ', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignTechnisian', '[{"key":"task_id","val":"5"},{"key":"order_id","val":"3"}]', '2017-09-20 11:04:30', NULL, NULL, 0, 0),
(70, 26, '3', 11, 'cm_0DNYM5FM:APA91bGiVMOwIrjp7LczQO-xLxxcCFuf3W-mWggeJsBs9BAxFFb-Z_uAYxF_G--yr24JmJFNL3_mIzjMRevEF6YovyR9MqxBvGbdoH9XQX3TTIgvwbCoKjtS0_R7ZuqSBczp1I2mCGk8', 'INDORACK', 'Technician in on the way to assembly your item PO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'DetailOrder', '[{"key":"order_id","val":"3"}]', '2017-09-20 11:04:30', NULL, NULL, 0, 0),
(71, 27, '3', 6, NULL, 'INDORACK', 'DO003 has completely assembled', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignTechnisian', '[{"key":"task_id","val":"5"},{"key":"order_id","val":"3"}]', '2017-09-20 11:04:49', NULL, NULL, 0, 0),
(72, 27, '3', 24, NULL, 'INDORACK', 'DO003 has completely assembled', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignTechnisian', '[{"key":"task_id","val":"5"},{"key":"order_id","val":"3"}]', '2017-09-20 11:04:49', NULL, NULL, 0, 0),
(73, 27, '3', 25, NULL, 'INDORACK', 'DO003 has completely assembled', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignTechnisian', '[{"key":"task_id","val":"5"},{"key":"order_id","val":"3"}]', '2017-09-20 11:04:49', NULL, NULL, 0, 0),
(74, 27, '3', 28, NULL, 'INDORACK', 'DO003 has completely assembled', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'AssignTechnisian', '[{"key":"task_id","val":"5"},{"key":"order_id","val":"3"}]', '2017-09-20 11:04:49', NULL, NULL, 0, 0),
(75, 28, '3', 11, 'cm_0DNYM5FM:APA91bGiVMOwIrjp7LczQO-xLxxcCFuf3W-mWggeJsBs9BAxFFb-Z_uAYxF_G--yr24JmJFNL3_mIzjMRevEF6YovyR9MqxBvGbdoH9XQX3TTIgvwbCoKjtS0_R7ZuqSBczp1I2mCGk8', 'INDORACK', 'Your item has completely assembly PO003', 'PO No. : PO003<br/>Delivery No. : DO003<br/>To : Thomas Ardianto(Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia)', 'DetailOrder', '[{"key":"order_id","val":"3"}]', '2017-09-20 11:04:49', NULL, NULL, 0, 0),
(76, 29, '4', 6, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO019991', 'PO No. : PO019991<br/>Delivery No. : DO019991<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-26 04:15:34', NULL, NULL, 0, 0),
(77, 29, '4', 24, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO019991', 'PO No. : PO019991<br/>Delivery No. : DO019991<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-26 04:15:34', NULL, NULL, 0, 0),
(78, 29, '4', 25, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO019991', 'PO No. : PO019991<br/>Delivery No. : DO019991<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-26 04:15:34', NULL, NULL, 0, 0),
(79, 29, '4', 28, NULL, 'INDORACK', 'NEW DELIVERY ORDER DO019991', 'PO No. : PO019991<br/>Delivery No. : DO019991<br/>To : Thomas Ardianto', 'DashboardKepalaGudang', '[{"key":"id","val":"1"},{"key":"task","val":"2"},{"key":"currentTabIndex","val":0}]', '2017-09-26 04:15:34', NULL, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `notification_groups`
--

DROP TABLE IF EXISTS `notification_groups`;
CREATE TABLE `notification_groups` (
  `id` bigint(20) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notification_groups`
--

INSERT INTO `notification_groups` (`id`, `created`) VALUES
(1, '2017-09-20 10:41:29'),
(2, '2017-09-20 10:45:30'),
(3, '2017-09-20 10:46:25'),
(4, '2017-09-20 10:46:32'),
(5, '2017-09-20 10:46:32'),
(6, '2017-09-20 10:55:05'),
(7, '2017-09-20 10:55:05'),
(8, '2017-09-20 10:56:46'),
(9, '2017-09-20 10:57:27'),
(10, '2017-09-20 10:57:43'),
(11, '2017-09-20 10:58:21'),
(12, '2017-09-20 10:58:26'),
(13, '2017-09-20 10:58:26'),
(14, '2017-09-20 10:58:55'),
(15, '2017-09-20 10:58:55'),
(16, '2017-09-20 11:00:46'),
(17, '2017-09-20 11:01:29'),
(18, '2017-09-20 11:01:42'),
(19, '2017-09-20 11:02:22'),
(20, '2017-09-20 11:02:28'),
(21, '2017-09-20 11:02:28'),
(22, '2017-09-20 11:02:48'),
(23, '2017-09-20 11:02:48'),
(24, '2017-09-20 11:04:23'),
(25, '2017-09-20 11:04:30'),
(26, '2017-09-20 11:04:30'),
(27, '2017-09-20 11:04:49'),
(28, '2017-09-20 11:04:49'),
(29, '2017-09-26 04:15:34');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_no` varchar(255) DEFAULT NULL,
  `delivery_no` varchar(255) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL COMMENT 'reference to users tables with foreignKey user_id',
  `receiver_name` varchar(255) DEFAULT NULL,
  `receiver_phone` varchar(255) DEFAULT NULL,
  `address` text,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `description` text,
  `delivery_type_id` int(11) DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `pickup_date` datetime DEFAULT NULL,
  `is_urgent` smallint(1) NOT NULL DEFAULT '0',
  `vehicle_no` varchar(255) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `is_assembling` smallint(1) NOT NULL DEFAULT '0',
  `assembly_date` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `delivery_status` smallint(2) DEFAULT '1',
  `assembly_status` smallint(2) DEFAULT NULL,
  `pickup_status` smallint(11) DEFAULT NULL,
  `last_notification_delivery` datetime DEFAULT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_no`, `delivery_no`, `customer_id`, `receiver_name`, `receiver_phone`, `address`, `latitude`, `longitude`, `description`, `delivery_type_id`, `delivery_date`, `pickup_date`, `is_urgent`, `vehicle_no`, `driver_id`, `is_assembling`, `assembly_date`, `created`, `modified`, `delivery_status`, `assembly_status`, `pickup_status`, `last_notification_delivery`, `status`) VALUES
(1, 'PO001', 'DO001', 11, 'Thomas Ardianto', '32234234234234', 'Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia', '-6.275359999999999', '106.87092495000002', 'Tolong di anter segera', 1, '2017-09-20 10:55:59', NULL, 1, NULL, NULL, 0, NULL, '2017-09-20 10:41:29', '2017-09-20 10:41:29', 6, NULL, NULL, NULL, 1),
(2, 'PO002', 'DO002', 11, 'Thomas Ardianto', '32234234234234', 'Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia', '-6.275359999999999', '106.87092495000002', '', 1, '2017-09-21 10:00:59', NULL, 1, NULL, NULL, 1, '2017-09-21 10:00:59', '2017-09-20 10:56:46', '2017-09-20 10:56:46', 6, 2, NULL, NULL, 1),
(3, 'PO003', 'DO003', 11, 'Thomas Ardianto', '32234234234234', 'Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia', '-6.275359999999999', '106.87092495000002', '', 1, '2017-09-21 11:00:59', NULL, 1, NULL, NULL, 1, '2017-09-21 11:00:59', '2017-09-20 11:00:46', '2017-09-20 11:00:46', 6, 6, NULL, NULL, 1),
(4, 'PO019991', 'DO019991', 11, 'Thomas Ardianto', '32234234234234', 'Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia', '-6.275359999999999', '106.87092495000002', '', 1, '2017-09-26 17:00:59', NULL, 0, NULL, NULL, 0, NULL, '2017-09-26 04:15:34', '2017-09-26 04:15:34', 1, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_products`
--

DROP TABLE IF EXISTS `order_products`;
CREATE TABLE `order_products` (
  `id` bigint(20) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT '1',
  `description` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `order_products`
--

INSERT INTO `order_products` (`id`, `order_id`, `product_id`, `qty`, `description`) VALUES
(1, 1, 2, 1, NULL),
(2, 2, 2, 1, NULL),
(3, 3, 2, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_statuses`
--

DROP TABLE IF EXISTS `order_statuses`;
CREATE TABLE `order_statuses` (
  `id` int(11) NOT NULL,
  `ass` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `order_statuses`
--

INSERT INTO `order_statuses` (`id`, `ass`, `name`, `color`) VALUES
(1, 0, 'driver &amp; technician has not assigned', '#ff6565'),
(2, 0, 'driver has not assigned', '#0085b6'),
(3, 0, 'driver has assigned', '#ffd200'),
(4, 0, 'driver &amp; technician has assigned', '#7f7fff'),
(5, 0, 'driver accepted', '#05f6ff'),
(6, 0, 'driver rejected', '#00d814'),
(7, 0, 'technician accepted', '#ab0065'),
(8, 0, 'technician rejected', '#ab0065'),
(9, 0, 'item on delivery process', '#05f6ff'),
(10, 0, 'item successfully received', '#05f6ff'),
(11, 0, 'technician on the way', '#ab0065'),
(12, 0, 'Item finish assembling', '#ab0065');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `pdf_download` smallint(2) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `code`, `name`, `description`, `pdf_download`, `created`, `modified`, `status`) VALUES
(4, 2, '3242', 'assad', 'asdasd', 1, '2017-08-07 13:40:46', '2017-08-09 14:24:12', 1),
(2, 1, 'PO10029994', 'IR9042G - Standing Close Rack 42U Glass Door', 'Standing Close Rack Complete Set with:\r\n\r\nGlass Front door, steel rear door and 2 side door wih lock\r\n1 Unit Vertical Powerset 12 outlet with switch\r\n4 FAN Included\r\n1pcs fix shelf and 50 set Cagenuts and Screws\r\n4pcs Adjustable foot and 4pcs Castors', 0, '2017-05-17 10:19:17', '2017-05-17 10:19:17', 1),
(3, 1, 'PO10029995', 'PRO11545 - Standing Heavy Duty Close Rack 42U Perforated Door', 'Standing Heavy Duty Close Rack Complete Set with:\r\n\r\nPerforated Front door, steel rear door and 2 side door wih lock\r\n2 Unit Horizontal Powerset 6 outlet with switch\r\n4 FAN Included\r\n1pcs fix shelf and 50 set Cagenuts and Screws\r\n4pcs Adjustable foot and 4pcs Castors', 0, '2017-05-17 10:22:54', '2017-08-07 13:40:28', 1),
(5, 1, 'XW32S', 'ASFASDASD', 'ASDASDAD', 1, '2017-08-08 14:50:16', '2017-08-09 14:23:12', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `status` smallint(2) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `created`, `modified`, `status`) VALUES
(1, 'Close Rack', '2017-08-09 14:12:16', '2017-08-09 14:15:24', 1),
(2, 'Wallmount', '2017-08-09 14:23:59', '2017-08-09 14:23:59', 1),
(3, 'Open Rack', '2017-08-09 16:51:45', '2017-08-23 15:15:21', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `pos` int(11) NOT NULL DEFAULT '0',
  `is_default` smallint(1) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `pos`, `is_default`, `created`) VALUES
(1, 1, 0, 0, '2017-05-16 10:35:41'),
(2, 2, 0, 0, '2017-05-17 10:19:17'),
(3, 3, 0, 0, '2017-05-17 10:22:54'),
(4, 4, 0, 0, '2017-08-07 13:44:04'),
(5, 5, 0, 0, '2017-08-08 14:51:01');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
CREATE TABLE `ratings` (
  `id` bigint(12) NOT NULL,
  `order_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `star` smallint(1) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `restricted_controllers`
--

DROP TABLE IF EXISTS `restricted_controllers`;
CREATE TABLE `restricted_controllers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `restricted_controllers`
--

INSERT INTO `restricted_controllers` (`id`, `name`) VALUES
(1, 'AccountController'),
(2, 'PagesController'),
(3, 'TemplateController'),
(4, 'AppController'),
(5, 'ApiController');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `cms_url` varchar(255) DEFAULT NULL,
  `cms_title` varchar(255) DEFAULT NULL,
  `cms_description` text,
  `cms_keywords` text,
  `cms_author` varchar(255) DEFAULT NULL,
  `cms_app_name` varchar(255) DEFAULT NULL,
  `cms_logo_url` varchar(255) DEFAULT NULL,
  `path_content` varchar(255) DEFAULT NULL,
  `path_webroot` varchar(255) DEFAULT NULL,
  `map_browser_api_key` varchar(255) DEFAULT NULL,
  `firebase_api_key` text NOT NULL,
  `default_lat` varchar(255) DEFAULT NULL,
  `default_lng` varchar(255) DEFAULT NULL,
  `product_width` int(11) NOT NULL DEFAULT '500',
  `product_height` int(11) NOT NULL DEFAULT '500',
  `modified` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `cms_url`, `cms_title`, `cms_description`, `cms_keywords`, `cms_author`, `cms_app_name`, `cms_logo_url`, `path_content`, `path_webroot`, `map_browser_api_key`, `firebase_api_key`, `default_lat`, `default_lng`, `product_width`, `product_height`, `modified`) VALUES
(1, 'http://192.168.1.41/indorack/cms/', 'INDORACK CMS', 'INDORACK CMS', 'INDORACK CMS', 'MSolving', 'INDORACK CMS 1.0', NULL, 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/', 'AIzaSyCgC44R6iu0UnzCuF9NfQ33LznETv3mZSA', 'AAAAAvM9NBg:APA91bHQVSXsGFL3pk99yN_VbC8QNYiTnJIIRqaziOGcqazK82v_D3zax_rVQvME2q45lh8FyPf53OkTaVlp1c2lLh34vlbVOnw3wJ5ktPuitn3qHyYDTrq6EiurseQyH3XqnwfpiD4y', '-6.175414', '106.827122', 300, 300, '2017-02-28 12:10:53');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` bigint(20) NOT NULL,
  `task_type_id` smallint(1) NOT NULL,
  `order_id` int(11) NOT NULL,
  `vehicle_no` varchar(255) DEFAULT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `task_type_id`, `order_id`, `vehicle_no`, `status`) VALUES
(1, 1, 1, 'B1234GTT', 6),
(2, 1, 2, 'B1234GTT', 6),
(3, 2, 2, NULL, 2),
(4, 1, 3, 'B1234GTT', 6),
(5, 2, 3, NULL, 6),
(6, 1, 4, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `task_assigns`
--

DROP TABLE IF EXISTS `task_assigns`;
CREATE TABLE `task_assigns` (
  `id` bigint(20) NOT NULL,
  `task_id` bigint(20) DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `reason` text,
  `status` smallint(2) NOT NULL DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `task_assigns`
--

INSERT INTO `task_assigns` (`id`, `task_id`, `order_id`, `employee_id`, `reason`, `status`, `created`, `modified`) VALUES
(1, 1, '1', 12, 'DO001', 6, '2017-09-20 10:45:30', '2017-09-20 10:45:30'),
(2, 2, '2', 12, 'DO002', 6, '2017-09-20 10:57:27', '2017-09-20 10:57:27'),
(3, 3, '2', 7, NULL, 2, '2017-09-20 10:57:43', '2017-09-20 10:57:43'),
(4, 3, '2', 22, NULL, 2, '2017-09-20 10:57:43', '2017-09-20 10:57:43'),
(5, 4, '3', 12, 'Barang sudah sampai', 6, '2017-09-20 11:01:29', '2017-09-20 11:01:29'),
(6, 5, '3', 7, NULL, 6, '2017-09-20 11:01:42', '2017-09-20 11:01:42'),
(7, 5, '3', 22, 'kurang baut 1', 6, '2017-09-20 11:01:42', '2017-09-20 11:01:42');

-- --------------------------------------------------------

--
-- Table structure for table `task_histories`
--

DROP TABLE IF EXISTS `task_histories`;
CREATE TABLE `task_histories` (
  `id` bigint(20) NOT NULL,
  `task_id` bigint(20) DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `reason` text,
  `status` smallint(2) DEFAULT '1',
  `created` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `task_histories`
--

INSERT INTO `task_histories` (`id`, `task_id`, `order_id`, `employee_id`, `reason`, `status`, `created`) VALUES
(1, 1, '1', 12, NULL, 2, '2017-09-20 10:45:30'),
(2, 1, '1', 12, NULL, 3, '2017-09-20 10:46:25'),
(3, 1, '1', 12, NULL, 5, '2017-09-20 10:46:32'),
(4, 1, '1', 12, '''DO001''', 6, '2017-09-20 10:55:05'),
(5, 2, '2', 12, NULL, 2, '2017-09-20 10:57:27'),
(6, 3, '2', 7, NULL, 2, '2017-09-20 10:57:43'),
(7, 3, '2', 22, NULL, 2, '2017-09-20 10:57:43'),
(8, 2, '2', 12, NULL, 3, '2017-09-20 10:58:21'),
(9, 2, '2', 12, NULL, 5, '2017-09-20 10:58:26'),
(10, 2, '2', 12, '''DO002''', 6, '2017-09-20 10:58:55'),
(11, 4, '3', 12, NULL, 2, '2017-09-20 11:01:29'),
(12, 5, '3', 7, NULL, 2, '2017-09-20 11:01:42'),
(13, 5, '3', 22, NULL, 2, '2017-09-20 11:01:42'),
(14, 4, '3', 12, NULL, 3, '2017-09-20 11:02:22'),
(15, 4, '3', 12, NULL, 5, '2017-09-20 11:02:28'),
(16, 4, '3', 12, '''Barang sudah sampai''', 6, '2017-09-20 11:02:48'),
(17, 5, '3', 22, NULL, 3, '2017-09-20 11:04:23'),
(18, 5, '3', 22, NULL, 5, '2017-09-20 11:04:30'),
(19, 5, '3', 7, NULL, 5, '2017-09-20 11:04:30'),
(20, 5, '3', 22, '''kurang baut 1''', 6, '2017-09-20 11:04:49'),
(21, 5, '3', 7, NULL, 6, '2017-09-20 11:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `task_statuses`
--

DROP TABLE IF EXISTS `task_statuses`;
CREATE TABLE `task_statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_technician` varchar(255) DEFAULT NULL,
  `name_driver` varchar(255) DEFAULT NULL,
  `name_customer` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `description` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `task_statuses`
--

INSERT INTO `task_statuses` (`id`, `name`, `name_technician`, `name_driver`, `name_customer`, `color`, `description`) VALUES
(1, 'Not Assign', NULL, NULL, 'In Process', '#B71C1C', NULL),
(2, 'Assigned', 'Waiting to Confirm', 'Waiting to Confirm', 'In Process', '#0085b6', NULL),
(3, 'Accepted', 'Waiting to Assembly', 'Waiting to Deliver', 'In Process', '#23bb0f', NULL),
(4, 'Rejected', 'Rejected Job', 'Rejected Job', 'In Process', '#7f7fff', NULL),
(5, 'On Progress', 'On Progress', 'On Progress', 'On Delivery', '#6a6e00', NULL),
(6, 'Completed', 'Completed', 'Completed', 'Completed', '#00d814', NULL),
(7, 'Failed', 'Failed', 'Failed', 'Failed', '#ab0065', NULL),
(8, 'Cancelled', 'Cancelled', 'Cancelled', 'Cancelled', '#ab0065', 'Di cancel/diganti oleh Admin'),
(9, 'Waiting to pickup', 'Not pickup yet', 'Not pickup yet', 'Not pickup yet', '#FF0000', 'Not pickup yet'),
(10, 'Has been pickup', 'Has been pickup', 'Has been pickup', 'Has been pickup', '#ab0065', 'Has been pickup');

-- --------------------------------------------------------

--
-- Table structure for table `task_types`
--

DROP TABLE IF EXISTS `task_types`;
CREATE TABLE `task_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `task_types`
--

INSERT INTO `task_types` (`id`, `name`) VALUES
(1, 'Delivery'),
(2, 'Assembly');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `aro_id` int(11) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `address` text,
  `phone1` varchar(255) DEFAULT NULL,
  `phone2` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `current_latitude` varchar(255) DEFAULT NULL,
  `current_longitude` varchar(255) DEFAULT NULL,
  `is_admin` smallint(1) NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT '',
  `password` varchar(255) DEFAULT NULL,
  `is_verify` smallint(1) NOT NULL DEFAULT '0',
  `verify_date` datetime DEFAULT NULL,
  `gcm_id` varchar(255) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `last_login_cms` datetime DEFAULT NULL,
  `last_login_web` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `aro_id`, `province_id`, `city_id`, `address`, `phone1`, `phone2`, `latitude`, `longitude`, `current_latitude`, `current_longitude`, `is_admin`, `email`, `firstname`, `lastname`, `password`, `is_verify`, `verify_date`, `gcm_id`, `status`, `created`, `modified`, `last_login_cms`, `last_login_web`) VALUES
(1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'developer@indorack.co.id', 'Developer', 'Admin', 'qpOVrZaYsJk=', 1, '2017-03-06 10:28:00', NULL, 1, NULL, NULL, '2017-09-26 04:14:52', NULL),
(2, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'superadmin@indorack.co.id', 'Super', 'Admin', 'qpOVrZaYsJk=', 1, '2017-03-06 10:28:00', NULL, 1, '2017-03-06 12:11:34', '2017-05-05 11:33:31', '2017-09-27 12:18:34', NULL),
(3, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'admin@indorack.co.id', 'Regular', 'Admin', 'qpOVrZaYsJk=', 1, '2017-03-08 00:00:00', NULL, 1, '2017-03-06 12:11:34', '2017-03-06 12:11:34', '2017-06-14 15:37:29', NULL),
(6, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'admin.gudang@indorack.co.id', 'Ahmad', 'Zaini Azay', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-05 16:41:34', '2017-05-12 10:38:41', NULL, NULL),
(7, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'teknisi@indorack.co.id', 'Rofi', 'Mujrofi Ahmad', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-05 16:44:55', '2017-05-12 10:39:33', NULL, NULL),
(10, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'abyfajar@gmail.com', 'Aby', 'Fajar', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-09 11:32:00', '2017-05-09 11:32:00', NULL, NULL),
(11, 7, NULL, NULL, 'Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia', '32234234234234', NULL, '-6.275359999999999', '106.87092495000002', NULL, NULL, 0, 'thomas@gmail.com', 'Thomas', 'Ardianto', 'qpOVrZaYsJk=', 0, NULL, 'cm_0DNYM5FM:APA91bGiVMOwIrjp7LczQO-xLxxcCFuf3W-mWggeJsBs9BAxFFb-Z_uAYxF_G--yr24JmJFNL3_mIzjMRevEF6YovyR9MqxBvGbdoH9XQX3TTIgvwbCoKjtS0_R7ZuqSBczp1I2mCGk8', 1, '2017-05-09 11:42:05', '2017-05-09 11:42:05', NULL, NULL),
(12, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'maman.slamet@indorack.co.id', 'Maman', 'Selamet', 'qpOVrZaYsJk=', 0, NULL, 'fWZz1BlY9-Y:APA91bFfXvqMMRe2jb1-H8r5gHgaJsH1p2rrE5LqA-EyHivs6Wfn0VDQ_ZjeI121Hh4x_36KowUWwEzKKsGyUMcvWzy1JCzZToAtJVpgDwbSwSCmU8QudaYqzGNkGJp641uT5iphDzPu', 1, '2017-05-12 11:46:11', '2017-05-12 11:46:11', NULL, NULL),
(13, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'putra.praditya@indorack.co.id', 'Putra', 'Praditya', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:46:44', '2017-05-12 11:46:44', NULL, NULL),
(14, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'bagus.aditha@indorack.co.id', 'Bagus', 'Adhita', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:47:11', '2017-05-12 11:47:11', NULL, NULL),
(15, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'bambang.wicaksono@indorack.co.id', 'Bambang', 'Wicaksono', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:47:40', '2017-05-12 11:47:40', NULL, NULL),
(16, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'syaid.jamal@indorack.co.id', 'Syaid', 'Jamal Handayani', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:48:21', '2017-05-12 11:51:46', NULL, NULL),
(17, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '-6.3696743', '106.9123025', 1, 'cosmas.siahaan@indorack.co.id', 'Cosmas', 'Siahaan', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:48:59', '2017-05-12 11:48:59', NULL, NULL),
(18, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'tulus.tobing@indorack.co.id', 'Tulus', 'Tobing', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:49:41', '2017-05-12 11:49:41', NULL, NULL),
(19, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'bayu.putro@indorack.co.id', 'Bayu', 'Putro', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:50:10', '2017-05-12 11:50:10', NULL, NULL),
(20, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'martin.adiyono@indorack.co.id', 'Martin', 'Adiyono', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:50:36', '2017-05-12 11:50:36', NULL, NULL),
(21, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'resi.kusumo@indorack.co.id', 'Resi', 'Kusumo', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:51:07', '2017-05-12 11:51:07', NULL, NULL),
(22, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '-6.19311', '106.8912836', 1, 'mahfud.azhary@indorack.co.id', 'Mahfud', 'Azhary', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:51:42', '2017-05-12 11:51:42', NULL, NULL),
(23, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'syahrul.ramadhan@indorack.co.id', 'Syahrul', 'Ramadhan', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:52:14', '2017-05-12 11:52:14', NULL, NULL),
(24, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'anthony.aang@indorack.co.id', 'Anthony', 'Aang', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:53:24', '2017-05-12 11:53:24', NULL, NULL),
(25, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'prio.triasmoro@indorack.co.id', 'Prio', 'Triasmoro', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:54:02', '2017-05-12 11:54:02', NULL, NULL),
(26, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'andre@indorack.co.id', 'Andre', 'Wibowo Harlim', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:54:32', '2017-05-12 11:54:32', NULL, NULL),
(27, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'iwan.setiawan@indorack.co.id', 'Iwan', 'Setiawan', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:55:14', '2017-05-12 11:55:14', NULL, NULL),
(28, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '-6.19311', '106.8912836', 1, 'beledug.bantolo@indorack.co.id', 'Beledug', 'Bantolo', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-05-12 11:55:55', '2017-05-12 11:55:55', NULL, NULL),
(29, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'backupdriver@indorack.co.id', 'Backup', 'Driver', 'qpOVrZaYsJk=', 0, NULL, NULL, 1, '2017-08-30 17:12:22', '2017-08-30 17:12:22', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `vehicle_no` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_no`, `user_id`, `created`, `modified`, `status`) VALUES
(1, 'B1234GTT', 12, '2017-08-28 10:34:51', '2017-09-15 03:35:06', 1),
(2, 'B1234GSU', 17, '2017-08-28 10:44:32', '2017-09-15 03:29:09', 1),
(3, 'BU7777GTT', 13, '2017-09-15 03:27:57', '2017-09-15 03:27:57', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acos`
--
ALTER TABLE `acos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lft` (`lft`),
  ADD KEY `rght` (`rght`);

--
-- Indexes for table `aros`
--
ALTER TABLE `aros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lft` (`lft`),
  ADD KEY `rght` (`rght`);

--
-- Indexes for table `aros_acos`
--
ALTER TABLE `aros_acos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ARO_ACO_KEY` (`aro_id`,`aco_id`),
  ADD KEY `aro_id` (`aro_id`),
  ADD KEY `aco_id` (`aco_id`);

--
-- Indexes for table `cms_menus`
--
ALTER TABLE `cms_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `lft` (`lft`),
  ADD KEY `rght` (`rght`);

--
-- Indexes for table `cms_menu_translations`
--
ALTER TABLE `cms_menu_translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `locale` (`locale`),
  ADD KEY `model` (`model`),
  ADD KEY `row_id` (`foreign_key`),
  ADD KEY `field` (`field`);

--
-- Indexes for table `contents`
--
ALTER TABLE `contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `model` (`model`),
  ADD KEY `model_id` (`model_id`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `delivery_statuses`
--
ALTER TABLE `delivery_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_types`
--
ALTER TABLE `delivery_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `i18n`
--
ALTER TABLE `i18n`
  ADD PRIMARY KEY (`id`),
  ADD KEY `locale` (`locale`),
  ADD KEY `model` (`model`),
  ADD KEY `row_id` (`foreign_key`),
  ADD KEY `field` (`field`);

--
-- Indexes for table `job_assigns`
--
ALTER TABLE `job_assigns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_assign_status`
--
ALTER TABLE `job_assign_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `langs`
--
ALTER TABLE `langs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `navigation_menus`
--
ALTER TABLE `navigation_menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `navigation_menus_2`
--
ALTER TABLE `navigation_menus_2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created` (`created`),
  ADD KEY `arrival_date` (`arrival_date`),
  ADD KEY `read_date` (`read_date`),
  ADD KEY `is_arrival` (`is_arrival`),
  ADD KEY `is_readed` (`is_readed`);

--
-- Indexes for table `notification_groups`
--
ALTER TABLE `notification_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_products`
--
ALTER TABLE `order_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_statuses`
--
ALTER TABLE `order_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_2` (`code`),
  ADD KEY `code` (`code`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `pos` (`pos`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`employee_id`);

--
-- Indexes for table `restricted_controllers`
--
ALTER TABLE `restricted_controllers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_assigns`
--
ALTER TABLE `task_assigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `task_histories`
--
ALTER TABLE `task_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `task_statuses`
--
ALTER TABLE `task_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_types`
--
ALTER TABLE `task_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gcm_id` (`gcm_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_no` (`vehicle_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acos`
--
ALTER TABLE `acos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
--
-- AUTO_INCREMENT for table `aros`
--
ALTER TABLE `aros`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `aros_acos`
--
ALTER TABLE `aros_acos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;
--
-- AUTO_INCREMENT for table `cms_menus`
--
ALTER TABLE `cms_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;
--
-- AUTO_INCREMENT for table `cms_menu_translations`
--
ALTER TABLE `cms_menu_translations`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
--
-- AUTO_INCREMENT for table `contents`
--
ALTER TABLE `contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT for table `delivery_statuses`
--
ALTER TABLE `delivery_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `delivery_types`
--
ALTER TABLE `delivery_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `i18n`
--
ALTER TABLE `i18n`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `job_assigns`
--
ALTER TABLE `job_assigns`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `job_assign_status`
--
ALTER TABLE `job_assign_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `langs`
--
ALTER TABLE `langs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `navigation_menus`
--
ALTER TABLE `navigation_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `navigation_menus_2`
--
ALTER TABLE `navigation_menus_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;
--
-- AUTO_INCREMENT for table `notification_groups`
--
ALTER TABLE `notification_groups`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `order_products`
--
ALTER TABLE `order_products`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `order_statuses`
--
ALTER TABLE `order_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` bigint(12) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `restricted_controllers`
--
ALTER TABLE `restricted_controllers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `task_assigns`
--
ALTER TABLE `task_assigns`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `task_histories`
--
ALTER TABLE `task_histories`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `task_statuses`
--
ALTER TABLE `task_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `task_types`
--
ALTER TABLE `task_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
