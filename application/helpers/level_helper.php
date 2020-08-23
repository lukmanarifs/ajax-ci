<?php
function level_user($nama_controller,$nama_function,$kategori,$akses){
    $ci =& get_instance();
    return $ci->db->select("a.akses_kategori")
                ->from("sys_hak_akses a")
                ->join('sys_modul b', 'b.modul_id = a.akses_modul AND b.modul_controller ="'.$nama_controller.'"')
                ->where(array('a.akses_kategori' => $kategori, 'a.akses_hak' => $akses, 'b.modul_function' => $nama_function))->get()->num_rows();
}
