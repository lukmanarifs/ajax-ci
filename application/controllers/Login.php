<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        //load library form validasi
        $this->load->library('form_validation');
        //load model admin
        $this->load->model('user_model');
        $this->load->model('apps');
        $this->load->helper(array('string','security','form'));
    }

    public function index()
    {
        if($this->user_model->is_logged_in())
        {
            //jika memang session sudah terdaftar, maka redirect ke halaman dahsboard
            redirect("dashboard");

        }else{
            //jika session belum terdaftar

            //set form validation
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');

            //set message form validation
            $this->form_validation->set_message('required', '<div class="alert alert-danger" style="margin-top: 3px">
                <div class="header"><b><i class="fa fa-exclamation-circle"></i> {field}</b> harus diisi</div></div>');

            //cek validasi
            if ($this->form_validation->run() == TRUE) {
              $userLogin = $this->apps->find('users','username',$this->input->post("username"));
              if ($userLogin) {
                if ($userLogin->password_default) {
                  if ($userLogin->password_default == $this->input->post('password')) {
                    $this->resetPassword($userLogin->id);
                  }else{
                    $data = array(
                        'csrf' => array(
                            'name' => $this->security->get_csrf_token_name(),
                            'hash' => $this->security->get_csrf_hash()
                        )
                      );
                    $this->load->view('login', $data);
                  }
                }else{
                  //get data dari FORM
                  $username = $this->input->post("username", TRUE);
                  $password = MD5($this->input->post('password', TRUE));
                  //checking data via model
                  $checking = $this->user_model->check_login('users', array('username' => $username), array('password' => $password));
                  //jika ditemukan, maka create session
                  if ($checking != FALSE) {
                      foreach ($checking as $apps) {
                          $kategori = $this->apps->find('sys_kategori', 'kategori_id', $apps->kategori);
                          $session_data = array(
                              'user_id'   => $apps->id,
                              'user_username' => $apps->username,
                              'user_password' => $apps->password,
                              'user_fullname' => $apps->fullname,
                              'user_role'      => $apps->kategori,
                              'user_kategori'  => $kategori->kategori_nama
                          );
                          //set session userdata
                          $this->session->set_userdata($session_data);

                          //redirect berdasarkan level user
                          redirect('dashboard/');
                      }
                  }else{
                    $data = array(
                        'csrf' => array(
                            'name' => $this->security->get_csrf_token_name(),
                            'hash' => $this->security->get_csrf_hash()
                        ),
                        'error' => '<div class="alert alert-danger" style="margin-top: 3px">
                            <div class="header"><b><i class="fa fa-exclamation-circle"></i> ERROR</b> username atau password salah!</div></div>'
                      );
                    $this->load->view('login', $data);
                  }
              }
            }else{
              $data = array(
                  'csrf' => array(
                      'name' => $this->security->get_csrf_token_name(),
                      'hash' => $this->security->get_csrf_hash()
                  ),
                  'error' => '<div class="alert alert-danger" style="margin-top: 3px">
                      <div class="header"><b><i class="fa fa-exclamation-circle"></i> ERROR</b> username atau password salah!</div></div>'
                );
              $this->load->view('login', $data);
            }
          }else{
            $data = array(
                'csrf' => array(
                    'name' => $this->security->get_csrf_token_name(),
                    'hash' => $this->security->get_csrf_hash()
                )
              );
            $this->load->view('login', $data);
          }
        }
    }

    public function resetPassword($id){
      $data = array(
          'id' => $id,
          'content'    => 'content/user/passwordReset',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template_reset", $data);
    }

    public function updatePassword(){

      $this->form_validation->set_rules('password', 'Password', 'required');
      $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required');

      if ($this->input->post('password') == $this->input->post('confirm_password')) {
        $password = MD5($this->input->post('password', TRUE));

        $data = array(
          'password'    => $password,
          'password_default'    => ''
        );
        $query = $this->apps->update('users', 'id', $this->input->post('id'), $data);
        $this->session->set_flashdata('message','Password berhasil diperbaharui, silahkan login kembali menggunakan password terbaru');
        return redirect('login');
      }else{
        $this->session->set_flashdata('danger','Password yang diinputan tidak sama');
        return redirect('login/resetPassword');
      }
    }



}
