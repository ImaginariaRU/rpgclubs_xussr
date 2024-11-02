CREATE TABLE `visitlog` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `dayvisit` date DEFAULT NULL COMMENT 'дата',
    `ipv4` int(10) unsigned DEFAULT NULL COMMENT 'ipv4 long',
    `hits` int(11) DEFAULT NULL COMMENT 'hits с айпишника в этот день',
    `id_poi` int(11) DEFAULT 0 COMMENT 'POI показа, 0 для главной страницы',
    PRIMARY KEY (`id`),
    UNIQUE KEY `date+ipv4` (`dayvisit`,`ipv4`),
    KEY `ipv4` (`ipv4`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;