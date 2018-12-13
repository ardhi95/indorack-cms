-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2017 at 08:06 PM
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
(1, NULL, 2, NULL, NULL, 'top', '', 1, 16, 1, '2016-11-21 00:00:00', '2016-11-21 00:00:00'),
(2, 1, 2, NULL, 'Dashboards', 'Dashboards', '', 2, 3, 1, '2016-11-21 22:41:45', '2016-11-22 16:50:47'),
(4, 1, 2, NULL, 'Admins', 'Admins', '', 4, 5, 1, '2016-11-21 22:42:07', '2016-11-22 16:51:00'),
(5, 1, 2, NULL, 'CmsMenus', 'CmsMenus', '', 6, 7, 1, '2016-11-21 22:47:10', '2016-11-22 16:57:18'),
(7, 1, 1, NULL, 'ModuleObjects', 'ModuleObjects', '', 8, 9, 1, '2016-11-22 16:27:57', '2016-11-22 16:58:48'),
(9, 1, 2, NULL, 'AdminGroups', 'UserGroups', '', 10, 11, 1, '2016-11-23 12:26:39', '2017-05-05 16:35:46'),
(45, 1, 2, NULL, 'Orders', 'Orders', '', 14, 15, 1, '2017-05-08 14:43:01', '2017-05-08 14:43:01');

-- --------------------------------------------------------

