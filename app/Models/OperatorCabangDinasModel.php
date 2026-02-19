<?php

namespace App\Models;

use CodeIgniter\Model;

class OperatorCabangDinasModel extends Model
{
    protected $table = 'operator_cabang_dinas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'cabang_dinas_id'];
}
