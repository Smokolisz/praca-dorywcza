-- Usunięcie istniejącej tabeli notifications, jeśli istnieje
DROP TABLE IF EXISTS `notifications`;

-- Utworzenie tabeli notifications
CREATE TABLE `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL, -- ID użytkownika, który otrzyma powiadomienie
    `type` ENUM('new_review', 'new_message', 'new_favourite_listing', 'new_negotiation', 'accepted_negotiation', 'rejected_negotiation', 'contract_accepted') NOT NULL, -- Typ powiadomienia
    `content` TEXT NOT NULL, -- Treść powiadomienia
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Data utworzenia powiadomienia
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dodanie klucza obcego dla user_id
ALTER TABLE `notifications`
ADD CONSTRAINT `fk_notifications_users`
FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE notifications ADD COLUMN listing_id INT DEFAULT NULL;

-- Dodanie klucza obcego, jeśli tabela `listings` istnieje
ALTER TABLE notifications
ADD CONSTRAINT fk_notifications_listings
FOREIGN KEY (listing_id) REFERENCES listings(id)
ON DELETE CASCADE ON UPDATE CASCADE;
