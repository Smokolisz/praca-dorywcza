CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,

) ENGINE=InnoDB;



ALTER TABLE listings
ADD COLUMN category_id INT DEFAULT NULL,
ADD CONSTRAINT fk_listings_category
    FOREIGN KEY (category_id) REFERENCES categories(id)
    ON DELETE SET NULL;