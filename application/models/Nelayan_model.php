<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nelayan_model extends CI_Model
{
    //fungsi cek session logged in
    function dataNelayan()
    {
      $this->db->select('nelayan_key, nelayan_nama, kelompok_nama, nelayan_nik, nelayan_alamat, desa_nama, kecamatan_nama, nelayan_create');
      $this->db->from('perikanan_nelayan');
      $this->db->join('perikanan_ref_kecamatan', 'kecamatan_kode = nelayan_kecamatan', 'left');
      $this->db->join('perikanan_ref_desa', 'desa_kode = nelayan_desa', 'left');
      $this->db->join('perikanan_ref_kelompok','kelompok_key = nelayan_kelompok_key','left');
      $this->db->order_by('nelayan_create', 'desc');
      $query = $this->db->get();
        return $query->result_array();
    }
    function getdataNelayan($nik)
    {
      $this->db->select('nelayan_key, nelayan_nama, nelayan_nik, nelayan_alamat, desa_nama, kecamatan_nama');
      $this->db->from('perikanan_nelayan');
      $this->db->join('perikanan_ref_kecamatan', 'kecamatan_kode = nelayan_kecamatan', 'left');
      $this->db->join('perikanan_ref_desa', 'desa_kode = nelayan_desa', 'left');
      $this->db->where('nelayan_nik',$nik);
      $query = $this->db->get();
      if($query){
          return $query->row();
      }else{
          return false;
      }
    }
}
