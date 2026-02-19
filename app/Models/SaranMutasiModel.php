<?php

namespace App\Models;

use CodeIgniter\Model;

class SaranMutasiModel extends Model
{
    protected $table = 'saran_mutasi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nomor_usulan', 'email', 'saran', 'balasan', 'status', 'created_at'];
    protected $useTimestamps = true;
    protected $returnType = 'array';


}

