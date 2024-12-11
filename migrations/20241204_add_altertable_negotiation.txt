ALTER TABLE `negotiations`
ADD COLUMN `completion_status` ENUM('pending', 'completed') DEFAULT 'pending';
