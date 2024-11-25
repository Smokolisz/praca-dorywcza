ALTER TABLE `SWIFT-JOBS`.`LISTINGS`
    ADD COLUMN `USER_ID` INT NOT NULL;

-- Dodaj klucz obcy do tabeli users
ALTER TABLE `SWIFT-JOBS`.`LISTINGS`
    ADD FOREIGN KEY (
        `USER_ID`
    )
        REFERENCES `USERS`(
            `ID`
        ) ON DELETE CASCADE;