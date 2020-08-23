<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    //fungsi cek session logged in
    function is_logged_in()
    {
        return $this->session->userdata('user_id');
    }

    //fungsi cek level
    function is_role()
    {
        return $this->session->userdata('user_role');
    }

    //fungsi check login
    function check_login($table, $field1, $field2)
    {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($field1);
        $this->db->where($field2);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            return FALSE;
        } else {
            return $query->result();
        }
    }
    function getAll(){
      $query =  $this->db->query('SELECT *
                                    FROM users
                                LEFT JOIN sys_kategori ON kategori_id = kategori');
          return $query->result_array();
    }

    public function cariuser($val, $id){
      $query = $this->db->where('username', $val)
              ->where('id !=', $id)
              ->get('users');
      if($query){
          return $query->row();
      }else{
          return false;
      }
    }
}
