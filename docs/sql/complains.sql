CREATE TABLE complains (
    id INT auto_increment NOT NULL,
    ipv4 varchar(100) NULL COMMENT 'ipv4 string',
    content TEXT NULL COMMENT 'текст комплейна',
    email varchar(250) NULL COMMENT 'емейл отправителя',
    sender varchar(100) NULL COMMENT 'имя отправителя',
    id_poi INT DEFAULT 0 NULL COMMENT 'id клуба, на который подается комплейн',
    CONSTRAINT complains_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;
