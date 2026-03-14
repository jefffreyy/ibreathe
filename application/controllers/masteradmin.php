<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Masteradmin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('user_model');

        if (!$this->session->userdata('master_admin_logged_in')) {
            redirect('admin/adminlogin');
        }
    }

    public function dashboard()
    {
        $this->load->view('master-admin.php');
    }

    public function manage_masterlist()
    {
        $this->load->model('user_model');
        $data['emails'] = $this->user_model->get_all_masterlist();
        $this->load->view('masteradmin_masterlist', $data);
    }


    public function bulk_upload_emails()
    {
        $this->load->model('user_model');

        if (!empty($_FILES['csv_file']['name'])) {
            $file = $_FILES['csv_file']['tmp_name'];
            $handle = fopen($file, "r");

            if ($handle !== false) {
                $inserted = 0;
                $skipped = 0;

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $email = trim($data[0]);

                    // Validate email format
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        if (!$this->user_model->email_exists($email)) {
                            $this->user_model->insert_email($email);
                            $inserted++;
                        } else {
                            $skipped++;
                        }
                    }
                }

                fclose($handle);

                $this->session->set_flashdata('success', "Bulk upload completed. Inserted: $inserted. Skipped (duplicates/invalid): $skipped");
            } else {
                $this->session->set_flashdata('error', "Failed to open CSV file.");
            }
        } else {
            $this->session->set_flashdata('error', "Please select a CSV file to upload.");
        }

        redirect('masteradmin/manage_masterlist');
    }


    public function delete_masterlist_email()
    {
        $email_id = $this->input->post('id');
        // var_dump($email_id);
        if ($email_id) {
            $this->db->delete('tbl_library_masterlist', array('id' => $email_id)); // replace with your table
            $this->session->set_flashdata('success', 'Email deleted successfully.');
        } else {
            $this->session->set_flashdata('error', 'Invalid email ID.');
        }
        redirect('masteradmin/manage_masterlist');
    }



    public function add_masterlist_email()
    {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $email = trim($this->input->post('email'));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->session->set_flashdata('error', 'Invalid email format.');
                redirect('masteradmin/manage_masterlist');
                return;
            }

            $this->load->model('user_model');
            if ($this->user_model->email_exists_in_masterlist($email)) {
                $this->session->set_flashdata('error', 'Email already exists in the masterlist.');
                redirect('masteradmin/manage_masterlist');
                return;
            }

            $this->user_model->insert_masterlist_email($email);
            $this->session->set_flashdata('success', 'Email added successfully.');
            redirect('masteradmin/manage_masterlist');
        }
    }

    public function manage_librarian()
    {
        $this->load->model('user_model');

        if (isset($_POST['update_librarian'])) {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'"\\|,.<>\/?]/', $password)) {
                $this->session->set_flashdata('error', 'Password must contain at least one special character.');
                redirect('masteradmin/manage_librarian');
                return;
            }

            $data = array(
                'username' => $username,
                'password' => $password
            );

            $this->user_model->update_librarian($data);
            $this->session->set_flashdata('success', 'Librarian account updated successfully!');
            redirect('masteradmin/manage_librarian');
        }

        $data['librarian'] = $this->user_model->get_librarian();
        $this->load->view('masteradmin_manage_librarian', $data);
    }





    public function logout()
    {
        $this->session->sess_destroy();
        redirect('admin/adminlogin');
    }
}
