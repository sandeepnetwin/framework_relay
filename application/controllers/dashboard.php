<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
         
    }

    public function login() {
        if ($this->session->userdata('is_admin_login')) 
        {
            redirect('home');
        } 
        else 
        {
            $this->load->view('Login');
        }
    }

     public function do_login() {

        if ($this->session->userdata('is_admin_login')) {
            redirect('admin/home/dashboard');
        } else {
            $user = $_POST['username'];
            $password = $_POST['password'];

            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('Login');
            } else {
                $enc_pass  = md5($password);
                $sql = "SELECT * FROM rlb_admin_users WHERE username = ? AND password = ?";
                $val = $this->db->query($sql,array($user ,$enc_pass ));

                if ($val->num_rows) {
                    foreach ($val->result_array() as $recs => $res) {
                        $this->session->set_userdata(array(
                            'id' => $res['id'],
                            'username' => $res['username'],
                            'email' => $res['email'],                            
                            'is_admin_login' => true,
                            'user_type' => $res['user_type']
                                )
                        );
                    }
                    redirect('home');
                } else {
                    $err['error'] = '<strong>Access Denied</strong> Invalid Username/Password';
                    $this->load->view('Login', $err);
                }
            }
        }
           }

        
    public function logout() {
        $this->session->unset_userdata('id');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('user_type');
        $this->session->unset_userdata('is_admin_login');   
        $this->session->sess_destroy();
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
        redirect('home', 'refresh');
    }

    
    

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */