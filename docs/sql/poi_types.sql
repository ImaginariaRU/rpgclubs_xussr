-- типы объектов POI с иконками
CREATE TABLE `poi_types` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `type` varchar(100) DEFAULT 'club' COMMENT 'тип POI, a-z',
    `comment` varchar(250) DEFAULT '' COMMENT 'комментарий',
    `icon` varchar(100) DEFAULT 'fa-cubes' COMMENT 'FA-класс иконки',
    `marker_color` varchar(100) DEFAULT '#00a9ce' COMMENT 'цвет FA-маркера',
    `marker_offset_x` tinyint(4) DEFAULT 0 COMMENT 'смещение иконки по X',
    `marker_offset_y` tinyint(4) DEFAULT 0 COMMENT 'смещение иконки по Y',
    `group_owner` int(11) DEFAULT 0 COMMENT 'владелец группы объектов',
    PRIMARY KEY (`id`),
    KEY `poi_types_type_IDX` (`type`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
