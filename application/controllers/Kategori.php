<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kategori extends CI_Controller {

    public function __construct()
    {
      parent::__construct();
      if (!$this->session->userdata('user_role')) {
        redirect('login');
      }
      $this->load->library('form_validation');
      $this->load->model('apps');
      $this->load->model('kategori_model');
      $this->load->helper(array('string','security','form'));
    }

    public function index()
    {
      level_user('kategori','index',$this->session->userdata('user_role'),'read') > 0 ? '': show_404();
      $query = $this->apps->getAll('sys_modul');
      $data = array(
          'content'    => 'content/kategori/index',
          'moduls'     => $query,
          'page'  => 'kategori',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }

    public function ajaxList()
    {
      $query = $this->apps->getAll('sys_kategori');
      foreach($query as $q) {
        $tomboledit  = level_user('kategori','index',$this->session->userdata('user_role'),'edit') > 0 ? '<a href="#" class="btn btn-warning" role="button" aria-pressed="true" onclick="editForm('.$q['kategori_id'].')">Edit</a>':'';
        $tombolhapus = level_user('kategori','index',$this->session->userdata('user_role'),'delete') > 0 ? '<a href="#" class="btn btn-danger" role="button" aria-pressed="true" onclick="show_confirmation('.$q['kategori_id'].')">Hapus</a>':'';

           $data[] = array(
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
    }

    public function store()
    {
      $data = array(
        'content' =>  'content/kategori/index'
      );
      $this->form_validation->set_rules('tambah_kategori', 'Jenis Kategori', 'required');
      if ($this->form_validation->run() == FALSE) {
        $validation = validation_errors();
        $errors = validation_errors('/', '/');
        if($errors != ''){
          $errors = explode("/", $errors);
          $this->session->set_flashdata('danger',$errors[1]);
        }
        $json = array(
               'tambah_kategori' => form_error('tambah_kategori', '<p class="mt-3 text-danger">', '</p>'),
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
        $data = $this->apps->find('sys_kategori','kategori_nama',$this->input->post("tambah_kategori"));
        if($data){
          $result = array(
            'status' => 'failed',
            'message' => 'Nama kategori Sudah Pernah diinput sebelumnya',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
          echo json_encode($result);
        }else{
          try{
            if($this->kategori_model->simpandatakategori()){
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
                'message' => 'Data Gagal Disimpan.',
                'csrf' => array(
                    'name' => $this->security->get_csrf_token_name(),
                    'hash' => $this->security->get_csrf_hash()
                )
              );
      			}
              echo json_encode($result);
            }
            catch(\Exception $e){
              $result = array(
                'status' => 'failed',
                'message' => 'Data Gagal Disimpan.',
                'csrf' => array(
                    'name' => $this->security->get_csrf_token_name(),
                    'hash' => $this->security->get_csrf_hash()
                )
              );
              echo json_encode($result);
            }
        }
      }
    }

    public function edit()
    {
     level_user('kategori','index',$this->session->userdata('user_role'),'edit') > 0 ? '': show_404();
      $id = $this->input->get('id');
      $role = $this->apps->find('sys_kategori', 'kategori_id', $id);
      $kategori = $this->apps->get('sys_hak_akses','akses_kategori',$id);
      $result = array(
        'role' => $role,
        'kategori' => $kategori,
        'csrf' => array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        )
      );
      echo json_encode($result);
    }

    public function update()
    {
        $data = array(
          'content' =>  'content/kategori/index'
        );
        $this->form_validation->set_rules('edit_kategori', 'Jenis Kategori', 'required');
        if ($this->form_validation->run() == FALSE) {
          $validation = validation_errors();
          $errors = validation_errors('/', '/');
          if($errors != ''){
            $errors = explode("/", $errors);
            $this->session->set_flashdata('danger',$errors[1]);
          }
          $json = array(
                 'edit_kategori' => form_error('edit_kategori', '<p class="mt-3 text-danger">', '</p>'),
                 'csrf' => array(
                     'name' => $this->security->get_csrf_token_name(),
                     'hash' => $this->security->get_csrf_hash()
                 )

             );
          $this->output
              ->set_content_type('application/json')
              ->set_output(json_encode($json));

        }else{
          $data = $this->kategori_model->carikategori($this->input->post("edit_kategori"), $this->input->post("id"));
          if($data){
            $result = array(
              'status' => 'failed',
              'message' => 'Nama kategori Sudah Pernah diinput sebelumnya',
              'csrf' => array(
                  'name' => $this->security->get_csrf_token_name(),
                  'hash' => $this->security->get_csrf_hash()
              )
            );
            echo json_encode($result);
         }else{
           try{
              if($this->kategori_model->gantidatakategori()){
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
                   'message' => 'Data Gagal Disimpan.',
                   'csrf' => array(
                       'name' => $this->security->get_csrf_token_name(),
                       'hash' => $this->security->get_csrf_hash()
                   )
                 );
              }
               echo json_encode($result);
             }
             catch(\Exception $e){
               $result = array(
                 'status' => 'failed',
                 'message' => 'Data Gagal Disimpan.',
                 'csrf' => array(
                     'name' => $this->security->get_csrf_token_name(),
                     'hash' => $this->security->get_csrf_hash()
                 )
               );
               echo json_encode($result);
             }
         }
      }
    }

    public function delete()
    {
      level_user('kategori','index',$this->session->userdata('user_role'),'delete') > 0 ? '': show_404();
      $id = $this->input->post('id');
      $data = array(
        'kategori' => $this->apps->find('sys_kategori','kategori_id',$id),
        'csrf' => array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        )
      );
      $this->load->view("content/kategori/_confirmDelete", $data);
    }

    public function destroy()
    {

      $id = $this->uri->segment(3);
      $data = $this->apps->find('users','kategori',$id);
      if($data){
        $this->session->set_flashdata('danger','gagal dihapus karena user masih menggunakan kategori ini');
        redirect('kategori');
      }else{
        $this->apps->destroy('sys_hak_akses','akses_kategori',$id);
        $this->apps->destroy('sys_kategori','kategori_id',$id);
        $this->session->set_flashdata('message','Data Berhasil Disimpan.');
        redirect('kategori');
      }


    }
}
