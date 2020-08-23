<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Grafik extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_role')) {
          redirect('login');
        }
        $this->load->library('form_validation');
        //$this->load->library('fpdf');
        $this->load->model('apps');
        $this->load->model('kantor_model');
        $this->load->model('grafik_model');
        $this->load->helper(array('string','security','form'));

    }
    public function BUDIDAYA()
    {
      level_user('grafikbudidaya','index',$this->session->userdata('user_role'),'read') > 0 ? '': show_404();

      $jeniss = $this->grafik_model->ReffBudidaya();
      $data = array(
          'content'    => 'content/laporan/grafik/budidaya',
          'jeniss'  => $jeniss,
          'page' => 'graph_budidaya',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }

    public function BUdidayaUsaha()
    {
      level_user('grafikbudidayausaha','index',$this->session->userdata('user_role'),'read') > 0 ? '': show_404();

      $jeniss = $this->grafik_model->ReffBudidayaUsaha();
      $data = array(
          'content'    => 'content/laporan/grafik/budidayaperusaha',
          'jeniss'  => $jeniss,
          'page' => 'graph_budidayausaha',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }

    public function grafik_budidaya()
    {
      $jenis_usaha = $this->input->post('jenis_usaha');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');
      $jeniss = $this->grafik_model->ReffBudidaya();
      $data = array(
          'content'    => 'content/laporan/grafik/budidaya',
          'jeniss'  => $jeniss,
          'page'  => 'graph_budidaya',
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
      }elseif($jenis_usaha == ''){
        $this->session->set_flashdata('danger','Jenis Usaha Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else{
        $query_budidaya = $this->grafik_model->Budidaya($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $jenis_usaha);
        // die(var_dump($query_budidaya));
        if($query_budidaya){
          define('FPDF_FONTPATH',$this->config->item('fonts_path'));
          $this->load->library('PDFDiag');
          $pdf = new PDFDiag();

          $pdf->SetMargins(15, 10, 10);
          $pdf->SetFillColor(212,239,247);
          $pdf->AliasNbPages();
          $pdf->AddPage();

          $pdf->SetFont('Arial','B',12);
          $pdf->Cell(180,5,'REKAPITULASI DATA PERIKANAN BUDIDAYA',0,1,'C',0);
          $pdf->Cell(180,5,'DINAS PERIKANAN KABUPATEN SUMENEP',0,1,'C',0);
          $pdf->Cell(180,5,'BIDANG KELEMBAGAAN DAN PENGENDALIAN USAHA PERIKANAN',0,1,'C',0);
          $pdf->Cell(180,5,'SIE DATA DAN INFORMASI',0,1,'C',0);
          $pdf->Ln(10);
          $pdf->SetFont('Arial','',11);
          $pdf->Cell(25,6,'Periode',0,0,'L',0);
          $pdf->Cell(60,6,' : '.$this->getbulan($dari_bulan).' '.$dari_tahun.' - '.$this->getbulan($sampai_bulan).' '.$sampai_tahun,0,1,'L',0);
          $pdf->Cell(25,6,'Jenis Usaha',0,0,'L',0);
          $pdf->Cell(60,6,' : '.$this->grafik_model->ReffHasilBudidaya($jenis_usaha),0,1,'L',0);
          $pdf->Ln(5);
          $data_nilai    = array();
          $data_produksi = array();

          $pdf->SetFont('Arial','B',10);
          $pdf->Cell(10,5,'No',1,0,'C',1);
          $pdf->Cell(55,5,'Jenis',1,0,'C',1);
          $pdf->Cell(25,5,'RTP',1,0,'C',1);
          $pdf->Cell(25,5,'Luas (M2)',1,0,'C',1);
          $pdf->Cell(30,5,'Produksi (Ton)',1,0,'C',1);
          $pdf->Cell(35,5,'Nilai Produksi (Rp)',1,1,'C',1);
          $no = 1;
          $pdf->SetFont('Arial','',10);
          foreach ($query_budidaya as $key => $q) {
            $pdf->Cell(10,5,$no,1,0,'C',0);
            $pdf->Cell(55,5,$q['budidaya'],1,0,'L',0);
            $pdf->Cell(25,5,number_format($q['rtp'], 0, "", "."),1,0,'C',0);
            $pdf->Cell(25,5,number_format($q['luas'], 0, "", "."),1,0,'C',0);
            $pdf->Cell(30,5,floatval($q['produksi']),1,0,'C',0);
            // $pdf->Cell(30,5,number_format($q['produksi'], 0, "", "."),1,0,'C',0);
            $pdf->Cell(35,5,number_format($q['nilai'], 0, "", "."),1,1,'R',0);
            $data_nilai[$q['budidaya']] = number_format($q['nilai'], 0, "", "");
            // $data_produksi[$q['budidaya']] = number_format($q['produksi'], 0, "", "");
            $data_produksi[$q['budidaya']] = floatval($q['produksi']);
            // die(var_dump($data_produksi[$q['budidaya']]));
            $no++;
          }
          $pdf->Ln(5);
          //Pie chart
          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(90, 5, 'Pie chart - Produksi (Ton)', 0, 0);
          $pdf->Ln(8);
          $pdf->SetFont('Arial', '', 10);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->Ln(8);
          $pdf->SetXY(14, $valY);

          $pdf->PieChart(120, 60, $data_produksi, '%l : %v (%p)', array());
          $pdf->SetXY($valX, $valY + 60);

          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(90, 5, 'Pie chart - Nilai Produksi (Rp)', 0, 1);
          $pdf->Ln(8);
          $pdf->SetFont('Arial', '', 10);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->Ln(8);
          $pdf->SetXY(14, $valY);
          $pdf->PieChart(120, 60, $data_nilai, '%l : %v (%p)', array());
          $pdf->SetXY($valX, $valY + 60);

          $pdf->AddPage();
          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(0, 5, 'Bar diagram - Produksi (Ton)', 0, 1);
          $pdf->Ln(8);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->BarDiagram(180, 60, $data_produksi, '%l : %v (%p)', array(0,102,51));
          $pdf->SetXY($valX, $valY + 80);

          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(0, 5, 'Bar diagram - Nilai Produksi (Rp)', 0, 1);
          $pdf->Ln(8);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->BarDiagram(180, 60, $data_nilai, '%l : %v (%p)', array(0,102,51));
          $pdf->SetXY($valX, $valY + 80);
          $pdf->Output('D','Rekapitulasi Budidaya.pdf');
        }else{
          $this->session->set_flashdata('danger','Jenis Usaha Tidak Ditemukan');
          $this->load->view("layout/template", $data);
        }

      }
    }

    public function grafik_budidaya_usaha()
    {
      $jenis_usaha = $this->input->post('jenis_usaha');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');
      $jeniss = $this->grafik_model->ReffBudidayaUsaha();
      $data = array(
          'content'    => 'content/laporan/grafik/budidayaperusaha',
          'jeniss'  => $jeniss,
          'page' => 'graph_budidayausaha',
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
      }elseif($jenis_usaha == ''){
        $this->session->set_flashdata('danger','Jenis Usaha Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else{
        $query_budidaya = $this->grafik_model->BudidayaPerusaha($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $jenis_usaha);

        if($query_budidaya){
          define('FPDF_FONTPATH',$this->config->item('fonts_path'));
          $this->load->library('PDFDiag');
          $pdf = new PDFDiag();

          $pdf->SetMargins(15, 10, 10);
        	$pdf->SetFillColor(212,239,247);
        	$pdf->AliasNbPages();
        	$pdf->AddPage();

          $pdf->SetFont('Arial','B',12);
          $pdf->Cell(180,5,'REKAPITULASI DATA PERIKANAN BUDIDAYA PER USAHA',0,1,'C',0);
          $pdf->Cell(180,5,'DINAS PERIKANAN KABUPATEN SUMENEP',0,1,'C',0);
          $pdf->Cell(180,5,'BIDANG KELEMBAGAAN DAN PENGENDALIAN USAHA PERIKANAN',0,1,'C',0);
          $pdf->Cell(180,5,'SIE DATA DAN INFORMASI',0,1,'C',0);
          $pdf->Ln(10);
          $pdf->SetFont('Arial','',11);
          $pdf->Cell(25,6,'Periode',0,0,'L',0);
          $pdf->Cell(60,6,' : '.$this->getbulan($dari_bulan).' '.$dari_tahun.' - '.$this->getbulan($sampai_bulan).' '.$sampai_tahun,0,1,'L',0);
          $pdf->Cell(25,6,'Jenis Usaha',0,0,'L',0);
          $pdf->Cell(60,6,' : '.$this->grafik_model->ReffHasilBudidayaUsaha($jenis_usaha),0,1,'L',0);
          $pdf->Ln(5);
          $data_nilai    = array();
          $data_produksi = array();

          $pdf->SetFont('Arial','B',10);
          $pdf->Cell(10,5,'No',1,0,'C',1);
          $pdf->Cell(55,5,'Jenis',1,0,'C',1);
          $pdf->Cell(25,5,'RTP',1,0,'C',1);
          $pdf->Cell(25,5,'Luas (M2)',1,0,'C',1);
          $pdf->Cell(30,5,'Produksi (Ton)',1,0,'C',1);
          $pdf->Cell(35,5,'Nilai Produksi (Rp)',1,1,'C',1);
          $no = 1;
          $pdf->SetFont('Arial','',10);
          foreach ($query_budidaya as $key => $q) {
            // die(var_dump());
            $pdf->Cell(10,5,$no,1,0,'C',0);
            $pdf->Cell(55,5,$q['budidaya'],1,0,'L',0);
            $pdf->Cell(25,5,number_format($q['rtp'], 0, "", "."),1,0,'C',0);
            $pdf->Cell(25,5,number_format($q['luas'], 0, "", "."),1,0,'C',0);
            $pdf->Cell(30,5,floatval($q['produksi']),1,0,'C',0);
            // $pdf->Cell(30,5,number_format($q['produksi'], 0, "", "."),1,0,'C',0);
            $pdf->Cell(35,5,number_format($q['nilai'], 0, "", "."),1,1,'R',0);

            // $data_nilai[$q['budidaya']] = number_format($q['nilai'], 0, "", "");
            $data_nilai[$q['budidaya']] = floatval($q['nilai']);
            // $data_produksi[$q['budidaya']] = number_format($q['produksi'], 0, "", "");
            $data_produksi[$q['budidaya']] = floatval($q['produksi']);
            $no++;
          }
          if ($q['produksi'] == '0.00') {
            $this->session->set_flashdata('danger','Data Produksi Periode Tersebut 0');
            redirect("laporan/grafik/BUdidayaUsaha");
            // $this->load->view("layout/template", $data);
          }
          $pdf->Ln(5);
          //Pie chart
          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(90, 5, 'Pie chart - Produksi (Ton)', 0, 0);
          $pdf->Ln(8);
          $pdf->SetFont('Arial', '', 10);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->Ln(8);
          $pdf->SetXY(14, $valY);
          $pdf->PieChart(120, 60, $data_produksi, '%l : %v (%p)', array());
          $pdf->SetXY($valX, $valY + 60);

          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(90, 5, 'Pie chart - Nilai Produksi (Rp)', 0, 1);
          $pdf->Ln(8);
          $pdf->SetFont('Arial', '', 10);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->Ln(8);
          $pdf->SetXY(14, $valY);
          $pdf->PieChart(120, 60, $data_nilai, '%l : %v (%p)', array());
          $pdf->SetXY($valX, $valY + 60);

          $pdf->AddPage();
          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(0, 5, 'Bar diagram - Produksi (Ton)', 0, 1);
          $pdf->Ln(8);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->BarDiagram(180, 60, $data_produksi, '%l : %v (%p)', array(0,102,51));
          $pdf->SetXY($valX, $valY + 80);

          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(0, 5, 'Bar diagram - Nilai Produksi (Rp)', 0, 1);
          $pdf->Ln(8);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->BarDiagram(180, 60, $data_nilai, '%l : %v (%p)', array(0,102,51));
          $pdf->SetXY($valX, $valY + 80);
          $pdf->Output('D','Rekapitulasi Budidaya Per Usaha.pdf');
        }else{
          $this->session->set_flashdata('danger','Jenis Usaha Tidak Ditemukan');
          $this->load->view("layout/template", $data);
        }

      }
    }

    public function LAHSAR()
    {
      level_user('grafiklahsar','index',$this->session->userdata('user_role'),'read') > 0 ? '': show_404();

      $jeniss = $this->grafik_model->ReffLahsar();
      $data = array(
          'content'    => 'content/laporan/grafik/lahsar',
          'jeniss'  => $jeniss,
          'page' => 'graph_lahsar',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }

    public function LAhsarUsaha()
    {
      level_user('grafiklahsarusaha','index',$this->session->userdata('user_role'),'read') > 0 ? '': show_404();

      $jeniss = $this->grafik_model->ReffLahsarUsaha();
      $data = array(
          'content'    => 'content/laporan/grafik/lahsarperusaha',
          'jeniss'  => $jeniss,
          'page' => 'graph_lahsarusaha',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }

    public function grafik_lahsar()
    {
      $jenis_usaha = $this->input->post('jenis_usaha');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');
      $jeniss = $this->grafik_model->ReffLahsar();
      $data = array(
          'content'    => 'content/laporan/grafik/lahsar',
          'jeniss'  => $jeniss,
          'page'  => 'graph_lahsar',
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
      }elseif($jenis_usaha == ''){
        $this->session->set_flashdata('danger','Jenis Usaha Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else{
        $query_lahsar = $this->grafik_model->Lahsar($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $jenis_usaha);
        if($query_lahsar){
          define('FPDF_FONTPATH',$this->config->item('fonts_path'));
          $this->load->library('PDFDiag');
          $pdf = new PDFDiag();

          $pdf->SetMargins(15, 10, 10);
          $pdf->SetFillColor(212,239,247);
          $pdf->AliasNbPages();
          $pdf->AddPage();

          $pdf->SetFont('Arial','B',12);
          $pdf->Cell(180,5,'REKAPITULASI DATA PERIKANAN PENGOLAHAN & PEMASARAN',0,1,'C',0);
          $pdf->Cell(180,5,'DINAS PERIKANAN KABUPATEN SUMENEP',0,1,'C',0);
          $pdf->Cell(180,5,'BIDANG KELEMBAGAAN DAN PENGENDALIAN USAHA PERIKANAN',0,1,'C',0);
          $pdf->Cell(180,5,'SIE DATA DAN INFORMASI',0,1,'C',0);
          $pdf->Ln(10);
          $pdf->SetFont('Arial','',11);
          $pdf->Cell(25,6,'Periode',0,0,'L',0);
          $pdf->Cell(60,6,' : '.$this->getbulan($dari_bulan).' '.$dari_tahun.' - '.$this->getbulan($sampai_bulan).' '.$sampai_tahun,0,1,'L',0);
          $pdf->Cell(25,6,'Jenis Usaha',0,0,'L',0);
          $pdf->Cell(60,6,' : '.$this->grafik_model->ReffHasilLahsar($jenis_usaha),0,1,'L',0);
          $pdf->Ln(5);
          $data_nilai    = array();
          $data_produksi = array();

          $pdf->SetFont('Arial','B',10);
          $pdf->Cell(10,5,'No',1,0,'C',1);
          $pdf->Cell(65,5,'Jenis',1,0,'C',1);
          $pdf->Cell(35,5,'RTP',1,0,'C',1);
          $pdf->Cell(35,5,'Produksi (Ton)',1,0,'C',1);
          $pdf->Cell(35,5,'Nilai Produksi (Rp)',1,1,'C',1);
          $no = 1;
          $pdf->SetFont('Arial','',10);
          foreach ($query_lahsar as $key => $q) {
            $pdf->Cell(10,5,$no,1,0,'C',0);
            $pdf->Cell(65,5,$q['lahsar'],1,0,'L',0);
            $pdf->Cell(35,5,number_format($q['rtp'], 0, "", "."),1,0,'C',0);
            // $pdf->Cell(35,5,number_format($q['produksi'], 0, "", "."),1,0,'C',0);
            $pdf->Cell(35,5,floatval($q['produksi']),1,0,'C',0);
            $pdf->Cell(35,5,number_format($q['nilai'], 0, "", "."),1,1,'R',0);

            $data_nilai[$q['lahsar']] = number_format($q['nilai'], 0, "", "");
            // $data_produksi[$q['lahsar']] = number_format($q['produksi'], 0, "", "");
            $data_produksi[$q['lahsar']] = floatval($q['produksi']);
            $no++;
          }
          $pdf->Ln(5);
          //Pie chart
          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(90, 5, 'Pie chart - Produksi (Ton)', 0, 0);
          $pdf->Ln(8);
          $pdf->SetFont('Arial', '', 10);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->Ln(8);
          $pdf->SetXY(14, $valY);
          $pdf->PieChart(120, 60, $data_produksi, '%l : %v (%p)', array());
          $pdf->SetXY($valX, $valY + 60);

          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(90, 5, 'Pie chart - Nilai Produksi (Rp)', 0, 1);
          $pdf->Ln(8);
          $pdf->SetFont('Arial', '', 10);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->Ln(8);
          $pdf->SetXY(14, $valY);
          $pdf->PieChart(120, 60, $data_nilai, '%l : %v (%p)', array());
          $pdf->SetXY($valX, $valY + 60);

          $pdf->AddPage();
          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(0, 5, 'Bar diagram - Produksi (Ton)', 0, 1);
          $pdf->Ln(8);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->BarDiagram(180, 60, $data_produksi, '%l : %v (%p)', array(0,102,51));
          $pdf->SetXY($valX, $valY + 80);

          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(0, 5, 'Bar diagram - Nilai Produksi (Rp)', 0, 1);
          $pdf->Ln(8);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->BarDiagram(180, 60, $data_nilai, '%l : %v (%p)', array(0,102,51));
          $pdf->SetXY($valX, $valY + 80);
          $pdf->Output('D','Rekapitulasi Lahsar.pdf');
        }else{
          $this->session->set_flashdata('danger','Jenis Usaha Tidak Ditemukan');
          $this->load->view("layout/template", $data);
        }

      }
    }

    public function grafik_lahsar_usaha()
    {
      $jenis_usaha = $this->input->post('jenis_usaha');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');
      $jeniss = $this->grafik_model->ReffLahsarUsaha();
      $data = array(
          'content'    => 'content/laporan/grafik/lahsarperusaha',
          'jeniss'  => $jeniss,
          'page' => 'graph_lahsarusaha',
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
      }elseif($jenis_usaha == ''){
        $this->session->set_flashdata('danger','Jenis Usaha Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else{
        $query_lahsar = $this->grafik_model->LahsarPerusaha($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $jenis_usaha);
        if($query_lahsar){
          define('FPDF_FONTPATH',$this->config->item('fonts_path'));
          $this->load->library('PDFDiag');
          $pdf = new PDFDiag();

          $pdf->SetMargins(15, 10, 10);
          $pdf->SetFillColor(212,239,247);
          $pdf->AliasNbPages();
          $pdf->AddPage();

          $pdf->SetFont('Arial','B',12);
          $pdf->Cell(180,5,'REKAPITULASI DATA PERIKANAN PENGOLAHAN & PEMASARAN PER USAHA',0,1,'C',0);
          $pdf->Cell(180,5,'DINAS PERIKANAN KABUPATEN SUMENEP',0,1,'C',0);
          $pdf->Cell(180,5,'BIDANG KELEMBAGAAN DAN PENGENDALIAN USAHA PERIKANAN',0,1,'C',0);
          $pdf->Cell(180,5,'SIE DATA DAN INFORMASI',0,1,'C',0);
          $pdf->Ln(10);
          $pdf->SetFont('Arial','',11);
          $pdf->Cell(25,6,'Periode',0,0,'L',0);
          $pdf->Cell(60,6,' : '.$this->getbulan($dari_bulan).' '.$dari_tahun.' - '.$this->getbulan($sampai_bulan).' '.$sampai_tahun,0,1,'L',0);
          $pdf->Cell(25,6,'Jenis Usaha',0,0,'L',0);
          $pdf->Cell(60,6,' : '.$this->grafik_model->ReffHasilLahsarUsaha($jenis_usaha),0,1,'L',0);
          $pdf->Ln(5);
          $data_nilai    = array();
          $data_produksi = array();

          $pdf->SetFont('Arial','B',10);
          $pdf->Cell(10,5,'No',1,0,'C',1);
          $pdf->Cell(65,5,'Jenis',1,0,'C',1);
          $pdf->Cell(35,5,'RTP',1,0,'C',1);
          $pdf->Cell(35,5,'Produksi (Ton)',1,0,'C',1);
          $pdf->Cell(35,5,'Nilai Produksi (Rp)',1,1,'C',1);
          $no = 1;
          $pdf->SetFont('Arial','',10);
          foreach ($query_lahsar as $key => $q) {
            $pdf->Cell(10,5,$no,1,0,'C',0);
            $pdf->Cell(65,5,$q['lahsar'],1,0,'L',0);
            $pdf->Cell(35,5,number_format($q['rtp'], 0, "", "."),1,0,'C',0);
            // $pdf->Cell(35,5,number_format($q['produksi'], 0, "", "."),1,0,'C',0);
            $pdf->Cell(35,5,floatval($q['produksi']),1,0,'C',0);
            $pdf->Cell(35,5,number_format($q['nilai'], 0, "", "."),1,1,'R',0);

            $data_nilai[$q['lahsar']] = number_format($q['nilai'], 0, "", "");
            // $data_produksi[$q['lahsar']] = number_format($q['produksi'], 0, "", "");
            $data_produksi[$q['lahsar']] = floatval($q['produksi']);
            $no++;
          }
          $pdf->Ln(5);
          //Pie chart
          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(90, 5, 'Pie chart - Produksi (Ton)', 0, 0);
          $pdf->Ln(8);
          $pdf->SetFont('Arial', '', 10);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->Ln(8);
          $pdf->SetXY(14, $valY);
          $pdf->PieChart(120, 60, $data_produksi, '%l : %v (%p)', array());
          $pdf->SetXY($valX, $valY + 60);

          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(90, 5, 'Pie chart - Nilai Produksi (Rp)', 0, 1);
          $pdf->Ln(8);
          $pdf->SetFont('Arial', '', 10);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->Ln(8);
          $pdf->SetXY(14, $valY);
          $pdf->PieChart(120, 60, $data_nilai, '%l : %v (%p)', array());
          $pdf->SetXY($valX, $valY + 60);

          $pdf->AddPage();
          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(0, 5, 'Bar diagram - Produksi (Ton)', 0, 1);
          $pdf->Ln(8);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->BarDiagram(180, 60, $data_produksi, '%l : %v (%p)', array(0,102,51));
          $pdf->SetXY($valX, $valY + 80);

          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(0, 5, 'Bar diagram - Nilai Produksi (Rp)', 0, 1);
          $pdf->Ln(8);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->BarDiagram(180, 60, $data_nilai, '%l : %v (%p)', array(0,102,51));
          $pdf->SetXY($valX, $valY + 80);
          $pdf->Output('D','Rekapitulasi Lahsar Per Usaha.pdf');
        }else{
          $this->session->set_flashdata('danger','Jenis Usaha Tidak Ditemukan');
          $this->load->view("layout/template", $data);
        }

      }
    }

    public function TANGKAP()
    {
      level_user('grafiktangkap','index',$this->session->userdata('user_role'),'read') > 0 ? '': show_404();

      $jeniss = $this->grafik_model->ReffTangkap();
      $data = array(
          'content'    => 'content/laporan/grafik/tangkap',
          'jeniss'  => $jeniss,
          'page' => 'graph_tangkap',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }

    public function grafik_tangkap()
    {
      $jenis_usaha = $this->input->post('jenis_usaha');
      $dari_bulan   = $this->input->post('dari_bulan');
      $dari_tahun   = $this->input->post('dari_tahun');
      $sampai_bulan = $this->input->post('sampai_bulan');
      $sampai_tahun = $this->input->post('sampai_tahun');
      $jeniss = $this->grafik_model->ReffTangkap();
      $data = array(
          'content'    => 'content/laporan/grafik/tangkap',
          'jeniss'  => $jeniss,
          'page' => 'graph_tangkap',
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
      }elseif($jenis_usaha == ''){
        $this->session->set_flashdata('danger','Jenis Usaha Data Belum ditentukan');
        $this->load->view("layout/template", $data);
      }else{
        $query_tangkap = $this->grafik_model->Tangkap($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $jenis_usaha);
        if($query_tangkap){
          define('FPDF_FONTPATH',$this->config->item('fonts_path'));
          $this->load->library('PDFDiag');
          $pdf = new PDFDiag();

          $pdf->SetMargins(15, 10, 10);
          $pdf->SetFillColor(212,239,247);
          $pdf->AliasNbPages();
          $pdf->AddPage();

          $pdf->SetFont('Arial','B',12);
          $pdf->Cell(180,5,'REKAPITULASI DATA PERIKANAN TANGKAP',0,1,'C',0);
          $pdf->Cell(180,5,'DINAS PERIKANAN KABUPATEN SUMENEP',0,1,'C',0);
          $pdf->Cell(180,5,'BIDANG KELEMBAGAAN DAN PENGENDALIAN USAHA PERIKANAN',0,1,'C',0);
          $pdf->Cell(180,5,'SIE DATA DAN INFORMASI',0,1,'C',0);
          $pdf->Ln(10);
          $pdf->SetFont('Arial','',11);
          $pdf->Cell(25,6,'Periode',0,0,'L',0);
          $pdf->Cell(60,6,' : '.$this->getbulan($dari_bulan).' '.$dari_tahun.' - '.$this->getbulan($sampai_bulan).' '.$sampai_tahun,0,1,'L',0);
          $pdf->Cell(25,6,'Jenis Usaha',0,0,'L',0);
          $pdf->Cell(60,6,' : '.$this->grafik_model->ReffHasilTangkap($jenis_usaha),0,1,'L',0);
          $pdf->Ln(5);
          $data_nilai    = array();
          $data_produksi = array();

          $pdf->SetFont('Arial','B',10);
          $pdf->Cell(10,5,'No',1,0,'C',1);
          $pdf->Cell(55,5,'Jenis',1,0,'C',1);
          $pdf->Cell(25,5,'RTP',1,0,'C',1);
          $pdf->Cell(25,5,'Jumlah Trip',1,0,'C',1);
          $pdf->Cell(30,5,'Produksi (Ton)',1,0,'C',1);
          $pdf->Cell(35,5,'Nilai Produksi (Rp)',1,1,'C',1);
          $no = 1;
          $pdf->SetFont('Arial','',10);
          foreach ($query_tangkap as $key => $q) {
            $pdf->Cell(10,5,$no,1,0,'C',0);
            $pdf->Cell(55,5,$q['tangkap'],1,0,'L',0);
            $pdf->Cell(25,5,number_format($q['rtp'], 0, "", "."),1,0,'C',0);
            $pdf->Cell(25,5,number_format($q['trip'], 0, "", "."),1,0,'C',0);
            // $pdf->Cell(30,5,number_format($q['produksi'], 0, "", "."),1,0,'C',0);
            $pdf->Cell(30,5,floatval($q['produksi']),1,0,'C',0);
            $pdf->Cell(35,5,number_format($q['nilai'], 0, "", "."),1,1,'R',0);

            $data_nilai[$q['tangkap']] = number_format($q['nilai'], 0, "", "");
            $data_produksi[$q['tangkap']] = number_format($q['trip'], 0, "", "");
            $no++;
          }
          $pdf->Ln(5);
          //Pie chart
          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(90, 5, 'Pie chart - Jumlah Trip', 0, 0);
          $pdf->Ln(8);
          $pdf->SetFont('Arial', '', 10);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->Ln(8);
          $pdf->SetXY(14, $valY);
          $pdf->PieChart(120, 60, $data_produksi, '%l : %v (%p)', array());
          $pdf->SetXY($valX, $valY + 60);

          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(90, 5, 'Pie chart - Nilai Produksi (Rp)', 0, 1);
          $pdf->Ln(8);
          $pdf->SetFont('Arial', '', 10);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->Ln(8);
          $pdf->SetXY(14, $valY);
          $pdf->PieChart(120, 60, $data_nilai, '%l : %v (%p)', array());
          $pdf->SetXY($valX, $valY + 60);

          $pdf->AddPage();
          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(0, 5, 'Bar diagram - Jumlah Trip', 0, 1);
          $pdf->Ln(8);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->BarDiagram(180, 60, $data_produksi, '%l : %v (%p)', array(0,102,51));
          $pdf->SetXY($valX, $valY + 80);

          $pdf->SetFont('Arial', 'B', 12);
          $pdf->Cell(0, 5, 'Bar diagram - Nilai Produksi (Rp)', 0, 1);
          $pdf->Ln(8);
          $valX = $pdf->GetX();
          $valY = $pdf->GetY();
          $pdf->BarDiagram(180, 60, $data_nilai, '%l : %v (%p)', array(0,102,51));
          $pdf->SetXY($valX, $valY + 80);

          // Berdasarkan Alat Tangkap
          $query_alat = $this->grafik_model->AlatTangkap($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $jenis_usaha);
          if($query_alat){
            $pdf->AddPage();
            $pdf->SetFillColor(212,239,247);
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(180,5,'REKAPITULASI DATA PERIKANAN TANGKAP',0,1,'C',0);
            $pdf->Cell(180,5,'DINAS PERIKANAN KABUPATEN SUMENEP',0,1,'C',0);
            $pdf->Cell(180,5,'BIDANG KELEMBAGAAN DAN PENGENDALIAN USAHA PERIKANAN',0,1,'C',0);
            $pdf->Cell(180,5,'SIE DATA DAN INFORMASI',0,1,'C',0);
            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(25,6,'Periode',0,0,'L',0);
            $pdf->Cell(60,6,' : '.$this->getbulan($dari_bulan).' '.$dari_tahun.' - '.$this->getbulan($sampai_bulan).' '.$sampai_tahun,0,1,'L',0);
            $pdf->Cell(25,6,'Jenis Usaha',0,0,'L',0);
            $pdf->Cell(60,6,' : '.$this->grafik_model->ReffHasilTangkap($jenis_usaha),0,1,'L',0);
            $pdf->Cell(25,6,'Berdasarkan',0,0,'L',0);
            $pdf->Cell(60,6,' : Alat Tangkap',0,1,'L',0);
            $pdf->Ln(5);
            $data_alat    = array();

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(10,5,'No',1,0,'C',1);
            $pdf->Cell(75,5,'Jenis Alat',1,0,'C',1);
            $pdf->Cell(35,5,'RTP',1,0,'C',1);
            $pdf->Cell(35,5,'Jumlah',1,1,'C',1);
            $no = 1;
            $pdf->SetFont('Arial','',10);
            foreach ($query_alat as $key => $q) {
              $pdf->Cell(10,5,$no,1,0,'C',0);
              $pdf->Cell(75,5,$q['tangkap'],1,0,'L',0);
              $pdf->Cell(35,5,number_format($q['rtp'], 0, "", "."),1,0,'C',0);
              $pdf->Cell(35,5,number_format($q['jumlah'], 0, "", "."),1,1,'C',0);

              $data_alat[$q['tangkap']] = number_format($q['jumlah'], 0, "", "");
              $no++;
            }
            $pdf->Ln(5);
            //Pie chart
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(90, 5, 'Pie chart - Jumlah Alat Tangkap', 0, 0);
            $pdf->Ln(8);
            $pdf->SetFont('Arial', '', 10);
            $valX = $pdf->GetX();
            $valY = $pdf->GetY();
            $pdf->Ln(8);
            $pdf->SetXY(14, $valY);
            $pdf->PieChart(120, 60, $data_alat, '%l : %v (%p)', array());
            $pdf->SetXY($valX, $valY + 60);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 5, 'Bar diagram - Jumlah Alat Tangkap', 0, 1);
            $pdf->Ln(8);
            $valX = $pdf->GetX();
            $valY = $pdf->GetY();
            $pdf->BarDiagram(180, 60, $data_alat, '%l : %v (%p)', array(0,102,51));
            $pdf->SetXY($valX, $valY + 80);
          }
          // Berdasarkan Jenis Perahu
          $query_perahu = $this->grafik_model->PerahuTangkap($dari_bulan, $dari_tahun, $sampai_bulan, $sampai_tahun, $jenis_usaha);
          if($query_perahu){
            $pdf->AddPage();
            $pdf->SetFillColor(212,239,247);
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(180,5,'REKAPITULASI DATA PERIKANAN TANGKAP',0,1,'C',0);
            $pdf->Cell(180,5,'DINAS PERIKANAN KABUPATEN SUMENEP',0,1,'C',0);
            $pdf->Cell(180,5,'BIDANG KELEMBAGAAN DAN PENGENDALIAN USAHA PERIKANAN',0,1,'C',0);
            $pdf->Cell(180,5,'SIE DATA DAN INFORMASI',0,1,'C',0);
            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(25,6,'Periode',0,0,'L',0);
            $pdf->Cell(60,6,' : '.$this->getbulan($dari_bulan).' '.$dari_tahun.' - '.$this->getbulan($sampai_bulan).' '.$sampai_tahun,0,1,'L',0);
            $pdf->Cell(25,6,'Jenis Usaha',0,0,'L',0);
            $pdf->Cell(60,6,' : '.$this->grafik_model->ReffHasilTangkap($jenis_usaha),0,1,'L',0);
            $pdf->Cell(25,6,'Berdasarkan',0,0,'L',0);
            $pdf->Cell(60,6,' : Jenis Perahu',0,1,'L',0);
            $pdf->Ln(5);
            $data_perahu   = array();

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(10,5,'No',1,0,'C',1);
            $pdf->Cell(75,5,'Jenis Perahu',1,0,'C',1);
            $pdf->Cell(35,5,'RTP',1,0,'C',1);
            $pdf->Cell(35,5,'Jumlah',1,1,'C',1);
            $no = 1;
            $pdf->SetFont('Arial','',10);
            foreach ($query_perahu as $key => $q) {
              $pdf->Cell(10,5,$no,1,0,'C',0);
              $pdf->Cell(75,5,$q['tangkap'],1,0,'L',0);
              $pdf->Cell(35,5,number_format($q['rtp'], 0, "", "."),1,0,'C',0);
              $pdf->Cell(35,5,number_format($q['jumlah'], 0, "", "."),1,1,'C',0);

              $data_perahu[$q['tangkap']] = number_format($q['jumlah'], 0, "", "");
              $no++;
            }
            $pdf->Ln(5);
            //Pie chart
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(90, 5, 'Pie chart - Jumlah Perahu', 0, 0);
            $pdf->Ln(8);
            $pdf->SetFont('Arial', '', 10);
            $valX = $pdf->GetX();
            $valY = $pdf->GetY();
            $pdf->Ln(8);
            $pdf->SetXY(14, $valY);
            $pdf->PieChart(120, 60, $data_perahu, '%l : %v (%p)', array());
            $pdf->SetXY($valX, $valY + 60);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 5, 'Bar diagram - Jumlah Perahu', 0, 1);
            $pdf->Ln(8);
            $valX = $pdf->GetX();
            $valY = $pdf->GetY();
            $pdf->BarDiagram(180, 60, $data_perahu, '%l : %v (%p)', array(0,102,51));
            $pdf->SetXY($valX, $valY + 80);
          }
          $pdf->Output('D','Rekapitulasi Usaha Tangkap.pdf');
        }else{
          $this->session->set_flashdata('danger','Jenis Usaha Tidak Ditemukan');
          $this->load->view("layout/template", $data);
        }

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
