<?php

namespace App\Models;

use CodeIgniter\Model;

class RekomkadisModel extends Model
{
    protected $table = 'rekom_kadis';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nomor_rekomkadis', 'tanggal_rekomkadis', 'perihal_rekomkadis', 'file_rekomkadis'];
}