--
-- Table structure for table `aros`
--

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
(38, 3, 9, '0', '0', '0', '0'),
(37, 3, 7, '0', '0', '0', '0'),
(36, 3, 5, '0', '0', '0', '0'),
(35, 3, 4, '0', '0', '0', '0'),
(33, 3, 2, '1', '1', '1', '1'),
(44, 4, 2, '0', '0', '0', '0'),
(46, 4, 4, '0', '0', '0', '0'),
(47, 4, 5, '0', '0', '0', '0'),
(48, 4, 9, '0', '0', '0', '0'),
(66, 5, 9, '0', '0', '0', '0'),
(65, 5, 5, '0', '0', '0', '0'),
(64, 5, 4, '0', '0', '0', '0'),
(62, 5, 2, '0', '0', '0', '0'),
(71, 7, 2, '0', '0', '0', '0'),
(73, 7, 4, '0', '0', '0', '0'),
(74, 7, 5, '0', '0', '0', '0'),
(75, 7, 9, '0', '0', '0', '0'),
(80, 1, 45, '1', '1', '1', '1'),
(81, 2, 45, '1', '1', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `cms_menus`
--

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
(1, NULL, NULL, 1, 18, 'Top Level Menu', '', 0, 1, '2016-11-13 15:58:17', '2016-11-13 15:58:17'),
(2, NULL, 1, 2, 3, 'Menu Utama', '', 1, 1, '2016-11-13 15:58:17', '2017-01-05 09:58:42'),
(3, 2, 1, 4, 5, 'Dashboard', 'fa fa-desktop', 0, 1, '2016-11-13 15:58:17', '2016-11-13 20:46:05'),
(5, NULL, 1, 8, 9, 'Settings', '', 1, 1, '2016-11-13 15:58:17', '2017-05-05 13:21:29'),
(6, 4, 1, 10, 11, 'Admin List', 'fa fa-user', 0, 1, '2016-11-13 15:59:25', '2017-05-10 13:55:28'),
(9, 5, 1, 14, 15, 'Menu CMS', 'fa fa-bars', 0, 1, '2016-11-14 10:15:59', '2017-01-05 10:02:00'),
(26, 7, 1, 16, 17, 'Objek Modul', 'glyphicon glyphicon-wrench', 0, 1, '2016-11-21 20:25:25', '2017-01-05 11:44:14'),
(29, 9, 1, 12, 13, 'Admin Groups', 'fa fa-users', 0, 1, '2016-11-22 17:09:30', '2017-05-10 13:55:40'),
(62, 45, 1, 6, 7, 'Orders', 'glyphicon glyphicon-edit', 0, 1, '2017-05-08 14:45:22', '2017-05-08 14:45:22');

-- --------------------------------------------------------

--
-- Table structure for table `cms_menu_translations`
--

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
(55, 'idn', 'CmsMenu', 62, 'name', 'Orders'),
(56, 'eng', 'CmsMenu', 62, 'name', 'Orders');

-- --------------------------------------------------------

--
-- Table structure for table `contents`
--

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
(1, 'User', 2, 'square', 'http://192.168.1.73/indorack/cms/', 'contents/User/2/2_square.jpg', 'image/jpg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/User/2/2_square.jpg', 200, 200, '2017-05-05 11:33:09', '2017-05-05 11:33:09'),
(2, 'User', 2, 'maxwidth', 'http://192.168.1.73/indorack/cms/', 'contents/User/2/2_maxwidth.jpg', 'image/jpg', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/User/2/2_maxwidth.jpg', 200, 200, '2017-05-05 11:33:09', '2017-05-05 11:33:09');

-- --------------------------------------------------------

--
-- Table structure for table `i18n`
--

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
-- Table structure for table `langs`
--

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
-- Table structure for table `orders`
--

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
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `delivery_date` datetime DEFAULT NULL,
  `is_urgent` smallint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_no`, `delivery_no`, `customer_id`, `receiver_name`, `receiver_phone`, `address`, `latitude`, `longitude`, `title`, `description`, `delivery_date`, `is_urgent`, `created`, `modified`, `status`) VALUES
(1, 'NO1003991002', 'SU1003991002', 10, 'Aby Fajar', '081229361946', 'Divertone, Jalan Balap Sepeda, Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia', '-6.1928959', '106.89135184999998', 'RAKC2099DK', 'Harus diantarkan secepatnya yah... ok ok!', '2017-05-10 05:25:59', 1, '2017-05-09 11:32:00', '2017-05-09 14:04:56', 1),
(2, 'NO1003991003', 'SU1003991003', 10, 'Aby Fajar', '081238829918', 'Divertone, Jalan Balap Sepeda, Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia', '-6.1928959', '106.89135184999998', 'RACK-2002', 'Kurang 1..', '2017-05-10 11:30:59', 0, '2017-05-09 11:36:01', '2017-05-09 11:36:01', 2),
(3, 'NO1003991004', 'SU1003991004', 11, 'Thomas Ardianto', '32234234234234', 'Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia', '-6.275359999999999', '106.87092495000002', 'RAKC2099DK', '', '2017-05-10 11:40:59', 1, '2017-05-09 11:42:05', '2017-05-09 11:42:05', 3),
(4, 'NO1003991005', 'SU1003991005', 11, 'Thomas Ardianto', '32234234234234', 'Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia', '-6.275359999999999', '106.87092495000002', 'RAKC2099DK', 'Jangan kelamaan yah..', '2017-05-11 13:00:59', 0, '2017-05-09 13:23:28', '2017-05-09 13:57:03', 4);

-- --------------------------------------------------------

--
-- Table structure for table `order_statuses`
--

CREATE TABLE `order_statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `order_statuses`
--

INSERT INTO `order_statuses` (`id`, `name`, `color`) VALUES
(1, 'Not Assign', '#ff6565'),
(2, 'Assigned', '#0085b6'),
(3, 'Accepted', '#ffd200'),
(4, 'Rejected', '#7f7fff'),
(5, 'On Progress', '#05f6ff'),
(6, 'Completed', '#00d814'),
(7, 'Failed', '#ab0065');

-- --------------------------------------------------------

--
-- Table structure for table `restricted_controllers`
--

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
  `default_lat` varchar(255) DEFAULT NULL,
  `default_lng` varchar(255) DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `cms_url`, `cms_title`, `cms_description`, `cms_keywords`, `cms_author`, `cms_app_name`, `cms_logo_url`, `path_content`, `path_webroot`, `map_browser_api_key`, `default_lat`, `default_lng`, `modified`) VALUES
(1, 'http://192.168.1.73/indorack/cms/', 'INDORACK CMS', 'INDORACK CMS', 'INDORACK CMS', 'MSolving', 'INDORACK CMS 1.0', NULL, 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/contents/', 'E:/abyfolder/xampp/htdocs/indorack/cms/app/webroot/', 'AIzaSyCgC44R6iu0UnzCuF9NfQ33LznETv3mZSA', '-6.175414', '106.827122', '2017-02-28 12:10:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

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
  `is_admin` smallint(1) NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT '',
  `password` varchar(255) DEFAULT NULL,
  `is_verify` smallint(1) NOT NULL DEFAULT '0',
  `verify_date` datetime DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `last_login_cms` datetime DEFAULT NULL,
  `last_login_web` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `aro_id`, `province_id`, `city_id`, `address`, `phone1`, `phone2`, `latitude`, `longitude`, `is_admin`, `email`, `firstname`, `lastname`, `password`, `is_verify`, `verify_date`, `status`, `created`, `modified`, `last_login_cms`, `last_login_web`) VALUES
(1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'developer@indorack.co.id', 'Developer', 'Admin', 'qpOVrZaYsJk=', 1, '2017-03-06 10:28:00', 1, NULL, NULL, '2017-05-08 17:42:33', NULL),
(2, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'superadmin@indorack.co.id', 'Super', 'Admin', 'qpOVrZaYsJk=', 1, '2017-03-06 10:28:00', 1, '2017-03-06 12:11:34', '2017-05-05 11:33:31', '2017-05-12 11:46:37', NULL),
(3, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'admin@indorack.co.id', 'Regular', 'Admin', 'qpOVrZaYsJk=', 1, '2017-03-08 00:00:00', 1, '2017-03-06 12:11:34', '2017-03-06 12:11:34', '2017-03-13 16:25:02', NULL),
(6, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'admin.gudang@indorack.co.id', 'Ahmad', 'Zaini Azay', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-05 16:41:34', '2017-05-12 10:38:41', NULL, NULL),
(7, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'teknisi@indorack.co.id', 'Rofi', 'Mujrofi Ahmad', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-05 16:44:55', '2017-05-12 10:39:33', NULL, NULL),
(10, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'abyfajar@gmail.com', 'Aby', 'Fajar', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-09 11:32:00', '2017-05-09 11:32:00', NULL, NULL),
(11, 7, NULL, NULL, 'Kramat Jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia', '32234234234234', NULL, '-6.275359999999999', '106.87092495000002', 0, 'thomas.ardianto.saputro@gmail.com', 'Thomas', 'Ardianto', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-09 11:42:05', '2017-05-09 11:42:05', NULL, NULL),
(12, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'mama.slamet@indorack.co.id', 'Maman', 'Selamet', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:46:11', '2017-05-12 11:46:11', NULL, NULL),
(13, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'putra.praditya@indorack.co.id', 'Putra', 'Praditya', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:46:44', '2017-05-12 11:46:44', NULL, NULL),
(14, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'bagus.aditha@indorack.co.id', 'Bagus', 'Adhita', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:47:11', '2017-05-12 11:47:11', NULL, NULL),
(15, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'bambang.wicaksono@indorack.co.id', 'Bambang', 'Wicaksono', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:47:40', '2017-05-12 11:47:40', NULL, NULL),
(16, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'syaid.jamal@indorack.co.id', 'Syaid', 'Jamal Handayani', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:48:21', '2017-05-12 11:51:46', NULL, NULL),
(17, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'cosmas.siahaan@indorack.co.id', 'Cosmas', 'Siahaan', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:48:59', '2017-05-12 11:48:59', NULL, NULL),
(18, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'tulus.tobing@indorack.co.id', 'Tulus', 'Tobing', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:49:41', '2017-05-12 11:49:41', NULL, NULL),
(19, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'bayu.putro@indorack.co.id', 'Bayu', 'Putro', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:50:10', '2017-05-12 11:50:10', NULL, NULL),
(20, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'martin.adiyono@indorack.co.id', 'Martin', 'Adiyono', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:50:36', '2017-05-12 11:50:36', NULL, NULL),
(21, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'resi.kusumo@indorack.co.id', 'Resi', 'Kusumo', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:51:07', '2017-05-12 11:51:07', NULL, NULL),
(22, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'mahfud.azhary@indorack.co.id', 'Mahfud', 'Azhary', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:51:42', '2017-05-12 11:51:42', NULL, NULL),
(23, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'syahrul.ramadhan@indorack.co.id', 'Syahrul', 'Ramadhan', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:52:14', '2017-05-12 11:52:14', NULL, NULL),
(24, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'anthony.aang@indorack.co.id', 'Anthony', 'Aang', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:53:24', '2017-05-12 11:53:24', NULL, NULL),
(25, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'prio.triasmoro@indorack.co.id', 'Prio', 'Triasmoro', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:54:02', '2017-05-12 11:54:02', NULL, NULL),
(26, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'andre@indorack.co.id', 'Andre', 'Wibowo Harlim', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:54:32', '2017-05-12 11:54:32', NULL, NULL),
(27, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'iwan.setiawan@indorack.co.id', 'Iwan', 'Setiawan', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:55:14', '2017-05-12 11:55:14', NULL, NULL),
(28, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'beledug.bantolo@indorack.co.id', 'Beledug', 'Bantolo', 'qpOVrZaYsJk=', 0, NULL, 1, '2017-05-12 11:55:55', '2017-05-12 11:55:55', NULL, NULL);

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
-- Indexes for table `i18n`
--
ALTER TABLE `i18n`
  ADD PRIMARY KEY (`id`),
  ADD KEY `locale` (`locale`),
  ADD KEY `model` (`model`),
  ADD KEY `row_id` (`foreign_key`),
  ADD KEY `field` (`field`);

--
-- Indexes for table `langs`
--
ALTER TABLE `langs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_statuses`
--
ALTER TABLE `order_statuses`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acos`
--
ALTER TABLE `acos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
--
-- AUTO_INCREMENT for table `aros`
--
ALTER TABLE `aros`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `aros_acos`
--
ALTER TABLE `aros_acos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;
--
-- AUTO_INCREMENT for table `cms_menus`
--
ALTER TABLE `cms_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
--
-- AUTO_INCREMENT for table `cms_menu_translations`
--
ALTER TABLE `cms_menu_translations`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;
--
-- AUTO_INCREMENT for table `contents`
--
ALTER TABLE `contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `i18n`
--
ALTER TABLE `i18n`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `langs`
--
ALTER TABLE `langs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `order_statuses`
--
ALTER TABLE `order_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
