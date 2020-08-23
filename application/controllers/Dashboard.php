<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        //load model admin
        $this->load->library('session');
        $this->load->model('user_model');
        $this->load->helper(array('html','form'));

        // print_r( );
        //cek session dan level user
        if (!$this->session->userdata('user_role')) {
  				redirect('login');
  			}
    }

    public function index()
    {
      $data = array(
          'content' => 'content/dashboard/index',
          'page' => 'dashboard'
      );
      $this->load->view("layout/template", $data);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('login');
    }

}
