
CREATE TABLE log (
    `id` INT(11) UNSIGNED auto_increment,
    `ip` VARCHAR(255),
    `key` VARCHAR(255),
    `level` VARCHAR(5),
    `message` TEXT,
    `date_time` DATETIME,
    `session_id` VARCHAR(255),
    `execution_time` VARCHAR(11),
    PRIMARY KEY(id)
);

