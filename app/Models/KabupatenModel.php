<?php

namespace App\Models;

use CodeIgniter\Model;

class KabupatenModel extends Model
{
    protected $table = 'kabupaten';
    protected $primaryKey = 'id_kab';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['nama_kab', 'nama_ibukotakab'];
}
