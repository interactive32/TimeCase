-- --------------------------------------------------------
-- TimeCase v2.0 Database dump
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;


DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

INSERT INTO `categories` (`id`, `name`) VALUES
	(1, 'planning'),
	(2, 'developement'),
	(3, 'testing'),
	(4, 'communication'),
	(5, 'team management'),
	(6, 'design'),
	(7, 'integration'),
	(8, 'implementation');

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(1000) NOT NULL,
  `status_id` int(10) NOT NULL,
  `contact_person` varchar(1000) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `password` varchar(1000) NOT NULL,
  `allow_login` int(1) NOT NULL DEFAULT '0',
  `address` varchar(1000) NOT NULL,
  `location` varchar(1000) NOT NULL,
  `web` varchar(1000) NOT NULL,
  `tel` varchar(1000) NOT NULL,
  `tel2` varchar(1000) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `status_id` (`status_id`)
) DEFAULT CHARSET=utf8;

INSERT INTO `customers` VALUES (1,'Default Customer',2,'','admin','$2y$10$sJTr47FYQp4oax75bF5cbeIMxpX3ZSMbH2qq32xsKrRWYvZoH1gEG',0,'','','','','','');


DROP TABLE IF EXISTS `levels`;
CREATE TABLE IF NOT EXISTS `levels` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


INSERT INTO `levels` (`id`, `name`) VALUES
	(1, 'admin'),
	(2, 'project manager'),
	(4, 'user'),
	(8, 'customer'),
	(16, 'basic user');

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `customer_id` int(10) NOT NULL DEFAULT '0',
  `status_id` int(10) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `closed` timestamp NULL DEFAULT NULL,
  `deadline` timestamp NOT NULL DEFAULT '2020-01-01 00:00:01',
  `progress` int(10) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `status_id` (`status_id`)
) DEFAULT CHARSET=utf8;

INSERT INTO `projects` VALUES (1,'Project1',1,2,'2023-01-01 13:00:00','2023-02-03 13:00:00','2023-01-01 13:00:00',0,'');

DROP TABLE IF EXISTS `statuses`;
CREATE TABLE IF NOT EXISTS `statuses` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `description` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


INSERT INTO `statuses` (`id`, `description`) VALUES
	(1, 'important'),
	(2, 'active'),
	(3, 'closed');

DROP TABLE IF EXISTS `time_entries`;
CREATE TABLE IF NOT EXISTS `time_entries` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `category_id` int(10) NOT NULL,
  `start` timestamp NULL DEFAULT NULL,
  `end` timestamp NULL DEFAULT NULL,
  `description` text NOT NULL,
  `location` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`)
) DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `level_id` int(10) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(1000) NOT NULL,
  `password` varchar(1000) DEFAULT NULL,
  `details` text,
  `current_project` int(10) DEFAULT NULL,
  `current_category` int(10) DEFAULT NULL,
  `timer` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `level_id` (`level_id`)
) AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


INSERT INTO `users` (`id`, `username`, `level_id`, `full_name`, `email`, `password`, `details`, `current_project`, `current_category`, `timer`) VALUES
	(1, 'customer', 8, '(default customer user)', '', '', '(default customer user)', NULL, NULL, NULL),
	(2, 'admin', 1, 'administraor', '', '', '', 1, 6, NULL);

/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
