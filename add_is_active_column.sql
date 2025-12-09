-- SQL script to add is_active column to users table
-- Run this in phpMyAdmin or MySQL command line if migration fails

ALTER TABLE `users` 
ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = active, 0 = deactivated' 
AFTER `role`;

-- Update existing users to be active by default (optional, but recommended)
UPDATE `users` SET `is_active` = 1 WHERE `is_active` IS NULL OR `is_active` = 0;

