-- типы объектов POI с иконками
CREATE TABLE poi_types (
    `id` INT auto_increment NOT NULL,
    `type` varchar(100) NULL COMMENT 'тип POI, a-z',
    `comment` varchar(250) NULL COMMENT 'комментарий',
    `icon` varchar(100) DEFAULT 'fa-cubes' NULL COMMENT 'FA-класс иконки',
    `marker_color` varchar(100) DEFAULT '#00a9ce' NULL COMMENT 'цвет FA-маркера',
    `marker_offset_x` TINYINT DEFAULT 0 NULL COMMENT 'смещение иконки по X',
    `marker_offset_y` TINYINT DEFAULT 0 NULL COMMENT 'смещение иконки по Y',
    `group_owner` INT DEFAULT 0 NULL COMMENT 'владелец группы объектов',
    PRIMARY KEY (id)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8mb4
    COLLATE=utf8mb4_ru_0900_ai_ci;

CREATE INDEX poi_types_type_IDX USING HASH ON poi_types (`type`);
