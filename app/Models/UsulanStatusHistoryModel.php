<?php

namespace App\Models;

use CodeIgniter\Model;

class UsulanStatusHistoryModel extends Model
{
    protected $table = 'usulan_status_history';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nomor_usulan',
        'status',
        'updated_at',
        'catatan_history',
    ];

    protected $useTimestamps = false; // Karena updated_at diatur manual.
}

