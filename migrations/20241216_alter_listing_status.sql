
ALTER TABLE `listings` ADD COLUMN `listing_status` ENUM('active', 'closed') DEFAULT 'active';
 
ALTER TABLE reviews DROP FOREIGN KEY reviews_ibfk_4;
ALTER TABLE reviews DROP COLUMN negotiation_id;
ALTER TABLE `reviews` DROP `photos`;