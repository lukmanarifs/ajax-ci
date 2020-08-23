<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH . 'phpspreadsheet/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class Rekap extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_role')) {
          redirect('login');
        }
        $this->load->library('form_validation');
        $this->load->model('apps');
        $this->load->model('kantor_model');
        $this->load->model('rekap_model');
        $this->load->helper(array('string','security','form'));

    }

    public function Nelayan()
    {
      level_user('rekapnelayan','index',$this->session->userdata('user_role'),'read') > 0 ? '': show_404();

      $data = array(
          'content'    => 'content/laporan/rekap/rekapnelayan',
          'page'  => 'rekap_nelayan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }

    public function surveyor()
    {
      level_user('rekapnelayan','index',$this->session->userdata('user_role'),'read') > 0 ? '': show_404();

      $data = array(
          'content'    => 'content/laporan/rekap/rekapsurveyor',
          'page'  => 'surveyor',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }

    public function excel_nel()
    {
      $spreadsheet = new Spreadsheet();
      $jenis_usaha = $this->input->post('jenis_usaha');
      $tambah_bulan = $this->input->post('tambah_bulan');
      $tambah_tahun = $this->input->post('tambah_tahun');

      $data = array(
          'content'    => 'content/laporan/rekap/rekapnelayan',
          'page'  => 'rekap_nelayan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      if($tambah_bulan == ''){
        $this->session->set_flashdata('danger','Bulan Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($tambah_tahun == ''){
        $this->session->set_flashdata('danger','Tahun Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else{
        if ($jenis_usaha) {
          $query_budidaya = false;
          $query_tangkap  = false;
          $query_lahsar   = false;
          $query_garam    = false;

          foreach($jenis_usaha as $q){
            if($q == 'budidaya'){
              $query_budidaya = $this->rekap_model->dataBudidaya($tambah_bulan, $tambah_tahun);
            }elseif($q == 'tangkap'){
              $query_tangkap = $this->rekap_model->dataTangkap($tambah_bulan, $tambah_tahun);
            }elseif($q == 'lahsar'){
              $query_lahsar = $this->rekap_model->dataLahsar($tambah_bulan, $tambah_tahun);
            }elseif($q == 'garam'){
              $query_garam = $this->rekap_model->dataGaram($tambah_bulan, $tambah_tahun);
            }
          }

          $spreadsheet->getProperties()->setCreator('Dinas Perikanan Sumenep')
          ->setLastModifiedBy('Dinas Perikanan Sumenep')
          ->setTitle('Laporan Rekap')
          ->setSubject('Laporan Rekap');
          $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
          $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
          $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
          $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
          $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
          $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
          $konfig = $this->kantor_model->cari();
          $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
          $drawing->setName('Logo');
          $drawing->setDescription('Logo');
          $drawing->setPath('./public/images/pdf/'.$konfig->kantor_logo);
          $drawing->setHeight(90);
          $drawing->setWorksheet($spreadsheet->getActiveSheet());

          $spreadsheet->getActiveSheet()->getStyle('C1:F1')->getFont()->setBold(true)->setSize(14);
          $spreadsheet->getActiveSheet()->getStyle('C1:F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
          $spreadsheet->getActiveSheet()->getStyle('C2:F2')->getFont()->setBold(true)->setSize(14);
          $spreadsheet->getActiveSheet()->getStyle('C2:F2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

          $spreadsheet->getActiveSheet()->getStyle('C3:F3')->getFont()->setSize(10);
          $spreadsheet->getActiveSheet()->getStyle('C3:F3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
          $spreadsheet->getActiveSheet()->getStyle('C4:F4')->getFont()->setBold(true)->setSize(12);
          $spreadsheet->getActiveSheet()->getStyle('C4:F4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

          $spreadsheet->setActiveSheetIndex(0)->mergeCells('C1:F1')->setCellValue('C1', 'PEMERINTAH KABUPATEN SUMENEP');
          $spreadsheet->setActiveSheetIndex(0)->mergeCells('C2:F2')->setCellValue('C2', $konfig->kantor_nama);
          $spreadsheet->setActiveSheetIndex(0)->mergeCells('C3:F3')->setCellValue('C3', $konfig->kantor_alamat.", Telp :".$konfig->kantor_telepon.", fax :".$konfig->kantor_fax.", email :".$konfig->kantor_email);
          $spreadsheet->setActiveSheetIndex(0)->mergeCells('C4:F4')->setCellValue('C4', 'SUMENEP');

          $spreadsheet->getActiveSheet()->getStyle('A6:F6')->getFont()->setBold(true);
          $spreadsheet->getActiveSheet()->getStyle('A6:F6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
          $spreadsheet->setActiveSheetIndex(0)
          ->setCellValue('A6', 'No')
          ->setCellValue('B6', 'Nama RTP')
          ->setCellValue('C6', 'NIK')
          ->setCellValue('D6', 'Alamat')
          ->setCellValue('E6', 'Desa')
          ->setCellValue('F6', 'Kecamatan');

          $i=6;
          if ($query_budidaya) {
            $i = $i + 1;
            $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
            $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':F'.$i)->setCellValue('A'.$i, 'Data Usaha Budidaya');
            $no = 1;
            $i = $i + 1;
            foreach($query_budidaya as $budidaya){
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $budidaya['nelayan_nama'])
              ->setCellValueExplicit(
                    'C'.$i,
                    $budidaya['nelayan_nik'],
                    DataType::TYPE_STRING
                  )
              ->setCellValue('D'.$i, $budidaya['nelayan_alamat'])
              ->setCellValue('E'.$i, $budidaya['desa_nama'])
              ->setCellValue('F'.$i, $budidaya['kecamatan_nama']);
              $i++;
              $no++;
            }
          }
          if ($query_garam) {
            $i = $i + 1;
            $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
            $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':F'.$i)->setCellValue('A'.$i, 'Data Usaha Budidaya Garam');
            $no = 1;
            $i = $i + 1;
            foreach($query_garam as $budidaya){
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $budidaya['nelayan_nama'])
              ->setCellValueExplicit(
                    'C'.$i,
                    $budidaya['nelayan_nik'],
                    DataType::TYPE_STRING
                  )
              ->setCellValue('D'.$i, $budidaya['nelayan_alamat'])
              ->setCellValue('E'.$i, $budidaya['desa_nama'])
              ->setCellValue('F'.$i, $budidaya['kecamatan_nama']);
              $i++;
              $no++;
            }
          }

          if ($query_tangkap) {
            $i = $i + 1;
            $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
            $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':F'.$i)->setCellValue('A'.$i, 'Data Usaha Tangkap');
            $no = 1;
            $i = $i + 1;
            foreach($query_tangkap as $tangkap){
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $tangkap['nelayan_nama'])
              ->setCellValueExplicit(
                    'C'.$i,
                    $tangkap['nelayan_nik'],
                    DataType::TYPE_STRING
                  )
              ->setCellValue('D'.$i, $tangkap['nelayan_alamat'])
              ->setCellValue('E'.$i, $tangkap['desa_nama'])
              ->setCellValue('F'.$i, $tangkap['kecamatan_nama']);
              $i++;
              $no++;
            }
          }

          if ($query_lahsar) {
            $i = $i + 1;
            $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
            $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':F'.$i)->setCellValue('A'.$i, 'Data Usaha Pengolahan dan Pemasaran');
            $no = 1;
            $i = $i + 1;
            foreach($query_lahsar as $lahsar){
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $lahsar['nelayan_nama'])
              ->setCellValueExplicit(
                    'C'.$i,
                    $lahsar['nelayan_nik'],
                    DataType::TYPE_STRING
                  )
              ->setCellValue('D'.$i, $lahsar['nelayan_alamat'])
              ->setCellValue('E'.$i, $lahsar['desa_nama'])
              ->setCellValue('F'.$i, $lahsar['kecamatan_nama']);
              $i++;
              $no++;
            }
          }
          // Rename worksheet
          $spreadsheet->getActiveSheet()->setTitle('Rekap Nelayan');

          // Set active sheet index to the first sheet, so Excel opens this as the first sheet
          $spreadsheet->setActiveSheetIndex(0);

          // Redirect output to a client’s web browser (Xlsx)
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment;filename="Rekap Nelayan.xlsx"');
          header('Cache-Control: max-age=0');
          // If you're serving to IE 9, then the following may be needed
          header('Cache-Control: max-age=1');

          // If you're serving to IE over SSL, then the following may be needed
          header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
          header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
          header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
          header('Pragma: public'); // HTTP/1.0

          $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
          $writer->save('php://output');
          exit;
        }else{
          $this->session->set_flashdata('danger','Jenis Usaha Belum ditentukan');
          $this->load->view("layout/template", $data);
        }
      }
    }

    public function daftar_nel()
    {
      $jenis_usaha = $this->input->post('jenis_usaha');
      $tambah_bulan = $this->input->post('tambah_bulan');
      $tambah_tahun = $this->input->post('tambah_tahun');
      $query_budidaya = false;
      $query_tangkap  = false;
      $query_lahsar   = false;
      $query_garam    = false;
      if($tambah_bulan == ''){
        $data = array(
          'keterangan' => 'Bulan Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datanelayan", $data);
      }elseif($tambah_tahun == ''){
        $data = array(
          'keterangan' => 'Tahun Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datanelayan", $data);
      }else{
        if ($jenis_usaha) {
          foreach($jenis_usaha as $q){
            if($q == 'budidaya'){
              $query_budidaya = $this->rekap_model->dataBudidaya($tambah_bulan, $tambah_tahun);
            }elseif($q == 'tangkap'){
              $query_tangkap = $this->rekap_model->dataTangkap($tambah_bulan, $tambah_tahun);
            }elseif($q == 'lahsar'){
              $query_lahsar = $this->rekap_model->dataLahsar($tambah_bulan, $tambah_tahun);
            }elseif($q == 'garam'){
              $query_garam = $this->rekap_model->dataGaram($tambah_bulan, $tambah_tahun);
            }
          }
          $data = array(
            'budidayas' => $query_budidaya ? $query_budidaya : false,
            'tangkaps' => $query_tangkap ? $query_tangkap : false,
            'lahsars' => $query_lahsar ? $query_lahsar : false,
            'garams' => $query_garam ? $query_garam : false,
            'keterangan' => '',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
          $this->load->view("content/laporan/rekap/_datanelayan", $data);
        }else{
          $data = array(
            'keterangan' => 'Jenis Usaha belum Ditentukan',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
          $this->load->view("content/laporan/rekap/_datanelayan", $data);
        }
      }
    }

    public function daftar_surveyor()
    {
      $tambah_bulan = $this->input->post('tambah_bulan');
      $tambah_tahun = $this->input->post('tambah_tahun');
      $data_surveyors = array();
      if($tambah_bulan == ''){
        $data = array(
          'keterangan' => 'Bulan Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datanelayan", $data);
      }elseif($tambah_tahun == ''){
        $data = array(
          'keterangan' => 'Tahun Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datanelayan", $data);
      }else{
        $query_surveyors = $this->rekap_model->dataSurveyor($tambah_bulan, $tambah_tahun);

        for ($i=0; $i < count($query_surveyors) ; $i++) {
          $budidaya = $query_surveyors[$i]['budidaya'];
          $garam = $query_surveyors[$i]['garam'];
          $tangkap = $query_surveyors[$i]['tangkap'];
          $tangkap_alat = $query_surveyors[$i]['tangkap_alat'];
          $tangkap_perahu = $query_surveyors[$i]['tangkap_perahu'];
          $lahsar = $query_surveyors[$i]['lahsar'];
          if (!empty($budidaya) || !empty($garam) || !empty($tangkap) || !empty($tangkap_alat) || !empty($tangkap_perahu) || !empty($lahsar)) {
              $no = 0;
              $data_surveyors[$no] = [
              'no' => $no+1,
              'username' => $query_surveyors[$i]['username'],
              'fullname' => $query_surveyors[$i]['fullname'],
              'budidaya' => $budidaya,
              'garam' => $garam,
              'total_tangkap' =>  $tangkap,
              'lahsar'  => $lahsar,
              'total' => $budidaya + $garam + $tangkap + $tangkap_alat + $tangkap_perahu + $lahsar
            ];
              $no++;
          }
        }

        if ($data_surveyors) {
          $data = array(
            'keterangan' => '',
            'surveyors' => $data_surveyors ? $data_surveyors : false,
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
        }else{
          $data = array(
            'keterangan' => 'Tidak terdapat data pada tanggal tersebut',
            'surveyors' => $data_surveyors ? $data_surveyors : false,
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
        }
        $this->load->view("content/laporan/rekap/_dataSurveyor", $data);
      }
    }

    public function surveyorPDF()
  	{
      $tambah_bulan = $this->input->post('tambah_bulan');
      $tambah_tahun = $this->input->post('tambah_tahun');
      define('FPDF_FONTPATH',$this->config->item('fonts_path'));
      $this->load->library('CustomPDF');
      $pdf = $this->custompdf->getInstance();

    	$pdf->SetMargins(15, 10, 10);
    	$pdf->SetFillColor(212,239,247);
    	$pdf->AliasNbPages();
    	$pdf->AddPage();

      $pdf->SetFont('Arial','B',12);
  		$pdf->Cell(180,5,'FORM REKAP INPUT DATA SURVEYOR',0,1,'C',0);
  		$pdf->Cell(180,5,'DINAS PERIKANAN KABUPATEN SUMENEP',0,1,'C',0);
      $pdf->Ln(10);

      $query_surveyors = $this->rekap_model->dataSurveyor($tambah_bulan, $tambah_tahun);

      for ($i=0; $i < count($query_surveyors) ; $i++) {
        $budidaya = $query_surveyors[$i]['budidaya'];
        $garam = $query_surveyors[$i]['garam'];
        $tangkap = $query_surveyors[$i]['tangkap'];
        $tangkap_alat = $query_surveyors[$i]['tangkap_alat'];
        $tangkap_perahu = $query_surveyors[$i]['tangkap_perahu'];
        $lahsar = $query_surveyors[$i]['lahsar'];
        if (!empty($budidaya) || !empty($garam) || !empty($tangkap) || !empty($tangkap_alat) || !empty($tangkap_perahu) || !empty($lahsar)) {
            $no = 0;
            $data_surveyors[$no] = [
            'no' => $no+1,
            'username' => $query_surveyors[$i]['username'],
            'fullname' => $query_surveyors[$i]['fullname'],
            'budidaya' => $budidaya,
            'garam' => $garam,
            'total_tangkap' =>  $tangkap + $tangkap_alat + $tangkap_perahu,
            'lahsar'  => $lahsar,
            'total' => $budidaya + $garam + $tangkap + $tangkap_alat + $tangkap_perahu + $lahsar
          ];
            $no++;
        }
      }
      for ($j=0; $j < count($data_surveyors); $j++) {
        if($j == 0){
          $pdf->SetFont('Arial','B',10);
          $pdf->Cell(10,5,'No',1,0,'C',1);
          $pdf->Cell(25,5,'Username',1,0,'C',1);
          $pdf->Cell(35,5,'Nama Lengkap',1,0,'C',1);
          $pdf->Cell(25,5,'Budidaya',1,0,'C',1);
          $pdf->Cell(25,5,'Garam',1,0,'C',1);
          $pdf->Cell(20,5,'Tangkap',1,0,'C',1);
          $pdf->Cell(20,5,'Lahsar',1,0,'C',1);
          $pdf->Cell(20,5,'Total',1,1,'C',1);
        }

        $pdf->SetWidths(array(10,25, 35, 25, 25, 20, 20, 20));
        $pdf->SetAligns(array('C','L','L','C','C','C','C','C'));
        $pdf->Row(array($j+1, $data_surveyors[$j]['username'],
        $data_surveyors[$j]['fullname'], $data_surveyors[$j]['budidaya'],
        $data_surveyors[$j]['garam'],$data_surveyors[$j]['total_tangkap'],
        $data_surveyors[$j]['lahsar'],$data_surveyors[$j]['total']));
      }
      if ($data_surveyors) {
        $pdf->Output('D','Rekap Surveyor '.$tambah_tahun.$tambah_bulan.'.pdf');
      }else{
        $this->session->set_flashdata('danger','Data tidak tersedia pada bulan tersebut');
        redirect('laporan/rekap/surveyor');
      }
  	}

    public function Budidaya()
    {
      level_user('rekapbudidaya','index',$this->session->userdata('user_role'),'read') > 0 ? '': show_404();

      $jeniss = $this->rekap_model->ReffBudidaya();
      $data = array(
          'content'    => 'content/laporan/rekap/rekapbudidaya',
          'jeniss'  => $jeniss,
          'page'  => 'rekap_budidaya',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }

    public function daftar_budidaya()
    {
      $jenis_usaha = $this->input->post('jenis_usaha');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');
      if($dari_bulan == ''){
        $data = array(
          'keterangan' => 'Dari Bulan Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_databudidaya", $data);
      }elseif($dari_tahun == ''){
        $data = array(
          'keterangan' => 'Dari Tahun Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_databudidaya", $data);
      }elseif($sampai_bulan == ''){
        $data = array(
          'keterangan' => 'Sampai Bulan Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_databudidaya", $data);
      }elseif($sampai_tahun == ''){
        $data = array(
          'keterangan' => 'Sampai Tahun Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_databudidaya", $data);
      }else{
        $query_budidaya = array();
        $query_usaha    = array();
        if($jenis_usaha){
          foreach($jenis_usaha as $q){
              $query_budidaya[$q] = $this->rekap_model->hasilBudidaya($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $q);
          }
          $data = array(
            'budidayas' => $query_budidaya,
            'jeniss' => $jenis_usaha,
            'keterangan' => '',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
          $this->load->view("content/laporan/rekap/_databudidaya", $data);
        }else{
          $data = array(
            'keterangan' => 'jenis Usaha Data Belum Ditentukan',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
          $this->load->view("content/laporan/rekap/_databudidaya", $data);
        }

      }

    }

    public function excel_budidaya()
    {
      $spreadsheet = new Spreadsheet();
      $jenis_usaha = $this->input->post('jenis_usaha');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');
      $query_budidaya = array();
      $query_usaha    = array();
      $jeniss = $this->rekap_model->ReffBudidaya();
      $data = array(
          'content'    => 'content/laporan/rekap/rekapbudidaya',
          'jeniss'  => $jeniss,
          'page'  => 'rekap_budidaya',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      if($dari_bulan == ''){
        $this->session->set_flashdata('danger','Dari Bulan Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($dari_tahun == ''){
        $this->session->set_flashdata('danger','Dari Tahun Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($sampai_bulan == ''){
        $this->session->set_flashdata('danger','Sampai Bulan Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($sampai_tahun == ''){
        $this->session->set_flashdata('danger','Sampai Tahun Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }elseif(!$jenis_usaha){
        $this->session->set_flashdata('danger','Jenis Usaha Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else{
        $spreadsheet->getProperties()->setCreator('Dinas Perikanan Sumenep')
        ->setLastModifiedBy('Dinas Perikanan Sumenep')
        ->setTitle('Laporan Rekap')
        ->setSubject('Laporan Rekap');
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $konfig = $this->kantor_model->cari();
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath('./public/images/pdf/'.$konfig->kantor_logo);
        $drawing->setHeight(90);

        $drawing->setWorksheet($spreadsheet->getActiveSheet());
        $spreadsheet->getActiveSheet()->getStyle('C1:L1')->getFont()->setBold(true)->setSize(14);
        $spreadsheet->getActiveSheet()->getStyle('C1:L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C2:L2')->getFont()->setBold(true)->setSize(14);
        $spreadsheet->getActiveSheet()->getStyle('C2:L2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->getStyle('C3:L3')->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getStyle('C3:L3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C4:L4')->getFont()->setBold(true)->setSize(12);
        $spreadsheet->getActiveSheet()->getStyle('C4:L4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C1:L1')->setCellValue('C1', 'PEMERINTAH KABUPATEN SUMENEP');
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C2:L2')->setCellValue('C2', $konfig->kantor_nama);
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C3:L3')->setCellValue('C3', $konfig->kantor_alamat.", Telp :".$konfig->kantor_telepon.", fax :".$konfig->kantor_fax.", email :".$konfig->kantor_email);
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C4:L4')->setCellValue('C4', 'SUMENEP');

        $spreadsheet->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('D6')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('B6', 'Dari')
        ->setCellValue('C6', ': '.$this->getbulan($dari_bulan).' '.$dari_tahun)
        ->setCellValue('D6', 'Sampai')
        ->setCellValue('E6', ': '.$this->getbulan($sampai_bulan).' '.$sampai_tahun);
        $spreadsheet->getActiveSheet()->getStyle('A7:L7')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A7:L7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A7', 'No')
        ->setCellValue('B7', 'Nama RTP')
        ->setCellValue('C7', 'NIK')
        ->setCellValue('D7', 'Alamat')
        ->setCellValue('E7', 'Desa')
        ->setCellValue('F7', 'Kecamatan')
        ->setCellValue('G7', 'Daftar')
        ->setCellValue('H7', 'Keterangan')
        ->setCellValue('I7', 'luas (m2)')
        ->setCellValue('J7', 'Produksi (ton)')
        ->setCellValue('K7', 'harga (Rp)')
        ->setCellValue('L7', 'Nilai Produksi (Rp)');

        $i=8;
        foreach($jenis_usaha as $q){
            $query_budidaya = $this->rekap_model->hasilBudidaya($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $q);
            $no = 1;
            $produksi = 0;
            $harga    = 0;
            $nilai    = 0;
           foreach($query_budidaya as $budidaya){
              if($no == 1){
                $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
                $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':L'.$i)->setCellValue('A'.$i, $budidaya['tipe_budidaya_nama']);
                $i = $i + 1;
              }
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $budidaya['nelayan_nama'])
              ->setCellValueExplicit(
                    'C'.$i,
                    $budidaya['nelayan_nik'],
                    DataType::TYPE_STRING
                  )
              ->setCellValue('D'.$i, $budidaya['nelayan_alamat'])
              ->setCellValue('E'.$i, $budidaya['desa_nama'])
              ->setCellValue('F'.$i, $budidaya['kecamatan_nama'])
              ->setCellValue('G'.$i, $budidaya['daftar_budidaya_keterangan'])
              ->setCellValue('H'.$i, $budidaya['laporan_budidaya_keterangan'])
              ->setCellValue('I'.$i, $budidaya['luas'])
              ->setCellValue('J'.$i, $budidaya['produksi'])
              ->setCellValue('K'.$i, $budidaya['harga'])
              ->setCellValue('L'.$i, $budidaya['nilai']);
              $produksi = $produksi + $budidaya['produksi'];
              $harga    = $harga + $budidaya['harga'];
              $nilai    = $nilai + $budidaya['nilai'];
              if(count($query_budidaya) == $no){
                $i = $i + 1;
                $spreadsheet->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('J'.$i, $produksi)
                ->setCellValue('K'.$i, $harga)
                ->setCellValue('L'.$i, $nilai);
              }
              $i++;
              $no++;
           }
        }
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Rekap Budidaya');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Rekap Budidaya.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
      }
    }

    public function Lahsar()
    {
      level_user('rekaplahsar','index',$this->session->userdata('user_role'),'read') > 0 ? '': show_404();

      $jeniss = $this->rekap_model->ReffLahsar();
      $data = array(
          'content'    => 'content/laporan/rekap/rekaplahsar',
          'jeniss'  => $jeniss,
          'page'  => 'rekap_lahsar',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }

    public function daftar_lahsar()
    {
      $jenis_usaha = $this->input->post('jenis_usaha');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');
      if($dari_bulan == ''){
        $data = array(
          'keterangan' => 'Dari Bulan Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datalahsar", $data);
      }elseif($dari_tahun == ''){
        $data = array(
          'keterangan' => 'Dari Tahun Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datalahsar", $data);
      }elseif($sampai_bulan == ''){
        $data = array(
          'keterangan' => 'Sampai Bulan Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datalahsar", $data);
      }elseif($sampai_tahun == ''){
        $data = array(
          'keterangan' => 'Sampai Tahun Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datalahsar", $data);
      }else{
        $query_lahsar = array();
        $query_usaha    = array();
        if($jenis_usaha){
          foreach($jenis_usaha as $q){
              $query_lahsar[$q] = $this->rekap_model->hasilLahsar($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $q);
          }
          $data = array(
            'lahsars' => $query_lahsar,
            'jeniss' => $jenis_usaha,
            'keterangan' => '',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
          $this->load->view("content/laporan/rekap/_datalahsar", $data);
        }else{
          $data = array(
            'keterangan' => 'jenis Usaha Data Belum Ditentukan',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
          $this->load->view("content/laporan/rekap/_datalahsar", $data);
        }
      }
    }

    public function excel_lahsar()
    {
      $spreadsheet = new Spreadsheet();
      $jenis_usaha = $this->input->post('jenis_usaha');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');
      $query_budidaya = array();
      $query_usaha    = array();
      $jeniss = $this->rekap_model->ReffLahsar();
      $data = array(
          'content'    => 'content/laporan/rekap/rekaplahsar',
          'jeniss'  => $jeniss,
          'page'  => 'rekap_lahsar',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      if($dari_bulan == ''){
        $this->session->set_flashdata('danger','Dari Bulan Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($dari_tahun == ''){
        $this->session->set_flashdata('danger','Dari Tahun Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($sampai_bulan == ''){
        $this->session->set_flashdata('danger','Sampai Bulan Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($sampai_tahun == ''){
        $this->session->set_flashdata('danger','Sampai Tahun Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }elseif(!$jenis_usaha){
        $this->session->set_flashdata('danger','Jenis Usaha Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else{
        $spreadsheet->getProperties()->setCreator('Dinas Perikanan Sumenep')
        ->setLastModifiedBy('Dinas Perikanan Sumenep')
        ->setTitle('Laporan Rekap')
        ->setSubject('Laporan Rekap');
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $konfig = $this->kantor_model->cari();
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath('./public/images/pdf/'.$konfig->kantor_logo);
        $drawing->setHeight(90);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $spreadsheet->getActiveSheet()->getStyle('C1:K1')->getFont()->setBold(true)->setSize(14);
        $spreadsheet->getActiveSheet()->getStyle('C1:K1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C2:K2')->getFont()->setBold(true)->setSize(14);
        $spreadsheet->getActiveSheet()->getStyle('C2:K2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->getStyle('C3:K3')->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getStyle('C3:K3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C4:K4')->getFont()->setBold(true)->setSize(12);
        $spreadsheet->getActiveSheet()->getStyle('C4:K4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C1:K1')->setCellValue('C1', 'PEMERINTAH KABUPATEN SUMENEP');
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C2:K2')->setCellValue('C2', $konfig->kantor_nama);
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C3:K3')->setCellValue('C3', $konfig->kantor_alamat.", Telp :".$konfig->kantor_telepon.", fax :".$konfig->kantor_fax.", email :".$konfig->kantor_email);
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C4:K4')->setCellValue('C4', 'SUMENEP');

        $spreadsheet->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('D6')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('B6', 'Dari')
        ->setCellValue('C6', ': '.$this->getbulan($dari_bulan).' '.$dari_tahun)
        ->setCellValue('D6', 'Sampai')
        ->setCellValue('E6', ': '.$this->getbulan($sampai_bulan).' '.$sampai_tahun);
        $spreadsheet->getActiveSheet()->getStyle('A7:K7')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A7:K7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A7', 'No')
        ->setCellValue('B7', 'Nama RTP')
        ->setCellValue('C7', 'NIK')
        ->setCellValue('D7', 'Alamat')
        ->setCellValue('E7', 'Desa')
        ->setCellValue('F7', 'Kecamatan')
        ->setCellValue('G7', 'Daftar')
        ->setCellValue('H7', 'Keterangan')
        ->setCellValue('I7', 'Produksi (ton)')
        ->setCellValue('J7', 'harga (Rp)')
        ->setCellValue('K7', 'Nilai Produksi (Rp)');

        $i=8;
        foreach($jenis_usaha as $q){
            $query_lahsar = $this->rekap_model->hasilLahsar($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $q);
            $no = 1;
            $produksi = 0;
            $harga    = 0;
            $nilai    = 0;
           foreach($query_lahsar as $lahsar){
              if($no == 1){
                $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
                $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':K'.$i)->setCellValue('A'.$i, $lahsar['tipe_lahsar_nama']);
                $i = $i + 1;
              }
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $lahsar['nelayan_nama'])
              ->setCellValueExplicit(
                    'C'.$i,
                    $lahsar['nelayan_nik'],
                    DataType::TYPE_STRING
                  )
              ->setCellValue('D'.$i, $lahsar['nelayan_alamat'])
              ->setCellValue('E'.$i, $lahsar['desa_nama'])
              ->setCellValue('F'.$i, $lahsar['kecamatan_nama'])
              ->setCellValue('G'.$i, $lahsar['daftar_lahsar_keterangan'])
              ->setCellValue('H'.$i, $lahsar['laporan_lahsar_keterangan'])
              ->setCellValue('I'.$i, $lahsar['produksi'])
              ->setCellValue('J'.$i, $lahsar['harga'])
              ->setCellValue('K'.$i, $lahsar['nilai']);
              $produksi = $produksi + $lahsar['produksi'];
              $harga    = $harga + $lahsar['harga'];
              $nilai    = $nilai + $lahsar['nilai'];
              if(count($query_lahsar) == $no){
                $i = $i + 1;
                $spreadsheet->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':H'.$i)->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('I'.$i, $produksi)
                ->setCellValue('J'.$i, $harga)
                ->setCellValue('K'.$i, $nilai);
              }
              $i++;
              $no++;
           }
        }
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Rekap Lahsar');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Rekap Pengolahan dan Pemasaran.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
      }
    }

    public function Tangkap()
    {
      level_user('rekaptangkap','index',$this->session->userdata('user_role'),'read') > 0 ? '': show_404();

      $jeniss = $this->rekap_model->ReffTangkap();
      $data = array(
          'content'    => 'content/laporan/rekap/rekaptangkap',
          'jeniss'  => $jeniss,
          'page'  => 'rekap_tangkap',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }

    public function daftar_tangkap()
    {
      $jenis_usaha = $this->input->post('jenis_usaha');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');
      if($dari_bulan == ''){
        $data = array(
          'keterangan' => 'Dari Bulan Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datatangkap", $data);
      }elseif($dari_tahun == ''){
        $data = array(
          'keterangan' => 'Dari Tahun Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datatangkap", $data);
      }elseif($sampai_bulan == ''){
        $data = array(
          'keterangan' => 'Sampai Bulan Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datatangkap", $data);
      }elseif($sampai_tahun == ''){
        $data = array(
          'keterangan' => 'Sampai Tahun Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datatangkap", $data);
      }else{
        $query_tangkap  = array();
        $query_alat     = array();
        $query_perahu   = array();
        $query_usaha    = array();
        if($jenis_usaha){
          foreach($jenis_usaha as $q){
              $query_tangkap[$q] = $this->rekap_model->hasilTangkap($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $q);
              $query_alat[$q]    = $this->rekap_model->hasilTangkapalat($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $q);
              $query_perahu[$q]  = $this->rekap_model->hasilTangkapperahu($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $q);
          }
          $data = array(
            'tangkaps' => $query_tangkap,
            'alats' => $query_alat,
            'perahus' => $query_perahu,
            'jeniss' => $jenis_usaha,
            'keterangan' => '',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
          $this->load->view("content/laporan/rekap/_datatangkap", $data);
        }else{
          $data = array(
            'keterangan' => 'jenis Usaha Data Belum Ditentukan',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
          $this->load->view("content/laporan/rekap/_datatangkap", $data);
        }
      }
    }

    public function excel_tangkap()
    {
      $spreadsheet = new Spreadsheet();
      $jenis_usaha = $this->input->post('jenis_usaha');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');
      $query_budidaya = array();
      $query_usaha    = array();
      $jeniss = $this->rekap_model->ReffTangkap();
      $data = array(
          'content'    => 'content/laporan/rekap/rekaptangkap',
          'jeniss'  => $jeniss,
          'page'  => 'rekap_tangkap',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      if($dari_bulan == ''){
        $this->session->set_flashdata('danger','Dari Bulan Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($dari_tahun == ''){
        $this->session->set_flashdata('danger','Dari Tahun Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($sampai_bulan == ''){
        $this->session->set_flashdata('danger','Sampai Bulan Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($sampai_tahun == ''){
        $this->session->set_flashdata('danger','Sampai Tahun Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }elseif(!$jenis_usaha){
        $this->session->set_flashdata('danger','Jenis Usaha Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else{
        $spreadsheet->getProperties()->setCreator('Dinas Perikanan Sumenep')
        ->setLastModifiedBy('Dinas Perikanan Sumenep')
        ->setTitle('Laporan Rekap')
        ->setSubject('Laporan Rekap');
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $konfig = $this->kantor_model->cari();
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath('./public/images/pdf/'.$konfig->kantor_logo);
        $drawing->setHeight(90);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $spreadsheet->getActiveSheet()->getStyle('C1:L1')->getFont()->setBold(true)->setSize(14);
        $spreadsheet->getActiveSheet()->getStyle('C1:L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C2:L2')->getFont()->setBold(true)->setSize(14);
        $spreadsheet->getActiveSheet()->getStyle('C2:L2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->getStyle('C3:L3')->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getStyle('C3:L3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C4:L4')->getFont()->setBold(true)->setSize(12);
        $spreadsheet->getActiveSheet()->getStyle('C4:L4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C1:L1')->setCellValue('C1', 'PEMERINTAH KABUPATEN SUMENEP');
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C2:L2')->setCellValue('C2', $konfig->kantor_nama);
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C3:L3')->setCellValue('C3', $konfig->kantor_alamat.", Telp :".$konfig->kantor_telepon.", fax :".$konfig->kantor_fax.", email :".$konfig->kantor_email);
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C4:L4')->setCellValue('C4', 'SUMENEP');

        $spreadsheet->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('D6')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('B6', 'Dari')
        ->setCellValue('C6', ': '.$this->getbulan($dari_bulan).' '.$dari_tahun)
        ->setCellValue('D6', 'Sampai')
        ->setCellValue('E6', ': '.$this->getbulan($sampai_bulan).' '.$sampai_tahun);

        $spreadsheet->getActiveSheet()->getStyle('A7')->getFont()->setBold(true)->setSize(12);
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('A7:L7')->setCellValue('A7', 'JENIS USAHA');

        $spreadsheet->getActiveSheet()->getStyle('A8:L8')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A8:L8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A8', 'No')
        ->setCellValue('B8', 'Nama RTP')
        ->setCellValue('C8', 'NIK')
        ->setCellValue('D8', 'Alamat')
        ->setCellValue('E8', 'Desa')
        ->setCellValue('F8', 'Kecamatan')
        ->setCellValue('G8', 'Jenis')
        ->setCellValue('H8', 'Keterangan')
        ->setCellValue('I8', 'Jumlah Trip')
        ->setCellValue('J8', 'Produksi (ton)')
        ->setCellValue('K8', 'harga (Rp)')
        ->setCellValue('L8', 'Nilai Produksi (Rp)');

        $i=9;
        foreach($jenis_usaha as $q){
            $query_tangkap = $this->rekap_model->hasilTangkap($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $q);
            $no = 1;
            $produksi = 0;
            $harga    = 0;
            $nilai    = 0;
           foreach($query_tangkap as $tangkap){
              if($no == 1){
                $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
                $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':L'.$i)->setCellValue('A'.$i, $tangkap['jenis_tangkap_nama']);
                $i = $i + 1;
              }
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $tangkap['nelayan_nama'])
              ->setCellValueExplicit(
                    'C'.$i,
                    $tangkap['nelayan_nik'],
                    DataType::TYPE_STRING
                  )
              ->setCellValue('D'.$i, $tangkap['nelayan_alamat'])
              ->setCellValue('E'.$i, $tangkap['desa_nama'])
              ->setCellValue('F'.$i, $tangkap['kecamatan_nama'])
              ->setCellValue('G'.$i, $tangkap['daftar_tangkap_keterangan'])
              ->setCellValue('H'.$i, $tangkap['laporan_tangkap_keterangan'])
              ->setCellValue('I'.$i, $tangkap['trip'])
              ->setCellValue('J'.$i, $tangkap['produksi'])
              ->setCellValue('K'.$i, $tangkap['harga'])
              ->setCellValue('L'.$i, $tangkap['nilai']);
              $produksi = $produksi + $tangkap['produksi'];
              $harga    = $harga + $tangkap['harga'];
              $nilai    = $nilai + $tangkap['nilai'];
              if(count($query_tangkap) == $no){
                $i = $i + 1;
                $spreadsheet->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('J'.$i, $produksi)
                ->setCellValue('K'.$i, $harga)
                ->setCellValue('L'.$i, $nilai);
              }
              $i++;
              $no++;
           }
        }

        // ALAT
        $i = $i + 1;
        $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true)->setSize(12);
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, 'JENIS ALAT TANGKAP');
        $i = $i + 1;
        $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A'.$i, 'No')
        ->setCellValue('B'.$i, 'Nama RTP')
        ->setCellValue('C'.$i, 'NIK')
        ->setCellValue('D'.$i, 'Alamat')
        ->setCellValue('E'.$i, 'Desa')
        ->setCellValue('F'.$i, 'Kecamatan')
        ->setCellValue('G'.$i, 'Jenis')
        ->setCellValue('H'.$i, 'Keterangan')
        ->setCellValue('I'.$i, 'Jumlah');
        $i = $i + 1;
        foreach($jenis_usaha as $q){
            $query_alat = $this->rekap_model->hasilTangkapalat($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $q);
            $no = 1;
            $produksi = 0;
           foreach($query_alat as $tangkap){
              if($no == 1){
                $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
                $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, $tangkap['jenis_tangkap_nama']);
                $i = $i + 1;
              }
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $tangkap['nelayan_nama'])
              ->setCellValueExplicit(
                    'C'.$i,
                    $tangkap['nelayan_nik'],
                    DataType::TYPE_STRING
                  )
              ->setCellValue('D'.$i, $tangkap['nelayan_alamat'])
              ->setCellValue('E'.$i, $tangkap['desa_nama'])
              ->setCellValue('F'.$i, $tangkap['kecamatan_nama'])
              ->setCellValue('G'.$i, $tangkap['alat_tangkap_nama'])
              ->setCellValue('H'.$i, $tangkap['laporan_tangkap_alat_keterangan'])
              ->setCellValue('I'.$i, $tangkap['jumlah']);
              $produksi = $produksi + $tangkap['jumlah'];
              if(count($query_alat) == $no){
                $i = $i + 1;
                $spreadsheet->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':H'.$i)->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('I'.$i, $produksi);
              }
              $i++;
              $no++;
           }
        }

        // Perahu
        $i = $i + 1;
        $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true)->setSize(12);
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, 'JENIS PERAHU');
        $i = $i + 1;
        $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A'.$i, 'No')
        ->setCellValue('B'.$i, 'Nama RTP')
        ->setCellValue('C'.$i, 'NIK')
        ->setCellValue('D'.$i, 'Alamat')
        ->setCellValue('E'.$i, 'Desa')
        ->setCellValue('F'.$i, 'Kecamatan')
        ->setCellValue('G'.$i, 'Jenis')
        ->setCellValue('H'.$i, 'Keterangan')
        ->setCellValue('I'.$i, 'Jumlah');

        $i = $i + 1;
        foreach($jenis_usaha as $q){
            $query_perahu = $this->rekap_model->hasilTangkapperahu($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $q);
            $no = 1;
            $produksi = 0;
           foreach($query_perahu as $tangkap){
              if($no == 1){
                $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
                $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':F'.$i)->setCellValue('A'.$i, $tangkap['jenis_tangkap_nama']);
                $i = $i + 1;
              }
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $tangkap['nelayan_nama'])
              ->setCellValueExplicit(
                    'C'.$i,
                    $tangkap['nelayan_nik'],
                    DataType::TYPE_STRING
                  )
              ->setCellValue('D'.$i, $tangkap['nelayan_alamat'])
              ->setCellValue('E'.$i, $tangkap['desa_nama'])
              ->setCellValue('F'.$i, $tangkap['kecamatan_nama'])
              ->setCellValue('G'.$i, $tangkap['perahu_tangkap_nama'])
              ->setCellValue('H'.$i, $tangkap['laporan_tangkap_perahu_keterangan'])
              ->setCellValue('I'.$i, $tangkap['jumlah']);
              $produksi = $produksi + $tangkap['jumlah'];
              if(count($query_perahu) == $no){
                $i = $i + 1;
                $spreadsheet->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':H'.$i)->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('I'.$i, $produksi);
              }
              $i++;
              $no++;
           }
        }

        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Rekap Tangkap');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Rekap Usaha Tangkap.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
      }
    }

    public function Garam()
    {
      level_user('rekapgaram','index',$this->session->userdata('user_role'),'read') > 0 ? '': show_404();

      $jeniss = $this->rekap_model->ReffGaram();
      $data = array(
          'content'    => 'content/laporan/rekap/rekapgaram',
          'jeniss'  => $jeniss,
          'page'  => 'rekap_garam',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }

    public function daftar_garam()
    {
      $jenis_usaha = $this->input->post('jenis_usaha');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');
      if($dari_bulan == ''){
        $data = array(
          'keterangan' => 'Dari Bulan Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datagaram", $data);
      }elseif($dari_tahun == ''){
        $data = array(
          'keterangan' => 'Dari Tahun Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datagaram", $data);
      }elseif($sampai_bulan == ''){
        $data = array(
          'keterangan' => 'Sampai Bulan Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datagaram", $data);
      }elseif($sampai_tahun == ''){
        $data = array(
          'keterangan' => 'Sampai Tahun Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datagaram", $data);
      }else{
        $query_budidaya = array();
        $query_usaha    = array();
        if($jenis_usaha){
          foreach($jenis_usaha as $q){
              $query_budidaya[$q] = $this->rekap_model->hasilGaram($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $q);
          }
          $data = array(
            'budidayas' => $query_budidaya,
            'jeniss' => $jenis_usaha,
            'keterangan' => '',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
          $this->load->view("content/laporan/rekap/_datagaram", $data);
        }else{
          $data = array(
            'keterangan' => 'jenis Usaha Data Belum Ditentukan',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
          $this->load->view("content/laporan/rekap/_datagaram", $data);
        }

      }

    }

    public function excel_garam()
    {
      $spreadsheet = new Spreadsheet();
      $jenis_usaha = $this->input->post('jenis_usaha');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');
      $query_budidaya = array();
      $query_usaha    = array();
      $jeniss = $this->rekap_model->ReffGaram();
      $data = array(
          'content'    => 'content/laporan/rekap/rekapgaram',
          'jeniss'  => $jeniss,
          'page'  => 'rekap_garam',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      if($dari_bulan == ''){
        $this->session->set_flashdata('danger','Dari Bulan Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($dari_tahun == ''){
        $this->session->set_flashdata('danger','Dari Tahun Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($sampai_bulan == ''){
        $this->session->set_flashdata('danger','Sampai Bulan Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($sampai_tahun == ''){
        $this->session->set_flashdata('danger','Sampai Tahun Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }elseif(!$jenis_usaha){
        $this->session->set_flashdata('danger','Jenis Usaha Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else{
        $spreadsheet->getProperties()->setCreator('Dinas Perikanan Sumenep')
        ->setLastModifiedBy('Dinas Perikanan Sumenep')
        ->setTitle('Laporan Rekap')
        ->setSubject('Laporan Rekap');
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $konfig = $this->kantor_model->cari();
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath('./public/images/pdf/'.$konfig->kantor_logo);
        $drawing->setHeight(90);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $spreadsheet->getActiveSheet()->getStyle('C1:L1')->getFont()->setBold(true)->setSize(14);
        $spreadsheet->getActiveSheet()->getStyle('C1:L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C2:L2')->getFont()->setBold(true)->setSize(14);
        $spreadsheet->getActiveSheet()->getStyle('C2:L2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->getStyle('C3:L3')->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getStyle('C3:L3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C4:L4')->getFont()->setBold(true)->setSize(12);
        $spreadsheet->getActiveSheet()->getStyle('C4:L4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C1:L1')->setCellValue('C1', 'PEMERINTAH KABUPATEN SUMENEP');
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C2:L2')->setCellValue('C2', $konfig->kantor_nama);
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C3:L3')->setCellValue('C3', $konfig->kantor_alamat.", Telp :".$konfig->kantor_telepon.", fax :".$konfig->kantor_fax.", email :".$konfig->kantor_email);
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C4:L4')->setCellValue('C4', 'SUMENEP');

        $spreadsheet->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('D6')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('B6', 'Dari')
        ->setCellValue('C6', ': '.$this->getbulan($dari_bulan).' '.$dari_tahun)
        ->setCellValue('D6', 'Sampai')
        ->setCellValue('E6', ': '.$this->getbulan($sampai_bulan).' '.$sampai_tahun);
        $spreadsheet->getActiveSheet()->getStyle('A7:L7')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A7:L7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A7', 'No')
        ->setCellValue('B7', 'Nama RTP')
        ->setCellValue('C7', 'NIK')
        ->setCellValue('D7', 'Alamat')
        ->setCellValue('E7', 'Desa')
        ->setCellValue('F7', 'Kecamatan')
        ->setCellValue('G7', 'Daftar')
        ->setCellValue('H7', 'Keterangan')
        ->setCellValue('I7', 'luas (m2)')
        ->setCellValue('J7', 'Produksi (ton)')
        ->setCellValue('K7', 'harga (Rp)')
        ->setCellValue('L7', 'Nilai Produksi (Rp)');

        $i=8;
        foreach($jenis_usaha as $q){
            $query_budidaya = $this->rekap_model->hasilGaram($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $q);
            $no = 1;
            $produksi = 0;
            $harga    = 0;
            $nilai    = 0;
           foreach($query_budidaya as $budidaya){
              if($no == 1){
                $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
                $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':L'.$i)->setCellValue('A'.$i, $budidaya['tipe_budidaya_nama']);
                $i = $i + 1;
              }
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $budidaya['nelayan_nama'])
              ->setCellValueExplicit(
                    'C'.$i,
                    $budidaya['nelayan_nik'],
                    DataType::TYPE_STRING
                  )
              ->setCellValue('D'.$i, $budidaya['nelayan_alamat'])
              ->setCellValue('E'.$i, $budidaya['desa_nama'])
              ->setCellValue('F'.$i, $budidaya['kecamatan_nama'])
              ->setCellValue('G'.$i, $budidaya['daftar_budidaya_keterangan'])
              ->setCellValue('H'.$i, $budidaya['laporan_budidaya_keterangan'])
              ->setCellValue('I'.$i, $budidaya['luas'])
              ->setCellValue('J'.$i, $budidaya['produksi'])
              ->setCellValue('K'.$i, $budidaya['harga'])
              ->setCellValue('L'.$i, $budidaya['nilai']);
              $produksi = $produksi + $budidaya['produksi'];
              $harga    = $harga + $budidaya['harga'];
              $nilai    = $nilai + $budidaya['nilai'];
              if(count($query_budidaya) == $no){
                $i = $i + 1;
                $spreadsheet->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('J'.$i, $produksi)
                ->setCellValue('K'.$i, $harga)
                ->setCellValue('L'.$i, $nilai);
              }
              $i++;
              $no++;
           }
        }
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Rekap Budidaya Garam');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Rekap Budidaya Garam.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
      }
    }

    public function Wilayah()
    {
      level_user('rekapwilayah','index',$this->session->userdata('user_role'),'read') > 0 ? '': show_404();

      $data = array(
          'content'    => 'content/laporan/rekap/rekapwilayah',
          'page'  => 'rekap_wilayah',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }

    public function lanjut_wilayah()
    {
      level_user('rekapwilayah','index',$this->session->userdata('user_role'),'read') > 0 ? '': show_404();

      $jenis_wilayah = $this->input->post('jenis_wilayah');
      if($jenis_wilayah == 'Kecamatan'){
        $data = array(
            'content'    => 'content/laporan/rekap/rekapkecamatan',
            'page'  => 'rekap_wilayah',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
        );
      }elseif($jenis_wilayah == 'Desa'){
        $jeniss = $this->apps->getAll('perikanan_ref_kecamatan');
        $data = array(
            'content'    => 'content/laporan/rekap/rekapdesa',
            'jeniss' => $jeniss,
            'page'  => 'rekap_wilayah',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
        );
      }else{
        $data = array(
            'content'    => 'content/laporan/rekap/rekapwilayah',
            'page'  => 'rekap_wilayah',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
        );
      }
      $this->load->view("layout/template", $data);
    }

    public function daftar_kecamatan()
    {
      $jenis_usaha = $this->input->post('jenis_usaha');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');

      $query_budidaya = array();
      $query_tangkap  = array();
      $query_lahsar   = array();
      $query_garam    = array();
      if($dari_bulan == ''){
        $data = array(
          'budidayas' => $query_budidaya,
          'tangkaps' => $query_tangkap,
          'lahsars' => $query_lahsar,
          'garams' => $query_garam,
          'keterangan' => 'Dari Bulan Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datakecamatan", $data);
      }elseif($dari_tahun == ''){
        $data = array(
          'budidayas' => $query_budidaya,
          'tangkaps' => $query_tangkap,
          'lahsars' => $query_lahsar,
          'garams' => $query_garam,
          'keterangan' => 'Dari Tahun Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datakecamatan", $data);
      }elseif($sampai_bulan == ''){
        $data = array(
          'budidayas' => $query_budidaya,
          'tangkaps' => $query_tangkap,
          'lahsars' => $query_lahsar,
          'garams' => $query_garam,
          'keterangan' => 'Sampai Bulan Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datakecamatan", $data);
      }elseif($sampai_tahun == ''){
        $data = array(
          'budidayas' => $query_budidaya,
          'tangkaps' => $query_tangkap,
          'lahsars' => $query_lahsar,
          'garams' => $query_garam,
          'keterangan' => 'Sampai Tahun Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datakecamatan", $data);
      }else{
        if ($jenis_usaha) {
          foreach($jenis_usaha as $q){
            if($q == 'budidaya'){
              $query_budidaya = $this->rekap_model->dataBudidayawil($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun);
            }elseif($q == 'tangkap'){
              $query_tangkap = $this->rekap_model->dataTangkapwil($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun);
            }elseif($q == 'lahsar'){
              $query_lahsar = $this->rekap_model->dataLahsarwil($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun);
            }elseif($q == 'garam'){
              $query_garam = $this->rekap_model->dataGaramwil($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun);
            }
          }
          $data = array(
            'budidayas' => $query_budidaya,
            'tangkaps' => $query_tangkap,
            'lahsars' => $query_lahsar,
            'garams' => $query_garam,
            'keterangan' => '',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
          $this->load->view("content/laporan/rekap/_datakecamatan", $data);
        }else{
          $data = array(
            'budidayas' => $query_budidaya,
            'tangkaps' => $query_tangkap,
            'lahsars' => $query_lahsar,
            'garams' => $query_garam,
            'keterangan' => 'Jenis Usaha belum Ditentukan',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
          $this->load->view("content/laporan/rekap/_datakecamatan", $data);
        }
      }
    }

    public function daftar_desa()
    {
      $jenis_usaha  = $this->input->post('jenis_usaha');
      $kecamatan    = $this->input->post('kecamatan');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');

      $query_budidaya = array();
      $query_tangkap  = array();
      $query_lahsar   = array();
      $query_garam    = array();
      if($dari_bulan == ''){
        $data = array(
          'budidayas' => $query_budidaya,
          'tangkaps' => $query_tangkap,
          'lahsars' => $query_lahsar,
          'garams' => $query_garam,
          'keterangan' => 'Dari Bulan Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datadesa", $data);
      }elseif($dari_tahun == ''){
        $data = array(
          'budidayas' => $query_budidaya,
          'tangkaps' => $query_tangkap,
          'lahsars' => $query_lahsar,
          'garams' => $query_garam,
          'keterangan' => 'Dari Tahun Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datadesa", $data);
      }elseif($sampai_bulan == ''){
        $data = array(
          'budidayas' => $query_budidaya,
          'tangkaps' => $query_tangkap,
          'lahsars' => $query_lahsar,
          'garams' => $query_garam,
          'keterangan' => 'Sampai Bulan Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datadesa", $data);
      }elseif($sampai_tahun == ''){
        $data = array(
          'budidayas' => $query_budidaya,
          'tangkaps' => $query_tangkap,
          'lahsars' => $query_lahsar,
          'garams' => $query_garam,
          'keterangan' => 'Sampai Tahun Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datadesa", $data);
      }elseif(!$kecamatan){
        $data = array(
          'budidayas' => $query_budidaya,
          'tangkaps' => $query_tangkap,
          'lahsars' => $query_lahsar,
          'garams' => $query_garam,
          'keterangan' => 'Kecamatan Data Belum Ditentukan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
        );
        $this->load->view("content/laporan/rekap/_datadesa", $data);
      }else{
        if ($jenis_usaha) {
          foreach($jenis_usaha as $q){
            if($q == 'budidaya'){
              $query_budidaya = $this->rekap_model->dataBudidayadesa($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $kecamatan);
            }elseif($q == 'tangkap'){
              $query_tangkap = $this->rekap_model->dataTangkapdesa($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $kecamatan);
            }elseif($q == 'lahsar'){
              $query_lahsar = $this->rekap_model->dataLahsardesa($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $kecamatan);
            }elseif($q == 'garam'){
              $query_garam = $this->rekap_model->dataGaramdesa($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $kecamatan);
            }
          }
          if(!$query_budidaya && !$query_tangkap && !$query_lahsar && !$query_garam){
            $data = array(
              'budidayas' => $query_budidaya,
              'tangkaps' => $query_tangkap,
              'lahsars' => $query_lahsar,
              'garams' => $query_garam,
              'keterangan' => 'Data Tidak Ditemukan',
              'csrf' => array(
                  'name' => $this->security->get_csrf_token_name(),
                  'hash' => $this->security->get_csrf_hash()
              )
            );
            $this->load->view("content/laporan/rekap/_datadesa", $data);
          }else{
            $data = array(
              'budidayas' => $query_budidaya,
              'tangkaps' => $query_tangkap,
              'lahsars' => $query_lahsar,
              'garams' => $query_garam,
              'keterangan' => '',
              'csrf' => array(
                  'name' => $this->security->get_csrf_token_name(),
                  'hash' => $this->security->get_csrf_hash()
              )
            );
            $this->load->view("content/laporan/rekap/_datadesa", $data);
          }

        }else{
          $data = array(
            'budidayas' => $query_budidaya,
            'tangkaps' => $query_tangkap,
            'lahsars' => $query_lahsar,
            'garams' => $query_garam,
            'keterangan' => 'Jenis Usaha belum Ditentukan',
            'csrf' => array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            )
          );
          $this->load->view("content/laporan/rekap/_datadesa", $data);
        }
      }
    }
    public function excel_desa()
    {
      $spreadsheet = new Spreadsheet();
      $jenis_usaha = $this->input->post('jenis_usaha');
      $kecamatan    = $this->input->post('kecamatan');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');

      $query_budidaya = array();
      $query_tangkap  = array();
      $query_lahsar   = array();
      $query_garam    = array();
      $data = array(
          'content'    => 'content/laporan/rekap/rekapdesa',
          'page'  => 'rekap_wilayah',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      if($dari_bulan == ''){
        $this->session->set_flashdata('danger','Dari Bulan Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($dari_tahun == ''){
        $this->session->set_flashdata('danger','Dari Tahun Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($sampai_bulan == ''){
        $this->session->set_flashdata('danger','Sampai Bulan Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($sampai_tahun == ''){
        $this->session->set_flashdata('danger','Sampai Tahun Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }elseif(!$jenis_usaha){
        $this->session->set_flashdata('danger','Jenis Usaha Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }elseif(!$kecamatan){
        $this->session->set_flashdata('danger','Kecamatan Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else{

        $spreadsheet->getProperties()->setCreator('Dinas Perikanan Sumenep')
        ->setLastModifiedBy('Dinas Perikanan Sumenep')
        ->setTitle('Laporan Rekap')
        ->setSubject('Laporan Rekap');
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $konfig = $this->kantor_model->cari();
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath('./public/images/pdf/'.$konfig->kantor_logo);
        $drawing->setHeight(90);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $spreadsheet->getActiveSheet()->getStyle('C1:I1')->getFont()->setBold(true)->setSize(14);
        $spreadsheet->getActiveSheet()->getStyle('C1:I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C2:I2')->getFont()->setBold(true)->setSize(14);
        $spreadsheet->getActiveSheet()->getStyle('C2:I2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->getStyle('C3:I3')->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getStyle('C3:I3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C4:I4')->getFont()->setBold(true)->setSize(12);
        $spreadsheet->getActiveSheet()->getStyle('C4:I4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C1:I1')->setCellValue('C1', 'PEMERINTAH KABUPATEN SUMENEP');
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C2:I2')->setCellValue('C2', $konfig->kantor_nama);
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C3:I3')->setCellValue('C3', $konfig->kantor_alamat.", Telp :".$konfig->kantor_telepon.", fax :".$konfig->kantor_fax.", email :".$konfig->kantor_email);
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C4:I4')->setCellValue('C4', 'SUMENEP');
        $kecamatan_nama = $this->apps->find('perikanan_ref_kecamatan', 'kecamatan_kode', $kecamatan);
        $spreadsheet->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('D6')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('B6', 'Dari')
        ->setCellValue('C6', ': '.$this->getbulan($dari_bulan).' '.$dari_tahun)
        ->setCellValue('D6', 'Sampai')
        ->setCellValue('E6', ': '.$this->getbulan($sampai_bulan).' '.$sampai_tahun);

        $spreadsheet->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('B7', 'Kecamatan')
        ->setCellValue('C7', ': '.$kecamatan_nama->kecamatan_nama);
        $i = 8;
        foreach($jenis_usaha as $q){
          if($q == 'budidaya'){
            $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
            $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, 'Data Usaha Budidaya');
            $i = $i + 1;
            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 'No')
            ->setCellValue('B'.$i, 'Desa')
            ->setCellValue('C'.$i, 'Tipe Usaha')
            ->setCellValue('D'.$i, 'Daftar')
            ->setCellValue('E'.$i, 'Keterangan')
            ->setCellValue('F'.$i, 'luas (m2)')
            ->setCellValue('G'.$i, 'Produksi (ton)')
            ->setCellValue('H'.$i, 'harga (Rp)')
            ->setCellValue('I'.$i, 'Nilai Produksi (Rp)');

            $query_budidaya = $this->rekap_model->dataBudidayadesa($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $kecamatan);
            $no = 1;
            $produksi = 0;
            $harga    = 0;
            $nilai    = 0;
            $i = $i + 1;
            foreach($query_budidaya as $budidaya){
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $budidaya['desa_nama'])
              ->setCellValue('C'.$i, $budidaya['tipe_budidaya_nama'])
              ->setCellValue('D'.$i, $budidaya['daftar_budidaya_keterangan'])
              ->setCellValue('E'.$i, $budidaya['laporan_budidaya_keterangan'])
              ->setCellValue('F'.$i, $budidaya['luas'])
              ->setCellValue('G'.$i, $budidaya['produksi'])
              ->setCellValue('H'.$i, $budidaya['harga'])
              ->setCellValue('I'.$i, $budidaya['nilai']);
              $produksi = $produksi + $budidaya['produksi'];
              $harga    = $harga + $budidaya['harga'];
              $nilai    = $nilai + $budidaya['nilai'];
              if(count($query_budidaya) == $no){
                $i = $i + 1;
                $spreadsheet->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':F'.$i)->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('G'.$i, $produksi)
                ->setCellValue('H'.$i, $harga)
                ->setCellValue('I'.$i, $nilai);
              }
              $i++;
              $no++;
            }
          }elseif($q == 'tangkap'){
            $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
            $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, 'Data Usaha tangkap');
            $i = $i + 1;

            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 'No')
            ->setCellValue('B'.$i, 'Desa')
            ->setCellValue('C'.$i, 'Tipe Usaha')
            ->setCellValue('D'.$i, 'Daftar')
            ->setCellValue('E'.$i, 'Keterangan')
            ->setCellValue('F'.$i, 'Jumlah Trip')
            ->setCellValue('G'.$i, 'Produksi (ton)')
            ->setCellValue('H'.$i, 'harga (Rp)')
            ->setCellValue('I'.$i, 'Nilai Produksi (Rp)');

            $query_tangkap = $this->rekap_model->dataTangkapdesa($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $kecamatan);
            $no = 1;
            $produksi = 0;
            $harga    = 0;
            $nilai    = 0;
            $i = $i + 1;
            foreach($query_tangkap as $tangkap){
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $tangkap['desa_nama'])
              ->setCellValue('C'.$i, $tangkap['jenis_tangkap_nama'])
              ->setCellValue('D'.$i, $tangkap['daftar_tangkap_keterangan'])
              ->setCellValue('E'.$i, $tangkap['laporan_tangkap_keterangan'])
              ->setCellValue('F'.$i, $tangkap['trip'])
              ->setCellValue('G'.$i, $tangkap['produksi'])
              ->setCellValue('H'.$i, $tangkap['harga'])
              ->setCellValue('I'.$i, $tangkap['nilai']);
              $produksi = $produksi + $tangkap['produksi'];
              $harga    = $harga + $tangkap['harga'];
              $nilai    = $nilai + $tangkap['nilai'];
              if(count($query_tangkap) == $no){
                $i = $i + 1;
                $spreadsheet->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':F'.$i)->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('G'.$i, $produksi)
                ->setCellValue('H'.$i, $harga)
                ->setCellValue('I'.$i, $nilai);
              }
              $i++;
              $no++;
            }
          }elseif($q == 'lahsar'){
            $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
            $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':H'.$i)->setCellValue('A'.$i, 'Data Usaha Pengolahan dan Pemasaran');
            $i = $i + 1;

            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 'No')
            ->setCellValue('B'.$i, 'Desa')
            ->setCellValue('C'.$i, 'Tipe Usaha')
            ->setCellValue('D'.$i, 'Daftar')
            ->setCellValue('E'.$i, 'Keterangan')
            ->setCellValue('F'.$i, 'Produksi (ton)')
            ->setCellValue('G'.$i, 'harga (Rp)')
            ->setCellValue('H'.$i, 'Nilai Produksi (Rp)');

            $query_lahsar = $this->rekap_model->dataLahsardesa($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $kecamatan);
            $no = 1;
            $produksi = 0;
            $harga    = 0;
            $nilai    = 0;
            $i = $i + 1;
            foreach($query_lahsar as $lahsar){
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $lahsar['desa_nama'])
              ->setCellValue('C'.$i, $lahsar['tipe_lahsar_nama'])
              ->setCellValue('D'.$i, $lahsar['daftar_lahsar_keterangan'])
              ->setCellValue('E'.$i, $lahsar['laporan_lahsar_keterangan'])
              ->setCellValue('F'.$i, $lahsar['produksi'])
              ->setCellValue('G'.$i, $lahsar['harga'])
              ->setCellValue('H'.$i, $lahsar['nilai']);
              $produksi = $produksi + $lahsar['produksi'];
              $harga    = $harga + $lahsar['harga'];
              $nilai    = $nilai + $lahsar['nilai'];
              if(count($query_lahsar) == $no){
                $i = $i + 1;
                $spreadsheet->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':F'.$i)->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('F'.$i, $produksi)
                ->setCellValue('G'.$i, $harga)
                ->setCellValue('H'.$i, $nilai);
              }
              $i++;
              $no++;
            }
          }elseif($q == 'garam'){
            $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
            $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, 'Data Usaha Budidaya Garam');
            $i = $i + 1;

            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 'No')
            ->setCellValue('B'.$i, 'Desa')
            ->setCellValue('C'.$i, 'Tipe Usaha')
            ->setCellValue('D'.$i, 'Daftar')
            ->setCellValue('E'.$i, 'Keterangan')
            ->setCellValue('F'.$i, 'luas (m2)')
            ->setCellValue('G'.$i, 'Produksi (ton)')
            ->setCellValue('H'.$i, 'harga (Rp)')
            ->setCellValue('I'.$i, 'Nilai Produksi (Rp)');

            $query_garam = $this->rekap_model->dataGaramdesa($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $kecamatan);
            $no = 1;
            $produksi = 0;
            $harga    = 0;
            $nilai    = 0;
            $i = $i + 1;
            foreach($query_garam as $garam){
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $garam['desa_nama'])
              ->setCellValue('C'.$i, $garam['tipe_budidaya_nama'])
              ->setCellValue('D'.$i, $garam['daftar_budidaya_keterangan'])
              ->setCellValue('E'.$i, $garam['laporan_budidaya_keterangan'])
              ->setCellValue('F'.$i, $garam['luas'])
              ->setCellValue('G'.$i, $garam['produksi'])
              ->setCellValue('H'.$i, $garam['harga'])
              ->setCellValue('I'.$i, $garam['nilai']);
              $produksi = $produksi + $garam['produksi'];
              $harga    = $harga + $garam['harga'];
              $nilai    = $nilai + $garam['nilai'];
              if(count($query_garam) == $no){
                $i = $i + 1;
                $spreadsheet->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':F'.$i)->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('G'.$i, $produksi)
                ->setCellValue('H'.$i, $harga)
                ->setCellValue('I'.$i, $nilai);
              }
              $i++;
              $no++;
            }
          }
          $i = $i +1;
        }
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Rekap Per Wilayah');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Rekap Per Wilayah - Desa.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
      }
    }
    public function excel_kecamatan()
    {
      $spreadsheet = new Spreadsheet();
      $jenis_usaha = $this->input->post('jenis_usaha');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');

      $query_budidaya = array();
      $query_tangkap  = array();
      $query_lahsar   = array();
      $query_garam    = array();
      $data = array(
          'content'    => 'content/laporan/rekap/rekapkecamatan',
          'page'  => 'rekap_wilayah',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      if($dari_bulan == ''){
        $this->session->set_flashdata('danger','Dari Bulan Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($dari_tahun == ''){
        $this->session->set_flashdata('danger','Dari Tahun Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($sampai_bulan == ''){
        $this->session->set_flashdata('danger','Sampai Bulan Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else if($sampai_tahun == ''){
        $this->session->set_flashdata('danger','Sampai Tahun Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }elseif(!$jenis_usaha){
        $this->session->set_flashdata('danger','Jenis Usaha Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else{

        $spreadsheet->getProperties()->setCreator('Dinas Perikanan Sumenep')
        ->setLastModifiedBy('Dinas Perikanan Sumenep')
        ->setTitle('Laporan Rekap')
        ->setSubject('Laporan Rekap');
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $konfig = $this->kantor_model->cari();
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath('./public/images/pdf/'.$konfig->kantor_logo);
        $drawing->setHeight(90);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $spreadsheet->getActiveSheet()->getStyle('C1:I1')->getFont()->setBold(true)->setSize(14);
        $spreadsheet->getActiveSheet()->getStyle('C1:I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C2:I2')->getFont()->setBold(true)->setSize(14);
        $spreadsheet->getActiveSheet()->getStyle('C2:I2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->getStyle('C3:I3')->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->getStyle('C3:I3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C4:I4')->getFont()->setBold(true)->setSize(12);
        $spreadsheet->getActiveSheet()->getStyle('C4:I4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C1:I1')->setCellValue('C1', 'PEMERINTAH KABUPATEN SUMENEP');
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C2:I2')->setCellValue('C2', $konfig->kantor_nama);
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C3:I3')->setCellValue('C3', $konfig->kantor_alamat.", Telp :".$konfig->kantor_telepon.", fax :".$konfig->kantor_fax.", email :".$konfig->kantor_email);
        $spreadsheet->setActiveSheetIndex(0)->mergeCells('C4:I4')->setCellValue('C4', 'SUMENEP');

        $spreadsheet->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('D6')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('B6', 'Dari')
        ->setCellValue('C6', ': '.$this->getbulan($dari_bulan).' '.$dari_tahun)
        ->setCellValue('D6', 'Sampai')
        ->setCellValue('E6', ': '.$this->getbulan($sampai_bulan).' '.$sampai_tahun);
        $i = 7;
        foreach($jenis_usaha as $q){
          if($q == 'budidaya'){
            $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
            $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, 'Data Usaha Budidaya');
            $i = $i + 1;
            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 'No')
            ->setCellValue('B'.$i, 'Kecamatan')
            ->setCellValue('C'.$i, 'Tipe Usaha')
            ->setCellValue('D'.$i, 'Daftar')
            ->setCellValue('E'.$i, 'Keterangan')
            ->setCellValue('F'.$i, 'luas (m2)')
            ->setCellValue('G'.$i, 'Produksi (ton)')
            ->setCellValue('H'.$i, 'harga (Rp)')
            ->setCellValue('I'.$i, 'Nilai Produksi (Rp)');

            $query_budidaya = $this->rekap_model->dataBudidayawil($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun);
            $no = 1;
            $produksi = 0;
            $harga    = 0;
            $nilai    = 0;
            $i = $i + 1;
            foreach($query_budidaya as $budidaya){
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $budidaya['kecamatan_nama'])
              ->setCellValue('C'.$i, $budidaya['tipe_budidaya_nama'])
              ->setCellValue('D'.$i, $budidaya['daftar_budidaya_keterangan'])
              ->setCellValue('E'.$i, $budidaya['laporan_budidaya_keterangan'])
              ->setCellValue('F'.$i, $budidaya['luas'])
              ->setCellValue('G'.$i, $budidaya['produksi'])
              ->setCellValue('H'.$i, $budidaya['harga'])
              ->setCellValue('I'.$i, $budidaya['nilai']);
              $produksi = $produksi + $budidaya['produksi'];
              $harga    = $harga + $budidaya['harga'];
              $nilai    = $nilai + $budidaya['nilai'];
              if(count($query_budidaya) == $no){
                $i = $i + 1;
                $spreadsheet->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':F'.$i)->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('G'.$i, $produksi)
                ->setCellValue('H'.$i, $harga)
                ->setCellValue('I'.$i, $nilai);
              }
              $i++;
              $no++;
            }
          }elseif($q == 'tangkap'){
            $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
            $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, 'Data Usaha tangkap');
            $i = $i + 1;

            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 'No')
            ->setCellValue('B'.$i, 'Kecamatan')
            ->setCellValue('C'.$i, 'Tipe Usaha')
            ->setCellValue('D'.$i, 'Daftar')
            ->setCellValue('E'.$i, 'Keterangan')
            ->setCellValue('F'.$i, 'Jumlah Trip')
            ->setCellValue('G'.$i, 'Produksi (ton)')
            ->setCellValue('H'.$i, 'harga (Rp)')
            ->setCellValue('I'.$i, 'Nilai Produksi (Rp)');

            $query_tangkap = $this->rekap_model->dataTangkapwil($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun);
            $no = 1;
            $produksi = 0;
            $harga    = 0;
            $nilai    = 0;
            $i = $i + 1;
            foreach($query_tangkap as $tangkap){
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $tangkap['kecamatan_nama'])
              ->setCellValue('C'.$i, $tangkap['jenis_tangkap_nama'])
              ->setCellValue('D'.$i, $tangkap['daftar_tangkap_keterangan'])
              ->setCellValue('E'.$i, $tangkap['laporan_tangkap_keterangan'])
              ->setCellValue('F'.$i, $tangkap['trip'])
              ->setCellValue('G'.$i, $tangkap['produksi'])
              ->setCellValue('H'.$i, $tangkap['harga'])
              ->setCellValue('I'.$i, $tangkap['nilai']);
              $produksi = $produksi + $tangkap['produksi'];
              $harga    = $harga + $tangkap['harga'];
              $nilai    = $nilai + $tangkap['nilai'];
              if(count($query_tangkap) == $no){
                $i = $i + 1;
                $spreadsheet->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':F'.$i)->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('G'.$i, $produksi)
                ->setCellValue('H'.$i, $harga)
                ->setCellValue('I'.$i, $nilai);
              }
              $i++;
              $no++;
            }
          }elseif($q == 'lahsar'){
            $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
            $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':H'.$i)->setCellValue('A'.$i, 'Data Usaha Pengolahan dan Pemasaran');
            $i = $i + 1;

            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 'No')
            ->setCellValue('B'.$i, 'Kecamatan')
            ->setCellValue('C'.$i, 'Tipe Usaha')
            ->setCellValue('D'.$i, 'Daftar')
            ->setCellValue('E'.$i, 'Keterangan')
            ->setCellValue('F'.$i, 'Produksi (ton)')
            ->setCellValue('G'.$i, 'harga (Rp)')
            ->setCellValue('H'.$i, 'Nilai Produksi (Rp)');

            $query_lahsar = $this->rekap_model->dataLahsarwil($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun);
            $no = 1;
            $produksi = 0;
            $harga    = 0;
            $nilai    = 0;
            $i = $i + 1;
            foreach($query_lahsar as $lahsar){
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $lahsar['kecamatan_nama'])
              ->setCellValue('C'.$i, $lahsar['tipe_lahsar_nama'])
              ->setCellValue('D'.$i, $lahsar['daftar_lahsar_keterangan'])
              ->setCellValue('E'.$i, $lahsar['laporan_lahsar_keterangan'])
              ->setCellValue('F'.$i, $lahsar['produksi'])
              ->setCellValue('G'.$i, $lahsar['harga'])
              ->setCellValue('H'.$i, $lahsar['nilai']);
              $produksi = $produksi + $lahsar['produksi'];
              $harga    = $harga + $lahsar['harga'];
              $nilai    = $nilai + $lahsar['nilai'];
              if(count($query_lahsar) == $no){
                $i = $i + 1;
                $spreadsheet->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':F'.$i)->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('F'.$i, $produksi)
                ->setCellValue('G'.$i, $harga)
                ->setCellValue('H'.$i, $nilai);
              }
              $i++;
              $no++;
            }
          }elseif($q == 'garam'){
            $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
            $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$i.':I'.$i)->setCellValue('A'.$i, 'Data Usaha Budidaya Garam');
            $i = $i + 1;
            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 'No')
            ->setCellValue('B'.$i, 'Kecamatan')
            ->setCellValue('C'.$i, 'Tipe Usaha')
            ->setCellValue('D'.$i, 'Daftar')
            ->setCellValue('E'.$i, 'Keterangan')
            ->setCellValue('F'.$i, 'luas (m2)')
            ->setCellValue('G'.$i, 'Produksi (ton)')
            ->setCellValue('H'.$i, 'harga (Rp)')
            ->setCellValue('I'.$i, 'Nilai Produksi (Rp)');

            $query_garam = $this->rekap_model->dataGaramwil($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun);
            $no = 1;
            $produksi = 0;
            $harga    = 0;
            $nilai    = 0;
            $i = $i + 1;
            foreach($query_garam as $garam){
              $spreadsheet->setActiveSheetIndex(0)
              ->setCellValue('A'.$i, $no)
              ->setCellValue('B'.$i, $garam['kecamatan_nama'])
              ->setCellValue('C'.$i, $garam['tipe_budidaya_nama'])
              ->setCellValue('D'.$i, $garam['daftar_budidaya_keterangan'])
              ->setCellValue('E'.$i, $garam['laporan_budidaya_keterangan'])
              ->setCellValue('F'.$i, $garam['luas'])
              ->setCellValue('G'.$i, $garam['produksi'])
              ->setCellValue('H'.$i, $garam['harga'])
              ->setCellValue('I'.$i, $garam['nilai']);
              $produksi = $produksi + $garam['produksi'];
              $harga    = $harga + $garam['harga'];
              $nilai    = $nilai + $garam['nilai'];
              if(count($query_garam) == $no){
                $i = $i + 1;
                $spreadsheet->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':F'.$i)->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('G'.$i, $produksi)
                ->setCellValue('H'.$i, $harga)
                ->setCellValue('I'.$i, $nilai);
              }
              $i++;
              $no++;
            }
          }
          $i = $i +1;
        }
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Rekap Per Wilayah');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Rekap Per Wilayah - Kecamatan.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
      }
    }

    function getbulan($length){
      $bulan['01'] = "Januari";
      $bulan['02'] = "Februari";
      $bulan['03'] = "Maret";
      $bulan['04'] = "April";
      $bulan['05'] = "Mei";
      $bulan['06'] = "Juni";
      $bulan['07'] = "Juli";
      $bulan['08'] = "Agustus";
      $bulan['09'] = "September";
      $bulan['10'] = "Oktober";
      $bulan['11'] = "November";
      $bulan['12'] = "Desember";
      return $bulan[$length];
    }
}
