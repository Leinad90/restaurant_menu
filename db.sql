CREATE DATABASE `restaurant_menu` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `restaurant_menu`;


CREATE TABLE `e_mails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `e_mail` varchar(255) DEFAULT NULL COMMENT 'Mail address (null means deleted)',
  `hash` varchar(255) NOT NULL COMMENT 'Hash of mail adress',
  `last_send_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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

CREATE TABLE `email_restaurants` (
  `e_mail` int(11) NOT NULL,
  `restaurant` int(11) NOT NULL,
  UNIQUE KEY `e_mail_restaurant` (`e_mail`,`restaurant`),
  KEY `restaurant` (`restaurant`),
  CONSTRAINT `email_restaurants_email` FOREIGN KEY (`e_mail`) REFERENCES `e_mails` (`id`) ON DELETE CASCADE,
  CONSTRAINT `email_restaurants_restaurant` FOREIGN KEY (`restaurant`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Witch user wants witch daily menu';