CREATE TABLE `swift-jobs`.`listings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `job_type` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `payment_type` ENUM('godzinowa', 'za_cala_prace') NOT NULL,
    `payment` DECIMAL(10, 2) NOT NULL,
    `estimated_time` INT DEFAULT NULL,
    `address` VARCHAR(255) DEFAULT NULL,
    `images` JSON DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;
