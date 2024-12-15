CREATE TABLE `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `reviewer_id` INT NOT NULL,
    `reviewed_user_id` INT NOT NULL,
    `listing_id` INT NOT NULL,
    `negotiation_id` INT NOT NULL,
    `rating` INT NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
    `pros` TEXT DEFAULT NULL,
    `cons` TEXT DEFAULT NULL,
    `comment` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`reviewer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`reviewed_user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`listing_id`) REFERENCES `listings`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`negotiation_id`) REFERENCES `negotiations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE `reviews`
ADD COLUMN `photos` TEXT DEFAULT NULL;
