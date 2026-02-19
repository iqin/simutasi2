<?php

namespace App\Models;

use CodeIgniter\Model;

class SkMutasiModel extends Model
{
    protected $table = 'sk_mutasi';
    protected $primaryKey = 'id_skmutasi';
    protected $allowedFields = ['nomor_usulan', 'nomor_skmutasi', 'jenis_mutasi', 'tanggal_skmutasi', 'file_skmutasi', 'created_at', 'updated_at'];
}
