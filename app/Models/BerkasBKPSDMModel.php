<?php

namespace App\Models;

use CodeIgniter\Model;

class BerkasBKPSDMModel extends Model
{
    protected $table = 'usulan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_usulan', 'guru_nama', 'sekolah_asal', 'sekolah_tujuan',
        'cabang_dinas_id', 'status', 'kirimbkpsdm', 'tglkirimbkpsdm'
    ];

    public function getSiapKirim($role, $userId, $perPage)
    {
        $db = \Config\Database::connect();
        $cabangDinasIds = [];

        if ($role === 'dinas') {
            $cabangDinasIds = array_column(
                $db->table('operator_cabang_dinas')
                    ->select('cabang_dinas_id')
                    ->where('user_id', $userId)
                    ->get()
                    ->getResultArray(),
                'cabang_dinas_id'
            );

            if (empty($cabangDinasIds)) {
                $cabangDinasIds = [0];
            }
        }

        $query = $this->select('usulan.*, cabang_dinas.nama_cabang, 
            pengiriman_usulan.dokumen_rekomendasi, pengiriman_usulan.operator, 
            pengiriman_usulan.no_hp, pengiriman_usulan.updated_at AS tanggal_dikirim, 
            pengiriman_usulan.status_telaah, pengiriman_usulan.updated_at_telaah, 
            pengiriman_usulan.catatan_telaah, rekom_kadis.nomor_rekomkadis, 
            rekom_kadis.tanggal_rekomkadis, rekom_kadis.file_rekomkadis')
            ->join('pengiriman_usulan', 'usulan.nomor_usulan = pengiriman_usulan.nomor_usulan', 'left')
            ->join('cabang_dinas', 'usulan.cabang_dinas_id = cabang_dinas.id', 'left')
            ->join('rekom_kadis', 'usulan.id_rekomkadis = rekom_kadis.id', 'left')
            ->where('usulan.status', '05')
            ->where('usulan.kirimbkpsdm IS NULL')
            ->where('usulan.jenis_usulan', 'mutasi_tetap');

        if ($role === 'dinas') {
            $query->whereIn('usulan.cabang_dinas_id', $cabangDinasIds);
        }

        return $query->orderBy('usulan.created_at', 'DESC')->paginate($perPage, 'usulanSiapKirim');
    }

    public function getSudahDikirim($role, $userId, $perPage)
    {
        $db = \Config\Database::connect();
        $cabangDinasIds = [];

        if ($role === 'dinas') {
            $cabangDinasIds = array_column(
                $db->table('operator_cabang_dinas')
                    ->select('cabang_dinas_id')
                    ->where('user_id', $userId)
                    ->get()
                    ->getResultArray(),
                'cabang_dinas_id'
            );

            if (empty($cabangDinasIds)) {
                $cabangDinasIds = [0];
            }
        }

        $query = $this->select('usulan.*, cabang_dinas.nama_cabang, 
            pengiriman_usulan.dokumen_rekomendasi, pengiriman_usulan.operator, 
            pengiriman_usulan.no_hp, pengiriman_usulan.updated_at AS tanggal_dikirim, 
            pengiriman_usulan.status_telaah, pengiriman_usulan.updated_at_telaah, 
            pengiriman_usulan.catatan_telaah, rekom_kadis.nomor_rekomkadis, 
            rekom_kadis.tanggal_rekomkadis, rekom_kadis.file_rekomkadis')
            ->join('pengiriman_usulan', 'usulan.nomor_usulan = pengiriman_usulan.nomor_usulan', 'left')
            ->join('cabang_dinas', 'usulan.cabang_dinas_id = cabang_dinas.id', 'left')
            ->join('rekom_kadis', 'usulan.id_rekomkadis = rekom_kadis.id', 'left')
            ->where('usulan.status', '06')
            ->where('usulan.kirimbkpsdm', '1')
            ->where('usulan.jenis_usulan', 'mutasi_tetap');

        if ($role === 'dinas') {
            $query->whereIn('usulan.cabang_dinas_id', $cabangDinasIds);
        }

        return $query->orderBy('usulan.tglkirimbkpsdm', 'DESC')->paginate($perPage, 'usulanSudahDikirim');
    }
}
