-- ====================================================================
-- digiwish.sql — run this in phpMyAdmin (or `mysql -u root -p < digiwish.sql`)
-- to create the database and tables this project needs.
-- ====================================================================





-- --------------------------------------------------------------------
-- users : one row per registered account (signup.html -> signup.php)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  fullname   VARCHAR(120)  NOT NULL,
  username   VARCHAR(60)   NOT NULL UNIQUE,
  gmail      VARCHAR(120)  NOT NULL UNIQUE,
  password   VARCHAR(255)  NOT NULL,          -- stores a password_hash(), never plain text
  created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------------------
-- orders : one row per wish order (dashboard.php -> submit_order.php)
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  user_id         INT           NOT NULL,
  festival        VARCHAR(60),                -- e.g. Birthday, Diwali, Pongal...
  recipient_name  VARCHAR(120)  NOT NULL,      -- who the wish is FOR
  wish_type       VARCHAR(120),
  dob             DATE          NULL,
  content         TEXT,
  extra           VARCHAR(255),
  need_image      ENUM('Yes','No') DEFAULT 'No',
  need_video      ENUM('Yes','No') DEFAULT 'No',
  need_audio      ENUM('Yes','No') DEFAULT 'No',
  song_details    TEXT,
  mobile          VARCHAR(15)   NOT NULL,
  email           VARCHAR(120)  NOT NULL,
  order_date      DATE          NOT NULL,
  delivery_date   DATE          NOT NULL,
  status          ENUM('Pending','In Progress','Delivered') DEFAULT 'Pending',
  created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
