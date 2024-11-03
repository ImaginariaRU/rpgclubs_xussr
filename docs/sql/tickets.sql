CREATE TABLE `tickets` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ipv4` varchar(100) DEFAULT '' COMMENT 'ipv4 string',
    `content` text DEFAULT NULL COMMENT 'текст комплейна',
    `email` varchar(250) DEFAULT '' COMMENT 'емейл отправителя',
    `sender` varchar(100) DEFAULT '' COMMENT 'имя отправителя',
    `id_poi` int(11) DEFAULT 0 COMMENT 'id клуба, на который подается комплейн',
    `status` varchar(30) DEFAULT 'new' COMMENT 'статус тикета new, fixed, rejected (не енум а строка)',
    `date_added` datetime DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


