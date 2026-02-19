<?php

namespace App\Models;

use CodeIgniter\Model;

class VerifikasiBerkasModel extends Model
{
    protected $table = 'usulan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'guru_nama', 'guru_nip', 'sekolah_asal', 'sekolah_tujuan',
        'nomor_usulan', 'status', 'cabang_dinas_id', 'created_at'
    ];

    /**
     * ✅ Mendapatkan daftar usulan berdasarkan status tertentu dengan pagination
     */
  /*kode ini kita ubah karena ada bug doble nama usulan walaupun di databse normal
  public function getUsulanByStatus($status, $cabangDinasIds = null, $perPage = 10, $paginationGroup = 'page_status03')
    {
        $query = $this->select('
                        usulan.*, 
                        cabang_dinas.nama_cabang, 
                        pengiriman_usulan.dokumen_rekomendasi, 
                        pengiriman_usulan.status_usulan_cabdin, 
                        pengiriman_usulan.operator, 
                        pengiriman_usulan.no_hp, 
                        pengiriman_usulan.updated_at AS tanggal_dikirim, 
                        pengiriman_usulan.catatan'
                    )
                    ->join('cabang_dinas', 'usulan.cabang_dinas_id = cabang_dinas.id', 'left') // ✅ Tambahkan relasi ke cabang_dinas
                    ->join('pengiriman_usulan', 'usulan.nomor_usulan = pengiriman_usulan.nomor_usulan', 'left') // ✅ Tambahkan relasi ke pengiriman_usulan
                    ->where('pengiriman_usulan.status_usulan_cabdin', $status)
                    ->orderBy('pengiriman_usulan.updated_at', 'ASC');

        if (!empty($cabangDinasIds)) {
            $query->whereIn('usulan.cabang_dinas_id', $cabangDinasIds);
        }

        return $query->paginate($perPage, $paginationGroup);
    }
    Kode penggantinya dibawah ini:
    */
    public function getUsulanByStatus($status, $cabangDinasIds = null, $perPage = 10, $paginationGroup = 'page_status03')
    {
        $query = $this->select('
                        usulan.*, 
                        cabang_dinas.nama_cabang, 
                        pu.dokumen_rekomendasi, 
                        pu.status_usulan_cabdin, 
                        pu.operator, 
                        pu.no_hp, 
                        pu.updated_at AS tanggal_dikirim, 
                        pu.catatan
                    ')
                    ->join(
                        'cabang_dinas',
                        'usulan.cabang_dinas_id = cabang_dinas.id',
                        'left'
                    )
                    ->join(
                        '(SELECT p1.*
                          FROM pengiriman_usulan p1
                          INNER JOIN (
                              SELECT nomor_usulan, MAX(id) AS max_id
                              FROM pengiriman_usulan
                              GROUP BY nomor_usulan
                          ) p2 ON p1.id = p2.max_id
                        ) pu',
                        'usulan.nomor_usulan = pu.nomor_usulan',
                        'left'
                    )
                    ->where('pu.status_usulan_cabdin', $status)
                    ->orderBy('pu.updated_at', 'ASC');

        if (!empty($cabangDinasIds)) {
            $query->whereIn('usulan.cabang_dinas_id', $cabangDinasIds);
        }

        return $query->paginate($perPage, $paginationGroup);
    }


    /**
     * ✅ Mendapatkan daftar usulan yang sudah diverifikasi dengan pagination
     */
    /*kode ini kita ubah karena ada bug doble nama usulan walaupun di databse normal
    public function getUsulanWithDokumenPaginated($statuses, $cabangDinasIds = null, $perPage = 10, $paginationGroup = 'page_status04')
    {
        $query = $this->select('
                        usulan.*, 
                        cabang_dinas.nama_cabang, 
                        pengiriman_usulan.dokumen_rekomendasi, 
                        pengiriman_usulan.status_usulan_cabdin, 
                        pengiriman_usulan.operator, 
                        pengiriman_usulan.no_hp, 
                        pengiriman_usulan.updated_at AS tanggal_dikirim, 
                        pengiriman_usulan.catatan'
                    )
                    ->join('cabang_dinas', 'usulan.cabang_dinas_id = cabang_dinas.id', 'left') // ✅ Tambahkan kembali relasi ke cabang_dinas
                    ->join('pengiriman_usulan', 'pengiriman_usulan.nomor_usulan = usulan.nomor_usulan', 'left') // ✅ Pastikan join ke pengiriman_usulan
                    ->whereIn('pengiriman_usulan.status_usulan_cabdin', $statuses)
                    ->orderBy('pengiriman_usulan.updated_at', 'DESC');

        if (!empty($cabangDinasIds)) {
            $query->whereIn('usulan.cabang_dinas_id', $cabangDinasIds);
        }

        return $query->paginate($perPage, $paginationGroup);
    }
    Kode yang baru dibawah ini:
    */
    public function getUsulanWithDokumenPaginated($statuses, $cabangDinasIds = null, $perPage = 10, $paginationGroup = 'page_status04')
    {
        $query = $this->select('
                        usulan.*, 
                        cabang_dinas.nama_cabang, 
                        pu.dokumen_rekomendasi, 
                        pu.status_usulan_cabdin, 
                        pu.operator, 
                        pu.no_hp, 
                        pu.updated_at AS tanggal_dikirim, 
                        pu.catatan
                    ')
                    ->join(
                        'cabang_dinas',
                        'usulan.cabang_dinas_id = cabang_dinas.id',
                        'left'
                    )
                    ->join(
                        '(SELECT p1.*
                          FROM pengiriman_usulan p1
                          INNER JOIN (
                              SELECT nomor_usulan, MAX(id) AS max_id
                              FROM pengiriman_usulan
                              GROUP BY nomor_usulan
                          ) p2 ON p1.id = p2.max_id
                        ) pu',
                        'usulan.nomor_usulan = pu.nomor_usulan',
                        'left'
                    )
                    ->whereIn('pu.status_usulan_cabdin', $statuses)
                    ->orderBy('pu.updated_at', 'DESC');

        if (!empty($cabangDinasIds)) {
            $query->whereIn('usulan.cabang_dinas_id', $cabangDinasIds);
        }

        return $query->paginate($perPage, $paginationGroup);
    }

}
