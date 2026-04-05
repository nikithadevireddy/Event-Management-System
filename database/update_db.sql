-- Add event status field to events table
ALTER TABLE `events` ADD COLUMN `event_status` INT(11) NOT NULL DEFAULT 0 COMMENT '0=Upcoming, 1=Ongoing, 2=Completed' AFTER `schedule`;

-- Add user registration table for public users
CREATE TABLE IF NOT EXISTS `registered_users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(200) NOT NULL,
  `email` VARCHAR(200) NOT NULL UNIQUE,
  `password` VARCHAR(200) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `address` TEXT,
  `status` INT(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive',
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add booking table for event bookings
CREATE TABLE IF NOT EXISTS `event_bookings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `event_id` INT(11) NOT NULL,
  `booking_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` INT(1) NOT NULL DEFAULT 0 COMMENT '0=Pending, 1=Confirmed, 2=Cancelled',
  `notes` TEXT,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `registered_users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Update existing events with status based on schedule
UPDATE events SET event_status = 0 WHERE schedule > NOW();
UPDATE events SET event_status = 1 WHERE schedule <= NOW() AND DATE_ADD(schedule, INTERVAL 4 HOUR) > NOW();
UPDATE events SET event_status = 2 WHERE DATE_ADD(schedule, INTERVAL 4 HOUR) <= NOW();
