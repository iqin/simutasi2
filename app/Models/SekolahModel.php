<?php

namespace App\Models;

use CodeIgniter\Model;

class SekolahModel extends Model
{
    protected $table = 'data_sekolah';
    protected $primaryKey = 'id';
    protected $allowedFields = ['npsn', 'nama_sekolah', 'alamat_sekolah', 'kabupaten_id', 'jenjang', 'status'];

    public function get_all_sekolah()
    {
        return $this->select('data_sekolah.*, kabupaten.nama_kab')
                    ->join('kabupaten', 'data_sekolah.kabupaten_id = kabupaten.id_kab', 'left')
                    ->orderBy('data_sekolah.nama_sekolah', 'ASC')
                    ->findAll();
    }
}
