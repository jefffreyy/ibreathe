<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('device_model');
        $this->load->model('reading_model');
        $this->load->model('alarm_model');
        $this->load->model('scada_model');
    }

    // ==================== DATA INGESTION ====================

    /**
     * POST /api/data
     * Receive sensor readings from IoT devices
     * Auth: X-Device-Key header
     * Body JSON: { "readings": { "temperature": 23.5, "humidity": 55.2, "co2": 450, "gas": 12.3 } }
     */
    public function data() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->_data_post();
        } else {
            $this->_json_response(array('error' => 'Method not allowed'), 405);
        }
    }

    private function _data_post() {
        $device_key = $this->input->get_request_header('X-Device-Key', true);
        if (!$device_key) {
            $this->_json_response(array('error' => 'Missing X-Device-Key header'), 401);
            return;
        }

        $device = $this->device_model->get_device_by_key($device_key);
        if (!$device) {
            $this->_json_response(array('error' => 'Invalid device key'), 401);
            return;
        }

        if ($device->status === 'maintenance') {
            $this->_json_response(array('error' => 'Device is in maintenance mode'), 403);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['readings'])) {
            $this->_json_response(array('error' => 'Invalid JSON body. Expected: {"readings": {...}}'), 400);
            return;
        }

        $valid_types = array('temperature', 'humidity', 'gas');
        $batch = array();
        $now = date('Y-m-d H:i:s');
        $alarms_triggered = array();

        foreach ($input['readings'] as $type => $value) {
            if (in_array($type, $valid_types) && is_numeric($value)) {
                $batch[] = array(
                    'device_id'   => $device->id,
                    'sensor_type' => $type,
                    'value'       => round($value, 2),
                    'recorded_at' => $now
                );

                // Check alarm thresholds
                $triggered = $this->alarm_model->check_thresholds($device->id, $type, $value);
                if (!empty($triggered)) {
                    $alarms_triggered = array_merge($alarms_triggered, $triggered);
                }
            }
        }

        if (!empty($batch)) {
            $this->reading_model->insert_batch_readings($batch);
            $this->device_model->update_last_seen($device->id, $this->input->ip_address());
        }

        $response = array(
            'status'   => 'ok',
            'received' => count($batch),
            'alarms'   => count($alarms_triggered)
        );

        // Check for pending commands
        $this->db->where('device_id', $device->id);
        $this->db->where('status', 'pending');
        $this->db->order_by('created_at', 'ASC');
        $this->db->limit(5);
        $commands = $this->db->get('tbl_scada_commands')->result();
        if (!empty($commands)) {
            $response['commands'] = array();
            foreach ($commands as $cmd) {
                $response['commands'][] = array(
                    'id'         => $cmd->id,
                    'type'       => $cmd->command_type,
                    'parameters' => json_decode($cmd->parameters, true)
                );
                // Mark as sent
                $this->db->where('id', $cmd->id);
                $this->db->update('tbl_scada_commands', array('status' => 'sent'));
            }
        }

        $this->_json_response($response);
    }

    // ==================== DASHBOARD DATA ====================

    /**
     * GET /api/dashboard
     * Returns aggregated data for the real-time dashboard
     */
    public function dashboard() {
        if (!$this->session->userdata('logged_in')) {
            $this->_json_response(array('error' => 'Unauthorized'), 401);
            return;
        }

        // Mark offline devices
        $timeout = $this->scada_model->get_setting('device_timeout_minutes') ?: 5;
        $this->device_model->mark_offline_devices($timeout);

        $dashboard = $this->reading_model->get_dashboard_data();

        // Get devices
        $devices = $this->device_model->get_devices();
        $device_data = array();
        foreach ($devices as $d) {
            $device_data[] = array(
                'id'        => $d->id,
                'name'      => $d->name,
                'location'  => $d->location,
                'status'    => $d->status,
                'last_seen' => $d->last_seen
            );
        }

        // Get active alarms
        $alarms = $this->alarm_model->get_active_events(10);
        $alarm_data = array();
        foreach ($alarms as $a) {
            $alarm_data[] = array(
                'id'          => $a->id,
                'device_name' => $a->device_name,
                'sensor_type' => $a->sensor_type,
                'value'       => $a->value,
                'severity'    => $a->severity,
                'message'     => $a->message,
                'triggered_at'=> $a->triggered_at
            );
        }

        $normalize_type = function($type) {
            return $type;
        };

        // Organize readings by device
        $readings = array();
        foreach ($dashboard['readings'] as $r) {
            if (!isset($readings[$r->device_id])) {
                $readings[$r->device_id] = array();
            }
            $key = $normalize_type($r->sensor_type);
            $readings[$r->device_id][$key] = array(
                'value'       => floatval($r->value),
                'recorded_at' => $r->recorded_at
            );
        }

        // Organize trends by device and sensor
        $trends = array();
        foreach ($dashboard['trends'] as $t) {
            $key = $t->device_id . '_' . $normalize_type($t->sensor_type);
            if (!isset($trends[$key])) {
                $trends[$key] = array('labels' => array(), 'values' => array());
            }
            $trends[$key]['labels'][] = date('H:i', strtotime($t->time_bucket));
            $trends[$key]['values'][] = round(floatval($t->avg_value), 2);
        }
        // Compute CO and calculate AQI from gas sensor
        $aqi_data = array();
        foreach ($readings as $device_id => $sensors) {
            // Compute CO from gas, temperature, humidity
            if (isset($sensors['gas'], $sensors['temperature'], $sensors['humidity'])) {
                $gas_val  = $sensors['gas']['value'];
                $temp_val = $sensors['temperature']['value'];
                $hum_val  = $sensors['humidity']['value'];

                $co_val = $this->co_calculator->calculate_co($gas_val, $temp_val, $hum_val);
                $readings[$device_id]['co'] = array(
                    'value'       => $co_val,
                    'recorded_at' => $sensors['gas']['recorded_at'],
                    'label'       => $this->co_calculator->get_co_label($co_val),
                    'color'       => $this->co_calculator->get_co_color($co_val)
                );

                $index = $this->aqi_calculator->calculate_index($gas_val, $temp_val, $hum_val);

                $aqi_data[$device_id] = array(
                    'value'      => $index,
                    'label'      => $this->aqi_calculator->get_aqi_label($index),
                    'color'      => $this->aqi_calculator->get_aqi_color($index),
                    'text_color' => $this->aqi_calculator->get_aqi_text_color($index),
                    'gas'        => $gas_val,
                    'temperature'=> $temp_val,
                    'humidity'   => $hum_val,
                );
            }
        }
        $this->_json_response(array(
            'readings'      => $readings,
            'devices'       => $device_data,
            'alarms'        => $alarm_data,
            'alarm_count'   => $this->alarm_model->count_active_events(),
            'trends'        => $trends,
            'aqi'           => $aqi_data,
            'timestamp'     => date('Y-m-d H:i:s')
        ));
    }

    // ==================== DATA RETRIEVAL ====================

    /**
     * GET /api/data/latest/{device_id}
     */
    public function data_latest($device_id) {
        if (!$this->session->userdata('logged_in')) {
            $this->_json_response(array('error' => 'Unauthorized'), 401);
            return;
        }
        $readings = $this->reading_model->get_latest_readings($device_id);
        $this->_json_response(array('readings' => $readings));
    }

    /**
     * GET /api/data/history/{device_id}?sensor_type=&from=&to=
     */
    public function data_history($device_id) {
        if (!$this->session->userdata('logged_in')) {
            $this->_json_response(array('error' => 'Unauthorized'), 401);
            return;
        }
        $sensor_type = $this->input->get('sensor_type') ?: 'temperature';
        $from = $this->input->get('from') ?: date('Y-m-d H:i:s', strtotime('-24 hours'));
        $to = $this->input->get('to') ?: date('Y-m-d H:i:s');

        $this->load->model('report_model');
        $data = $this->report_model->get_trend_data($device_id, $sensor_type, $from, $to);
        $this->_json_response(array('data' => $data));
    }

    // ==================== DEVICES ====================

    /**
     * GET /api/devices
     */
    public function devices_list() {
        if (!$this->session->userdata('logged_in')) {
            $this->_json_response(array('error' => 'Unauthorized'), 401);
            return;
        }
        $devices = $this->device_model->get_devices();
        $this->_json_response(array('devices' => $devices));
    }

    // ==================== ALARMS ====================

    /**
     * GET /api/alarms/active
     */
    public function alarms_active() {
        if (!$this->session->userdata('logged_in')) {
            $this->_json_response(array('error' => 'Unauthorized'), 401);
            return;
        }
        $alarms = $this->alarm_model->get_active_events();
        $this->_json_response(array('alarms' => $alarms));
    }

    // ==================== COMMANDS ====================

    /**
     * POST/GET /api/commands/{device_id}
     */
    public function commands($device_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Send command (from web UI)
            if (!$this->session->userdata('logged_in')) {
                $this->_json_response(array('error' => 'Unauthorized'), 401);
                return;
            }
            $input = json_decode(file_get_contents('php://input'), true);
            $data = array(
                'device_id'    => $device_id,
                'command_type' => isset($input['command']) ? $input['command'] : '',
                'parameters'   => isset($input['parameters']) ? json_encode($input['parameters']) : null,
                'status'       => 'pending',
                'created_by'   => $this->session->userdata('user_id'),
                'created_at'   => date('Y-m-d H:i:s')
            );
            $this->db->insert('tbl_scada_commands', $data);
            $this->scada_model->log_audit('command_sent', "Command '{$data['command_type']}' sent to device #$device_id");
            $this->_json_response(array('status' => 'ok', 'command_id' => $this->db->insert_id()));
        } else {
            // Device polling for commands
            $device_key = $this->input->get_request_header('X-Device-Key', true);
            $device = $this->device_model->get_device_by_key($device_key);
            if (!$device || $device->id != $device_id) {
                $this->_json_response(array('error' => 'Unauthorized'), 401);
                return;
            }
            $this->db->where('device_id', $device_id);
            $this->db->where('status', 'pending');
            $this->db->order_by('created_at', 'ASC');
            $commands = $this->db->get('tbl_scada_commands')->result();
            $this->_json_response(array('commands' => $commands));
        }
    }

    // ==================== DEVICE SELF-REGISTRATION ====================

    /**
     * POST /api/device/register
     * Body: { "name": "...", "location": "...", "type": "..." }
     */
    public function device_register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_json_response(array('error' => 'Method not allowed'), 405);
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['name'])) {
            $this->_json_response(array('error' => 'Name is required'), 400);
            return;
        }
        $data = array(
            'name'     => $input['name'],
            'location' => isset($input['location']) ? $input['location'] : '',
            'type'     => isset($input['type']) ? $input['type'] : 'air_quality_monitor'
        );
        $device_id = $this->device_model->add_device($data);
        $device = $this->device_model->get_device($device_id);
        $this->_json_response(array(
            'status'     => 'ok',
            'device_id'  => $device_id,
            'device_key' => $device->device_key
        ), 201);
    }

    // ==================== DEVICE LOOKUP (for simulator) ====================

    /**
     * GET /api/device/lookup?name=Living Room Sensor
     * Returns device_key if device exists (for simulator to avoid duplicates)
     */
    public function device_lookup() {
        $name = $this->input->get('name');
        if (!$name) {
            $this->_json_response(array('error' => 'Name parameter required'), 400);
            return;
        }
        $this->db->where('name', $name);
        $device = $this->db->get('tbl_scada_devices')->row();
        if ($device) {
            $this->_json_response(array(
                'status'     => 'found',
                'device_id'  => $device->id,
                'device_key' => $device->device_key
            ));
        } else {
            $this->_json_response(array('status' => 'not_found'));
        }
    }

    // ==================== FLOOR PLAN DATA ====================

    /**
     * GET /api/floorplan
     * Returns all devices with their latest readings for floor plan display
     */
    public function floorplan() {
        if (!$this->session->userdata('logged_in')) {
            $this->_json_response(array('error' => 'Unauthorized'), 401);
            return;
        }

        $timeout = $this->scada_model->get_setting('device_timeout_minutes') ?: 5;
        $this->device_model->mark_offline_devices($timeout);

        $devices = $this->device_model->get_devices();
        $result = array();

        foreach ($devices as $d) {
            $readings = $this->reading_model->get_latest_readings($d->id);
            $sensors = array();
            foreach ($readings as $r) {
                $sensors[$r->sensor_type] = array(
                    'value'       => floatval($r->value),
                    'recorded_at' => $r->recorded_at
                );
            }

            // Compute CO from gas, temperature, humidity
            if (isset($sensors['gas'], $sensors['temperature'], $sensors['humidity'])) {
                $co_val = $this->co_calculator->calculate_co(
                    $sensors['gas']['value'],
                    $sensors['temperature']['value'],
                    $sensors['humidity']['value']
                );
                $sensors['co'] = array(
                    'value'       => $co_val,
                    'recorded_at' => $sensors['gas']['recorded_at'],
                    'label'       => $this->co_calculator->get_co_label($co_val),
                    'color'       => $this->co_calculator->get_co_color($co_val)
                );
            }

            $aqi_value = null;
            $aqi_label = null;
            $aqi_color = null;
            if (isset($sensors['gas'])) {
                $aqi_value = $this->aqi_calculator->calculate_aqi($sensors['gas']['value']);
                $aqi_label = $this->aqi_calculator->get_aqi_label($aqi_value);
                $aqi_color = $this->aqi_calculator->get_aqi_color($aqi_value);
            }

            $result[] = array(
                'id'        => $d->id,
                'name'      => $d->name,
                'location'  => $d->location,
                'status'    => $d->status,
                'last_seen' => $d->last_seen,
                'sensors'   => $sensors,
                'aqi'       => array('value' => $aqi_value, 'label' => $aqi_label, 'color' => $aqi_color)
            );
        }

        $this->_json_response(array(
            'devices'   => $result,
            'timestamp' => date('Y-m-d H:i:s')
        ));
    }

    // ==================== AI INSIGHTS ====================

    /**
     * GET /api/insights
     * Returns AI-generated insights based on current sensor data
     */
    public function insights() {
        if (!$this->session->userdata('logged_in')) {
            $this->_json_response(array('error' => 'Unauthorized'), 401);
            return;
        }

        $timeout = $this->scada_model->get_setting('device_timeout_minutes') ?: 5;
        $this->device_model->mark_offline_devices($timeout);

        $dashboard = $this->reading_model->get_dashboard_data();
        $devices = $this->device_model->get_devices();

        // Organize readings by device
        $readings = array();
        foreach ($dashboard['readings'] as $r) {
            if (!isset($readings[$r->device_id])) {
                $readings[$r->device_id] = array();
            }
            $readings[$r->device_id][$r->sensor_type] = array(
                'value'       => floatval($r->value),
                'recorded_at' => $r->recorded_at
            );
        }

        // Organize trends
        $trends = array();
        foreach ($dashboard['trends'] as $t) {
            $key = $t->device_id . '_' . $t->sensor_type;
            if (!isset($trends[$key])) {
                $trends[$key] = array('labels' => array(), 'values' => array());
            }
            $trends[$key]['labels'][] = date('H:i', strtotime($t->time_bucket));
            $trends[$key]['values'][] = round(floatval($t->avg_value), 2);
        }

        // Compute CO and calculate AQI
        $aqi_data = array();
        foreach ($readings as $device_id => $sensors) {
            if (isset($sensors['gas'], $sensors['temperature'], $sensors['humidity'])) {
                $co_val = $this->co_calculator->calculate_co(
                    $sensors['gas']['value'],
                    $sensors['temperature']['value'],
                    $sensors['humidity']['value']
                );
                $readings[$device_id]['co'] = array(
                    'value'       => $co_val,
                    'recorded_at' => $sensors['gas']['recorded_at']
                );
            }
            if (isset($sensors['gas'])) {
                $gas_val = $sensors['gas']['value'];
                $aqi_val = $this->aqi_calculator->calculate_aqi($gas_val);
                $aqi_data[$device_id] = array(
                    'value' => $aqi_val,
                    'label' => $this->aqi_calculator->get_aqi_label($aqi_val),
                    'color' => $this->aqi_calculator->get_aqi_color($aqi_val)
                );
            }
        }

        $alarm_count = $this->alarm_model->count_active_events();

        // Build device data
        $device_data = array();
        foreach ($devices as $d) {
            $device_data[] = array(
                'id'       => $d->id,
                'name'     => $d->name,
                'location' => $d->location,
                'status'   => $d->status
            );
        }

        // Generate rule-based insights
        $this->load->library('ai_insights');
        $result = $this->ai_insights->generate($readings, $device_data, $trends, $aqi_data, $alarm_count);

        // ML-powered insights (graceful fallback)
        $this->load->library('ml_client');
        $ml_available = $this->ml_client->is_available();
        $forecasts = array();

        if ($ml_available) {
            $names = array();
            foreach ($device_data as $d) {
                $names[$d['id']] = $d['name'];
            }

            // Anomaly detection
            $anomalies = $this->ml_client->get_anomaly_insights($readings, $names);
            $result = array_merge($result, $anomalies);

            // Forecasting
            $fc_insights = $this->ml_client->get_forecast_insights($readings, $names);
            $result = array_merge($result, $fc_insights);

            // Re-sort by severity
            usort($result, function($a, $b) {
                $order = array('critical' => 0, 'warning' => 1, 'info' => 2, 'good' => 3);
                $va = isset($order[$a['severity']]) ? $order[$a['severity']] : 4;
                $vb = isset($order[$b['severity']]) ? $order[$b['severity']] : 4;
                return $va - $vb;
            });
            $result = array_slice($result, 0, 12);

            // Raw forecast data for forecast card
            $first_id = !empty($device_data) ? $device_data[0]['id'] : null;
            if ($first_id) {
                foreach (array('temperature', 'humidity', 'gas') as $st) {
                    $fc = $this->ml_client->get_forecast($first_id, $st);
                    if ($fc && isset($fc['predictions']) && $fc['status'] === 'ok') {
                        $forecasts[$st] = $fc;
                    }
                }
            }
        }

        $this->_json_response(array(
            'insights'     => $result,
            'count'        => count($result),
            'ml_available' => $ml_available,
            'forecasts'    => $forecasts,
            'timestamp'    => date('Y-m-d H:i:s')
        ));
    }

    // ==================== REPORT INSIGHTS ====================

    /**
     * GET /api/report_insights?device_id=&sensor_type=&from=&to=
     * Returns AI-generated interpretation of trend data for the reports page
     */
    public function report_insights() {
        if (!$this->session->userdata('logged_in')) {
            $this->_json_response(array('error' => 'Unauthorized'), 401);
            return;
        }

        $device_id   = $this->input->get('device_id');
        $sensor_type = $this->input->get('sensor_type') ?: 'temperature';
        $from        = $this->input->get('from') ?: date('Y-m-d H:i:s', strtotime('-24 hours'));
        $to          = $this->input->get('to') ?: date('Y-m-d H:i:s');

        if (!$device_id) {
            $this->_json_response(array('error' => 'device_id is required'), 400);
            return;
        }

        // Get device info
        $device = $this->device_model->get_device($device_id);
        if (!$device) {
            $this->_json_response(array('error' => 'Device not found'), 404);
            return;
        }

        // Get trend data
        $this->load->model('report_model');
        $data = $this->report_model->get_trend_data($device_id, $sensor_type, $from, $to);

        if (empty($data)) {
            $this->_json_response(array(
                'insights' => array(array(
                    'severity' => 'info',
                    'icon'     => 'fas fa-info-circle',
                    'message'  => 'No data available for the selected period.',
                    'category' => 'data',
                    'source'   => 'rule'
                )),
                'count' => 1
            ));
            return;
        }

        $insights = $this->_generate_report_insights($data, $device, $sensor_type, $from, $to);

        // Optional: ML anomaly check on latest value
        $ml_available = false;
        $this->load->library('ml_client');
        if ($this->ml_client->is_available()) {
            $ml_available = true;
            $last_row = end($data);
            $latest_val = floatval($last_row->avg_value);
            $anomaly = $this->ml_client->check_anomaly($device_id, $sensor_type, $latest_val);
            if ($anomaly && isset($anomaly['is_anomaly']) && $anomaly['is_anomaly']) {
                array_unshift($insights, array(
                    'severity' => $anomaly['severity'] === 'critical' ? 'critical' : 'warning',
                    'icon'     => 'fas fa-exclamation-triangle',
                    'message'  => 'ML anomaly detected: latest ' . $sensor_type . ' value (' . round($latest_val, 1) . ') is unusual (z-score: ' . round($anomaly['z_score'], 2) . ')',
                    'category' => 'anomaly',
                    'source'   => 'ml'
                ));
            }
        }

        $this->_json_response(array(
            'insights'     => $insights,
            'count'        => count($insights),
            'ml_available' => $ml_available
        ));
    }

    /**
     * Generate interpretive insights from trend data
     */
    private function _generate_report_insights($data, $device, $sensor_type, $from, $to) {
        $insights = array();
        $values = array();
        $timestamps = array();

        foreach ($data as $row) {
            $values[] = floatval($row->avg_value);
            $timestamps[] = $row->time_bucket;
        }

        $count = count($values);
        $min_val = min($values);
        $max_val = max($values);
        $avg_val = array_sum($values) / $count;
        $range = $max_val - $min_val;

        // Sensor metadata
        $sensor_labels = array(
            'temperature' => array('label' => 'Temperature', 'unit' => '°C'),
            'humidity'    => array('label' => 'Humidity', 'unit' => '%'),
            'gas'         => array('label' => 'PM2.5', 'unit' => 'µg/m³'),
            'co'          => array('label' => 'CO', 'unit' => 'ppm')
        );
        $info = isset($sensor_labels[$sensor_type]) ? $sensor_labels[$sensor_type] : array('label' => $sensor_type, 'unit' => '');
        $label = $info['label'];
        $unit  = $info['unit'];

        // 1. Summary
        $insights[] = array(
            'severity' => 'info',
            'icon'     => 'fas fa-chart-bar',
            'message'  => $label . ' ranged from ' . round($min_val, 1) . $unit . ' to ' . round($max_val, 1) . $unit . ' with an average of ' . round($avg_val, 1) . $unit . ' across ' . $count . ' data points.',
            'category' => 'summary',
            'source'   => 'rule'
        );

        // 2. Trend direction (first-half vs second-half average)
        $half = intval($count / 2);
        if ($half > 0) {
            $first_half = array_slice($values, 0, $half);
            $second_half = array_slice($values, $half);
            $avg_first = array_sum($first_half) / count($first_half);
            $avg_second = array_sum($second_half) / count($second_half);
            $change = $avg_second - $avg_first;
            $pct = ($avg_first != 0) ? abs($change / $avg_first) * 100 : 0;

            if ($pct > 5) {
                if ($change > 0) {
                    $insights[] = array(
                        'severity' => 'warning',
                        'icon'     => 'fas fa-arrow-trend-up',
                        'message'  => $label . ' shows a rising trend: second half averaged ' . round($avg_second, 1) . $unit . ' vs ' . round($avg_first, 1) . $unit . ' (+' . round($pct, 1) . '%).',
                        'category' => 'trend',
                        'source'   => 'rule'
                    );
                } else {
                    $insights[] = array(
                        'severity' => 'info',
                        'icon'     => 'fas fa-arrow-trend-down',
                        'message'  => $label . ' shows a declining trend: second half averaged ' . round($avg_second, 1) . $unit . ' vs ' . round($avg_first, 1) . $unit . ' (-' . round($pct, 1) . '%).',
                        'category' => 'trend',
                        'source'   => 'rule'
                    );
                }
            } else {
                $insights[] = array(
                    'severity' => 'good',
                    'icon'     => 'fas fa-arrows-alt-h',
                    'message'  => $label . ' remained stable throughout the period with minimal variation (' . round($pct, 1) . '% change).',
                    'category' => 'trend',
                    'source'   => 'rule'
                );
            }
        }

        // 3. Variability (coefficient of variation)
        if ($avg_val != 0) {
            $variance = 0;
            foreach ($values as $v) {
                $variance += pow($v - $avg_val, 2);
            }
            $std_dev = sqrt($variance / $count);
            $cv = ($std_dev / abs($avg_val)) * 100;

            if ($cv > 20) {
                $insights[] = array(
                    'severity' => 'warning',
                    'icon'     => 'fas fa-wave-square',
                    'message'  => 'High variability detected (CV: ' . round($cv, 1) . '%). ' . $label . ' fluctuated significantly, suggesting unstable conditions.',
                    'category' => 'variability',
                    'source'   => 'rule'
                );
            } else if ($cv > 10) {
                $insights[] = array(
                    'severity' => 'info',
                    'icon'     => 'fas fa-wave-square',
                    'message'  => 'Moderate variability observed (CV: ' . round($cv, 1) . '%). Some fluctuation in ' . strtolower($label) . ' readings.',
                    'category' => 'variability',
                    'source'   => 'rule'
                );
            } else {
                $insights[] = array(
                    'severity' => 'good',
                    'icon'     => 'fas fa-check-circle',
                    'message'  => 'Low variability (CV: ' . round($cv, 1) . '%). ' . $label . ' readings are consistent and stable.',
                    'category' => 'variability',
                    'source'   => 'rule'
                );
            }
        }

        // 4. Peak analysis
        $min_idx = array_search($min_val, $values);
        $max_idx = array_search($max_val, $values);
        $min_time = isset($timestamps[$min_idx]) ? $timestamps[$min_idx] : 'unknown';
        $max_time = isset($timestamps[$max_idx]) ? $timestamps[$max_idx] : 'unknown';

        $insights[] = array(
            'severity' => 'info',
            'icon'     => 'fas fa-mountain',
            'message'  => 'Peak of ' . round($max_val, 1) . $unit . ' occurred at ' . substr($max_time, 5) . '. Lowest of ' . round($min_val, 1) . $unit . ' at ' . substr($min_time, 5) . '.',
            'category' => 'peaks',
            'source'   => 'rule'
        );

        // 5. Threshold analysis
        $thresholds = array(
            'temperature' => array('warn' => 30, 'crit' => 35, 'low_warn' => 16, 'low_crit' => 10),
            'humidity'    => array('warn' => 70, 'crit' => 80, 'low_warn' => 30, 'low_crit' => 20),
            'gas'         => array('warn' => 35, 'crit' => 55),
            'co'          => array('warn' => 9, 'crit' => 35)
        );

        if (isset($thresholds[$sensor_type])) {
            $th = $thresholds[$sensor_type];
            $above_warn = 0;
            $above_crit = 0;
            $below_warn = 0;

            foreach ($values as $v) {
                if (isset($th['crit']) && $v >= $th['crit']) $above_crit++;
                else if (isset($th['warn']) && $v >= $th['warn']) $above_warn++;
                if (isset($th['low_warn']) && $v <= $th['low_warn']) $below_warn++;
            }

            if ($above_crit > 0) {
                $pct_crit = round(($above_crit / $count) * 100, 1);
                $insights[] = array(
                    'severity' => 'critical',
                    'icon'     => 'fas fa-exclamation-circle',
                    'message'  => $pct_crit . '% of readings (' . $above_crit . '/' . $count . ') exceeded the critical threshold (' . $th['crit'] . $unit . ').',
                    'category' => 'threshold',
                    'source'   => 'rule'
                );
            }
            if ($above_warn > 0) {
                $pct_warn = round(($above_warn / $count) * 100, 1);
                $insights[] = array(
                    'severity' => 'warning',
                    'icon'     => 'fas fa-exclamation-triangle',
                    'message'  => $pct_warn . '% of readings (' . $above_warn . '/' . $count . ') exceeded the warning threshold (' . $th['warn'] . $unit . ').',
                    'category' => 'threshold',
                    'source'   => 'rule'
                );
            }
            if ($below_warn > 0 && isset($th['low_warn'])) {
                $pct_low = round(($below_warn / $count) * 100, 1);
                $insights[] = array(
                    'severity' => 'warning',
                    'icon'     => 'fas fa-temperature-low',
                    'message'  => $pct_low . '% of readings fell below the low threshold (' . $th['low_warn'] . $unit . ').',
                    'category' => 'threshold',
                    'source'   => 'rule'
                );
            }
            if ($above_crit === 0 && $above_warn === 0 && $below_warn === 0) {
                $insights[] = array(
                    'severity' => 'good',
                    'icon'     => 'fas fa-shield-alt',
                    'message'  => 'All readings within safe thresholds. No alarm conditions detected.',
                    'category' => 'threshold',
                    'source'   => 'rule'
                );
            }
        }

        // 6. Comfort zone analysis (temp & humidity only)
        $comfort = array(
            'temperature' => array('min' => 20, 'max' => 26, 'label' => 'thermal comfort zone (20–26°C)'),
            'humidity'    => array('min' => 40, 'max' => 60, 'label' => 'ideal humidity range (40–60%)')
        );

        if (isset($comfort[$sensor_type])) {
            $cz = $comfort[$sensor_type];
            $in_zone = 0;
            foreach ($values as $v) {
                if ($v >= $cz['min'] && $v <= $cz['max']) $in_zone++;
            }
            $pct_in = round(($in_zone / $count) * 100, 1);

            if ($pct_in >= 80) {
                $insights[] = array(
                    'severity' => 'good',
                    'icon'     => 'fas fa-smile',
                    'message'  => $pct_in . '% of the time within ' . $cz['label'] . '. Excellent conditions.',
                    'category' => 'comfort',
                    'source'   => 'rule'
                );
            } else if ($pct_in >= 50) {
                $insights[] = array(
                    'severity' => 'info',
                    'icon'     => 'fas fa-meh',
                    'message'  => 'Only ' . $pct_in . '% within ' . $cz['label'] . '. Consider HVAC adjustments.',
                    'category' => 'comfort',
                    'source'   => 'rule'
                );
            } else {
                $insights[] = array(
                    'severity' => 'warning',
                    'icon'     => 'fas fa-frown',
                    'message'  => 'Only ' . $pct_in . '% within ' . $cz['label'] . '. Conditions are mostly outside the comfort range.',
                    'category' => 'comfort',
                    'source'   => 'rule'
                );
            }
        }

        // 7. Data coverage & time span
        $first_time = $timestamps[0];
        $last_time = end($timestamps);
        $time_diff_sec = strtotime($last_time) - strtotime($first_time);
        $time_diff_hours = round($time_diff_sec / 3600, 1);
        $time_diff_days = round($time_diff_sec / 86400, 1);

        if ($time_diff_hours > 0) {
            $readings_per_hour = round($count / $time_diff_hours, 1);
            $span_text = $time_diff_days >= 1 ? round($time_diff_days, 1) . ' day(s)' : round($time_diff_hours, 1) . ' hour(s)';
            $insights[] = array(
                'severity' => 'info',
                'icon'     => 'fas fa-database',
                'message'  => 'Data spans ' . $span_text . ' with ' . $count . ' data points (~' . $readings_per_hour . ' readings/hour). Device: ' . $device->name . '.',
                'category' => 'coverage',
                'source'   => 'rule'
            );
        }

        // 8. Percentile analysis (median and 90th percentile)
        $sorted_vals = $values;
        sort($sorted_vals);
        $median = $sorted_vals[intval($count / 2)];
        $p90_idx = intval($count * 0.9);
        $p10_idx = intval($count * 0.1);
        $p90 = $sorted_vals[min($p90_idx, $count - 1)];
        $p10 = $sorted_vals[max($p10_idx, 0)];

        $insights[] = array(
            'severity' => 'info',
            'icon'     => 'fas fa-percentage',
            'message'  => $label . ' — Median: ' . round($median, 1) . $unit . ', 10th percentile: ' . round($p10, 1) . $unit . ', 90th percentile: ' . round($p90, 1) . $unit . '. Most readings cluster around ' . round($median, 1) . $unit . '.',
            'category' => 'statistics',
            'source'   => 'rule'
        );

        // 9. Hourly pattern analysis — find peak and quiet hours
        $hourly_data = array();
        foreach ($data as $row) {
            $hour = date('G', strtotime($row->time_bucket));
            if (!isset($hourly_data[$hour])) {
                $hourly_data[$hour] = array('sum' => 0, 'count' => 0);
            }
            $hourly_data[$hour]['sum'] += floatval($row->avg_value);
            $hourly_data[$hour]['count']++;
        }

        if (count($hourly_data) >= 4) {
            $hourly_avg = array();
            foreach ($hourly_data as $h => $d) {
                $hourly_avg[$h] = $d['sum'] / $d['count'];
            }
            $peak_hour = array_keys($hourly_avg, max($hourly_avg))[0];
            $low_hour = array_keys($hourly_avg, min($hourly_avg))[0];
            $peak_formatted = sprintf('%02d:00', $peak_hour);
            $low_formatted = sprintf('%02d:00', $low_hour);

            $insights[] = array(
                'severity' => 'info',
                'icon'     => 'fas fa-clock',
                'message'  => $label . ' peaks around ' . $peak_formatted . ' (avg ' . round(max($hourly_avg), 1) . $unit . ') and is lowest around ' . $low_formatted . ' (avg ' . round(min($hourly_avg), 1) . $unit . ').',
                'category' => 'pattern',
                'source'   => 'rule'
            );
        }

        // 10. Rate of change — find the fastest rising/dropping period
        if ($count >= 3) {
            $max_rise = 0;
            $max_drop = 0;
            $rise_time = '';
            $drop_time = '';
            for ($i = 1; $i < $count; $i++) {
                $diff = $values[$i] - $values[$i - 1];
                if ($diff > $max_rise) {
                    $max_rise = $diff;
                    $rise_time = isset($timestamps[$i]) ? $timestamps[$i] : '';
                }
                if ($diff < $max_drop) {
                    $max_drop = $diff;
                    $drop_time = isset($timestamps[$i]) ? $timestamps[$i] : '';
                }
            }

            if ($max_rise > 0) {
                $sev_rise = ($sensor_type === 'gas' && $max_rise > 20) || ($sensor_type === 'co' && $max_rise > 5) ? 'warning' : 'info';
                $insights[] = array(
                    'severity' => $sev_rise,
                    'icon'     => 'fas fa-bolt',
                    'message'  => 'Largest spike: +' . round($max_rise, 1) . $unit . ' at ' . substr($rise_time, 5) . '. ' . ($sev_rise === 'warning' ? 'This rapid increase may indicate a pollution event or source activation.' : 'Normal fluctuation.'),
                    'category' => 'rate',
                    'source'   => 'rule'
                );
            }

            if ($max_drop < 0) {
                $insights[] = array(
                    'severity' => 'info',
                    'icon'     => 'fas fa-arrow-down',
                    'message'  => 'Largest drop: ' . round($max_drop, 1) . $unit . ' at ' . substr($drop_time, 5) . '. May indicate ventilation improvement or air purifier activation.',
                    'category' => 'rate',
                    'source'   => 'rule'
                );
            }
        }

        // 11. Consecutive threshold breach duration (for gas/co)
        if (in_array($sensor_type, array('gas', 'co'))) {
            $warn_thresh = ($sensor_type === 'gas') ? 35 : 9;
            $max_streak = 0;
            $current_streak = 0;
            $streak_start = '';

            foreach ($data as $idx => $row) {
                if (floatval($row->avg_value) > $warn_thresh) {
                    if ($current_streak === 0) $streak_start = $row->time_bucket;
                    $current_streak++;
                    if ($current_streak > $max_streak) $max_streak = $current_streak;
                } else {
                    $current_streak = 0;
                }
            }

            if ($max_streak >= 3) {
                $insights[] = array(
                    'severity' => 'warning',
                    'icon'     => 'fas fa-hourglass-half',
                    'message'  => $label . ' exceeded ' . $warn_thresh . $unit . ' for ' . $max_streak . ' consecutive readings starting at ' . substr($streak_start, 5) . '. Prolonged exposure may pose health risks.',
                    'category' => 'duration',
                    'source'   => 'rule'
                );
            }
        }

        // 12. PM2.5 health impact based on WHO guidelines
        if ($sensor_type === 'gas') {
            $who_24h = 15; // WHO 24-hour guideline: 15 µg/m³
            $above_who = 0;
            foreach ($values as $v) {
                if ($v > $who_24h) $above_who++;
            }
            $pct_who = round(($above_who / $count) * 100, 1);

            if ($pct_who > 0) {
                $sev = $pct_who > 75 ? 'critical' : ($pct_who > 40 ? 'warning' : 'info');
                $insights[] = array(
                    'severity' => $sev,
                    'icon'     => 'fas fa-lungs',
                    'message'  => $pct_who . '% of PM2.5 readings exceeded the WHO 24-hour guideline of ' . $who_24h . 'µg/m³. Average exposure: ' . round($avg_val, 1) . 'µg/m³. ' . ($avg_val > 55 ? 'Consider using HEPA air purifiers and minimizing outdoor air intake.' : ($avg_val > 35 ? 'Sensitive individuals should take precautions.' : 'Generally manageable but monitor closely.')),
                    'category' => 'health',
                    'source'   => 'rule'
                );
            } else {
                $insights[] = array(
                    'severity' => 'good',
                    'icon'     => 'fas fa-heartbeat',
                    'message'  => 'All PM2.5 readings are within the WHO 24-hour guideline (' . $who_24h . 'µg/m³). Air quality is safe for all individuals.',
                    'category' => 'health',
                    'source'   => 'rule'
                );
            }
        }

        // 13. CO health impact based on EPA guidelines
        if ($sensor_type === 'co') {
            $epa_8h = 9; // EPA 8-hour average: 9 ppm
            $above_epa = 0;
            foreach ($values as $v) {
                if ($v > $epa_8h) $above_epa++;
            }
            $pct_epa = round(($above_epa / $count) * 100, 1);

            if ($pct_epa > 0) {
                $sev = $avg_val > 35 ? 'critical' : ($pct_epa > 30 ? 'warning' : 'info');
                $insights[] = array(
                    'severity' => $sev,
                    'icon'     => 'fas fa-skull-crossbones',
                    'message'  => $pct_epa . '% of CO readings exceeded the EPA 8-hour limit of ' . $epa_8h . 'ppm. Peak: ' . round($max_val, 1) . 'ppm. ' . ($avg_val > 35 ? 'DANGER: Evacuate and ventilate immediately. Check for combustion sources.' : 'Check for potential sources: gas stoves, heaters, or vehicle exhaust.'),
                    'category' => 'health',
                    'source'   => 'rule'
                );
            } else {
                $insights[] = array(
                    'severity' => 'good',
                    'icon'     => 'fas fa-shield-alt',
                    'message'  => 'All CO readings are within the EPA 8-hour limit (' . $epa_8h . 'ppm). No carbon monoxide hazard detected.',
                    'category' => 'health',
                    'source'   => 'rule'
                );
            }
        }

        // 14. Day vs Night comparison (if data spans enough time)
        if ($time_diff_hours >= 12 && count($hourly_data) >= 6) {
            $day_vals = array();
            $night_vals = array();
            foreach ($data as $row) {
                $h = (int)date('G', strtotime($row->time_bucket));
                if ($h >= 6 && $h < 22) {
                    $day_vals[] = floatval($row->avg_value);
                } else {
                    $night_vals[] = floatval($row->avg_value);
                }
            }
            if (count($day_vals) > 0 && count($night_vals) > 0) {
                $day_avg = array_sum($day_vals) / count($day_vals);
                $night_avg = array_sum($night_vals) / count($night_vals);
                $diff_pct = ($day_avg > 0) ? round(abs($night_avg - $day_avg) / $day_avg * 100, 1) : 0;

                if ($diff_pct > 10) {
                    $higher = $day_avg > $night_avg ? 'daytime' : 'nighttime';
                    $insights[] = array(
                        'severity' => 'info',
                        'icon'     => 'fas fa-adjust',
                        'message'  => $label . ' is ' . $diff_pct . '% higher during ' . $higher . '. Day avg: ' . round($day_avg, 1) . $unit . ', Night avg: ' . round($night_avg, 1) . $unit . '.',
                        'category' => 'pattern',
                        'source'   => 'rule'
                    );
                }
            }
        }

        return $insights;
    }

    // ==================== ANALYTICS ====================

    /**
     * GET /api/analytics?device_id=&from=&to=
     * Returns comprehensive statistics per sensor for the analytics page
     */
    public function analytics() {
        if (!$this->session->userdata('logged_in')) {
            $this->_json_response(array('error' => 'Unauthorized'), 401);
            return;
        }

        $device_id = $this->input->get('device_id');
        $from = $this->input->get('from') ?: date('Y-m-d H:i:s', strtotime('-24 hours'));
        $to = $this->input->get('to') ?: date('Y-m-d H:i:s');

        if (!$device_id) {
            $this->_json_response(array('error' => 'device_id is required'), 400);
            return;
        }

        $device = $this->device_model->get_device($device_id);
        if (!$device) {
            $this->_json_response(array('error' => 'Device not found'), 404);
            return;
        }

        $this->load->model('report_model');
        $sensor_types = array('temperature', 'humidity', 'gas');
        $analytics = array();

        foreach ($sensor_types as $st) {
            $data = $this->report_model->get_trend_data($device_id, $st, $from, $to);
            if (empty($data)) continue;

            $values = array();
            $hourly_buckets = array();
            foreach ($data as $row) {
                $v = floatval($row->avg_value);
                $values[] = $v;
                $hour = intval(date('G', strtotime($row->time_bucket)));
                if (!isset($hourly_buckets[$hour])) $hourly_buckets[$hour] = array();
                $hourly_buckets[$hour][] = $v;
            }

            $count = count($values);
            $min_val = min($values);
            $max_val = max($values);
            $avg_val = array_sum($values) / $count;

            // Standard deviation
            $variance = 0;
            foreach ($values as $v) { $variance += pow($v - $avg_val, 2); }
            $std_dev = sqrt($variance / $count);

            // Median
            $sorted = $values;
            sort($sorted);
            $mid = intval($count / 2);
            $median = ($count % 2 === 0) ? ($sorted[$mid - 1] + $sorted[$mid]) / 2 : $sorted[$mid];

            // Percentiles (25th, 75th)
            $p25_idx = intval($count * 0.25);
            $p75_idx = intval($count * 0.75);
            $p25 = $sorted[min($p25_idx, $count - 1)];
            $p75 = $sorted[min($p75_idx, $count - 1)];

            // Distribution (histogram bins)
            $bin_count = 10;
            $bin_width = ($max_val - $min_val) / $bin_count;
            $bins = array();
            if ($bin_width > 0) {
                for ($i = 0; $i < $bin_count; $i++) {
                    $bin_start = $min_val + $i * $bin_width;
                    $bin_end = $bin_start + $bin_width;
                    $c = 0;
                    foreach ($values as $v) {
                        if ($v >= $bin_start && ($i === $bin_count - 1 ? $v <= $bin_end : $v < $bin_end)) $c++;
                    }
                    $bins[] = array('label' => round($bin_start, 1) . '-' . round($bin_end, 1), 'count' => $c);
                }
            }

            // Hourly averages (0-23)
            $hourly_avgs = array();
            for ($h = 0; $h < 24; $h++) {
                if (isset($hourly_buckets[$h]) && count($hourly_buckets[$h]) > 0) {
                    $hourly_avgs[$h] = round(array_sum($hourly_buckets[$h]) / count($hourly_buckets[$h]), 2);
                } else {
                    $hourly_avgs[$h] = null;
                }
            }

            $analytics[$st] = array(
                'count'    => $count,
                'min'      => round($min_val, 2),
                'max'      => round($max_val, 2),
                'mean'     => round($avg_val, 2),
                'median'   => round($median, 2),
                'std_dev'  => round($std_dev, 2),
                'p25'      => round($p25, 2),
                'p75'      => round($p75, 2),
                'distribution' => $bins,
                'hourly_avg'   => $hourly_avgs
            );
        }

        // Correlations between sensor pairs
        $correlations = array();
        $sensor_keys = array_keys($analytics);
        for ($i = 0; $i < count($sensor_keys); $i++) {
            for ($j = $i + 1; $j < count($sensor_keys); $j++) {
                $s1 = $sensor_keys[$i];
                $s2 = $sensor_keys[$j];
                $d1 = $this->report_model->get_trend_data($device_id, $s1, $from, $to);
                $d2 = $this->report_model->get_trend_data($device_id, $s2, $from, $to);

                // Align by time bucket
                $map2 = array();
                foreach ($d2 as $r) { $map2[$r->time_bucket] = floatval($r->avg_value); }

                $pairs = array();
                foreach ($d1 as $r) {
                    if (isset($map2[$r->time_bucket])) {
                        $pairs[] = array(floatval($r->avg_value), $map2[$r->time_bucket]);
                    }
                }

                if (count($pairs) > 5) {
                    $corr = $this->_pearson_correlation($pairs);
                    $correlations[] = array('pair' => $s1 . ' vs ' . $s2, 'r' => round($corr, 3));
                }
            }
        }

        $this->_json_response(array(
            'device'       => array('id' => $device->id, 'name' => $device->name, 'location' => $device->location),
            'period'       => array('from' => $from, 'to' => $to),
            'analytics'    => $analytics,
            'correlations' => $correlations
        ));
    }

    private function _pearson_correlation($pairs) {
        $n = count($pairs);
        $sum_x = $sum_y = $sum_xy = $sum_x2 = $sum_y2 = 0;
        foreach ($pairs as $p) {
            $sum_x += $p[0];
            $sum_y += $p[1];
            $sum_xy += $p[0] * $p[1];
            $sum_x2 += $p[0] * $p[0];
            $sum_y2 += $p[1] * $p[1];
        }
        $denom = sqrt(($n * $sum_x2 - $sum_x * $sum_x) * ($n * $sum_y2 - $sum_y * $sum_y));
        if ($denom == 0) return 0;
        return ($n * $sum_xy - $sum_x * $sum_y) / $denom;
    }

    // ==================== PREDICTIVE ====================

    /**
     * GET /api/predictive?device_id=
     * Returns ML forecasts + risk assessment for all sensors
     */
    public function predictive() {
        if (!$this->session->userdata('logged_in')) {
            $this->_json_response(array('error' => 'Unauthorized'), 401);
            return;
        }

        $device_id = $this->input->get('device_id');
        if (!$device_id) {
            $this->_json_response(array('error' => 'device_id is required'), 400);
            return;
        }

        $device = $this->device_model->get_device($device_id);
        if (!$device) {
            $this->_json_response(array('error' => 'Device not found'), 404);
            return;
        }

        $this->load->library('ml_client');
        $ml_available = $this->ml_client->is_available();

        $sensor_types = array('temperature', 'humidity', 'gas', 'co');
        $forecasts = array();
        $anomalies = array();
        $risks = array();

        // Get latest readings for anomaly check
        $latest = $this->reading_model->get_latest_readings($device_id);
        $latest_map = array();
        foreach ($latest as $r) {
            $latest_map[$r->sensor_type] = floatval($r->value);
        }

        // Compute CO from gas, temperature, humidity
        if (isset($latest_map['gas'], $latest_map['temperature'], $latest_map['humidity'])) {
            $latest_map['co'] = $this->co_calculator->calculate_co(
                $latest_map['gas'],
                $latest_map['temperature'],
                $latest_map['humidity']
            );
        }

        // Thresholds for risk assessment
        $thresholds = array(
            'temperature' => array('warn' => 30, 'crit' => 35),
            'humidity'    => array('warn' => 70, 'crit' => 80),
            'gas'         => array('warn' => 35, 'crit' => 55),
            'co'          => array('warn' => 9, 'crit' => 35)
        );

        foreach ($sensor_types as $st) {
            // Forecast
            if ($ml_available) {
                $fc = $this->ml_client->get_forecast($device_id, $st);
                if ($fc && isset($fc['predictions']) && $fc['status'] === 'ok') {
                    $forecasts[$st] = $fc;

                    // Risk: check if any predicted value will exceed threshold
                    $risk = 'low';
                    if (isset($thresholds[$st])) {
                        foreach ($fc['predictions'] as $p) {
                            if ($p['predicted_value'] >= $thresholds[$st]['crit']) { $risk = 'critical'; break; }
                            if ($p['predicted_value'] >= $thresholds[$st]['warn']) { $risk = 'warning'; }
                        }
                    }
                    $risks[$st] = $risk;
                }

                // Anomaly check
                if (isset($latest_map[$st])) {
                    $anom = $this->ml_client->check_anomaly($device_id, $st, $latest_map[$st]);
                    if ($anom && isset($anom['is_anomaly'])) {
                        $anomalies[$st] = $anom;
                    }
                }
            } else {
                // Fallback: simple risk from current values
                if (isset($latest_map[$st]) && isset($thresholds[$st])) {
                    $v = $latest_map[$st];
                    $risk = 'low';
                    if ($v >= $thresholds[$st]['crit']) $risk = 'critical';
                    else if ($v >= $thresholds[$st]['warn']) $risk = 'warning';
                    $risks[$st] = $risk;
                }
            }
        }

        // Recommendations
        $recommendations = array();
        foreach ($risks as $st => $risk_level) {
            if ($risk_level === 'critical') {
                $msg = $this->_predictive_recommendation($st, 'critical');
                if ($msg) $recommendations[] = array('sensor' => $st, 'severity' => 'critical', 'message' => $msg);
            } else if ($risk_level === 'warning') {
                $msg = $this->_predictive_recommendation($st, 'warning');
                if ($msg) $recommendations[] = array('sensor' => $st, 'severity' => 'warning', 'message' => $msg);
            }
        }

        $this->_json_response(array(
            'device'          => array('id' => $device->id, 'name' => $device->name, 'location' => $device->location),
            'ml_available'    => $ml_available,
            'forecasts'       => $forecasts,
            'anomalies'       => $anomalies,
            'risks'           => $risks,
            'current_values'  => $latest_map,
            'recommendations' => $recommendations,
            'timestamp'       => date('Y-m-d H:i:s')
        ));
    }

    private function _predictive_recommendation($sensor, $level) {
        $msgs = array(
            'temperature' => array(
                'critical' => 'Temperature is predicted to reach critical levels. Activate cooling systems immediately and check HVAC.',
                'warning'  => 'Temperature is trending upward. Consider pre-emptive ventilation or AC adjustment.'
            ),
            'humidity' => array(
                'critical' => 'Humidity forecast shows critical levels. Activate dehumidification and check for leaks.',
                'warning'  => 'Humidity is rising. Turn on exhaust fans to prevent condensation.'
            ),
            'gas' => array(
                'critical' => 'PM2.5 is forecast to reach unhealthy levels. Activate air purifiers and seal windows.',
                'warning'  => 'PM2.5 is rising. Turn on air filtration as a preventive measure.'
            ),
            'co' => array(
                'critical' => 'CO levels are dangerously high. Evacuate immediately, ventilate the area, and check for combustion sources.',
                'warning'  => 'CO levels are elevated. Increase ventilation and inspect potential sources (gas appliances, vehicles).'
            )
        );
        return isset($msgs[$sensor][$level]) ? $msgs[$sensor][$level] : null;
    }

    // ==================== HELPERS ====================

    private function _json_response($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: X-Device-Key, Content-Type');
        echo json_encode($data);
        exit;
    }
}
