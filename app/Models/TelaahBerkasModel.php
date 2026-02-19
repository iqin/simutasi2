<?php

namespace App\Models;

use CodeIgniter\Model;

class TelaahBerkasModel extends Model
{
    protected $table = 'pengiriman_usulan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_usulan', 'status_telaah', 'catatan_telaah', 'updated_at_telaah', 
        'status_usulan_cabdin', 'dokumen_rekomendasi', 'operator', 'no_hp'
    ];
}
