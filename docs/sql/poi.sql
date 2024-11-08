CREATE TABLE `poi` (
     `id` int NOT NULL AUTO_INCREMENT COMMENT 'id',
     `id_owner` int DEFAULT 1,
     `id_approver` int DEFAULT 1,
     `is_public` int DEFAULT 0,
     `lat` decimal(10,7) DEFAULT '0.0000000',
     `lng` decimal(10,7) DEFAULT '0.0000000',
     `zoom` int DEFAULT 12,
     `title` varchar(250) DEFAULT '',
     `description` text,
     `address` text,
     `address_hint` TEXT NULL COMMENT 'хинт для адреса',
     `address_city` varchar(250) DEFAULT '',
     `banner_type` enum('h','v') DEFAULT 'h',
     `banner_url` varchar(255) DEFAULT '',
     `infobox_layout` enum('VK','Other') DEFAULT 'VK' COMMENT 'лэйаут инфобокса',
     `url_site` varchar(255) DEFAULT 'Основной сайт (обычно, ВК)',
     `dt_create` datetime DEFAULT CURRENT_TIMESTAMP,
     `dt_update` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
     `poi_type` varchar(100) DEFAULT '' COMMENT 'тип POI, технически - enum',
     `owner_email` varchar(255) DEFAULT '' COMMENT 'EMail владельца (подавшего заявку)',
     `owner_about` text COMMENT 'Owner о себе',
     `ipv4_add` int unsigned DEFAULT '0',
     `ipv4_update` int unsigned DEFAULT '0',
     PRIMARY KEY (`id`),
     KEY `poi_type` (`poi_type`) USING HASH,
     KEY `is_public` (`is_public`) USING BTREE,
     KEY `id_owner` (`id_owner`) USING BTREE,
     KEY `lat+lng` (`lat`,`lng`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE poi ADD contact_email varchar(100) NULL COMMENT 'EMail';
ALTER TABLE poi ADD contact_telegram varchar(100) NULL COMMENT 'telegram';
ALTER TABLE poi ADD contact_discord varchar(100) NULL COMMENT 'discord';
ALTER TABLE poi ADD contact_site varchar(100) NULL COMMENT 'сайт';
ALTER TABLE poi ADD contact_phone varchar(100) NULL COMMENT 'телефон';
