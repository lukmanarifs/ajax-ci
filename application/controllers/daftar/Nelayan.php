<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nelayan extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // if (!$this->session->userdata('user_role')) {
        //   redirect('login');
        // }
        $this->load->library('form_validation');
        $this->load->model('apps');
        $this->load->model('nelayan_model');
        // $this->load->helper(array('string','security','form'));
    }

    public function index()
    {
      $kecamatans = $this->apps->getAll('perikanan_ref_kecamatan');
      $data = array(
          'content'    => 'content/daftar/nelayan/index',
          'kecamatans'  => $kecamatans,
          'page' => 'nelayan',
          'kelompoks' => $this->apps->getAll('perikanan_ref_kelompok'),
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }

    public function ajaxList()
    {
      cekajax();
      $draw = intval($this->input->get("draw"));
      $start = intval($this->input->get("start"));
      $length = intval($this->input->get("length"));
      $query = $this->nelayan_model->dataNelayan();
      // print_r($query);
      $data = array();
      foreach($query as $q) {
        $tomboledit  = '<a href="#" class="btn btn-warning mini-btn" role="button" aria-pressed="true" title="EDIT" onclick="editForm('.$q['nelayan_key'].')"><i class="mdi mdi-lead-pencil"></i></a>';
        $tombolhapus = '<a href="#" class="btn btn-danger mini-btn" role="button" aria-pressed="true" title="HAPUS" onclick="showConfirmation('.$q['nelayan_key'].')"><i class="mdi mdi-delete"></i></a>';

          $data[]  = array(
                $this->security->xss_clean($q['nelayan_create']),
                $this->security->xss_clean($q['nelayan_nama']),
                $this->security->xss_clean($q['nelayan_nik']),
                $this->security->xss_clean($q['nelayan_alamat']),
                $this->security->xss_clean($q['desa_nama']),
                $this->security->xss_clean($q['kecamatan_nama']),
                $this->security->xss_clean($q['kelompok_nama']),
                $tomboledit.'
                 '.$tombolhapus
           );
      }
      $result = array(
             "draw" => $draw,
             "recordsTotal" => count($query),
             "recordsFiltered" => count($query),
             "data" => $data
        );

      echo json_encode($result);
    }

    public function create()
    {
      $data = array(
          'content'    => 'content/daftar/nelayan/create',
      );
      $this->load->view("layout/template", $data);
    }

    public function store()
    {
      $data = array(
        'content' =>  'content/daftar/nelayan/index'
      );
      $this->form_validation->set_rules('tambah_nama', 'Nama RTP', 'required');
      $this->form_validation->set_rules('tambah_nik', 'NIK', array('required', 'min_length[16]', 'max_length[16]'));
      $this->form_validation->set_rules('tambah_alamat', 'Alamat', 'required');
      $this->form_validation->set_rules('tambah_kecamatan', 'Kecamatan', 'required');
      $this->form_validation->set_rules('tambah_desa', 'Desa', 'required');
      if ($this->form_validation->run() == FALSE) {
        $validation = validation_errors();
        $errors = validation_errors('/', '/');
        if($errors != ''){
          $errors = explode("/", $errors);
          $this->session->set_flashdata('danger',$errors[1]);
        }
        $json = array(
               'tambah_nama' => form_error('tambah_nama', '<p class="mt-3 text-danger">', '</p>'),
               'tambah_nik' => form_error('tambah_nik', '<p class="mt-3 text-danger">', '</p>'),
               'tambah_alamat' => form_error('tambah_alamat', '<p class="mt-3 text-danger">', '</p>'),
               'tambah_kecamatan' => form_error('tambah_kecamatan', '<p class="mt-3 text-danger">', '</p>'),
               'tambah_desa' => form_error('tambah_desa', '<p class="mt-3 text-danger">', '</p>'),
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

        $query_cek = $this->apps->findLimit('perikanan_nelayan', 'nelayan_nik', $this->input->post("tambah_nik"));
        if($query_cek){
          $hasil = FALSE;
          $result = array(
            'status' => 'failed',
            'message' => 'Gagal! Data Dengan NIK '. $this->input->post("tambah_nik").' Sudah Digunakan',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
          echo json_encode($result);
        }

        if($hasil == TRUE){
          try{
            $micro_id = explode(" ", microtime());
            $micro_id = $micro_id[1].substr($micro_id[0],2,6);
            $data = array(
                'nelayan_key'       => $micro_id,
                'nelayan_nik'       => $this->input->post("tambah_nik"),
                'nelayan_kelompok_key'       => $this->input->post("tambah_kelompok"),
                'nelayan_nama'      => $this->input->post("tambah_nama"),
                'nelayan_alamat'    => $this->input->post("tambah_alamat"),
                'nelayan_desa'      => $this->input->post("tambah_desa"),
                'nelayan_kecamatan' => $this->input->post("tambah_kecamatan"),
                'nelayan_kusuka_nomor' => $this->input->post("tambah_kusuka"),
                'nelayan_kusuka_asuransi_status' => $this->input->post("tambah_asuransi") == "1" ? 1 : 0,
                'nelayan_create'    => date('Y-m-d H:i:s'),
                'nelayan_create_by'  => $this->session->userdata("user_username")
              );
              $query = $this->apps->save("perikanan_nelayan",$data);
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
      $id = $this->input->get('id');
      $nelayan = $this->apps->find('perikanan_nelayan', 'nelayan_key', $id);
      echo json_encode($nelayan);
    }

    public function update()
    {
      $data = array(
        'content' =>  'content/daftar/nelayan/index'
      );
      $this->form_validation->set_rules('edit_nama', 'Nama RTP', 'required');
      $this->form_validation->set_rules('edit_nik', 'NIK', array('required', 'min_length[16]', 'max_length[16]'));
      $this->form_validation->set_rules('edit_alamat', 'Alamat', 'required');
      $this->form_validation->set_rules('edit_kecamatan', 'Kecamatan', 'required');
      $this->form_validation->set_rules('edit_desa', 'Desa', 'required');
      if ($this->form_validation->run() == FALSE) {
        $validation = validation_errors();
        $errors = validation_errors('/', '/');
        if($errors != ''){
          $errors = explode("/", $errors);
          $this->session->set_flashdata('danger',$errors[1]);
        }
        $json = array(
               'edit_nama' => form_error('edit_nama', '<p class="mt-3 text-danger">', '</p>'),
               'edit_nik' => form_error('edit_nik', '<p class="mt-3 text-danger">', '</p>'),
               'edit_alamat' => form_error('edit_alamat', '<p class="mt-3 text-danger">', '</p>'),
               'edit_kecamatan' => form_error('edit_kecamatan', '<p class="mt-3 text-danger">', '</p>'),
               'edit_desa' => form_error('edit_desa', '<p class="mt-3 text-danger">', '</p>'),
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
            $id = $this->input->post("id");
            $data = array(
                'nelayan_nik'       => $this->input->post("edit_nik"),
                'nelayan_kelompok_key'       => $this->input->post("edit_kelompok"),
                'nelayan_nama'      => $this->input->post("edit_nama"),
                'nelayan_alamat'    => $this->input->post("edit_alamat"),
                'nelayan_desa'      => $this->input->post("edit_desa"),
                'nelayan_kecamatan' => $this->input->post("edit_kecamatan"),
                'nelayan_update'    => date('Y-m-d H:i:s'),
                'nelayan_update_by'  => $this->session->userdata("user_username")
              );
              $query = $this->apps->update('perikanan_nelayan', 'nelayan_key', $id, $data);
              $result = array(
                'status' => 'success',
                'message' => 'Data Berhasil Diubah.',
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
      $id = $this->input->post('id');
      $data = array(
        'nelayan' => $this->apps->find('perikanan_nelayan', 'nelayan_key', $id),
        'csrf' => array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        )
      );
      $this->load->view("content/daftar/nelayan/_confirmDelete", $data);
    }

    public function destroy()
    {
      $id = $this->uri->segment(4);
      $this->apps->destroy('perikanan_nelayan','nelayan_key',$id);
      $this->session->set_flashdata('message','Success! data berhasil dihapus.');
      redirect('daftar/nelayan/index');
    }

    public function getDesa()
    {
      $kecamatan = $this->input->get('kecamatan');
      $desas = $this->apps->get('perikanan_ref_desa', 'desa_kecamatan_kode', $kecamatan);
      echo json_encode($desas);
    }
}
