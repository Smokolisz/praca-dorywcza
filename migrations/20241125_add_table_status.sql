-- Dodaj status ogloszenia
ALTER TABLE `swift-jobs`.`listings`
    ADD COLUMN `status` ENUM(
        'open',
        'closed'
    ) NOT NULL DEFAULT 'open';