<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Alarm_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ==================== ALARM RULES ====================

    public function create_rule($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('tbl_scada_alarms', $data);
    }

    public function get_rules($active_only = false) {
        $this->db->select('tbl_scada_alarms.*, tbl_scada_devices.name as device_name');
        $this->db->join('tbl_scada_devices', 'tbl_scada_devices.id = tbl_scada_alarms.device_id', 'left');
        if ($active_only) {
            $this->db->where('tbl_scada_alarms.is_active', 1);
        }
        $this->db->order_by('tbl_scada_alarms.severity', 'DESC');
        return $this->db->get('tbl_scada_alarms')->result();
    }

    public function get_rule($id) {
        return $this->db->get_where('tbl_scada_alarms', array('id' => $id))->row();
    }

    public function update_rule($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tbl_scada_alarms', $data);
    }

    public function delete_rule($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_scada_alarms');
    }

    // ==================== ALARM CHECKING ====================

    public function check_thresholds($device_id, $sensor_type, $value) {
        $triggered = array();

        // Get active alarm rules for this sensor type (global or device-specific)
        $sql = "SELECT * FROM tbl_scada_alarms
                WHERE is_active = 1
                AND sensor_type = ?
                AND (device_id = ? OR device_id IS NULL)";
        $rules = $this->db->query($sql, array($sensor_type, $device_id))->result();

        foreach ($rules as $rule) {
            $should_trigger = false;
            switch ($rule->condition_type) {
                case 'above':
                    $should_trigger = ($value > $rule->threshold);
                    break;
                case 'below':
                    $should_trigger = ($value < $rule->threshold);
                    break;
                case 'equal':
                    $should_trigger = (abs($value - $rule->threshold) < 0.01);
                    break;
            }

            if ($should_trigger) {
                // Check cooldown
                if ($rule->last_triggered) {
                    $cooldown_end = strtotime($rule->last_triggered) + ($rule->cooldown_minutes * 60);
                    if (time() < $cooldown_end) {
                        continue;
                    }
                }

                // Create alarm event
                $event_data = array(
                    'alarm_id'    => $rule->id,
                    'device_id'   => $device_id,
                    'sensor_type' => $sensor_type,
                    'value'       => $value,
                    'severity'    => $rule->severity,
                    'message'     => $rule->message,
                    'triggered_at'=> date('Y-m-d H:i:s')
                );
                $this->db->insert('tbl_scada_alarm_events', $event_data);

                // Update last_triggered
                $this->db->where('id', $rule->id);
                $this->db->update('tbl_scada_alarms', array('last_triggered' => date('Y-m-d H:i:s')));

                $triggered[] = $event_data;
            }
        }

        return $triggered;
    }

    // ==================== ALARM EVENTS ====================

    public function get_active_events($limit = 50) {
        $this->db->select('tbl_scada_alarm_events.*, tbl_scada_devices.name as device_name, tbl_scada_alarms.name as alarm_name');
        $this->db->join('tbl_scada_devices', 'tbl_scada_devices.id = tbl_scada_alarm_events.device_id', 'left');
        $this->db->join('tbl_scada_alarms', 'tbl_scada_alarms.id = tbl_scada_alarm_events.alarm_id', 'left');
        $this->db->where('tbl_scada_alarm_events.acknowledged', 0);
        $this->db->order_by('tbl_scada_alarm_events.triggered_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('tbl_scada_alarm_events')->result();
    }

    public function get_alarm_history($limit = 100, $offset = 0, $filters = array()) {
        $this->db->select('tbl_scada_alarm_events.*, tbl_scada_devices.name as device_name, tbl_scada_alarms.name as alarm_name, u.username as ack_username');
        $this->db->join('tbl_scada_devices', 'tbl_scada_devices.id = tbl_scada_alarm_events.device_id', 'left');
        $this->db->join('tbl_scada_alarms', 'tbl_scada_alarms.id = tbl_scada_alarm_events.alarm_id', 'left');
        $this->db->join('tbl_scada_users u', 'u.id = tbl_scada_alarm_events.acknowledged_by', 'left');

        if (!empty($filters['severity'])) {
            $this->db->where('tbl_scada_alarm_events.severity', $filters['severity']);
        }
        if (!empty($filters['device_id'])) {
            $this->db->where('tbl_scada_alarm_events.device_id', $filters['device_id']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('tbl_scada_alarm_events.triggered_at >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('tbl_scada_alarm_events.triggered_at <=', $filters['date_to'] . ' 23:59:59');
        }

        $this->db->order_by('tbl_scada_alarm_events.triggered_at', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get('tbl_scada_alarm_events')->result();
    }

    public function acknowledge_event($event_id, $user_id) {
        $this->db->where('id', $event_id);
        return $this->db->update('tbl_scada_alarm_events', array(
            'acknowledged'    => 1,
            'acknowledged_by' => $user_id,
            'acknowledged_at' => date('Y-m-d H:i:s')
        ));
    }

    public function acknowledge_all($user_id) {
        $this->db->where('acknowledged', 0);
        return $this->db->update('tbl_scada_alarm_events', array(
            'acknowledged'    => 1,
            'acknowledged_by' => $user_id,
            'acknowledged_at' => date('Y-m-d H:i:s')
        ));
    }

    public function count_active_events() {
        $this->db->where('acknowledged', 0);
        return $this->db->count_all_results('tbl_scada_alarm_events');
    }

    public function count_alarm_history($filters = array()) {
        if (!empty($filters['severity'])) {
            $this->db->where('severity', $filters['severity']);
        }
        if (!empty($filters['device_id'])) {
            $this->db->where('device_id', $filters['device_id']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('triggered_at >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('triggered_at <=', $filters['date_to'] . ' 23:59:59');
        }
        return $this->db->count_all_results('tbl_scada_alarm_events');
    }
}
