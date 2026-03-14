<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('report_model');
        $this->load->model('device_model');
        $this->load->model('alarm_model');
        $this->load->model('scada_model');
        $this->load->helper('scada');
        if (!$this->session->userdata('logged_in')) redirect('scada/');
    }

    public function trends() {
        $data['devices'] = $this->device_model->get_devices();
        $data['active_alarms'] = $this->alarm_model->count_active_events();
        $data['page_title'] = 'Reports - Trends';
        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('reports/trends', $data);
        $this->load->view('templates/scada_footer', $data);
    }

    public function summary() {
        $report_type = $this->input->get('report_type') ?: 'daily';
        $date = $this->input->get('date') ?: date('Y-m-d');
        $month = $this->input->get('month') ?: date('Y-m');
        
        $data['report_type'] = $report_type;
        $data['selected_date'] = $date;
        $data['selected_month'] = $month;
        
        if ($report_type == 'daily') {
            $data['summary'] = $this->report_model->get_daily_summary($date);
        } else {
            $data['summary'] = $this->report_model->get_monthly_summary($month);
        }
        
        $data['devices'] = $this->device_model->get_devices();
        $data['active_alarms'] = $this->alarm_model->count_active_events();
        $data['page_title'] = 'Reports - Summary';
        
        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('reports/summary', $data);
        $this->load->view('templates/scada_footer', $data);
    }

    public function export() {
        $device_id = $this->input->get('device_id');
        $sensor_type = $this->input->get('sensor_type');
        $from = $this->input->get('from') ?: date('Y-m-d', strtotime('-7 days'));
        $to = $this->input->get('to') ?: date('Y-m-d');

        if (!$device_id) {
            $this->session->set_flashdata('error', 'Please select a device.');
            redirect('reports/summary');
        }

        $data = $this->report_model->export_data($device_id, $sensor_type, $from, $to . ' 23:59:59');

        // Generate CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="scada_export_' . date('Ymd') . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, array('Device', 'Sensor Type', 'Value', 'Recorded At'));
        foreach ($data as $row) {
            fputcsv($output, array($row->device_name, $row->sensor_type, $row->value, $row->recorded_at));
        }
        fclose($output);
        exit;
    }

    /**
     * Excel export using SpreadsheetML XML (zero dependencies)
     * GET /reports/export_excel?device_id=&sensor_type=&from=&to=
     * Also: GET /reports/export_excel?mode=summary&date=YYYY-MM-DD
     */
    public function export_excel() {
        $mode = $this->input->get('mode') ?: 'raw';

        if ($mode === 'summary') {
            $date = $this->input->get('date') ?: date('Y-m-d');
            $summary = $this->report_model->get_daily_summary($date);
            $filename = 'iBreathe_Summary_' . $date . '.xls';

            $xml = $this->_excel_header('Daily Summary - ' . $date);
            // Header row
            $xml .= '<Row>';
            foreach (array('Device', 'Sensor', 'Min', 'Max', 'Average', 'Readings') as $h) {
                $xml .= '<Cell ss:StyleID="header"><Data ss:Type="String">' . $this->_xml_esc($h) . '</Data></Cell>';
            }
            $xml .= '</Row>';
            // Data rows
            foreach ($summary as $s) {
                $xml .= '<Row>'
                    . '<Cell><Data ss:Type="String">' . $this->_xml_esc($s->device_name) . '</Data></Cell>'
                    . '<Cell><Data ss:Type="String">' . ucfirst($s->sensor_type) . '</Data></Cell>'
                    . '<Cell><Data ss:Type="Number">' . $s->min_val . '</Data></Cell>'
                    . '<Cell><Data ss:Type="Number">' . $s->max_val . '</Data></Cell>'
                    . '<Cell><Data ss:Type="Number">' . $s->avg_val . '</Data></Cell>'
                    . '<Cell><Data ss:Type="Number">' . $s->reading_count . '</Data></Cell>'
                    . '</Row>';
            }
            $xml .= $this->_excel_footer();
        } else {
            $device_id = $this->input->get('device_id');
            $sensor_type = $this->input->get('sensor_type');
            $from = $this->input->get('from') ?: date('Y-m-d', strtotime('-7 days'));
            $to = $this->input->get('to') ?: date('Y-m-d');

            if (!$device_id) {
                $this->session->set_flashdata('error', 'Please select a device.');
                redirect('reports/trends');
                return;
            }

            $data = $this->report_model->export_data($device_id, $sensor_type, $from, $to . ' 23:59:59');
            $filename = 'iBreathe_Data_' . date('Ymd') . '.xls';

            $xml = $this->_excel_header('Sensor Data Export');
            // Header row
            $xml .= '<Row>';
            foreach (array('Device', 'Sensor Type', 'Value', 'Recorded At') as $h) {
                $xml .= '<Cell ss:StyleID="header"><Data ss:Type="String">' . $this->_xml_esc($h) . '</Data></Cell>';
            }
            $xml .= '</Row>';
            // Data rows
            foreach ($data as $row) {
                $xml .= '<Row>'
                    . '<Cell><Data ss:Type="String">' . $this->_xml_esc($row->device_name) . '</Data></Cell>'
                    . '<Cell><Data ss:Type="String">' . ucfirst($row->sensor_type) . '</Data></Cell>'
                    . '<Cell><Data ss:Type="Number">' . $row->value . '</Data></Cell>'
                    . '<Cell><Data ss:Type="String">' . $row->recorded_at . '</Data></Cell>'
                    . '</Row>';
            }
            $xml .= $this->_excel_footer();
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        echo $xml;
        exit;
    }

    private function _excel_header($title) {
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<?mso-application progid="Excel.Sheet"?>' . "\n"
            . '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"'
            . ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n"
            . '<Styles>'
            . '<Style ss:ID="header">'
            . '<Font ss:Bold="1" ss:Size="11" ss:Color="#FFFFFF"/>'
            . '<Interior ss:Color="#4F46E5" ss:Pattern="Solid"/>'
            . '<Alignment ss:Horizontal="Center"/>'
            . '</Style>'
            . '</Styles>'
            . '<Worksheet ss:Name="' . $this->_xml_esc($title) . '">'
            . '<Table>' . "\n";
    }

    private function _excel_footer() {
        return '</Table></Worksheet></Workbook>';
    }

    private function _xml_esc($str) {
        return htmlspecialchars($str, ENT_XML1, 'UTF-8');
    }

    /**
     * Analytics page
     */
    public function analytics() {
        $data['devices'] = $this->device_model->get_devices();
        $data['active_alarms'] = $this->alarm_model->count_active_events();
        $data['page_title'] = 'Reports - Analytics';
        $data['page_js'] = 'analytics.js';
        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('reports/analytics', $data);
        $this->load->view('templates/scada_footer', $data);
    }

    /**
     * Predictive Analysis page
     */
    public function predictive() {
        $data['devices'] = $this->device_model->get_devices();
        $data['active_alarms'] = $this->alarm_model->count_active_events();
        $data['page_title'] = 'Reports - Predictive';
        $data['page_js'] = 'predictive.js';
        $this->load->view('templates/scada_header', $data);
        $this->load->view('templates/scada_sidebar', $data);
        $this->load->view('reports/predictive', $data);
        $this->load->view('templates/scada_footer', $data);
    }
}
