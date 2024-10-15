CREATE TABLE `swift-jobs`.`users` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(60) NOT NULL,
    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_removed` DATETIME DEFAULT NULL,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `last_login_date` DATETIME DEFAULT NULL,
    `active` BOOLEAN NOT NULL,
    `role` ENUM('user', 'admin') NOT NULL,
    `verified` BOOLEAN NOT NULL DEFAULT FALSE,
    `password_reset_token` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE `user_email` (`email`)
) ENGINE = InnoDB;
