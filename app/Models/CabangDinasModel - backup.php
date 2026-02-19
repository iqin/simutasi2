<?php

namespace App\Models;

use CodeIgniter\Model;

class CabangDinasModel extends Model
{
    protected $table = 'cabang_dinas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode_cabang', 'nama_cabang'];
}
