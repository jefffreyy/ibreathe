<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * iBreathe SCADA - AI Insights Engine
 *
 * Rule-based analytics engine that generates human-readable insights
 * from sensor data across all IoT devices.
 */
class Ai_insights {

    const GOOD     = 'good';
    const INFO     = 'info';
    const WARNING  = 'warning';
    const CRITICAL = 'critical';

    private $comfort = array(
        'temperature' => array('min' => 18, 'max' => 26, 'unit' => '°C', 'label' => 'Temperature'),
        'humidity'    => array('min' => 30, 'max' => 60, 'unit' => '%', 'label' => 'Humidity'),
        'co2'         => array('min' => 0,  'max' => 800, 'unit' => 'ppm', 'label' => 'CO₂'),
        'pm25'        => array('min' => 0,  'max' => 12,  'unit' => 'μg/m³', 'label' => 'PM2.5')
    );

    /**
     * Generate insights from sensor data.
     *
     * @param array $readings  device_id => [sensor_type => [value, recorded_at]]
     * @param array $devices   array of [id, name, location, status]
     * @param array $trends    "{device_id}_{sensor}" => [labels=>[], values=>[]]
     * @param array $aqi       device_id => [value, label, color]
     * @param int   $alarm_count
     * @return array
     */
    public function generate($readings, $devices, $trends, $aqi, $alarm_count) {
        $insights = array();
        if (empty($readings) || empty($devices)) {
            return array($this->_make('info', 'fas fa-hourglass-half', 'Waiting for sensor data to accumulate...', 'System'));
        }

        $names = array();
        $locations = array();
        foreach ($devices as $d) {
            $id = is_object($d) ? $d->id : (isset($d['id']) ? $d['id'] : null);
            $name = is_object($d) ? $d->name : (isset($d['name']) ? $d['name'] : '');
            $loc = is_object($d) ? $d->location : (isset($d['location']) ? $d['location'] : '');
            if ($id) {
                $names[$id] = $name;
                $locations[$id] = $loc;
            }
        }

        $insights = array_merge($insights, $this->_overall_aqi($aqi, $names));
        $insights = array_merge($insights, $this->_cross_room($readings, $names));
        $insights = array_merge($insights, $this->_comfort_check($readings, $names));
        $insights = array_merge($insights, $this->_trend_analysis($trends, $names));
        $insights = array_merge($insights, $this->_device_health($devices));
        $insights = array_merge($insights, $this->_health_tips($readings, $aqi, $names));
        $insights = array_merge($insights, $this->_alarm_summary($alarm_count));

        usort($insights, array($this, '_severity_sort'));
        return array_slice($insights, 0, 8);
    }

    // ==================== RULE GROUP 1: Overall AQI ====================

    private function _overall_aqi($aqi, $names) {
        $insights = array();
        if (empty($aqi)) return $insights;

        $values = array();
        $worst_device = null;
        $worst_val = 0;

        foreach ($aqi as $did => $data) {
            $val = isset($data['value']) ? $data['value'] : 0;
            $values[] = $val;
            if ($val > $worst_val) {
                $worst_val = $val;
                $worst_device = $did;
            }
        }

        $avg = count($values) > 0 ? round(array_sum($values) / count($values)) : 0;

        if ($worst_val <= 50) {
            $insights[] = $this->_make(self::GOOD, 'fas fa-leaf', 'Overall air quality is Good across all rooms (AQI: ' . $avg . ')', 'Air Quality');
        } elseif ($worst_val <= 100) {
            $room = isset($names[$worst_device]) ? $names[$worst_device] : 'Unknown';
            $insights[] = $this->_make(self::INFO, 'fas fa-info-circle', 'Air quality is Moderate in ' . $room . ' — sensitive individuals should monitor', 'Air Quality');
        } elseif ($worst_val <= 150) {
            $room = isset($names[$worst_device]) ? $names[$worst_device] : 'Unknown';
            $insights[] = $this->_make(self::WARNING, 'fas fa-exclamation-triangle', 'Air quality is Unhealthy for Sensitive Groups in ' . $room . ' (AQI: ' . $worst_val . ')', 'Air Quality');
        } else {
            $room = isset($names[$worst_device]) ? $names[$worst_device] : 'Unknown';
            $insights[] = $this->_make(self::CRITICAL, 'fas fa-skull-crossbones', 'Air quality is Unhealthy in ' . $room . ' (AQI: ' . $worst_val . ') — use air purifiers', 'Air Quality');
        }

        return $insights;
    }

