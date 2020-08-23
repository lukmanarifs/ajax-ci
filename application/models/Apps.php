<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Apps extends CI_Model
{
  public function getAll($table){
    $query = $this->db->select("*")
               ->from($table)
               ->get();
      return $query->result_array();
  }

  public function get($table, $id, $val){
    $query = $this->db->where($id, $val)
            ->get($table);
      return $query->result_array();
  }

  public function find($table, $id, $val){
    $query = $this->db->where($id, $val)
            ->get($table);
    if($query){
        return $query->row();
    }else{
        return false;
    }
  }

  public function findLimit($table, $id, $val){
    $query = $this->db->where($id, $val)
            ->limit(1)
            ->get($table);
    if($query){
        return $query->row();
    }else{
        return false;
    }
  }

  public function save($table, $data)
  {
      $query = $this->db->insert($table, $data);
      if($query){
          return true;
      }else{
          return false;
      }
  }

  public function update($table, $id, $val, $data)
  {
    $this->db->where($id, $val);
    $query = $this->db->update($table, $data);
    if($query){
        return true;
    }else{
        return false;
    }
  }
  public function destroy($table, $id, $val)
  {
    $this->db->where($id, $val);
    $query = $this->db->delete($table);
    if($query){
        return true;
    }else{
        return false;
    }
  }

  public function getBulan($bln){
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

    return $bulan[$bln];
  }
}
