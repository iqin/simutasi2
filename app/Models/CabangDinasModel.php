<?php

namespace App\Models;

use CodeIgniter\Model;

class CabangDinasModel extends Model
{
    protected $table = 'cabang_dinas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode_cabang', 'nama_cabang'];

    public function getAllWithKabupaten($perPage)
    {
        return $this->select('cabang_dinas.*, GROUP_CONCAT(kabupaten.nama_kab SEPARATOR ", ") as kabupaten_wilayah')
                    ->join('cabang_dinas_kabupaten', 'cabang_dinas_kabupaten.cabang_dinas_id = cabang_dinas.id', 'left')
                    ->join('kabupaten', 'kabupaten.id_kab = cabang_dinas_kabupaten.kabupaten_id', 'left')
                    ->groupBy('cabang_dinas.id')
                    ->orderBy('cabang_dinas.id', 'DESC')
                    ->paginate($perPage, 'cabang_dinas');
    }

}
