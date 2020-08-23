<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('user_model');
        $this->load->model('apps');

        if (!$this->session->userdata('user_role')) {
          redirect('login');
        }
    }

    public function index()
    {
      $query = $this->apps->getAll('sys_kategori');
      $data = array(
          'content'    => 'content/user/index',
          'kategoris'     => $query,
          'page'  => 'pengguna',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }
    public function ajaxList()
    {
      $query = $this->user_model->getAll();
      foreach($query as $q) {
              $tomboledit  = '<a href="#" class="btn btn-warning" role="button" aria-pressed="true" onclick="editForm('.$q['id'].')">Edit</a>';
              $tombolhapus = '<a href="#" class="btn btn-danger" role="button" aria-pressed="true" onclick="show_confirmation('.$q['id'].')">Hapus</a>';

           $data[] = array(
                $q['username'],
                $q['fullname'],
                $q['kategori_nama'],
                $tomboledit.'
                '.$tombolhapus
           );
      }
      $result = array(
          "data" => $data
      );


      // print_r($data);
      echo json_encode($result);
    }

    public function create()
    {

      $data = array(
          'content'    => 'content/user/create',
          'page'  => 'pengguna'
      );
      $this->load->view("layout/template", $data);
    }

    public function store()
    {
      $data = array(
        'content' =>  'content/user/index'
      );
      $this->form_validation->set_rules('tambah_username', 'Nama', 'required|is_unique[users.username]');
      $this->form_validation->set_rules('tambah_fullname', 'Password', 'required');
      $this->form_validation->set_rules('tambah_password', 'Password', 'required');
      $this->form_validation->set_rules('tambah_role', 'Hak Akses', 'required');
      if ($this->form_validation->run() == FALSE) {
        $validation = validation_errors();
        $errors = validation_errors('/', '/');
        if($errors != ''){
          $errors = explode("/", $errors);
          $this->session->set_flashdata('danger',$errors[1]);
        }
        $json = array(
               'tambah_username' => form_error('tambah_username', '<p class="mt-3 text-danger">', '</p>'),
               'tambah_fullname' => form_error('tambah_fullname', '<p class="mt-3 text-danger">', '</p>'),
               'tambah_password' => form_error('tambah_password', '<p class="mt-3 text-danger">', '</p>'),
               'tambah_role' => form_error('tambah_role', '<p class="mt-3 text-danger">', '</p>'),
               'csrf' => array(
                   'name' => $this->security->get_csrf_token_name(),
                   'hash' => $this->security->get_csrf_hash()
               )
           );
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json));

      }else{
        $hasil = TRUE;

        // $query_cek = $this->apps->findLimit('perikanan_nelayan', 'nelayan_nik', $this->input->post("tambah-nik"));
        // if($query_cek){
        //   $this->session->set_flashdata('danger', 'Gagal! Data Dengan NIK '. $this->input->post("tambah-nik").' Sudah Diinput');
        //   $hasil = FALSE;
        // }

        if($hasil == TRUE){
          try{
            $data = array(
              'username'  => $this->input->post("tambah_username"),
              'fullname'    => $this->input->post("tambah_fullname"),
              'password'    => MD5($this->input->post("tambah_password")),
              'kategori'    => $this->input->post("tambah_role"),
              'created_at'  => date('Y-m-d H:i:s'),
              'created_by'  => $this->session->userdata("user_username")
              );
              $query = $this->apps->save("users",$data);
              $result = array(
                'status' => 'success',
                'message' => 'Data Berhasil Disimpan.',
                'csrf' => array(
                    'name' => $this->security->get_csrf_token_name(),
                    'hash' => $this->security->get_csrf_hash()
                )
              );
              echo json_encode($result);
            }
            catch(\Exception $e){
              $result = array(
                'status' => 'failed',
                'message' => 'Data Gagal Disimpan.'
              );
              echo json_encode($result);
            }
        }
      }
    }

    public function edit()
    {
      $id = $this->input->get('id');
      $user = $this->apps->find('users','id',$id);

      $data = array(
          'user'  => $user,
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      echo json_encode($user);
    }

    public function update()
    {
      $user = $this->apps->find('users','id',$this->input->post('id'));
      if ($this->input->post('edit_username') != $user->username) {
        // code...
      }
      $data = array(
        'content' =>  'content/user/index'
      );
      $this->form_validation->set_rules('edit_username', 'Nama', 'required');
      $this->form_validation->set_rules('edit_fullname', 'Nama Lengkap', 'required');
      $this->form_validation->set_rules('edit_role', 'Hak Akses', 'required');
      if ($this->form_validation->run() == FALSE) {
        $validation = validation_errors();
        $errors = validation_errors('/', '/');
        if($errors != ''){
          $errors = explode("/", $errors);
          $this->session->set_flashdata('danger',$errors[1]);
        }
        $json = array(
               'edit_username' => form_error('edit_username', '<p class="mt-3 text-danger">', '</p>'),
               'edit_fullname' => form_error('edit_fullname', '<p class="mt-3 text-danger">', '</p>'),
               'edit_role' => form_error('edit_role', '<p class="mt-3 text-danger">', '</p>'),
               'csrf' => array(
                   'name' => $this->security->get_csrf_token_name(),
                   'hash' => $this->security->get_csrf_hash()
               )
           );
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json));

      }else{
        $hasil = TRUE;

        $data = $this->user_model->cariuser($this->input->post("edit_username"), $this->input->post("id"));
        if($data){
          $result = array(
            'status' => 'failed',
            'message' => 'Username Sudah Pernah diinput sebelumnya'
          );
          echo json_encode($result);
       }else{
         try{
           $id = $this->input->post("id");
           if($this->input->post("edit_password") != ''){
             $data = array(
               'username'    => $this->input->post("edit_username"),
               'fullname'    => $this->input->post("edit_fullname"),
               'password'    => MD5($this->input->post("edit_password")),
               'kategori'    => $this->input->post("edit_role"),
               'updated_at'  => date('Y-m-d H:i:s'),
               'updated_by'  => $this->session->userdata("user_username")
               );
           }else{
             $data = array(
               'username'    => $this->input->post("edit_username"),
               'fullname'    => $this->input->post("edit_fullname"),
               'kategori'    => $this->input->post("edit_role"),
               'updated_at'  => date('Y-m-d H:i:s'),
               'updated_by'  => $this->session->userdata("user_username")
               );
           }

             $query = $this->apps->update('users', 'id', $id, $data);
             if ($query) {
               $result = array(
                 'status' => 'success',
                 'message' => 'Data Berhasil Disimpan.',
                 'csrf' => array(
                     'name' => $this->security->get_csrf_token_name(),
                     'hash' => $this->security->get_csrf_hash()
                 )
               );
             }else{
               $result = array(
                 'status' => 'failed',
                 'message' => 'Data Gagal Disimpan.'
               );
             }

             echo json_encode($result);
           }
           catch(\Exception $e){
             $result = array(
               'status' => 'failed',
               'message' => 'Data Gagal Disimpan.'
             );
             echo json_encode($result);
           }
       }
      }
    }

    public function delete()
    {
      $id = $this->input->post('id');
      $data = array(
        'user' => $this->apps->find('users','id',$id),
        'csrf' => array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        )
      );
      $this->load->view("content/user/_confirmDelete", $data);
    }

    public function destroy()
    {
      $id = $this->uri->segment(3);
      $this->apps->destroy('users','id',$id);
      $this->session->set_flashdata('message','Success! data berhasil dihapus.');
      redirect('user/index');
    }

    public function confirmReset(){
      $data = array(
        'user' => $data = $this->apps->find('users','id', $this->input->post('id')),
        'csrf' => array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        )
      );

      $this->load->view("content/user/_confirmReset", $data);
    }

    public function generatePassword(){
      $id = $this->input->get('id');
      $user = $this->apps->find('users','id',$id);
      $pass = str_pad(mt_rand(1,999999),6,'0',STR_PAD_LEFT);
      $data = array(
        'password_default'=> $pass,
        'updated_at'      => date('Y-m-d H:i:s'),
        'updated_by'      => $this->session->userdata("user_username")
        );

      $query = $this->apps->update('users', 'id', $id, $data);
      $result = array(
        'status' => 'success',
        'pass'   => $pass,
        'users'  => $id,
        'csrf' => array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        ),
        'message' => 'Password Telah Direset, Silahkan User <strong>'.$user->username.'</strong> melakukan Login Kembali Menggunakan Password Default'
      );
      echo json_encode($result);
    }


}
