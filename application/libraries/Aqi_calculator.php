<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Aqi_calculator {

    // EPA AQI breakpoints for PM2.5 (24-hour average)
    private $breakpoints = array(
        array('pm_lo' => 0.0,   'pm_hi' => 12.0,   'aqi_lo' => 0,   'aqi_hi' => 50),
        array('pm_lo' => 12.1,  'pm_hi' => 35.4,   'aqi_lo' => 51,  'aqi_hi' => 100),
        array('pm_lo' => 35.5,  'pm_hi' => 55.4,   'aqi_lo' => 101, 'aqi_hi' => 150),
        array('pm_lo' => 55.5,  'pm_hi' => 150.4,  'aqi_lo' => 151, 'aqi_hi' => 200),
        array('pm_lo' => 150.5, 'pm_hi' => 250.4,  'aqi_lo' => 201, 'aqi_hi' => 300),
        array('pm_lo' => 250.5, 'pm_hi' => 350.4,  'aqi_lo' => 301, 'aqi_hi' => 400),
        array('pm_lo' => 350.5, 'pm_hi' => 500.4,  'aqi_lo' => 401, 'aqi_hi' => 500)
    );

    public function calculate_aqi($pm25) {
        $pm25 = round($pm25, 1);

        if ($pm25 < 0) return 0;
        if ($pm25 > 500.4) return 500;

        foreach ($this->breakpoints as $bp) {
            if ($pm25 >= $bp['pm_lo'] && $pm25 <= $bp['pm_hi']) {
                $aqi = (($bp['aqi_hi'] - $bp['aqi_lo']) / ($bp['pm_hi'] - $bp['pm_lo']))
                       * ($pm25 - $bp['pm_lo']) + $bp['aqi_lo'];
                return round($aqi);
            }
        }

        return 500;
    }
    
    // =========================================================
    // Gas Score (MQ135 ppm → 0–500)
    // =========================================================
    private function gas_score($ppm) {
        $ppm = max(0, $ppm);
        if ($ppm <= 400)  return ($ppm / 400) * 50;
        if ($ppm <= 700)  return 50  + (($ppm - 400)  / 300)  * 50;
        if ($ppm <= 1000) return 100 + (($ppm - 700)  / 300)  * 50;
        if ($ppm <= 2000) return 150 + (($ppm - 1000) / 1000) * 50;
        if ($ppm <= 5000) return 200 + (($ppm - 2000) / 3000) * 100;
        return min(500, 300 + (($ppm - 5000) / 5000) * 200);
    }

    // =========================================================
    // Temperature Score (°C → 0–500)
    // Ideal range: 18°C – 24°C = score 0 (best)
    // Sobrang init o sobrang lamig = mas mataas score
    // =========================================================
    private function temp_score($temp_c) {
        if ($temp_c >= 18 && $temp_c <= 24) return 0;    // Ideal
        if ($temp_c >= 25 && $temp_c <= 30) return 50;   // Comfortable
        if ($temp_c >= 31 && $temp_c <= 35) return 100;  // Warm
        if ($temp_c >= 36 && $temp_c <= 40) return 150;  // Hot
        if ($temp_c >= 41 && $temp_c <= 45) return 200;  // Very Hot
        if ($temp_c > 45)                   return 300;  // Extreme Heat
        // Below 18°C (cold side)
        if ($temp_c >= 10 && $temp_c < 18)  return 50;   // Cool
        if ($temp_c >= 0  && $temp_c < 10)  return 100;  // Cold
        return 200;                                        // Extreme Cold
    }

    // =========================================================
    // Humidity Score (%RH → 0–500)
    // Ideal range: 40% – 60% = score 0 (best)
    // =========================================================
    private function humidity_score($humidity) {
        if ($humidity >= 40 && $humidity <= 60) return 0;    // Ideal
        if ($humidity >= 61 && $humidity <= 70) return 50;   // Slightly humid
        if ($humidity >= 71 && $humidity <= 80) return 100;  // Humid
        if ($humidity >= 81 && $humidity <= 90) return 150;  // Very Humid
        if ($humidity > 90)                     return 200;  // Extremely Humid
        // Below 40% (dry side)
        if ($humidity >= 30 && $humidity < 40)  return 50;   // Slightly Dry
        if ($humidity >= 20 && $humidity < 30)  return 100;  // Dry
        return 200;                                            // Extremely Dry
    }

    // =========================================================
    // MAIN: Composite AQI
    // Formula: (Gas×0.50) + (Temp×0.30) + (Humidity×0.20)
    // =========================================================
    public function calculate_index($ppm, $temp_c, $humidity) {
        $g = $this->gas_score($ppm);
        $t = $this->temp_score($temp_c);
        $h = $this->humidity_score($humidity);

        $composite = ($g * 0.50) + ($t * 0.30) + ($h * 0.20);

        return round($composite);
    }
    
    

    public function get_aqi_label($aqi) {
        if ($aqi <= 50)  return 'Good';
        if ($aqi <= 100) return 'Moderate';
        if ($aqi <= 150) return 'Unhealthy for Sensitive Groups';
        if ($aqi <= 200) return 'Unhealthy';
        if ($aqi <= 300) return 'Very Unhealthy';
        return 'Hazardous';
    }

    public function get_aqi_color($aqi) {
        if ($aqi <= 50)  return '#00e400'; // Green
        if ($aqi <= 100) return '#ffff00'; // Yellow
        if ($aqi <= 150) return '#ff7e00'; // Orange
        if ($aqi <= 200) return '#ff0000'; // Red
        if ($aqi <= 300) return '#8f3f97'; // Purple
        return '#7e0023';                  // Maroon
    }

    public function get_aqi_text_color($aqi) {
        if ($aqi <= 50)  return '#000000';
        if ($aqi <= 100) return '#000000';
        return '#ffffff';
    }
}