    // ==================== RULE GROUP 2: Cross-Room Comparison ====================

    private function _cross_room($readings, $names) {
        $insights = array();
        if (count($readings) < 2) return $insights;

        $sensor_types = array('temperature', 'humidity', 'co2', 'pm25');

        foreach ($sensor_types as $type) {
            $vals = array();
            foreach ($readings as $did => $sensors) {
                if (isset($sensors[$type])) {
                    $vals[$did] = $sensors[$type]['value'];
                }
            }
            if (count($vals) < 2) continue;

            $mean = array_sum($vals) / count($vals);
            $range = max($vals) - min($vals);
            $label = $this->comfort[$type]['label'];
            $unit = $this->comfort[$type]['unit'];

            // Check for rooms significantly above average
            foreach ($vals as $did => $v) {
                if ($mean > 0) {
                    $pct_above = (($v - $mean) / $mean) * 100;
                    $room = isset($names[$did]) ? $names[$did] : 'Device #' . $did;

                    if ($type === 'co2' && $pct_above > 30) {
                        $insights[] = $this->_make(self::WARNING, 'fas fa-wind', $room . ' CO₂ is ' . round($pct_above) . '% higher than other rooms — consider ventilation', 'Comparison');
                    } elseif ($type === 'pm25' && $pct_above > 50) {
                        $insights[] = $this->_make(self::WARNING, 'fas fa-smog', $room . ' PM2.5 is significantly higher than other rooms (' . round($v, 1) . ' ' . $unit . ')', 'Comparison');
                    }
                }
            }

            // Temperature variance
            if ($type === 'temperature' && $range > 5) {
                $insights[] = $this->_make(self::INFO, 'fas fa-temperature-high', 'Temperature varies ' . round($range, 1) . '°C across rooms — check HVAC balance', 'Comparison');
            }

            // Humidity variance
            if ($type === 'humidity' && $range > 20) {
                $insights[] = $this->_make(self::INFO, 'fas fa-tint', 'Humidity levels vary significantly between rooms (' . round($range) . '% range)', 'Comparison');
            }
        }

        return $insights;
    }

    // ==================== RULE GROUP 3: Comfort Levels ====================

    private function _comfort_check($readings, $names) {
        $insights = array();
        $all_comfortable = true;

        foreach ($readings as $did => $sensors) {
            $room = isset($names[$did]) ? $names[$did] : 'Device #' . $did;

            // Temperature
            if (isset($sensors['temperature'])) {
                $v = $sensors['temperature']['value'];
                if ($v > 35) {
                    $insights[] = $this->_make(self::CRITICAL, 'fas fa-fire', $room . ' temperature is dangerously high (' . round($v, 1) . '°C)', 'Comfort');
                    $all_comfortable = false;
                } elseif ($v > 30) {
                    $insights[] = $this->_make(self::WARNING, 'fas fa-sun', $room . ' is too warm (' . round($v, 1) . '°C) — consider cooling', 'Comfort');
                    $all_comfortable = false;
                } elseif ($v < 16) {
                    $insights[] = $this->_make(self::WARNING, 'fas fa-snowflake', $room . ' is too cold (' . round($v, 1) . '°C) — consider heating', 'Comfort');
                    $all_comfortable = false;
                }
            }

            // Humidity
            if (isset($sensors['humidity'])) {
                $v = $sensors['humidity']['value'];
                if ($v > 70) {
                    $insights[] = $this->_make(self::WARNING, 'fas fa-tint', $room . ' humidity is high (' . round($v) . '%) — risk of mold growth', 'Comfort');
                    $all_comfortable = false;
                } elseif ($v < 25) {
                    $insights[] = $this->_make(self::WARNING, 'fas fa-tint-slash', $room . ' air is very dry (' . round($v) . '%) — may cause discomfort', 'Comfort');
                    $all_comfortable = false;
                }
            }

            // CO2
            if (isset($sensors['co2'])) {
                $v = $sensors['co2']['value'];
                if ($v > 2000) {
                    $insights[] = $this->_make(self::CRITICAL, 'fas fa-exclamation-circle', $room . ' CO₂ is dangerously high (' . round($v) . ' ppm) — ventilate immediately', 'Comfort');
                    $all_comfortable = false;
                } elseif ($v > 1000) {
                    $insights[] = $this->_make(self::WARNING, 'fas fa-cloud', $room . ' CO₂ is elevated (' . round($v) . ' ppm) — room needs ventilation', 'Comfort');
                    $all_comfortable = false;
                }
            }

            // PM2.5
            if (isset($sensors['pm25'])) {
                $v = $sensors['pm25']['value'];
                if ($v > 55) {
                    $insights[] = $this->_make(self::CRITICAL, 'fas fa-lungs', $room . ' PM2.5 is unhealthy (' . round($v, 1) . ' μg/m³) — use air purifier', 'Comfort');
                    $all_comfortable = false;
                } elseif ($v > 35) {
                    $insights[] = $this->_make(self::WARNING, 'fas fa-smog', $room . ' PM2.5 is elevated (' . round($v, 1) . ' μg/m³)', 'Comfort');
                    $all_comfortable = false;
                }
            }
        }

        if ($all_comfortable && count($readings) > 0) {
            $insights[] = $this->_make(self::GOOD, 'fas fa-check-circle', 'All rooms are within comfortable ranges for temperature, humidity, CO₂, and PM2.5', 'Comfort');
        }

        return $insights;
    }

