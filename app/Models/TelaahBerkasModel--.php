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

        /**
     * ✅ Mendapatkan daftar usulan yang menunggu telaah dengan pagination
     */
    public function getUsulanMenungguTelaah($role, $cabangDinasIds, $perPage)
    {
        $query = $this->select('pengiriman_usulan.nomor_usulan,
                                usulan.guru_nama, usulan.guru_nip, usulan.guru_nik, 
                                usulan.sekolah_asal, usulan.sekolah_tujuan, usulan.alasan, usulan.google_drive_link, usulan.created_at, 
                                cabang_dinas.id as cabang_dinas_id, cabang_dinas.nama_cabang, 
                                pengiriman_usulan.dokumen_rekomendasi, pengiriman_usulan.operator, 
                                pengiriman_usulan.no_hp, pengiriman_usulan.status_usulan_cabdin, pengiriman_usulan.created_at AS tanggal_dikirim, 
                                pengiriman_usulan.updated_at AS tanggal_update, pengiriman_usulan.catatan')
                     ->join('usulan', 'pengiriman_usulan.nomor_usulan = usulan.nomor_usulan', 'inner') 
                     ->join('cabang_dinas', 'usulan.cabang_dinas_id = cabang_dinas.id', 'left')
                     ->where('pengiriman_usulan.status_usulan_cabdin', 'Lengkap')
                     ->where('pengiriman_usulan.status_telaah', NULL)
                     ->orderBy('tanggal_dikirim', 'ASC');

        if ($role === 'dinas') {
            $query->whereIn('usulan.cabang_dinas_id', $cabangDinasIds);
        }

        return $query->paginate($perPage, 'usulanMenunggu');
    }

    /**
     * ✅ Mendapatkan daftar usulan yang sudah ditelaah dengan pagination
     */
    public function getUsulanSudahDitelaah($role, $cabangDinasIds, $perPage)
    {
        $query = $this->select('pengiriman_usulan.nomor_usulan, usulan.guru_nama, usulan.guru_nip, usulan.guru_nik, 
                                usulan.sekolah_asal, usulan.sekolah_tujuan, usulan.alasan, usulan.google_drive_link, usulan.created_at, 
                                cabang_dinas.id as cabang_dinas_id, cabang_dinas.nama_cabang, 
                                pengiriman_usulan.status_telaah, pengiriman_usulan.updated_at_telaah, 
                                pengiriman_usulan.dokumen_rekomendasi, pengiriman_usulan.operator, 
                                pengiriman_usulan.no_hp, pengiriman_usulan.status_usulan_cabdin, pengiriman_usulan.created_at AS tanggal_dikirim, 
                                pengiriman_usulan.updated_at AS tanggal_update, pengiriman_usulan.catatan')
                     ->join('usulan', 'pengiriman_usulan.nomor_usulan = usulan.nomor_usulan', 'inner') 
                     ->join('cabang_dinas', 'usulan.cabang_dinas_id = cabang_dinas.id', 'left')
                     ->where('pengiriman_usulan.status_telaah !=', NULL)
                     ->orderBy('pengiriman_usulan.updated_at_telaah', 'DESC');

        if ($role === 'dinas') {
            $query->whereIn('usulan.cabang_dinas_id', $cabangDinasIds);
        }

        return $query->paginate($perPage, 'usulanDitelaah');
    }
}
