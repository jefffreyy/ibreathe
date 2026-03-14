<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class System_functions{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }
    
    function fomatName($lastName,$firstName,$midName){
      if(empty($midName)){
          return $lastName.','.$firstName;
      }
      return $lastName.','.$firstName.' '.$midName[0].'.';
    }
    
    function formatName2($lastName,$suffix,$firstName,$middleName){
        $fullName = '';
        if (!empty($lastName))$fullName=$lastName;
        if (!empty($suffix))$fullName=$fullName.' '.$suffix;
        if (!empty($firstName))$fullName=$fullName.', '.$firstName;
        if (!empty($middleName))$fullName=$fullName.' '.$middleName[0].'.';
        return $fullName; 
    }

    function getFileType($fileName){
        // Get the file extension
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
        // Define allowed file extensions and their corresponding types
        $typeMap = [
            'jpg' => 'Image',
            'jpeg' => 'Image',
            'png' => 'Image',
            'gif' => 'Image',
            'pdf' => 'PDF',
            'xlsx' => 'Excel',
            'xls' => 'Excel',
        ];
    
        // Check if the file extension is in the allowed extensions list
        if (array_key_exists($fileExtension, $typeMap)) {
            return $typeMap[$fileExtension];
        } else {
            return false;
        }
    }
    // check for existing profile image
    function profileImageCheck($url,$file_name){
        if (file_exists(FCPATH . $file_name) && !empty($file_name)){
             return base_url($file_name);
        }
        if (file_exists(FCPATH . $url.$file_name) && !empty($file_name)){
             return base_url($url.$file_name);
        }
        return base_url('assets_system/images/default_user.jpg');
    }
    function encryptData($data){
        // return $data;
    }
    private function get_device_info($userAgent){
        // echo '<pre>';
        // var_dump($userAgent);
        // $deviceMapping = array(
        //     'iPhone' => 'iPhone',
        //     'iPad' => 'iPad',
        //     'Windows NT 10.0' => 'Windows 10',
        //     // Add more mappings as needed
        // );

        // $osMapping = array(
        //     'Windows NT 10.0' => 'Windows 10',
        //     // Add more mappings as needed
        // );

        // $browserMapping = array(
        //     'Chrome' => 'Google Chrome',
        //     'Safari' => 'Safari',
        //     // Add more mappings as needed
        // );

        // // Extract device, OS, and browser information from the user agent
        // preg_match('/\(([^;]+); ([^)]+)\)/', $userAgent, $matches);
        // $device = $matches[1] ?? 'Unknown Device';
        // $os = $matches[2] ?? 'Unknown OS';

        // preg_match('/\b(\w+)\//', $userAgent, $matches);
        // $browser = $matches[1] ?? 'Unknown Browser';

        // // Check if the user agent contains the word "Mobile"
        $isMobile = preg_match('/\bMobile\b/i', $userAgent) === 1;
        if($isMobile){
            return 'Mobile';
        }
        return 'Desktop/Other Devices';
        // // Map technical terms to user-friendly terms
        // $friendlyDeviceInfo = array(
        //     'Device' => isset($deviceMapping[$device]) ? $deviceMapping[$device] : $device,
        //     'Operating System' => isset($osMapping[$os]) ? $osMapping[$os] : $os,
        //     'Browser' => isset($browserMapping[$browser]) ? $browserMapping[$browser] : $browser,
        //     'Is Mobile' => $isMobile ? 'Yes' : 'No',
        // );

        // var_dump($friendlyDeviceInfo);
        // return $friendlyDeviceInfo;
    }
    function log_access($description='',$type=''){
        $user_id=$this->CI->session->userdata('SESS_USER_ID');
        $is_admin=$this->CI->session->userdata('SESS_ADMIN')? 1 : 0;
        if($is_admin==1){
            return;
        }
        
        $uri         = $this->CI->uri->uri_string();
        $ip_address  = $this->CI->input->ip_address();
        $user_agent  = $this->CI->input->user_agent();
        $device_info = $this->get_device_info($user_agent);
        $this->CI->db->insert('tbl_access_logs', array(
            'create_date'   => date('Y-m-d H:i:s'),
            'empl_id'       => $user_id,
            'ip_address'    => $ip_address,
            'user_agent'    => $user_agent,
            'device'        =>$device_info,
            'uri'           => $uri,
            'description'   => $description
        ));
    }
}