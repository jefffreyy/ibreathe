<?php defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Worksheet\SheetView;

ob_start();
class index extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('user_model');

        ini_set('display_errors', 1);

        error_reporting(~0);
    }


    public function index()
    {
        $this->load->view('login.php');
    }
    public function general()
    {
        $this->load->model('user_model');

        // // Check if the user is logged in
        // if (!$this->session->userdata('logged_in')) {
        //     redirect('index/');
        // }

        // $user_id = $this->session->userdata('student_id');
        // $is_professor = $this->user_model->is_professor($user_id);
        // $data['is_professor'] = $is_professor;

        $current_date = date('Y-m-d H:i:s');

        // Add 15 hours
        // $future_date = date('Y-m-d H:i:s', strtotime($current_date . ' +8 hours'));
        $future_date = date('Y-m-d H:i:s', strtotime($current_date . ' 0 hours'));

        $timeslot =  $this->getTimeSlot($future_date);
        $timename =  $this->getTimeName($future_date);
        $slot_status_db = $this->user_model->get_all_slots($future_date, $timeslot);

        $slot_display = [];

        foreach ($slot_status_db as $row) {
            if ($timeslot != 0) {
                if ($row->final_status == "Open") {
                    $slot_display[] = "available";
                } elseif ($row->final_status == "Closed") {
                    $slot_display[] = "closed";
                } elseif ($row->final_status == "Occupied") {
                    $slot_display[] = "occupied";
                } elseif ($row->final_status == "Cancelled") {
                    $slot_display[] = "available";
                } elseif ($row->final_status == "not-available") {
                    $slot_display[] = "not-available";
                } else {
                    $slot_display[] = "not-available";
                }
            } else {
                $slot_display[] = "not-available";
            }
        }

        // var_dump($slot_display);

        $data['current_date'] = $future_date;
        $data['time_slot'] = $timeslot;
        $data['time_name'] = $timename;
        $data['slot_display'] = $slot_display;
        $this->load->view('general', $data);
    }
    public function homepage()
    {
        $this->load->model('user_model');

        // Check if the user is logged in
        if (!$this->session->userdata('logged_in')) {
            redirect('index/');
        }

        $user_id = $this->session->userdata('student_id');
        $is_professor = $this->user_model->is_professor($user_id);
        $data['is_professor'] = $is_professor;

        $notification = $this->user_model->get_seat_expiry_notification($user_id);
        $data['seat_notification'] = $notification;

        $current_date = date('Y-m-d H:i:s');

        // Add 15 hours
        // $future_date = date('Y-m-d H:i:s', strtotime($current_date . ' +8 hours'));
        // $future_date = date('Y-m-d H:i:s', strtotime($current_date . ' 0 hours'));

        // $timeslot =  $this->getTimeSlot($future_date);
        // $timename =  $this->getTimeName($future_date);

        $seat_status = $this->user_model->get_current_slot();

        $data['current_date'] = $current_date;
     
        $data['seat_status'] = $seat_status;
        $this->load->view('home', $data);
    }
    public function reject_request($id)
    {
        $this->load->model('user_model');

        $request = $this->user_model->get_request_by_id($id);

         $request['status'] = 'WITHDRAWED';
        unset($request['id']);
        var_dump($request);
        $this->user_model->insert_rejected_request($request);
        $this->user_model->delete_request_by_id($id);

        redirect('index/reservationrequest');
    }
    public function ongoing_request()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('index/');
        }

        $prof_id = $this->session->userdata('student_id');
        $this->load->model('user_model');
        $data['ongoing_requests'] = $this->user_model->get_professor_ongoing_by_id($prof_id);
        $this->load->view('prof_ongoing_reservation_request', $data);
    }

    public function registration()
    {
        $this->load->model('user_model');
        $otp_array = $this->input->post('otp');
        $otp_code = implode('', $otp_array);
        $otp = $this->user_model->get_OTP();
        if ($otp_code == $otp) {
            $this->load->view('registration.php');
        } else {
            // OTP is incorrect, reload view with error
            $data['otp_error'] = 'Invalid verification code. Please try again.';
            $this->load->view('email_verification', $data);
        }
    }
    function generateOTP($length = 6)
    {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= random_int(0, 9); // Cryptographically secure
        }
        return $otp;
    }
    public function emailverification()
    {
        $this->load->view('email_verification.php');
    }

    public function verify_email()
    {
        $email = $this->input->post('email');

        $this->load->model('user_model');
        if ($this->user_model->is_email_in_masterlist($email)) {
            $otp = $this->generateOTP();
            $data['email'] = $email;
            $data['error'] = "OTP is sent";
            $this->user_model->update_OTP($otp);
            $this->testemail($email, $otp);
            $this->load->view('email_verification.php', $data);
            // redirect('index/registration');
        } else {
            $data['error'] = "Email did not match from the Master List";
            $this->load->view('email_verification.php', $data);
        }
    }
 public function tests()
    {
        $email = "june.padrid@gmail.com";

        $this->load->model('user_model');
        // if ($this->user_model->is_email_in_masterlist($email)) {
            $otp = $this->generateOTP();
            $data['email'] = $email;
            $data['error'] = "OTP is sent";
            $this->user_model->update_OTP($otp);
            $this->testemail($email, $otp);
            $this->load->view('email_verification.php', $data);
            // redirect('index/registration');
        // } else {
        //     $data['error'] = "Email did not match from the Master List";
        //     $this->load->view('email_verification.php', $data);
        // }
    }

    public function forgotpassword()
    {
        $this->load->view('forgot-password.php');
    }
    public function resetpassword()
    {
        $this->load->view('reset-password.php');
    }
    public function profile()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('index/');
        }

        $student_id = $this->session->userdata('student_id');
        $this->load->model('user_model');
        $data['user'] = $this->user_model->getUserDetails($student_id);
        $this->load->view('profile', $data);
    }

    public function reservationrequest()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('index/');
        }

        $student_id = $this->session->userdata('student_id');
        $is_professor = $this->user_model->is_professor($student_id);

        var_dump($is_professor);
        if ($is_professor == false) {
            $this->load->model('user_model');

            $data['date_today'] = date('Y-m-d');
      
            $data['reservations'] = $this->user_model->getReservationsRequests($student_id);
            $data['reservations_occupied'] = $this->user_model->getReservationsRequests_occupied($student_id);
            $data['reservations_cancelled'] = $this->user_model->getReservationsRequests_cancelled($student_id);
            $this->load->view('reservation-request', $data);
        } else {
            $prof_id = $this->session->userdata('student_id');
            $data['requests'] = $this->user_model->get_professor_requests_by_id($prof_id);
            $this->load->view('prof_reservation_request.php', $data);
        }
    }


    function getTimeSlot(string $dateTimeStr): int
    {
        $dateTime = new DateTime($dateTimeStr);

        $timeSlots = [
            1 => ['07:00', '08:30'],
            2 => ['08:30', '10:00'],
            3 => ['10:00', '11:30'],
            4 => ['11:30', '13:00'],
            5 => ['13:00', '14:30'],
            6 => ['14:30', '16:00'],
            7 => ['16:00', '17:00']
        ];

        $time = $dateTime->format('H:i');

        foreach ($timeSlots as $slot => [$start, $end]) {
            if ($time >= $start && $time < $end) {
                return $slot;
            }
        }

        return 0; // Return 0 if the time does not fall in any slot
    }

    function getTimeName(string $dateTimeStr): String
    {
        $dateTime = new DateTime($dateTimeStr);

        $timeSlots = [
            1 => ['07:00', '08:30', '07:00 - 08:30 AM'],
            2 => ['08:30', '10:00', '08:30 - 10:00 AM'],
            3 => ['10:00', '11:30', '10:00 - 11:30 AM'],
            4 => ['11:30', '13:00', '11:30 - 01:00 PM'],
            5 => ['13:00', '14:30', '01:00 - 02:30 PM'],
            6 => ['14:30', '16:00', '02:30 - 04:00 PM'],
            7 => ['16:00', '17:00', '04:00 - 05:00 PM']
        ];

        $time = $dateTime->format('H:i');

        foreach ($timeSlots as $slot => [$start, $end, $dur]) {
            if ($time >= $start && $time < $end) {
                return $dur;
            }
        }

        return '04:00 - 05:00 PM'; // Return 0 if the time does not fall in any slot
    }

    function getTimeName_fromint(int $slot_id): String
    {

        $timeSlots = [
            1 => ['07:00', '08:30', '07:00 - 08:30 AM'],
            2 => ['08:30', '10:00', '08:30 - 10:00 AM'],
            3 => ['10:00', '11:30', '10:00 - 11:30 AM'],
            4 => ['11:30', '13:00', '11:30 - 01:00 PM'],
            5 => ['13:00', '14:30', '01:00 - 02:30 PM'],
            6 => ['14:30', '16:00', '02:30 - 04:00 PM'],
            7 => ['16:00', '17:00', '04:00 - 05:00 PM']
        ];

        foreach ($timeSlots as $slot => [$start, $end, $dur]) {
            if ($slot_id == $slot) {
                return $dur;
            }
        }

        return 'Outside Library Hours'; // Return 0 if the time does not fall in any slot
    }

    function check_status($seat_id, $date)
    {
        $monthNumber = date('m', strtotime($date));  // returns "05"

        $currentmonth = date('m');
        // var_dump($currentmonth);
        // var_dump($monthNumber);

        if ($currentmonth == $monthNumber) {
            $this->load->model('user_model');
            $slots_status = $this->user_model->check_slots($seat_id, $date);

            $data = [
                'slots_status' => $slots_status,
                'selected_date' => $date,
                'seat_id' => $seat_id,
                'current_datetime' => date('Y-m-d H:i:s') // Get current time
            ];

            $this->load->view('slot_views', $data);
        } else {
            redirect("index/reservationnotsamemonth");
        }
    }
    public function testemail($email, $otp)
    {
        $apiKey = 'mlsn.4375eaa530e2055b8d21086c658d1534cb4a6ce04e40663fdceffa7312a60186'; // Use your actual API key here

        $data = [
            'from' => [
                'email' => 'MS_vTuAxU@test-z0vklo6nnpxl7qrx.mlsender.net',
                'name'  => 'Library'
            ],
            'to' => [
                [
                    'email' => $email,
                    'name'  => $email
                ]
            ],
            'subject' => 'OTP From Library',
            'text'    => 'The OTP is ' . $otp,
            'html'    => ''
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.mailersend.com/v1/email");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            echo "✅ Email sent successfully!";
        } else {
            echo "❌ Failed to send email: HTTP $httpCode\nResponse:\n" . $response;
        }
    }

    function reservation($seat_id)
    {
        $this->load->model('user_model');
        $student_id = $this->session->userdata('student_id');
        $start_time = date("Y-m-d H:i:s");

        $now = new DateTime();
        $now->add(new DateInterval('PT1H30M'));

        $end_time = $now->format('Y-m-d H:i:s');


        $reserve_id = $this->user_model->insert_reservation($student_id, $seat_id, $start_time, $end_time);

        redirect("index/display_qr/" . $reserve_id);
    }
    function reservationconflict()
    {
        $this->load->view('reservationconflict_views');
    }
    function reservationmax()
    {
        $this->load->view('reservationmax_views');
    }
    function reservationnotsamemonth()
    {
        $this->load->view('reservationnotsamemonth_views');
    }

    function display_qr($reserve_id)
    {
        $this->load->model('user_model');



        $reservation = $this->user_model->getReservations_details($reserve_id);

        $student_id = $this->session->userdata('student_id');


        $data['student_id'] = (string)$student_id;
        $data['reserve_id'] = (string)$reserve_id;
        $data['seat_id'] = (string)$reservation->seat_id;
        $data['date_reserve'] = (string)$reservation->date_reserve;
        $data['start_time'] = date('H:i:s', strtotime($reservation->start_time));
        $data['end_time'] = date('H:i:s', strtotime($reservation->end_time));

        $this->load->view('qrcode_views.php', $data);
    }

  
 function closed_reservation()
{
    $this->load->model('user_model');

    $reservations = $this->user_model->get_reservation_5min();

    if (!empty($reservations)) {
        foreach ($reservations as $res) {
            // Convert object to array
            $data = (array) $res;

            // Insert into tbl_library_assign_deleted
            $this->user_model->insert_deleted_reservation($data);

            // Delete from tbl_library_assign
            $this->user_model->delete_reservation_by_id($res->id);
        }

        echo "Reservations transferred and deleted.";
    } else {
        echo "No reservations found.";
    }
}

 function lapse_reservation()
{
    $this->load->model('user_model');

    $reservations = $this->user_model->get_reservation_lapse();
    var_dump($reservations);
    if (!empty($reservations)) {
        foreach ($reservations as $res) {
            // Convert object to array
            $data = (array) $res;

            // // Insert into tbl_library_assign_deleted
            $this->user_model->update_status_reservation($data);

            // // Delete from tbl_library_assign
            // $this->user_model->delete_reservation_by_id($res->id);
        }

       
    } 
}
    function scan($reserve_id)
    {
        $this->load->model('user_model');
        $student_id = $this->session->userdata('student_id');

        $current_datetime = date('Y-m-d H:i:s');
        $current_date = date('Y-m-d');
        $timeslot = (int)$this->getTimeSlot($current_datetime);
        $timename = $this->getTimeName($current_datetime);

        $reservation = $this->user_model->getReservations_details($reserve_id);
        $reservation_slot_id = (int)$reservation->slot_id;
        $reservation_slot_date = $reservation->date_reserve;

        
                $this->user_model->update_reservation($reserve_id);
               
        
    }


    function registerUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $secret = '6LdhljErAAAAAHd2NheePXbms761peNGIVcmqEyC';
            $response = $_POST['g-recaptcha-response'];
            $remoteip = $_SERVER['REMOTE_ADDR'];

            $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$remoteip");
            $captcha_success = json_decode($verify);

            if ($captcha_success->success) {
                $student_id = $this->input->post('student_id');
                $fname = $this->input->post('fname');
                $mname = $this->input->post('mname');
                $lname = $this->input->post('lname');

                $role = $this->input->post('role');
                var_dump($role);

                $password = $this->input->post('password');

                $department = $this->input->post('department');
                $secquestion = $this->input->post('secquestion');
                $answer1 = $this->input->post('answer1');


                $this->load->model('user_model');

                // Check if student ID already exists
                if ($this->user_model->checkExistingStudentID($student_id)) {
                    $this->session->set_flashdata('error', 'Student ID already exists!');
                    redirect("index/registration");
                } else {
                    // Register new user if student ID is unique
                    $this->user_model->registerNewUser($student_id, $fname, $mname, $lname, $role, $password, $department, $secquestion, $answer1);

                    $this->session->set_flashdata('success', 'Registration successful!');
                    redirect("index/");
                }
            } else {
                // CAPTCHA failed
                $this->session->set_flashdata('error', 'CAPTCHA verification failed. Try again.');
                redirect("index/registration");
                // echo "CAPTCHA verification failed. Try again.";
            }
        }
    }

    public function forgot_password()
    {
        $student_id = $this->input->post('student_id');

        $this->load->model('user_model');

        // Check if student ID exists
        $userExists = $this->user_model->checkUserExists($student_id);

        if (!$userExists) {
            redirect("index/forgotpassword?error=User+ID+does+not+exist.");
            return;
        }

        // Check if account is locked
        if ($this->user_model->isAccountLocked($student_id)) {
            redirect("index/forgotpassword?error=Account+is+locked.+Please+see+the+librarian+for+assistance.");
            return;
        }

        // Proceed to security question
        redirect("index/secquestion?student_id=$student_id");
    }

    public function secquestion()
    {
        $student_id = $this->input->get('student_id');

        $this->load->model('user_model');
        $secquestion_code = $this->user_model->getSecurityQuestionByStudentID($student_id);
        $secanswer = $this->user_model->getSecurityAnswerByStudentID($student_id);

        if ($secquestion_code && $secanswer) {
            $attempts = $this->user_model->getAttempts($student_id);
            $remaining = 3 - $attempts;

            $secquestion = $this->user_model->getSecurityQuestionText($secquestion_code);

            $data['student_id'] = $student_id;
            $data['secquestion'] = $secquestion;
            $data['secanswer'] = $secanswer;
            $data['remaining_attempts'] = $remaining;

            $this->load->view('secquestion', $data);
        } else {
            redirect('index/forgotpassword?error=Invalid+Student+ID');
        }
    }

    public function secquestion_validate()
    {
        $this->load->model('user_model');
        $answer_form = $this->input->post('answer1');
        $correct_answer = $this->input->post('correct_answer');
        $student_id = $this->input->post('student_id');

        if ($this->user_model->isAccountLocked($student_id)) {
            redirect('index/forgotpassword?error=Account+is+locked.+Please+see+the+librarian+for+assistance.');
            return;
        }

        if ($answer_form === $correct_answer) {
            $this->user_model->resetAttempts($student_id);
            redirect("index/resetpassword?student_id=$student_id");
        } else {
            $this->user_model->incrementAttempts($student_id);

            if ($this->user_model->getAttempts($student_id) >= 3) {
                $this->user_model->lockAccount($student_id);
                redirect('index/forgotpassword?error=Account+is+locked.+Please+see+the+librarian+for+assistance.');
            } else {
                redirect("index/secquestion?student_id=$student_id&error=Incorrect+answer");
            }
        }
    }





    function reset_password()
    {
        $student_id = $this->input->post('student_id');
        $password = $this->input->post('password');
        $confirm_password = $this->input->post('confirm_password');

        if ($password !== $confirm_password) {
            redirect("index/resetpassword?student_id=$student_id&error=Passwords+do+not+match");
        }

        $this->load->model('user_model');
        $updateSuccess = $this->user_model->updatePassword($student_id, $password);

        if ($updateSuccess) {
            redirect("index/");
        } else {
            redirect("index/resetpassword?student_id=$student_id&error=Failed+to+update+password");
        }
    }

    function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $secret = '6LdhljErAAAAAHd2NheePXbms761peNGIVcmqEyC';
            $response = $_POST['g-recaptcha-response'];
            $remoteip = $_SERVER['REMOTE_ADDR'];

            $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$remoteip");
            $captcha_success = json_decode($verify);

            if (true) {
                // if ($captcha_success->success) {
                $student_id = $this->input->post('student_id');
                $password = $this->input->post('password');

                $user = $this->user_model->checkLogin($student_id, $password);

                if ($user) {
                    $session_data = array(
                        'student_id' => $user->student_id,
                        'logged_in' => TRUE
                    );
                    $this->session->set_userdata($session_data);
                    redirect('index/homepage');
                } else {
                    $data['error'] = 'Invalid username or password.';
                    $this->load->view('login', $data);
                }
            } else {
                // CAPTCHA failed
                $data['error'] = 'CAPTCHA verification failed. Try again..';
                $this->load->view('login', $data);
                // echo "CAPTCHA verification failed. Try again.";
            }
        }
    }

    function logout()
    {
        $this->session->unset_userdata('student_id');
        $this->session->unset_userdata('logged_in');
        $this->session->sess_destroy();
        redirect('index/');
    }

    function cancel_reservation($reservation_id)
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('index/');
        }

        $this->user_model->cancelReservation($reservation_id);

        redirect('index/reservationrequest');
    }


    //from here on, new updates especially for reservation class
    function class_reservation_request()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('index/');
        }

        $this->load->model('user_model');

        $user_id = $this->session->userdata('student_id');

        if (!$this->user_model->is_professor($user_id)) {
            show_error('Access denied. Professors only.', 403);
            return;
        }

        // Fetch professor data
        $professor_data = $this->user_model->get_professor_by_id($user_id);
        $professor_name = $professor_data['first_name'] . ' ' . $professor_data['last_name'];

        $slots = $this->user_model->get_class_reservation_slots();

        // Filter out past slots if request is for today
        $current_time = strtotime(date('H:i'));
        $filtered_slots = array_filter($slots, function ($slot) use ($current_time) {
            $parts = explode(' - ', $slot['slot_name']);
            $start_time = strtotime($parts[0]);
            return $start_time > $current_time;
        });

        $data = array(
            'professor_name' => $professor_name,
            'professor_id'   => $user_id,
            'slots'          => $slots // OR $filtered_slots if today only
        );


        $this->load->view('reserve_class_seat.php', $data);
    }

    public function send_prof_request()
    {
        $this->load->model('user_model');

        $data = array(
            'prof_name'     => $this->input->post('prof_name'),
            'prof_id'       => $this->input->post('prof_id'),
            'date'          => $this->input->post('date'),
            'time_slot'    => $this->input->post('time_slot'),
            'num_of_seats'  => $this->input->post('num_of_seats'),
            'prof_note'     => $this->input->post('prof_note'),
            'status'        => 'PENDING'
        );

        $inserted = $this->user_model->insert_prof_request($data);

        if ($inserted) {
            $this->session->set_flashdata('success', 'Request sent successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to send request.');
        }

        redirect('index/homepage');
    }

    public function dataprivacypolicy()
    {
        $this->load->view('data_privacy_policy.php');
    }


    public function notification()
    {
        $this->load->model('user_model');

        if (!$this->session->userdata('logged_in')) {
            redirect('index/');
        }

        $user_id = $this->session->userdata('student_id');

        if (!$this->user_model->is_professor($user_id)) {
            show_error('Access denied. Professors only.', 403);
            return;
        }

        $current_date = date('Y-m-d');

        $requests = $this->user_model->get_professor_requests_by_user($user_id);
        $ongoing_reservations = $this->user_model->get_professor_ongoing_by_user($user_id);
        $terminated_reservations = $this->user_model->get_professor_terminated_by_user($user_id);

        $ongoing = [];
        $lapsed = [];

        foreach ($requests as $req) {
            if ($req->date < $current_date) {
                $req->status = 'PENDING';
                $lapsed[] = $req;
            } else {
                $req->status = 'PENDING';
                $ongoing[] = $req;
            }
        }

        foreach ($ongoing_reservations as $res) {
            if ($res->date < $current_date) {
                $res->status = 'APPROVED';
                $lapsed[] = $res;
            } else {
                $res->status = 'APPROVED';
                $ongoing[] = $res;
            }
        }

        foreach ($terminated_reservations as $term) {
            if (isset($term->status) && strtoupper($term->status) === 'REJECTED') {
                $term->status = 'REJECTED';
            } else {
                $term->status = 'TERMINATED';
            }
            $lapsed[] = $term;
        }


        foreach (array_merge($ongoing, $lapsed) as $entry) {
            $entry->slot_name = $this->user_model->get_slot_name_by_id($entry->time_slot);
        }

        $data['ongoing'] = $ongoing;
        $data['lapsed'] = $lapsed;
        $data['is_professor'] = true;

        $this->load->view('prof_notification.php', $data);
    }
    public function process_request($id)
    {
        $this->load->model('user_model');
        $data['request'] = $this->user_model->get_request_by_id($id);

        if (!$data['request']) {
            show_404(); // Optional: show 404 if not found
        }

        // Get slot name from ID
        $slot_id = $data['request']['time_slot'];
        $data['request']['slot_name'] = $this->user_model->get_slot_name_by_id($slot_id);

        $this->load->view('prof_process_reservation_request', $data);
    }
    public function process_request_ongoing($id)
    {
        $this->load->model('user_model');
        $data['request'] = $this->user_model->get_request_by_id_ongoing($id);

        if (!$data['request']) {
            show_404(); // Optional: show 404 if not found
        }

        // Get slot name from ID
        $slot_id = $data['request']['time_slot'];
        $data['request']['slot_name'] = $this->user_model->get_slot_name_by_id($slot_id);

        $this->load->view('prof_ongoing_process_request.php', $data);
    }
    public function notification_detail($type, $id)
    {
        $this->load->model('user_model');

        if (!$this->session->userdata('logged_in')) {
            redirect('index/');
        }

        $prof_id = $this->session->userdata('student_id');
        $reservation = null;
        $table = '';

        // Try finding the record in each possible table
        $tables = [
            'tbl_library_professor_request',
            'tbl_library_professor_ongoing',
            'tbl_library_professor_terminated'
        ];

        foreach ($tables as $tbl) {
            $this->db->where('id', $id);
            $this->db->where('prof_id', $prof_id);
            $result = $this->db->get($tbl)->row();

            if ($result) {
                $reservation = $result;
                $table = $tbl;
                break;
            }
        }

        if (!$reservation) {
            show_error('Reservation not found or access denied.', 404);
            return;
        }

        $slot_name = $this->user_model->get_slot_name_by_id($reservation->time_slot);

        // Determine the type based on table and status
        if ($table === 'tbl_library_professor_request') {
            $type = 'request';
        } elseif ($table === 'tbl_library_professor_ongoing') {
            $type = 'ongoing';
        } elseif ($table === 'tbl_library_professor_terminated') {
            $type = strtolower($reservation->status); // could be 'terminated' or 'rejected'
        }

        $data['reservation'] = $reservation;
        $data['slot_name'] = $slot_name;
        $data['type'] = $type;

        $this->load->view('notification_detail', $data);
    }
}
