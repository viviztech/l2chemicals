ALTER TABLE users ADD COLUMN username VARCHAR(80) NULL AFTER name;
UPDATE users SET username=CONCAT('admin',id) WHERE username IS NULL OR username='';
ALTER TABLE users MODIFY username VARCHAR(80) NOT NULL;
ALTER TABLE users ADD UNIQUE KEY uq_users_username (username);
