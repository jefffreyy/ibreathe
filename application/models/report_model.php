<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_trend_data($device_id, $sensor_type, $from, $to, $interval = '5 MINUTE') {
        $sql = "SELECT
                    DATE_FORMAT(recorded_at, '%Y-%m-%d %H:%i:00') as time_bucket,
                    AVG(value) as avg_value,
                    MIN(value) as min_value,
                    MAX(value) as max_value
                FROM tbl_scada_readings
                WHERE device_id = ? AND sensor_type = ?
                AND recorded_at >= ? AND recorded_at <= ?
                GROUP BY time_bucket
                ORDER BY time_bucket ASC";
        return $this->db->query($sql, array($device_id, $sensor_type, $from, $to))->result();
    }

    public function get_daily_summary($date = null) {
        if (!$date) $date = date('Y-m-d');
        $sql = "SELECT
                    d.name as device_name,
                    r.device_id,
                    r.sensor_type,
                    MIN(r.value) as min_val,
                    MAX(r.value) as max_val,
                    ROUND(AVG(r.value), 2) as avg_val,
                    COUNT(r.id) as reading_count
                FROM tbl_scada_readings r
                JOIN tbl_scada_devices d ON d.id = r.device_id
                WHERE DATE(r.recorded_at) = ?
                GROUP BY r.device_id, r.sensor_type, d.name
                ORDER BY d.name, r.sensor_type";
        return $this->db->query($sql, array($date))->result();
    }
    
    public function get_monthly_summary($month = null) {
        if (!$month) $month = date('Y-m');
        
        // Extract year and month from the input
        $year = substr($month, 0, 4);
        $month_num = substr($month, 5, 2);
        
        $sql = "SELECT
                    d.name as device_name,
                    r.device_id,
                    r.sensor_type,
                    MIN(r.value) as min_val,
                    MAX(r.value) as max_val,
                    ROUND(AVG(r.value), 2) as avg_val,
                    COUNT(r.id) as reading_count
                FROM tbl_scada_readings r
                JOIN tbl_scada_devices d ON d.id = r.device_id
                WHERE YEAR(r.recorded_at) = ? AND MONTH(r.recorded_at) = ?
                GROUP BY r.device_id, r.sensor_type, d.name
                ORDER BY d.name, r.sensor_type";
        
        return $this->db->query($sql, array($year, $month_num))->result();
    }

    public function get_weekly_summary($start_date = null) {
        if (!$start_date) $start_date = date('Y-m-d', strtotime('-7 days'));
        $end_date = date('Y-m-d', strtotime($start_date . ' +7 days'));
        $sql = "SELECT
                    d.name as device_name,
                    r.device_id,
                    r.sensor_type,
                    DATE(r.recorded_at) as reading_date,
                    MIN(r.value) as min_val,
                    MAX(r.value) as max_val,
                    ROUND(AVG(r.value), 2) as avg_val,
                    COUNT(r.id) as reading_count
                FROM tbl_scada_readings r
                JOIN tbl_scada_devices d ON d.id = r.device_id
                WHERE DATE(r.recorded_at) >= ? AND DATE(r.recorded_at) < ?
                GROUP BY r.device_id, r.sensor_type, reading_date, d.name
                ORDER BY reading_date, d.name, r.sensor_type";
        return $this->db->query($sql, array($start_date, $end_date))->result();
    }

    // public function get_monthly_summary($year = null, $month = null) {
    //     if (!$year) $year = date('Y');
    //     if (!$month) $month = date('m');
    //     $sql = "SELECT
    //                 d.name as device_name,
    //                 r.device_id,
    //                 r.sensor_type,
    //                 MIN(r.value) as min_val,
    //                 MAX(r.value) as max_val,
    //                 ROUND(AVG(r.value), 2) as avg_val,
    //                 COUNT(r.id) as reading_count
    //             FROM tbl_scada_readings r
    //             JOIN tbl_scada_devices d ON d.id = r.device_id
    //             WHERE YEAR(r.recorded_at) = ? AND MONTH(r.recorded_at) = ?
    //             GROUP BY r.device_id, r.sensor_type, d.name
    //             ORDER BY d.name, r.sensor_type";
    //     return $this->db->query($sql, array($year, $month))->result();
    // }

    public function get_statistics($device_id = null, $from = null, $to = null) {
        $this->db->select('device_id, sensor_type, MIN(value) as min_val, MAX(value) as max_val, ROUND(AVG(value),2) as avg_val, COUNT(*) as total_readings');
        if ($device_id) {
            $this->db->where('device_id', $device_id);
        }
        if ($from) {
            $this->db->where('recorded_at >=', $from);
        }
        if ($to) {
            $this->db->where('recorded_at <=', $to);
        }
        $this->db->group_by(array('device_id', 'sensor_type'));
        return $this->db->get('tbl_scada_readings')->result();
    }

    public function export_data($device_id, $sensor_type, $from, $to) {
        $this->db->select('tbl_scada_readings.*, tbl_scada_devices.name as device_name');
        $this->db->join('tbl_scada_devices', 'tbl_scada_devices.id = tbl_scada_readings.device_id');
        $this->db->where('tbl_scada_readings.device_id', $device_id);
        if ($sensor_type) {
            $this->db->where('tbl_scada_readings.sensor_type', $sensor_type);
        }
        $this->db->where('tbl_scada_readings.recorded_at >=', $from);
        $this->db->where('tbl_scada_readings.recorded_at <=', $to);
        $this->db->order_by('tbl_scada_readings.recorded_at', 'ASC');
        return $this->db->get('tbl_scada_readings')->result();
    }
}
