ALTER TABLE `swift-jobs`.`listings`
    ADD COLUMN `user_id` INT NOT NULL;

-- Dodaj klucz obcy do tabeli users
ALTER TABLE `swift-jobs`.`listings`
    ADD FOREIGN KEY (`user_id`)
        REFERENCES `users`(`id`) 
        ON DELETE CASCADE;
