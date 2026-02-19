<?php

namespace App\Models;

use CodeIgniter\Model;

class CabangDinasKabupatenModel extends Model
{
    protected $table = 'cabang_dinas_kabupaten';
    protected $primaryKey = 'id';
    protected $allowedFields = ['cabang_dinas_id', 'kabupaten_id'];

    public function getByCabangDinas($cabangDinasId)
    {
        return $this->where('cabang_dinas_id', $cabangDinasId)->findAll();
    }

    public function getByKabupaten($kabupatenId)
    {
        return $this->where('kabupaten_id', $kabupatenId)->findAll();
    }
}
