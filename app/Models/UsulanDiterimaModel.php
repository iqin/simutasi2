<?php

namespace App\Models;

use CodeIgniter\Model;

class UsulanDiterimaModel extends Model
{
    protected $table = 'usulan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'guru_nama',
        'guru_nip',
        'sekolah_asal',
        'sekolah_tujuan',
        'alasan',
        'nomor_usulan',
        'id_rekomkadis',
        'status',
    ];

    public function getBelumTerkait()
    {
        // Ambil data usulan yang belum terkait dengan rekom_kadis
        return $this->where('id_rekomkadis', null)
            ->where('status', '04') // Filter status diterima
            ->findAll();
    }

    public function getTerkait()
    {
        // Ambil data usulan yang sudah terkait dengan rekom_kadis
        return $this->select('usulan.*, rekom_kadis.nomor_rekomkadis, rekom_kadis.tanggal_rekomkadis')
            ->join('rekom_kadis', 'usulan.id_rekomkadis = rekom_kadis.id', 'inner')
            ->findAll();
    }
}
