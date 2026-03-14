-- ============================================
-- SCADA IoT Home Air Quality System
-- Database Schema for iotdash_db
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+08:00";

-- -------------------------------------------
-- Table: tbl_scada_users
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_scada_users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) DEFAULT NULL,
  `role` ENUM('admin','user') NOT NULL DEFAULT 'user',
  `status` ENUM('active','inactive','locked') NOT NULL DEFAULT 'active',
  `last_login` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------
-- Table: tbl_scada_devices
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_scada_devices` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `device_key` VARCHAR(64) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `location` VARCHAR(200) DEFAULT NULL,
  `type` VARCHAR(50) NOT NULL DEFAULT 'air_quality_monitor',
  `status` ENUM('online','offline','maintenance') NOT NULL DEFAULT 'offline',
  `last_seen` DATETIME DEFAULT NULL,
  `firmware_version` VARCHAR(20) DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_device_key` (`device_key`),
  KEY `idx_status` (`status`),
  KEY `fk_device_creator` (`created_by`),
  CONSTRAINT `fk_device_creator` FOREIGN KEY (`created_by`) REFERENCES `tbl_scada_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------
-- Table: tbl_scada_sensors
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_scada_sensors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `device_id` INT(11) NOT NULL,
  `sensor_type` ENUM('temperature','humidity','co2','pm25') NOT NULL,
  `unit` VARCHAR(20) NOT NULL,
  `min_range` DECIMAL(10,2) DEFAULT NULL,
  `max_range` DECIMAL(10,2) DEFAULT NULL,
  `calibration_offset` DECIMAL(10,4) DEFAULT 0.0000,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_sensor_device` (`device_id`),
  UNIQUE KEY `uk_device_sensor` (`device_id`, `sensor_type`),
  CONSTRAINT `fk_sensor_device` FOREIGN KEY (`device_id`) REFERENCES `tbl_scada_devices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------
-- Table: tbl_scada_readings
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_scada_readings` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `device_id` INT(11) NOT NULL,
  `sensor_type` ENUM('temperature','humidity','co2','pm25') NOT NULL,
  `value` DECIMAL(10,2) NOT NULL,
  `recorded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_device_time` (`device_id`, `recorded_at`),
  KEY `idx_sensor_time` (`sensor_type`, `recorded_at`),
  KEY `idx_recorded` (`recorded_at`),
  CONSTRAINT `fk_reading_device` FOREIGN KEY (`device_id`) REFERENCES `tbl_scada_devices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------
-- Table: tbl_scada_alarms
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_scada_alarms` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `device_id` INT(11) DEFAULT NULL,
  `sensor_type` ENUM('temperature','humidity','co2','pm25') NOT NULL,
  `condition_type` ENUM('above','below','equal') NOT NULL DEFAULT 'above',
  `threshold` DECIMAL(10,2) NOT NULL,
  `severity` ENUM('info','warning','critical') NOT NULL DEFAULT 'warning',
  `message` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `cooldown_minutes` INT(11) NOT NULL DEFAULT 5,
  `last_triggered` DATETIME DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active_sensor` (`is_active`, `sensor_type`),
  KEY `fk_alarm_device` (`device_id`),
  CONSTRAINT `fk_alarm_device` FOREIGN KEY (`device_id`) REFERENCES `tbl_scada_devices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------
-- Table: tbl_scada_alarm_events
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_scada_alarm_events` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `alarm_id` INT(11) NOT NULL,
  `device_id` INT(11) NOT NULL,
  `sensor_type` ENUM('temperature','humidity','co2','pm25') NOT NULL,
  `value` DECIMAL(10,2) NOT NULL,
  `severity` ENUM('info','warning','critical') NOT NULL,
  `message` VARCHAR(255) DEFAULT NULL,
  `triggered_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `acknowledged` TINYINT(1) NOT NULL DEFAULT 0,
  `acknowledged_by` INT(11) DEFAULT NULL,
  `acknowledged_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_unacknowledged` (`acknowledged`, `triggered_at`),
  KEY `idx_device_events` (`device_id`, `triggered_at`),
  KEY `fk_event_alarm` (`alarm_id`),
  CONSTRAINT `fk_event_alarm` FOREIGN KEY (`alarm_id`) REFERENCES `tbl_scada_alarms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_device` FOREIGN KEY (`device_id`) REFERENCES `tbl_scada_devices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------
-- Table: tbl_scada_commands
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_scada_commands` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `device_id` INT(11) NOT NULL,
  `command_type` VARCHAR(50) NOT NULL,
  `parameters` TEXT DEFAULT NULL,
  `status` ENUM('pending','sent','acknowledged','failed') NOT NULL DEFAULT 'pending',
  `response` TEXT DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `executed_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_device_pending` (`device_id`, `status`),
  CONSTRAINT `fk_command_device` FOREIGN KEY (`device_id`) REFERENCES `tbl_scada_devices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------
-- Table: tbl_scada_settings
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_scada_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(50) NOT NULL,
  `setting_value` VARCHAR(255) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------
-- Table: tbl_scada_audit_log
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_scada_audit_log` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `action` VARCHAR(50) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_action` (`user_id`, `action`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================
-- SEED DATA
-- ============================================

-- Default admin user (password: admin123)
INSERT INTO `tbl_scada_users` (`username`, `email`, `password`, `full_name`, `role`, `status`) VALUES
('admin', 'admin@scada.local', '$2y$10$iycpltRpV2mikfZtEJrEhOakG36JqOGEdE//PIdD8nU.JZqjcc2uG', 'System Administrator', 'admin', 'active');

-- Default system settings
INSERT INTO `tbl_scada_settings` (`setting_key`, `setting_value`, `description`) VALUES
('polling_interval', '5', 'Dashboard polling interval in seconds'),
('data_retention_days', '90', 'Number of days to keep sensor readings'),
('device_timeout_minutes', '5', 'Minutes before device is marked offline'),
('system_name', 'iBreathe SCADA', 'System display name'),
('system_timezone', 'Asia/Manila', 'System timezone'),
('alarm_email_enabled', '0', 'Enable email notifications for alarms'),
('alarm_email_recipient', '', 'Email address for alarm notifications'),
('temp_unit', 'celsius', 'Temperature display unit (celsius/fahrenheit)');

-- Default alarm rules
INSERT INTO `tbl_scada_alarms` (`name`, `device_id`, `sensor_type`, `condition_type`, `threshold`, `severity`, `message`, `is_active`, `created_by`) VALUES
('High Temperature', NULL, 'temperature', 'above', 35.00, 'warning', 'Temperature exceeds 35°C', 1, 1),
('Low Temperature', NULL, 'temperature', 'below', 16.00, 'warning', 'Temperature below 16°C', 1, 1),
('High Humidity', NULL, 'humidity', 'above', 75.00, 'warning', 'Humidity exceeds 75%', 1, 1),
('Low Humidity', NULL, 'humidity', 'below', 25.00, 'info', 'Humidity below 25%', 1, 1),
('High CO2', NULL, 'co2', 'above', 1000.00, 'warning', 'CO2 level exceeds 1000pm2.5 - ventilate room', 1, 1),
('Critical CO2', NULL, 'co2', 'above', 2000.00, 'critical', 'CO2 level exceeds 2000pm2.5 - immediate ventilation needed', 1, 1),
('Unhealthy PM2.5', NULL, 'pm25', 'above', 35.40, 'warning', 'PM2.5 exceeds moderate level', 1, 1),
('Hazardous PM2.5', NULL, 'pm25', 'above', 150.40, 'critical', 'PM2.5 at hazardous level', 1, 1);

COMMIT;
