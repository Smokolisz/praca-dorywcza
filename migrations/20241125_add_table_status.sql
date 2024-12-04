--Dodaj status ogloszenia
ALTER TABLE `SWIFT-JOBS`.`LISTINGS`
    ADD COLUMN `STATUS` ENUM(
        'open',
        'closed'
    ) NOT NULL DEFAULT 'open';