    // ==================== RULE GROUP 4: Trend Analysis ====================

    private function _trend_analysis($trends, $names) {
        $insights = array();
        if (empty($trends)) return $insights;

        foreach ($trends as $key => $data) {
            $values = isset($data['values']) ? $data['values'] : array();
            if (count($values) < 6) continue;

            // Extract device_id and sensor_type from key
            $parts = explode('_', $key, 2);
            if (count($parts) < 2) continue;
            $did = $parts[0];
            $sensor = $parts[1];
            $room = isset($names[$did]) ? $names[$did] : 'Device #' . $did;
            $label = isset($this->comfort[$sensor]) ? $this->comfort[$sensor]['label'] : $sensor;

            $recent = array_slice($values, -6);

            // Check for monotonic increase
            $increasing = true;
            $decreasing = true;
            for ($i = 1; $i < count($recent); $i++) {
                if ($recent[$i] <= $recent[$i - 1]) $increasing = false;
                if ($recent[$i] >= $recent[$i - 1]) $decreasing = false;
            }

            if ($increasing) {
                $change = $recent[count($recent) - 1] - $recent[0];
                $sev = ($sensor === 'co2' && $change > 200) || ($sensor === 'pm25' && $change > 15) ? self::WARNING : self::INFO;
                $insights[] = $this->_make($sev, 'fas fa-arrow-trend-up', $label . ' in ' . $room . ' is steadily rising over the last hour (+' . round($change, 1) . ')', 'Trends');
            }

            if ($decreasing && $sensor !== 'temperature') {
                $insights[] = $this->_make(self::INFO, 'fas fa-arrow-trend-down', $label . ' in ' . $room . ' is trending downward — improving', 'Trends');
            }

            // Spike detection for PM2.5
            if ($sensor === 'pm25' && count($values) >= 6) {
                $mean = array_sum($values) / count($values);
                $spikes = 0;
                foreach ($values as $v) {
                    if ($mean > 0 && $v > $mean * 2) $spikes++;
                }
                if ($spikes >= 3) {
                    $insights[] = $this->_make(self::WARNING, 'fas fa-chart-bar', 'PM2.5 in ' . $room . ' has spiked ' . $spikes . ' times in the last hour', 'Trends');
                }
            }
        }

        return $insights;
    }

    // ==================== RULE GROUP 5: Device Health ====================

    private function _device_health($devices) {
        $insights = array();
        $online = 0;
        $offline = 0;
        $maintenance = 0;
        $offline_names = array();

        foreach ($devices as $d) {
            $status = is_object($d) ? $d->status : (isset($d['status']) ? $d['status'] : 'offline');
            $name = is_object($d) ? $d->name : (isset($d['name']) ? $d['name'] : '');

            if ($status === 'online') {
                $online++;
            } elseif ($status === 'maintenance') {
                $maintenance++;
            } else {
                $offline++;
                $offline_names[] = $name;
            }
        }

        $total = $online + $offline + $maintenance;

        if ($offline > 0) {
            $rooms = implode(', ', array_slice($offline_names, 0, 3));
            $insights[] = $this->_make(self::WARNING, 'fas fa-unlink', $offline . ' device(s) offline (' . $rooms . ') — not being monitored', 'Devices');
        } elseif ($total > 0 && $online === $total) {
            $insights[] = $this->_make(self::GOOD, 'fas fa-satellite-dish', 'All ' . $total . ' monitoring devices are online and reporting', 'Devices');
        }

        if ($maintenance > 0) {
            $insights[] = $this->_make(self::INFO, 'fas fa-wrench', $maintenance . ' device(s) in maintenance mode', 'Devices');
        }

        return $insights;
    }

