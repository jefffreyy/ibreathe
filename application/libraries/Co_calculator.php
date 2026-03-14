<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * iBreathe SCADA - CO (Carbon Monoxide) Calculator
 *
 * Computes an estimated CO value from MQ-135 sensor data (PM2.5 raw),
 * corrected for temperature and humidity.
 *
 * Formula:
 *   1. MQcorr = MQraw × (1 + kT·(T−25)) × (1 + kH·(RH−50))
 *   2. COest  = a · MQcorr + b
 */
class Co_calculator {

    // Temperature correction coefficient
    private $kT = 0.02;

    // Humidity correction coefficient
    private $kH = 0.015;

    // Linear conversion coefficients (MQ-135 sensitivity curve approximation)
    private $a = 0.12;
    private $b = 0.5;

    /**
     * Calculate estimated CO (ppm) from MQ-135 raw, temperature, humidity
     *
     * @param float $mq_raw   MQ-135 raw reading (PM2.5 µg/m³ value)
     * @param float $temp_c   Temperature in °C
     * @param float $humidity  Relative humidity %
     * @return float  Estimated CO in ppm
     */
    public function calculate_co($mq_raw, $temp_c, $humidity) {
        // Step 1: Temperature and humidity correction
        $mq_corr = $mq_raw * (1 + $this->kT * ($temp_c - 25)) * (1 + $this->kH * ($humidity - 50));

        // Step 2: Convert to CO estimate
        $co_est = $this->a * $mq_corr + $this->b;

        // Clamp to reasonable range (0-200 ppm)
        $co_est = max(0, min(200, $co_est));

        return round($co_est, 2);
    }

    /**
     * Get CO safety level label
     */
    public function get_co_label($co_ppm) {
        if ($co_ppm <= 9)   return 'Good';
        if ($co_ppm <= 35)  return 'Moderate';
        if ($co_ppm <= 100) return 'Unhealthy';
        if ($co_ppm <= 150) return 'Very Unhealthy';
        return 'Hazardous';
    }

    /**
     * Get CO level color
     */
    public function get_co_color($co_ppm) {
        if ($co_ppm <= 9)   return '#22c55e'; // Green
        if ($co_ppm <= 35)  return '#eab308'; // Yellow
        if ($co_ppm <= 100) return '#f97316'; // Orange
        if ($co_ppm <= 150) return '#ef4444'; // Red
        return '#7e0023';                     // Maroon
    }
}
