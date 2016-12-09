<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Controller {

    public function index() {
        if ($this->session->userdata('iUserId') > 0) {
            $url = $this->config->item('site_url') . "dashboard.html";
            header('Location:' . $url);
            exit;
        } else {
            $url = $this->config->item('site_url') . "login.html";
            header('Location:' . $url);
            exit;
        }
    }

    public function login() {
        $this->load->view('view_login');
    }

    public function login_action() {
        $this->load->model('model_users');

        $post_arr = $this->input->post();

        $vEmail = $post_arr['email'];
        $vPassword = $post_arr['password'];

        $condition = "vEmail = '" . $vEmail . "' AND vPassword = '" . $vPassword . "' ";
        $user_arr = $this->model_users->getData($condition);

        if (count($user_arr) > 0) {

            $this->session->set_userdata('iUserId', $user_arr[0]['iUserId']);
            $this->session->set_userdata('vFirstName', $user_arr[0]['vFirstName']);
            $this->session->set_userdata('vLastName', $user_arr[0]['vLastName']);
            $this->session->set_userdata('vUserName', $user_arr[0]['vUserName']);
            $this->session->set_userdata('vEmail', $user_arr[0]['vEmail']);
            $this->session->set_userdata('vProfileImage', $user_arr[0]['vProfileImage']);
            $this->session->set_userdata('eStatus', $user_arr[0]['eStatus']);

            //$url = $this->config->item('site_url') . "dashboard.html";

        } else {

            //$url = $this->config->item('site_url') . "login.html";
            
        }

        header('Location:' . $url);
        exit;
    }

    public function dashboard() {
        $this->load->view('view_dashboard');
    }

    public function users() {
        $this->load->model('model_users');

        $user_arr['user_arr'] = $this->model_users->getData('', '', '', '', 'iUserId ASC');

        //$this->load->view('view_users', $user_arr);
    }

    public function logout() {
        $this->session->sess_destroy();
        $url = $this->config->item('site_url') . "login.html";
        header('Location:' . $url);
        exit;
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */