/**
 * Author:  Daniel Hejduk <daniel.hejduk at gmail.com>
 * Created: 27. 9. 2021
 */

-- Adminer 4.8.1 MySQL 5.5.5-10.5.11-MariaDB-1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;

CREATE DATABASE `restaurant_menu` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `restaurant_menu`;

CREATE TABLE `email_restaurants` (
  `e_mail` int(11) NOT NULL,
  `restaurant` int(11) NOT NULL,
  UNIQUE KEY `e_mail_restaurant` (`e_mail`,`restaurant`),
  KEY `restaurant` (`restaurant`),
  CONSTRAINT `email_restaurants_ibfk_1` FOREIGN KEY (`e_mail`) REFERENCES `e_mails` (`id`) ON DELETE CASCADE,
  CONSTRAINT `email_restaurants_ibfk_2` FOREIGN KEY (`restaurant`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Witch user wants witch daily menu';


CREATE TABLE `e_mails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `e_mail` varchar(255) DEFAULT NULL COMMENT 'Mail address (null means deleted)',
  `hash` varchar(255) NOT NULL COMMENT 'Hash of mail adress',
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='List of mail to sent daily menu';


CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api_id` int(11) NOT NULL COMMENT 'id in api',
  `name` varchar(255) NOT NULL COMMENT 'restaurant name',
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_id` (`api_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='List of restaurants';


-- 2021-09-27 17:13:44