    // ==================== RULE GROUP 6: Health Recommendations ====================

    private function _health_tips($readings, $aqi, $names) {
        $insights = array();
        $hour = (int) date('G');

        foreach ($readings as $did => $sensors) {
            $room = isset($names[$did]) ? $names[$did] : 'Device #' . $did;
            $co2 = isset($sensors['co2']) ? $sensors['co2']['value'] : 0;
            $hum = isset($sensors['humidity']) ? $sensors['humidity']['value'] : 0;
            $temp = isset($sensors['temperature']) ? $sensors['temperature']['value'] : 0;
            $pm25 = isset($sensors['pm25']) ? $sensors['pm25']['value'] : 0;

            // High CO2 + High humidity → ventilation
            if ($co2 > 800 && $hum > 60) {
                $insights[] = $this->_make(self::INFO, 'fas fa-door-open', 'Open windows in ' . $room . ' to improve air circulation and reduce moisture', 'Recommendation');
                continue;
            }

            // Dry air + comfortable temp → humidifier
            if ($hum < 30 && $temp >= 18 && $temp <= 26) {
                $insights[] = $this->_make(self::INFO, 'fas fa-spray-can', 'Consider using a humidifier in ' . $room . ' — air is dry (' . round($hum) . '%)', 'Recommendation');
            }
        }

        // Night-time good air quality
        if ($hour >= 22 || $hour <= 6) {
            $all_good = true;
            foreach ($aqi as $data) {
                if (isset($data['value']) && $data['value'] > 50) {
                    $all_good = false;
                    break;
                }
            }
            if ($all_good && !empty($aqi)) {
                $insights[] = $this->_make(self::GOOD, 'fas fa-moon', 'Air quality is ideal for sleeping — all rooms within healthy ranges', 'Recommendation');
            }
        }

        // Daytime good air quality
        if ($hour >= 8 && $hour <= 20) {
            $avg_aqi_values = array();
            foreach ($aqi as $data) {
                if (isset($data['value'])) $avg_aqi_values[] = $data['value'];
            }
            if (!empty($avg_aqi_values)) {
                $avg_aqi = array_sum($avg_aqi_values) / count($avg_aqi_values);
                if ($avg_aqi <= 50) {
                    $insights[] = $this->_make(self::GOOD, 'fas fa-sun', 'Excellent air quality — great conditions for indoor activities', 'Recommendation');
                }
            }
        }

        return $insights;
    }

    // ==================== RULE GROUP 7: Alarm Summary ====================

    private function _alarm_summary($count) {
        $insights = array();

        if ($count == 0) {
            $insights[] = $this->_make(self::GOOD, 'fas fa-shield-alt', 'No active alarms — all systems operating normally', 'Alarms');
        } elseif ($count <= 3) {
            $insights[] = $this->_make(self::WARNING, 'fas fa-bell', $count . ' active alarm(s) require attention', 'Alarms');
        } else {
            $insights[] = $this->_make(self::CRITICAL, 'fas fa-exclamation-circle', 'Multiple alarms active (' . $count . ') — immediate review recommended', 'Alarms');
        }

        return $insights;
    }

    // ==================== HELPERS ====================

    private function _make($severity, $icon, $message, $category) {
        return array(
            'severity' => $severity,
            'icon'     => $icon,
            'message'  => $message,
            'category' => $category,
            'time'     => date('H:i')
        );
    }

    private function _severity_sort($a, $b) {
        $order = array('critical' => 0, 'warning' => 1, 'info' => 2, 'good' => 3);
        $va = isset($order[$a['severity']]) ? $order[$a['severity']] : 4;
        $vb = isset($order[$b['severity']]) ? $order[$b['severity']] : 4;
        return $va - $vb;
    }
}
