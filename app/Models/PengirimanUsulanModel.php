<?php

namespace App\Models;

use CodeIgniter\Model;

class PengirimanUsulanModel extends Model
{
    protected $table = 'pengiriman_usulan'; // Mengarahkan ke tabel pengiriman_usulan
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_usulan',
        'dokumen_rekomendasi',
        'operator',
        'no_hp',
        'status_usulan_cabdin',
        'catatan',
        'created_at',
        'updated_at',
        'status_telaah',
        'catatan_telaah',
        'updated_at_telaah',
    ];
}
