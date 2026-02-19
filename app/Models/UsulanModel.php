<?php

namespace App\Models;

use CodeIgniter\Model;

class UsulanModel extends Model
{
    protected $table = 'usulan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_usulan',
        'guru_nama',
        'guru_nip',
        'guru_nik',
        'sekolah_asal',
        'sekolah_tujuan',
        'status',
        'alasan',
        'google_drive_link',
        'cabang_dinas_id',
        'created_at',
        'updated_at',
        'id_rekomkadis',
        'jenis_usulan',
    ];

    /**
     * Mendapatkan usulan berdasarkan status dan cabang dinas.
     *
     * @param string $status
     * @param string|null $cabangDinasId
     * @return array
     */
    public function filterByCabangDinas($cabangDinasId, $searchNIP = null)
    {
        $query = $this->where('cabang_dinas_id', $cabangDinasId);

        if (!empty($searchNIP)) {
            $query->like('guru_nip', $searchNIP);
        }

        return $query;
    }
}
