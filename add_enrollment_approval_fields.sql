-- Add enrollment approval fields to enrollments table
-- Run this SQL script if migration doesn't work

-- Add status column
ALTER TABLE `enrollments` 
ADD COLUMN `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' AFTER `enrollment_date`;

-- Add rejection_reason column
ALTER TABLE `enrollments` 
ADD COLUMN `rejection_reason` TEXT NULL AFTER `status`;

-- Add approved_at column
ALTER TABLE `enrollments` 
ADD COLUMN `approved_at` DATETIME NULL AFTER `rejection_reason`;

-- Add rejected_at column
ALTER TABLE `enrollments` 
ADD COLUMN `rejected_at` DATETIME NULL AFTER `approved_at`;

-- Update existing enrollments to 'approved' status
UPDATE `enrollments` SET `status` = 'approved' WHERE `status` IS NULL OR `status` = '';

