<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reading_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function insert_reading($data) {
        $data['recorded_at'] = isset($data['recorded_at']) ? $data['recorded_at'] : date('Y-m-d H:i:s');
        return $this->db->insert('tbl_scada_readings', $data);
    }

    public function insert_batch_readings($readings) {
        if (!empty($readings)) {
            return $this->db->insert_batch('tbl_scada_readings', $readings);
        }
        return false;
    }

    public function get_latest_readings($device_id = null) {
        if ($device_id) {
            $sql = "SELECT r.* FROM tbl_scada_readings r
                    INNER JOIN (
                        SELECT device_id, sensor_type, MAX(recorded_at) as max_time
                        FROM tbl_scada_readings
                        WHERE device_id = ?
                        GROUP BY device_id, sensor_type
                    ) latest ON r.device_id = latest.device_id
                        AND r.sensor_type = latest.sensor_type
                        AND r.recorded_at = latest.max_time
                    WHERE r.device_id = ?";
            return $this->db->query($sql, array($device_id, $device_id))->result();
        } else {
            $sql = "SELECT r.* FROM tbl_scada_readings r
                    INNER JOIN (
                        SELECT device_id, sensor_type, MAX(recorded_at) as max_time
                        FROM tbl_scada_readings
                        GROUP BY device_id, sensor_type
                    ) latest ON r.device_id = latest.device_id
                        AND r.sensor_type = latest.sensor_type
                        AND r.recorded_at = latest.max_time";
            return $this->db->query($sql)->result();
        }
    }
    
    public function get_last_reading($device_id) {
        $this->db->where('device_id', $device_id);
        $this->db->order_by('recorded_at', 'DESC');
        $this->db->limit(1);
        return $this->db->get('tbl_scada_readings')->row(); // .row() hindi .result()
    }

    public function get_device_readings($device_id, $sensor_type = null, $limit = 100) {
        $this->db->where('device_id', $device_id);
        if ($sensor_type) {
            $this->db->where('sensor_type', $sensor_type);
        }
        $this->db->order_by('recorded_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('tbl_scada_readings')->result();
    }

    public function get_history($device_id, $sensor_type, $from, $to) {
        $this->db->where('device_id', $device_id);
        $this->db->where('sensor_type', $sensor_type);
        $this->db->where('recorded_at >=', $from);
        $this->db->where('recorded_at <=', $to);
        $this->db->order_by('recorded_at', 'ASC');
        return $this->db->get('tbl_scada_readings')->result();
    }

    public function get_dashboard_data() {
        $data = array();

        // Latest readings per device per sensor
        $data['readings'] = $this->get_latest_readings();

        // Last hour trend data (grouped by 1-minute intervals)
        $one_hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $sql = "SELECT device_id, sensor_type,
                    DATE_FORMAT(recorded_at, '%Y-%m-%d %H:%i:00') as time_bucket,
                    AVG(value) as avg_value
                FROM tbl_scada_readings
                WHERE recorded_at >= ?
                GROUP BY device_id, sensor_type, time_bucket
                ORDER BY time_bucket ASC";
        $data['trends'] = $this->db->query($sql, array($one_hour_ago))->result();

        return $data;
    }

    public function get_statistics($device_id, $sensor_type, $from, $to) {
        $sql = "SELECT
                    MIN(value) as min_val,
                    MAX(value) as max_val,
                    AVG(value) as avg_val,
                    COUNT(*) as reading_count
                FROM tbl_scada_readings
                WHERE device_id = ? AND sensor_type = ?
                AND recorded_at >= ? AND recorded_at <= ?";
        return $this->db->query($sql, array($device_id, $sensor_type, $from, $to))->row();
    }

    public function cleanup_old_readings($days = 90) {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $this->db->where('recorded_at <', $cutoff);
        return $this->db->delete('tbl_scada_readings');
    }

    public function get_reading_count($device_id = null) {
        if ($device_id) {
            $this->db->where('device_id', $device_id);
        }
        return $this->db->count_all_results('tbl_scada_readings');
    }
}
