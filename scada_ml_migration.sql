-- ============================================
-- iBreathe SCADA - ML Feature Migration
-- Smart Adaptive Thresholds & Forecasting
-- ============================================

-- Baseline statistics cache (per device/sensor/hour)
CREATE TABLE IF NOT EXISTS `tbl_scada_ml_baselines` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `device_id` INT(11) NOT NULL,
  `sensor_type` ENUM('temperature','humidity','co2','pm25') NOT NULL,
  `hour_of_day` TINYINT(2) NOT NULL COMMENT '0-23',
  `mean_value` DECIMAL(12,4) NOT NULL,
  `std_dev` DECIMAL(12,4) NOT NULL,
  `sample_count` INT(11) NOT NULL DEFAULT 0,
  `min_value` DECIMAL(10,2) DEFAULT NULL,
  `max_value` DECIMAL(10,2) DEFAULT NULL,
  `computed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_baseline` (`device_id`, `sensor_type`, `hour_of_day`),
  KEY `idx_computed` (`computed_at`),
  CONSTRAINT `fk_baseline_device` FOREIGN KEY (`device_id`)
    REFERENCES `tbl_scada_devices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Forecast predictions cache
CREATE TABLE IF NOT EXISTS `tbl_scada_ml_forecasts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `device_id` INT(11) NOT NULL,
  `sensor_type` ENUM('temperature','humidity','co2','pm25') NOT NULL,
  `horizon_minutes` INT(11) NOT NULL COMMENT '30, 60, or 120',
  `predicted_value` DECIMAL(10,2) NOT NULL,
  `confidence_lower` DECIMAL(10,2) NOT NULL,
  `confidence_upper` DECIMAL(10,2) NOT NULL,
  `trend_per_hour` DECIMAL(10,4) DEFAULT NULL,
  `computed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_forecast` (`device_id`, `sensor_type`, `horizon_minutes`),
  KEY `idx_forecast_computed` (`computed_at`),
  CONSTRAINT `fk_forecast_device` FOREIGN KEY (`device_id`)
    REFERENCES `tbl_scada_devices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
