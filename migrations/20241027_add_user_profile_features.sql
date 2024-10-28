-- Tworzenie tabeli notifications
CREATE TABLE `swift-jobs`.`notifications` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- Tworzenie tabeli activity_log
CREATE TABLE `swift-jobs`.`activity_log` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `action` VARCHAR(255) NOT NULL,
    `action_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- Dodanie kolumny profile_picture do tabeli users
ALTER TABLE `swift-jobs`.`users`
ADD COLUMN `profile_picture` VARCHAR(255) DEFAULT NULL;
