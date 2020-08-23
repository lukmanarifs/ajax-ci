<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Formulir extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_role')) {
          redirect('login');
        }
        $this->load->library('form_validation');
        //$this->load->library('fpdf');
        $this->load->model('apps');
        $this->load->model('nelayan_model');
        $this->load->model('lapnelayan_model');
        $this->load->helper(array('string','security','form'));

    }

    public function index()
    {
      level_user('formulir','index',$this->session->userdata('user_role'),'read') > 0 ? '': show_404();

      $data = array(
          'content'    => 'content/laporan/nelayan/index',
          'page' => 'lap_nelayan',
          'csrf' => array(
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          )
      );
      $this->load->view("layout/template", $data);
    }

    public function nelayanList()
    {
      cekajax();
      $draw = intval($this->input->get("draw"));
      $start = intval($this->input->get("start"));
      $length = intval($this->input->get("length"));

      $query = $this->nelayan_model->dataNelayan();
      $data = array();
      foreach($query as $q) {
          $data[]  = array(
                $this->security->xss_clean($q['nelayan_nama']),
                $this->security->xss_clean($q['nelayan_nik']),
                $this->security->xss_clean($q['nelayan_alamat']),
                $this->security->xss_clean($q['desa_nama']),
                $this->security->xss_clean($q['kecamatan_nama']),
                '<a href="#" onclick="pilihitem(this)"
                data-nama="'.$q['nelayan_nama'].'"
                data-alamat="'.$q['nelayan_alamat'].'"
                data-desa="'.$q['desa_nama'].'"
                data-kecamatan="'.$q['kecamatan_nama'].'"
                data-id="'.$q['nelayan_nik'].'"
                data-dismiss="modal"
                class="btn btn-danger"
                role="button"
                aria-pressed="true">Pilih</a>'
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

    public function LaporanUsaha()
  	{
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
      $id = base64_decode($this->input->get("id"));
      $data = explode("|", $id);
      $nik     = $data[0];
      $jenis   = $data[1];
      $periode = $data[2];
      $tahun   = $data[3];
      define('FPDF_FONTPATH',$this->config->item('fonts_path'));
      $this->load->library('CustomPDF');
      $pdf = $this->custompdf->getInstance();

    	$pdf->SetMargins(15, 10, 10);
    	$pdf->SetFillColor(212,239,247);
    	$pdf->AliasNbPages();
    	$pdf->AddPage();
      if($jenis == "budidaya"){
        $pdf->SetFont('Arial','B',12);
    		$pdf->Cell(180,5,'FORMULIR PENGISIAN DATA PERIKANAN BUDIDAYA',0,1,'C',0);
    		$pdf->Cell(180,5,'DINAS PERIKANAN KABUPATEN SUMENEP',0,1,'C',0);
    		$pdf->Cell(180,5,'BIDANG KELEMBAGAAN DAN PENGENDALIAN USAHA PERIKANAN',0,1,'C',0);
    		$pdf->Cell(180,5,'SIE DATA DAN INFORMASI',0,1,'C',0);
        $pdf->Ln(10);
        $pdf->SetFont('Arial','',11);
        $pdf->Cell(150,5,'Bulan',0,0,'R',0);
        $pdf->Cell(30,5,' : '.$bulan[$periode],0,1,'L',0);
        $pdf->Cell(150,5,'tahun',0,0,'R',0);
        $pdf->Cell(30,5,' : '.$tahun,0,1,'L',0);
        $pdf->Ln(5);
        $query_nelayan = $this->lapnelayan_model->detailnelyanan($nik);
        $pdf->Cell(35,5,'Nama RTP',0,0,'L',0);
        $pdf->Cell(130,5,' : '.$query_nelayan->nelayan_nama,0,1,'L',0);
        $pdf->Ln(1);
        $pdf->Cell(35,5,'NIK',0,0,'L',0);
        $pdf->Cell(130,5,' : '.$query_nelayan->nelayan_nik,0,1,'L',0);
        $pdf->Ln(1);
        $pdf->Cell(35,5,'Alamat',0,0,'L',0);
        $pdf->Cell(130,5,' : '.$query_nelayan->nelayan_alamat,0,1,'L',0);
        $pdf->Ln(1);
        $pdf->Cell(35,5,'Desa',0,0,'L',0);
        $pdf->Cell(130,5,' : '.$query_nelayan->desa_nama,0,1,'L',0);
        $pdf->Ln(1);
        $pdf->Cell(35,5,'Kecamatan',0,0,'L',0);
        $pdf->Cell(130,5,' : '.$query_nelayan->kecamatan_nama,0,1,'L',0);
        $pdf->Ln(2);
        $query_budidaya = $this->lapnelayan_model->detailbudidaya($query_nelayan->nelayan_key, $periode, $tahun);
        $baris = 0;

        $cek_jenis = '';
        $cek_tipe  = '';
        foreach($query_budidaya as $q) {
          if(($cek_jenis == '' && $baris ==0 )){
            $query_jenis = $this->apps->findLimit('perikanan_ref_budidaya_jenis', 'jenis_budidaya_kode', $q['tipe_budidaya_jenis_kode']);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(180,5,ucwords(strtolower($query_jenis->jenis_budidaya_nama)),1,1,'L',1);
          }

          if($cek_tipe == '' && $baris == 0){
            $no    = 1;
            $pdf->SetFont('Arial','B',10);
        		$pdf->Cell(10,5,'No',1,0,'C',1);
        		$pdf->Cell(60,5,ucwords(strtolower($q['tipe_budidaya_nama'])),1,0,'C',1);
        		$pdf->Cell(25,5,'Luas',1,0,'C',1);
        		$pdf->Cell(25,5,'Produksi',1,0,'C',1);
        		$pdf->Cell(30,5,'Harga',1,0,'C',1);
        		$pdf->Cell(30,5,'Nilai Produksi',1,1,'C',1);
          }

          if($cek_jenis != $q['tipe_budidaya_jenis_kode'] && $baris > 0){
            $query_jenis = $this->apps->findLimit('perikanan_ref_budidaya_jenis', 'jenis_budidaya_kode', $q['tipe_budidaya_jenis_kode']);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(180,5,ucwords(strtolower($query_jenis->jenis_budidaya_nama)),1,1,'L',1);
          }

          if($cek_tipe != $q['tipe_budidaya_kode'] && $baris > 0){
            $no    = 1;
            $pdf->SetFont('Arial','B',10);
        		$pdf->Cell(10,5,'No',1,0,'C',1);
        		$pdf->Cell(60,5,ucwords(strtolower($q['tipe_budidaya_nama'])),1,0,'C',1);
        		$pdf->Cell(25,5,'Luas',1,0,'C',1);
        		$pdf->Cell(25,5,'Produksi',1,0,'C',1);
        		$pdf->Cell(30,5,'Harga',1,0,'C',1);
        		$pdf->Cell(30,5,'Nilai Produksi',1,1,'C',1);
          }
          $pdf->SetWidths(array(10,60, 25, 25, 30, 30));
          $pdf->SetAligns(array('C','L','C', 'C', 'R', 'R'));
          $pdf->Row(array($no, $q['laporan_budidaya_keterangan'], number_format($q['laporan_budidaya_luas_lahan'], 0, "", ".") , number_format($q['laporan_budidaya_produksi'], 0, "", "."), number_format($q['laporan_budidaya_harga'], 0, "", "."), number_format($q['laporan_budidaya_nilai_produksi'], 0, "", ".")));
          $cek_jenis = $q['tipe_budidaya_jenis_kode'] ;
          $cek_tipe  = $q['tipe_budidaya_kode'] ;
          $baris++;
          $no++;
        }
      }elseif($jenis == "tangkap"){
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(180,5,'FORMULIR PENGISIAN DATA PERIKANAN TANGKAP',0,1,'C',0);
    		$pdf->Cell(180,5,'DINAS PERIKANAN KABUPATEN SUMENEP',0,1,'C',0);
    		$pdf->Cell(180,5,'BIDANG KELEMBAGAAN DAN PENGENDALIAN USAHA PERIKANAN',0,1,'C',0);
    		$pdf->Cell(180,5,'SIE DATA DAN INFORMASI',0,1,'C',0);
        $pdf->Ln(10);
        $pdf->SetFont('Arial','',11);
        $pdf->Cell(150,5,'Bulan',0,0,'R',0);
        $pdf->Cell(30,5,' : '.$bulan[$periode],0,1,'L',0);
        $pdf->Cell(150,5,'tahun',0,0,'R',0);
        $pdf->Cell(30,5,' : '.$tahun,0,1,'L',0);
        $pdf->Ln(5);
        $query_nelayan = $this->lapnelayan_model->detailnelyanan($nik);
        $pdf->Cell(35,5,'Nama RTP',0,0,'L',0);
        $pdf->Cell(130,5,' : '.$query_nelayan->nelayan_nama,0,1,'L',0);
        $pdf->Ln(1);
        $pdf->Cell(35,5,'NIK',0,0,'L',0);
        $pdf->Cell(130,5,' : '.$query_nelayan->nelayan_nik,0,1,'L',0);
        $pdf->Ln(1);
        $pdf->Cell(35,5,'Alamat',0,0,'L',0);
        $pdf->Cell(130,5,' : '.$query_nelayan->nelayan_alamat,0,1,'L',0);
        $pdf->Ln(1);
        $pdf->Cell(35,5,'Desa',0,0,'L',0);
        $pdf->Cell(130,5,' : '.$query_nelayan->desa_nama,0,1,'L',0);
        $pdf->Ln(1);
        $pdf->Cell(35,5,'Kecamatan',0,0,'L',0);
        $pdf->Cell(130,5,' : '.$query_nelayan->kecamatan_nama,0,1,'L',0);
        $pdf->Ln(2);
        $query_budidaya = $this->lapnelayan_model->detailtangkap($query_nelayan->nelayan_key, $periode, $tahun);
        $baris = 0;

        $cek_jenis = '';
        $cek_tipe  = '';
        foreach($query_budidaya as $q) {
          if(($cek_jenis == '' && $baris ==0 )){
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(180,5,ucwords(strtolower('JENIS USAHA - '.$q['jenis_tangkap_nama'])),1,1,'L',1);
        		$pdf->Cell(10,5,'No',1,0,'C',1);
        		$pdf->Cell(60,5,'Jenis',1,0,'C',1);
        		$pdf->Cell(25,5,'Jumlah Trip',1,0,'C',1);
        		$pdf->Cell(25,5,'Produksi',1,0,'C',1);
        		$pdf->Cell(30,5,'Harga',1,0,'C',1);
        		$pdf->Cell(30,5,'Nilai Produksi',1,1,'C',1);
            $no    = 1;
          }

          if($cek_jenis != $q['laporan_tangkap_jenis'] && $baris > 0){
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(180,5,ucwords(strtolower('JENIS USAHA - '.$q['jenis_tangkap_nama'])),1,1,'L',1);
        		$pdf->Cell(10,5,'No',1,0,'C',1);
        		$pdf->Cell(60,5,'Jenis',1,0,'C',1);
        		$pdf->Cell(25,5,'Jumlah Trip',1,0,'C',1);
        		$pdf->Cell(25,5,'Produksi',1,0,'C',1);
        		$pdf->Cell(30,5,'Harga',1,0,'C',1);
        		$pdf->Cell(30,5,'Nilai Produksi',1,1,'C',1);
            $no    = 1;
          }
          $pdf->SetWidths(array(10,60, 25, 25, 30, 30));
          $pdf->SetAligns(array('C','L','C', 'C', 'R', 'R'));
          $pdf->Row(array($no, $q['laporan_tangkap_keterangan'], number_format($q['laporan_tangkap_jumlah_trip'], 0, "", "") , number_format($q['laporan_tangkap_produksi'], 0, "", "."), number_format($q['laporan_tangkap_harga'], 0, "", "."), number_format($q['laporan_tangkap_nilai_produksi'], 0, "", ".")));

          $cek_jenis = $q['laporan_tangkap_jenis'] ;
          $baris++;
          $no++;
        }
        $query_alat = $this->lapnelayan_model->detailtangkapalat($query_nelayan->nelayan_key, $periode, $tahun);
        if($query_alat){
          $pdf->Ln(3);
          $baris = 0;

          $cek_jenis = '';
          $cek_tipe  = '';
          foreach($query_alat as $q) {
            if(($cek_jenis == '' && $baris ==0 )){
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell(180,5,ucwords(strtolower('ALAT TANGKAP - '.$q['jenis_tangkap_nama'])),1,1,'L',1);
          		$pdf->Cell(10,5,'No',1,0,'C',1);
          		$pdf->Cell(135,5,'Alat tangkap yang dimiliki',1,0,'C',1);
          		$pdf->Cell(35,5,'Jumlah (Buah)',1,1,'C',1);
              $no    = 1;
            }

            if($cek_jenis != $q['laporan_tangkap_alat_jenis_tangkap'] && $baris > 0){
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell(180,5,ucwords(strtolower('ALAT TANGKAP - '.$q['jenis_tangkap_nama'])),1,1,'L',1);
          		$pdf->Cell(10,5,'No',1,0,'C',1);
          		$pdf->Cell(135,5,'Alat tangkap yang dimiliki',1,0,'C',1);
          		$pdf->Cell(35,5,'Jumlah (Buah)',1,1,'C',1);
              $no    = 1;
            }
            $pdf->SetWidths(array(10,135, 35));
            $pdf->SetAligns(array('C','L','C'));
            $pdf->Row(array($no, $q['laporan_tangkap_alat_keterangan'], number_format($q['laporan_tangkap_alat_jumlah'], 0, "", ".")));
            $cek_jenis = $q['laporan_tangkap_alat_jenis_tangkap'] ;
            $baris++;
            $no++;
          }
        }
        $query_perahu = $this->lapnelayan_model->detailtangkapperahu($query_nelayan->nelayan_key, $periode, $tahun);
        if($query_perahu){
          $pdf->Ln(3);
          $baris = 0;

          $cek_jenis = '';
          $cek_tipe  = '';
          foreach($query_perahu as $q) {
            if(($cek_jenis == '' && $baris ==0 )){
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell(180,5,ucwords(strtolower('PERAHU - '.$q['jenis_tangkap_nama'])),1,1,'L',1);
          		$pdf->Cell(10,5,'No',1,0,'C',1);
          		$pdf->Cell(135,5,'Perahu yang dimiliki',1,0,'C',1);
          		$pdf->Cell(35,5,'Jumlah (Buah)',1,1,'C',1);
              $no    = 1;
            }

            if($cek_jenis != $q['laporan_tangkap_perahu_jenis_perahu'] && $baris > 0){
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell(180,5,ucwords(strtolower('PERAHU - '.$q['jenis_tangkap_nama'])),1,1,'L',1);
          		$pdf->Cell(10,5,'No',1,0,'C',1);
          		$pdf->Cell(135,5,'Perahu yang dimiliki',1,0,'C',1);
          		$pdf->Cell(35,5,'Jumlah (Buah)',1,1,'C',1);
              $no    = 1;
            }
            $pdf->SetWidths(array(10,135, 35));
            $pdf->SetAligns(array('C','L','C'));
            $pdf->Row(array($no, $q['perahu_tangkap_nama'], number_format($q['laporan_tangkap_perahu_jumlah'], 0, "", ".")));
            $cek_jenis = $q['laporan_tangkap_perahu_jenis_perahu'] ;
            $baris++;
            $no++;
          }
        }
      }elseif($jenis == "lahsar"){
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(180,5,'FORMULIR PENGISIAN DATA PERIKANAN PENGOLAHAN & PEMASARAN',0,1,'C',0);
    		$pdf->Cell(180,5,'DINAS PERIKANAN KABUPATEN SUMENEP',0,1,'C',0);
    		$pdf->Cell(180,5,'BIDANG KELEMBAGAAN DAN PENGENDALIAN USAHA PERIKANAN',0,1,'C',0);
    		$pdf->Cell(180,5,'SIE DATA DAN INFORMASI',0,1,'C',0);
        $pdf->Ln(10);
        $pdf->SetFont('Arial','',11);
        $pdf->Cell(150,5,'Bulan',0,0,'R',0);
        $pdf->Cell(30,5,' : '.$bulan[$periode],0,1,'L',0);
        $pdf->Cell(150,5,'tahun',0,0,'R',0);
        $pdf->Cell(30,5,' : '.$tahun,0,1,'L',0);
        $pdf->Ln(5);
        $query_nelayan = $this->lapnelayan_model->detailnelyanan($nik);
        $pdf->Cell(35,5,'Nama RTP',0,0,'L',0);
        $pdf->Cell(130,5,' : '.$query_nelayan->nelayan_nama,0,1,'L',0);
        $pdf->Ln(1);
        $pdf->Cell(35,5,'NIK',0,0,'L',0);
        $pdf->Cell(130,5,' : '.$query_nelayan->nelayan_nik,0,1,'L',0);
        $pdf->Ln(1);
        $pdf->Cell(35,5,'Alamat',0,0,'L',0);
        $pdf->Cell(130,5,' : '.$query_nelayan->nelayan_alamat,0,1,'L',0);
        $pdf->Ln(1);
        $pdf->Cell(35,5,'Desa',0,0,'L',0);
        $pdf->Cell(130,5,' : '.$query_nelayan->desa_nama,0,1,'L',0);
        $pdf->Ln(1);
        $pdf->Cell(35,5,'Kecamatan',0,0,'L',0);
        $pdf->Cell(130,5,' : '.$query_nelayan->kecamatan_nama,0,1,'L',0);
        $pdf->Ln(2);
        $query_budidaya = $this->lapnelayan_model->detaillahsar($query_nelayan->nelayan_key, $periode, $tahun);
        $baris = 0;

        $cek_jenis = '';
        $cek_tipe  = '';

        foreach($query_budidaya as $q) {
          if(($cek_jenis == '' && $baris ==0 )){
            $pdf->SetFont('Arial','B',10);
            $query_jenis = $this->apps->findLimit('perikanan_ref_lahsar_jenis', 'jenis_lahsar_kode', $q['tipe_lahsar_jenis_kode']);
            $pdf->Cell(180,5,ucwords(strtolower($query_jenis->jenis_lahsar_nama)),1,1,'L',1);
          }

          if($cek_tipe == '' && $baris == 0){
            $no    = 1;
        		$pdf->SetFont('Arial','B',10);
        		$pdf->Cell(15,5,'No',1,0,'C',1);
        		$pdf->Cell(65,5,ucwords(strtolower($q['tipe_lahsar_nama'])),1,0,'C',1);
        		$pdf->Cell(30,5,'Produksi',1,0,'C',1);
        		$pdf->Cell(35,5,'Harga',1,0,'C',1);
        		$pdf->Cell(35,5,'Nilai Produksi',1,1,'C',1);
          }

          if($cek_jenis != $q['tipe_lahsar_jenis_kode'] && $baris > 0){
            $pdf->SetFont('Arial','B',10);
            $query_jenis= $this->apps->findLimit('perikanan_ref_lahsar_jenis', 'jenis_lahsar_kode', $q['tipe_lahsar_jenis_kode']);
            $pdf->Cell(180,5,ucwords(strtolower($query_jenis->jenis_lahsar_nama)),1,1,'L',1);
          }

          if($cek_tipe != $q['tipe_lahsar_kode'] && $baris > 0){
            $no    = 1;
            $pdf->SetFont('Arial','B',10);
        		$pdf->Cell(15,5,'No',1,0,'C',1);
        		$pdf->Cell(65,5,ucwords(strtolower($q['tipe_lahsar_nama'])),1,0,'C',1);
        		$pdf->Cell(30,5,'Produksi',1,0,'C',1);
        		$pdf->Cell(35,5,'Harga',1,0,'C',1);
        		$pdf->Cell(35,5,'Nilai Produksi',1,1,'C',1);
          }
          $pdf->SetWidths(array(15,65,30, 35, 35));
          $pdf->SetAligns(array('C','L','C', 'R', 'R'));
          $pdf->Row(array($no, $q['laporan_lahsar_keterangan'], number_format($q['laporan_lahsar_produksi'], 0, "", ""), number_format($q['laporan_lahsar_harga'], 0, "", "."), number_format($q['laporan_lahsar_nilai_produksi'], 0, "", ".")));

          $cek_jenis = $q['tipe_lahsar_jenis_kode'] ;
          $cek_tipe  = $q['tipe_lahsar_kode'] ;
          $baris++;
          $no++;
        }
      }else if($jenis == 'garam'){
          $pdf->SetFont('Arial','B',12);
          $pdf->Cell(180,5,'FORMULIR PENGISIAN DATA PERIKANAN BUDIDAYA GARAM',0,1,'C',0);
          $pdf->Cell(180,5,'DINAS PERIKANAN KABUPATEN SUMENEP',0,1,'C',0);
          $pdf->Cell(180,5,'BIDANG KELEMBAGAAN DAN PENGENDALIAN USAHA PERIKANAN',0,1,'C',0);
          $pdf->Cell(180,5,'SIE DATA DAN INFORMASI',0,1,'C',0);
          $pdf->Ln(10);
          $pdf->SetFont('Arial','',11);
          $pdf->Cell(150,5,'Bulan',0,0,'R',0);
          $pdf->Cell(30,5,' : '.$bulan[$periode],0,1,'L',0);
          $pdf->Cell(150,5,'tahun',0,0,'R',0);
          $pdf->Cell(30,5,' : '.$tahun,0,1,'L',0);
          $pdf->Ln(5);
          $query_nelayan = $this->lapnelayan_model->detailnelyanan($nik);
          $pdf->Cell(35,5,'Nama RTP',0,0,'L',0);
          $pdf->Cell(130,5,' : '.$query_nelayan->nelayan_nama,0,1,'L',0);
          $pdf->Ln(1);
          $pdf->Cell(35,5,'NIK',0,0,'L',0);
          $pdf->Cell(130,5,' : '.$query_nelayan->nelayan_nik,0,1,'L',0);
          $pdf->Ln(1);
          $pdf->Cell(35,5,'Alamat',0,0,'L',0);
          $pdf->Cell(130,5,' : '.$query_nelayan->nelayan_alamat,0,1,'L',0);
          $pdf->Ln(1);
          $pdf->Cell(35,5,'Desa',0,0,'L',0);
          $pdf->Cell(130,5,' : '.$query_nelayan->desa_nama,0,1,'L',0);
          $pdf->Ln(1);
          $pdf->Cell(35,5,'Kecamatan',0,0,'L',0);
          $pdf->Cell(130,5,' : '.$query_nelayan->kecamatan_nama,0,1,'L',0);
          $pdf->Ln(2);
          $query_garam = $this->lapnelayan_model->detailgaram($query_nelayan->nelayan_key, $periode, $tahun);
          $baris = 0;

          $cek_jenis = '';
          $cek_tipe  = '';
          foreach($query_garam as $q) {
            if(($cek_jenis == '' && $baris ==0 )){
              $query_jenis = $this->apps->findLimit('perikanan_ref_garam_jenis', 'jenis_budidaya_kode', $q['tipe_budidaya_jenis_kode']);
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell(180,5,ucwords(strtolower($query_jenis->jenis_budidaya_nama)),1,1,'L',1);
            }

            if($cek_tipe == '' && $baris == 0){
              $no    = 1;
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell(10,5,'No',1,0,'C',1);
              $pdf->Cell(60,5,ucwords(strtolower($q['tipe_budidaya_nama'])),1,0,'C',1);
              $pdf->Cell(25,5,'Luas',1,0,'C',1);
              $pdf->Cell(25,5,'Produksi',1,0,'C',1);
              $pdf->Cell(30,5,'Harga',1,0,'C',1);
              $pdf->Cell(30,5,'Nilai Produksi',1,1,'C',1);
            }

            if($cek_jenis != $q['tipe_budidaya_jenis_kode'] && $baris > 0){
              $query_jenis = $this->apps->findLimit('perikanan_ref_garam_jenis', 'jenis_budidaya_kode', $q['tipe_budidaya_jenis_kode']);
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell(180,5,ucwords(strtolower($query_jenis->jenis_budidaya_nama)),1,1,'L',1);
            }

            if($cek_tipe != $q['tipe_budidaya_kode'] && $baris > 0){
              $no    = 1;
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell(10,5,'No',1,0,'C',1);
              $pdf->Cell(60,5,ucwords(strtolower($q['tipe_budidaya_nama'])),1,0,'C',1);
              $pdf->Cell(25,5,'Luas',1,0,'C',1);
              $pdf->Cell(25,5,'Produksi',1,0,'C',1);
              $pdf->Cell(30,5,'Harga',1,0,'C',1);
              $pdf->Cell(30,5,'Nilai Produksi',1,1,'C',1);
            }
            $pdf->SetWidths(array(10,60, 25, 25, 30, 30));
            $pdf->SetAligns(array('C','L','C', 'C', 'R', 'R'));
            $pdf->Row(array($no, $q['laporan_budidaya_keterangan'], number_format($q['laporan_budidaya_luas_lahan'], 0, "", ".") , number_format($q['laporan_budidaya_produksi'], 0, "", "."), number_format($q['laporan_budidaya_harga'], 0, "", "."), number_format($q['laporan_budidaya_nilai_produksi'], 0, "", ".")));
            $cek_jenis = $q['tipe_budidaya_jenis_kode'] ;
            $cek_tipe  = $q['tipe_budidaya_kode'] ;
            $baris++;
            $no++;
          }
      }
  		$pdf->Output('D','Formulir RTP '.$query_nelayan->nelayan_nama.'.pdf');
  	}
    public function getDetail()
    {
      $data = array(
        'content' =>  'content/laporan/nelayan/index'
      );
      $this->form_validation->set_rules('tambah_nik', 'NIK', 'required');
      if ($this->form_validation->run() == FALSE) {
        $validation = validation_errors();
        $errors = validation_errors('/', '/');
        if($errors != ''){
          $errors = explode("/", $errors);
          $this->session->set_flashdata('danger',$errors[1]);
        }
        $json = array(
               'tambah_nik' => form_error('tambah_nik', '<p class="mt-3 text-danger">', '</p>'),
               'csrf' => array(
                   'name' => $this->security->get_csrf_token_name(),
                   'hash' => $this->security->get_csrf_hash()
               )
           );
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json));

      }else{
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

        try{
          $hasil = TRUE;
          $query_cek = $this->apps->findLimit('perikanan_nelayan', 'nelayan_nik', $this->input->post("tambah_nik"));
          if(!$query_cek){
            $result = array(
              'status' => 'failed',
              'message' => 'Data NIK Tidak Ditemukan',
              'csrf' => array(
                  'name' => $this->security->get_csrf_token_name(),
                  'hash' => $this->security->get_csrf_hash()
              )
            );
            echo json_encode($result);
            exit();
          }
            $nik   = $this->input->post("tambah_nik");
            $jenis = $this->input->post("jenis_usaha");
            $periode = $this->input->post("tambah_bulan");
            $tahun   = $this->input->post("tambah_tahun");
            $hasil = "";

            if($jenis == "budidaya"){
              $query_nelayan = $this->lapnelayan_model->detailnelyanan($nik);
              if(!$query_nelayan){
                $result = array(
                  'status' => 'failed',
                  'message' => 'Data NIK Tidak Ditemukan',
                  'csrf' => array(
                      'name' => $this->security->get_csrf_token_name(),
                      'hash' => $this->security->get_csrf_hash()
                  )
                );
                echo json_encode($result);
                exit();
              }
              $query_budidaya = $this->lapnelayan_model->detailbudidaya($query_nelayan->nelayan_key, $periode, $tahun);
              if(!$query_budidaya){
                $result = array(
                  'status' => 'failed',
                  'message' => 'Data Detail Jenis Usaha Budidaya Tidak Ditemukan',
                  'csrf' => array(
                      'name' => $this->security->get_csrf_token_name(),
                      'hash' => $this->security->get_csrf_hash()
                  )
                );
                echo json_encode($result);
                exit();
              }
              $hasil .= '<div class="row">';
              $hasil .= '  <div class="col-md-6">';
              $hasil .= '     <table class="table table-bordered table-hover no-footer">';
              $hasil .= '       <t><th>Nama RTP</th><th> '.$query_nelayan->nelayan_nama.'</th></tr>';
              $hasil .= '       <tr><th>NIK</th><th> '.$query_nelayan->nelayan_nik.'</th></tr>';
              $hasil .= '       <tr><th>Alamat</th><th> '.$query_nelayan->nelayan_alamat.'</th></tr>';
              $hasil .= '       <tr><th>Desa</th><th> '.$query_nelayan->desa_nama.'</th></tr>';
              $hasil .= '       <tr><th>Kecamatan</th><th> '.$query_nelayan->kecamatan_nama.'</th></tr>';
              $hasil .= '     </table>';
              $hasil .= '  </div>';
              $hasil .= '  <div class="col-md-6">';
              $hasil .= '     <table class="table table-bordered table-hover no-footer">';
              $hasil .= '       <tr><th>Periode</th><th> '.$bulan[$periode].' '.$tahun.'</th></tr>';
              $hasil .= '     </table>';
              $hasil .= '  </div>';
              $hasil .= '</div>';
              $hasil .= '<div class="row">';
              $hasil .= '  <div class="col-md-12">';
              $hasil .= '     <table class="table table-bordered table-hover no-footer">';
              $baris = 0;

              $cek_jenis = '';
              $cek_tipe  = '';
              foreach($query_budidaya as $q) {
                if(($cek_jenis == '' && $baris ==0 )){
                  $query_jenis = $this->apps->findLimit('perikanan_ref_budidaya_jenis', 'jenis_budidaya_kode', $q['tipe_budidaya_jenis_kode']);
                  $hasil .= '       <tr class="table-title"><th colspan="6">'.$query_jenis->jenis_budidaya_nama.'</th></tr>';
                }

                if($cek_tipe == '' && $baris == 0){
                  $no    = 1;
                  $hasil .= '       <tr class="table-header"><th>No</th><th>'.$q['tipe_budidaya_nama'].'</th><th>Luas</th><th>Produksi</th><th>Harga</th><th>Nilai Produksi</th></tr>';
                }

                if($cek_jenis != $q['tipe_budidaya_jenis_kode'] && $baris > 0){
                  $query_jenis= $this->apps->findLimit('perikanan_ref_budidaya_jenis', 'jenis_budidaya_kode', $q['tipe_budidaya_jenis_kode']);
                  $hasil .= '       <tr><th colspan="6" class="table-title">'.$query_jenis->jenis_budidaya_nama.'</th></tr>';
                }

                if($cek_tipe != $q['tipe_budidaya_kode'] && $baris > 0){
                  $no    = 1;
                  $hasil .= '       <tr class="table-header"><th>No</th><th>'.$q['tipe_budidaya_nama'].'</th><th>Luas</th><th>Produksi</th><th>Harga</th><th>Nilai Produksi</th></tr>';
                }
                $hasil .= '       <tr><th>'.$no.'</th><th>'.$q['laporan_budidaya_keterangan'].'</th><th style="text-align: center">'.number_format($q['laporan_budidaya_luas_lahan'], 0, "", ".").'</th><th style="text-align: center">'.number_format($q['laporan_budidaya_produksi'], 0, "", ".").'</th><th style="text-align: right">'.number_format($q['laporan_budidaya_harga'], 0, "", ".").'</th><th style="text-align: right">'.number_format($q['laporan_budidaya_nilai_produksi'], 0, "", ".").'</th></tr>';

                $cek_jenis = $q['tipe_budidaya_jenis_kode'] ;
                $cek_tipe  = $q['tipe_budidaya_kode'] ;
                $baris++;
                $no++;
              }

              $hasil .= '     </table>';
              $hasil .= '  </div>';
              $hasil .= '</div>';
            }elseif ($jenis == "tangkap") {
              $query_nelayan = $this->lapnelayan_model->detailnelyanan($nik);
              if(!$query_nelayan){
                $result = array(
                  'status' => 'failed',
                  'message' => 'Data NIK Tidak Ditemukan',
                  'csrf' => array(
                      'name' => $this->security->get_csrf_token_name(),
                      'hash' => $this->security->get_csrf_hash()
                  )
                );
                echo json_encode($result);
                exit();
              }
              $query_budidaya = $this->lapnelayan_model->detailtangkap($query_nelayan->nelayan_key, $periode, $tahun);
              if(!$query_budidaya){
                $result = array(
                  'status' => 'failed',
                  'message' => 'Data Detail Jenis Usaha Tangkap Tidak Ditemukan',
                  'csrf' => array(
                      'name' => $this->security->get_csrf_token_name(),
                      'hash' => $this->security->get_csrf_hash()
                  )
                );
                echo json_encode($result);
                exit();
              }
              $hasil .= '<div class="row">';
              $hasil .= '  <div class="col-md-6">';
              $hasil .= '     <table class="table table-bordered table-hover no-footer">';
              $hasil .= '       <t><th>Nama RTP</th><th> '.$query_nelayan->nelayan_nama.'</th></tr>';
              $hasil .= '       <tr><th>NIK</th><th> '.$query_nelayan->nelayan_nik.'</th></tr>';
              $hasil .= '       <tr><th>Alamat</th><th> '.$query_nelayan->nelayan_alamat.'</th></tr>';
              $hasil .= '       <tr><th>Desa</th><th> '.$query_nelayan->desa_nama.'</th></tr>';
              $hasil .= '       <tr><th>Kecamatan</th><th> '.$query_nelayan->kecamatan_nama.'</th></tr>';
              $hasil .= '     </table>';
              $hasil .= '  </div>';
              $hasil .= '  <div class="col-md-6">';
              $hasil .= '     <table class="table table-bordered table-hover no-footer">';
              $hasil .= '       <tr><th>Periode</th><th> '.$bulan[$periode].' '.$tahun.'</th></tr>';
              $hasil .= '     </table>';
              $hasil .= '  </div>';
              $hasil .= '</div>';
              $hasil .= '<div class="row">';
              $hasil .= '  <div class="col-md-12">';
              $hasil .= '     <table class="table table-bordered table-hover no-footer">';
              $baris = 0;

              $cek_jenis = '';
              $cek_tipe  = '';
              foreach($query_budidaya as $q) {
                if(($cek_jenis == '' && $baris ==0 )){
                  $hasil .= '       <tr class="table-title"><th colspan="6">JENIS USAHA - '.$q['jenis_tangkap_nama'].'</th></tr>';
                  $hasil .= '       <tr class="table-header"><th>No</th><th>Jenis</th><th>Jumlah Trip</th><th>Produksi</th><th>Harga</th><th>Nilai Produksi</th></tr>';
                  $no    = 1;
                }

                if($cek_jenis != $q['laporan_tangkap_jenis'] && $baris > 0){
                  $hasil .= '       <tr class="table-title"><th colspan="6">JENIS USAHA - '.$q['jenis_tangkap_nama'].'</th></tr>';
                  $hasil .= '       <tr class="table-header"><th>No</th><th>Jenis</th><th>Jumlah Trip</th><th>Produksi</th><th>Harga</th><th>Nilai Produksi</th></tr>';
                  $no    = 1;
                }
                $hasil .= '       <tr><th>'.$no.'</th><th>'.$q['laporan_tangkap_keterangan'].'</th><th style="text-align: center">'.number_format($q['laporan_tangkap_jumlah_trip'], 0, "", ".").'</th><th style="text-align: center">'.number_format($q['laporan_tangkap_produksi'], 0, "", ".").'</th><th style="text-align: right">'.number_format($q['laporan_tangkap_harga'], 0, "", ".").'</th><th style="text-align: right">'.number_format($q['laporan_tangkap_nilai_produksi'], 0, "", ".").'</th></tr>';

                $cek_jenis = $q['laporan_tangkap_jenis'] ;
                $baris++;
                $no++;
              }
              $hasil .= '     </table>';
              $hasil .= '  </div>';
              $hasil .= '</div>';
              $query_alat = $this->lapnelayan_model->detailtangkapalat($query_nelayan->nelayan_key, $periode, $tahun);
              if($query_alat){
                $hasil .= '<div class="row">';
                $hasil .= '  <div class="col-md-12">';
                $hasil .= '     <table class="table table-bordered table-hover no-footer">';
                $baris = 0;

                $cek_jenis = '';
                $cek_tipe  = '';
                foreach($query_alat as $q) {
                  if(($cek_jenis == '' && $baris ==0 )){
                    $hasil .= '       <tr class="table-title"><th colspan="3">ALAT TANGKAP - '.$q['jenis_tangkap_nama'].'</th></tr>';
                    $hasil .= '       <tr class="table-header"><th>No</th><th>Alat tangkap yang dimiliki</th><th>Jumlah (Buah)</th></tr>';
                    $no    = 1;
                  }

                  if($cek_jenis != $q['laporan_tangkap_alat_jenis_tangkap'] && $baris > 0){
                    $hasil .= '       <tr class="table-title"><th colspan="3">ALAT TANGKAP - '.$q['jenis_tangkap_nama'].'</th></tr>';
                    $hasil .= '       <tr class="table-header"><th>No</th><th>Alat tangkap yang dimiliki</th><th>Jumlah (Buah)</th></tr>';
                    $no    = 1;
                  }
                  $hasil .= '       <tr><th>'.$no.'</th><th>'.$q['laporan_tangkap_alat_keterangan'].'</th><th style="text-align: right">'.number_format($q['laporan_tangkap_alat_jumlah'], 0, "", ".").'</th></tr>';

                  $cek_jenis = $q['laporan_tangkap_alat_jenis_tangkap'] ;
                  $baris++;
                  $no++;
                }
                $hasil .= '     </table>';
                $hasil .= '  </div>';
                $hasil .= '</div>';
              }
              $query_perahu = $this->lapnelayan_model->detailtangkapperahu($query_nelayan->nelayan_key, $periode, $tahun);
              if($query_perahu){
                $hasil .= '<div class="row">';
                $hasil .= '  <div class="col-md-12">';
                $hasil .= '     <table class="table table-bordered table-hover no-footer">';
                $baris = 0;

                $cek_jenis = '';
                $cek_tipe  = '';
                foreach($query_perahu as $q) {
                  if(($cek_jenis == '' && $baris ==0 )){
                    $hasil .= '       <tr class="table-title"><th colspan="3">PERAHU - '.$q['jenis_tangkap_nama'].'</th></tr>';
                    $hasil .= '       <tr class="table-header"><th>No</th><th>Perahu yang dimiliki</th><th>Jumlah (Buah)</th></tr>';
                    $no    = 1;
                  }

                  if($cek_jenis != $q['laporan_tangkap_perahu_jenis_perahu'] && $baris > 0){
                    $hasil .= '       <tr class="table-title"><th colspan="3">PERAHU - '.$q['jenis_tangkap_nama'].'</th></tr>';
                    $hasil .= '       <tr class="table-header"><th>No</th><th>Perahu yang dimiliki</th><th>Jumlah (Buah)</th></tr>';
                    $no    = 1;
                  }
                  $hasil .= '       <tr><th>'.$no.'</th><th>'.$q['perahu_tangkap_nama'].'</th><th style="text-align: right">'.number_format($q['laporan_tangkap_perahu_jumlah'], 0, "", ".").'</th></tr>';

                  $cek_jenis = $q['laporan_tangkap_perahu_jenis_perahu'] ;
                  $baris++;
                  $no++;
                }
                $hasil .= '     </table>';
                $hasil .= '  </div>';
                $hasil .= '</div>';
              }
            }elseif ($jenis == "lahsar") {
              $query_nelayan = $this->lapnelayan_model->detailnelyanan($nik);
              if(!$query_nelayan){
                $result = array(
                  'status' => 'failed',
                  'message' => 'Data NIK Tidak Ditemukan',
                  'csrf' => array(
                      'name' => $this->security->get_csrf_token_name(),
                      'hash' => $this->security->get_csrf_hash()
                  )
                );
                echo json_encode($result);
                exit();
              }
              $query_budidaya = $this->lapnelayan_model->detaillahsar($query_nelayan->nelayan_key, $periode, $tahun);
              if(!$query_budidaya){
                $result = array(
                  'status' => 'failed',
                  'message' => 'Data Detail Jenis Usaha Pengolahan dan Pemasaran Tidak Ditemukan',
                  'csrf' => array(
                      'name' => $this->security->get_csrf_token_name(),
                      'hash' => $this->security->get_csrf_hash()
                  )
                );
                echo json_encode($result);
                exit();
              }
              $hasil .= '<div class="row">';
              $hasil .= '  <div class="col-md-6">';
              $hasil .= '     <table class="table table-bordered table-hover no-footer">';
              $hasil .= '       <t><th>Nama RTP</th><th> '.$query_nelayan->nelayan_nama.'</th></tr>';
              $hasil .= '       <tr><th>NIK</th><th> '.$query_nelayan->nelayan_nik.'</th></tr>';
              $hasil .= '       <tr><th>Alamat</th><th> '.$query_nelayan->nelayan_alamat.'</th></tr>';
              $hasil .= '       <tr><th>Desa</th><th> '.$query_nelayan->desa_nama.'</th></tr>';
              $hasil .= '       <tr><th>Kecamatan</th><th> '.$query_nelayan->kecamatan_nama.'</th></tr>';
              $hasil .= '     </table>';
              $hasil .= '  </div>';
              $hasil .= '  <div class="col-md-6">';
              $hasil .= '     <table class="table table-bordered table-hover no-footer">';
              $hasil .= '       <tr><th>Periode</th><th> '.$bulan[$periode].' '.$tahun.'</th></tr>';
              $hasil .= '     </table>';
              $hasil .= '  </div>';
              $hasil .= '</div>';
              $hasil .= '<div class="row">';
              $hasil .= '  <div class="col-md-12">';
              $hasil .= '     <table class="table table-bordered table-hover no-footer">';
              $baris = 0;

              $cek_jenis = '';
              $cek_tipe  = '';
              foreach($query_budidaya as $q) {
                if(($cek_jenis == '' && $baris ==0 )){
                  $query_jenis = $this->apps->findLimit('perikanan_ref_lahsar_jenis', 'jenis_lahsar_kode', $q['tipe_lahsar_jenis_kode']);
                  $hasil .= '       <tr class="table-title"><th colspan="5">'.$query_jenis->jenis_lahsar_nama.'</th></tr>';
                }

                if($cek_tipe == '' && $baris == 0){
                  $no    = 1;
                  $hasil .= '       <tr class="table-header"><th>No</th><th>'.$q['tipe_lahsar_nama'].'</th><th>Produksi</th><th>Harga</th><th>Nilai Produksi</th></tr>';
                }

                if($cek_jenis != $q['tipe_lahsar_jenis_kode'] && $baris > 0){
                  $query_jenis= $this->apps->findLimit('perikanan_ref_lahsar_jenis', 'jenis_lahsar_kode', $q['tipe_lahsar_jenis_kode']);
                  $hasil .= '       <tr class="table-title"><th colspan="5">'.$query_jenis->jenis_lahsar_nama.'</th></tr>';
                }

                if($cek_tipe != $q['tipe_lahsar_kode'] && $baris > 0){
                  $no    = 1;
                  $hasil .= '       <tr class="table-header"><th>No</th><th>'.$q['tipe_lahsar_nama'].'</th><th>Produksi</th><th>Harga</th><th>Nilai Produksi</th></tr>';
                }
                $hasil .= '       <tr><th>'.$no.'</th><th>'.$q['laporan_lahsar_keterangan'].'</th><th style="text-align: center">'.number_format($q['laporan_lahsar_produksi'], 0, "", "").'</th><th style="text-align: right">'.number_format($q['laporan_lahsar_harga'], 0, "", ".").'</th><th style="text-align: right">'.number_format($q['laporan_lahsar_nilai_produksi'], 0, "", ".").'</th></tr>';

                $cek_jenis = $q['tipe_lahsar_jenis_kode'] ;
                $cek_tipe  = $q['tipe_lahsar_kode'] ;
                $baris++;
                $no++;
              }

              $hasil .= '     </table>';
              $hasil .= '  </div>';
              $hasil .= '</div>';
            }elseif($jenis == "garam"){
              $query_nelayan = $this->lapnelayan_model->detailnelyanan($nik);
              if(!$query_nelayan){
                $result = array(
                  'status' => 'failed',
                  'message' => 'Data NIK Tidak Ditemukan',
                  'csrf' => array(
                      'name' => $this->security->get_csrf_token_name(),
                      'hash' => $this->security->get_csrf_hash()
                  )
                );
                echo json_encode($result);
                exit();
              }
              $query_garam = $this->lapnelayan_model->detailgaram($query_nelayan->nelayan_key, $periode, $tahun);
              if(!$query_garam){
                $result = array(
                  'status' => 'failed',
                  'message' => 'Data Detail Jenis Usaha Budidaya Garam Tidak Ditemukan',
                  'csrf' => array(
                      'name' => $this->security->get_csrf_token_name(),
                      'hash' => $this->security->get_csrf_hash()
                  )
                );
                echo json_encode($result);
                exit();
              }
              $hasil .= '<div class="row">';
              $hasil .= '  <div class="col-md-6">';
              $hasil .= '     <table class="table table-bordered table-hover no-footer">';
              $hasil .= '       <t><th>Nama RTP</th><th> '.$query_nelayan->nelayan_nama.'</th></tr>';
              $hasil .= '       <tr><th>NIK</th><th> '.$query_nelayan->nelayan_nik.'</th></tr>';
              $hasil .= '       <tr><th>Alamat</th><th> '.$query_nelayan->nelayan_alamat.'</th></tr>';
              $hasil .= '       <tr><th>Desa</th><th> '.$query_nelayan->desa_nama.'</th></tr>';
              $hasil .= '       <tr><th>Kecamatan</th><th> '.$query_nelayan->kecamatan_nama.'</th></tr>';
              $hasil .= '     </table>';
              $hasil .= '  </div>';
              $hasil .= '  <div class="col-md-6">';
              $hasil .= '     <table class="table table-bordered table-hover no-footer">';
              $hasil .= '       <tr><th>Periode</th><th> '.$bulan[$periode].' '.$tahun.'</th></tr>';
              $hasil .= '     </table>';
              $hasil .= '  </div>';
              $hasil .= '</div>';
              $hasil .= '<div class="row">';
              $hasil .= '  <div class="col-md-12">';
              $hasil .= '     <table class="table table-bordered table-hover no-footer">';
              $baris = 0;

              $cek_jenis = '';
              $cek_tipe  = '';
              foreach($query_garam as $q) {
                if(($cek_jenis == '' && $baris ==0 )){
                  $query_jenis = $this->apps->findLimit('perikanan_ref_garam_jenis', 'jenis_budidaya_kode', $q['tipe_budidaya_jenis_kode']);
                  $hasil .= '       <tr class="table-title"><th colspan="6">'.$query_jenis->jenis_budidaya_nama.'</th></tr>';
                }

                if($cek_tipe == '' && $baris == 0){
                  $no    = 1;
                  $hasil .= '       <tr class="table-header"><th>No</th><th>'.$q['tipe_budidaya_nama'].'</th><th>Luas</th><th>Produksi</th><th>Harga</th><th>Nilai Produksi</th></tr>';
                }

                if($cek_jenis != $q['tipe_budidaya_jenis_kode'] && $baris > 0){
                  $query_jenis= $this->apps->findLimit('perikanan_ref_garam_jenis', 'jenis_budidaya_kode', $q['tipe_budidaya_jenis_kode']);
                  $hasil .= '       <tr><th colspan="6" class="table-title">'.$query_jenis->jenis_budidaya_nama.'</th></tr>';
                }

                if($cek_tipe != $q['tipe_budidaya_kode'] && $baris > 0){
                  $no    = 1;
                  $hasil .= '       <tr class="table-header"><th>No</th><th>'.$q['tipe_budidaya_nama'].'</th><th>Luas</th><th>Produksi</th><th>Harga</th><th>Nilai Produksi</th></tr>';
                }
                $hasil .= '       <tr><th>'.$no.'</th><th>'.$q['laporan_budidaya_keterangan'].'</th><th style="text-align: center">'.number_format($q['laporan_budidaya_luas_lahan'], 0, "", ".").'</th><th style="text-align: center">'.number_format($q['laporan_budidaya_produksi'], 0, "", ".").'</th><th style="text-align: right">'.number_format($q['laporan_budidaya_harga'], 0, "", ".").'</th><th style="text-align: right">'.number_format($q['laporan_budidaya_nilai_produksi'], 0, "", ".").'</th></tr>';

                $cek_jenis = $q['tipe_budidaya_jenis_kode'] ;
                $cek_tipe  = $q['tipe_budidaya_kode'] ;
                $baris++;
                $no++;
              }

              $hasil .= '     </table>';
              $hasil .= '  </div>';
              $hasil .= '</div>';
            }else{
              $result = array(
                'status' => 'failed',
                'message' => 'Jenis Usaha Belum Ditentukan',
                'csrf' => array(
                    'name' => $this->security->get_csrf_token_name(),
                    'hash' => $this->security->get_csrf_hash()
                )
              );
              echo json_encode($result);
              exit();
            }
            $result = array(
              'status' => 'success',
              'message' => $hasil,
              'id' => base64_encode($nik."|".$jenis."|".$periode."|".$tahun),
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
