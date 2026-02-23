<?php

namespace App\Models;

use CodeIgniter\Model;

class FilterCabdinUsulanModel extends Model
{
    protected $table = 'usulan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_usulan',
        'guru_nama',
        'guru_nip',
        'sekolah_asal',
        'status',
        'cabang_dinas_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Mendapatkan usulan berdasarkan status dan cabang dinas dengan pagination.
     *
     * @param string $status
     * @param string|null $cabangDinasId
     * @param int $perPage
     * @param string $search
     * @return array
     */
    public function getUsulanByStatus($status, $cabangDinasId = null, $perPage = 10, $search = '')
    {
        $query = $this->where('status', $status);

        if (!empty($cabangDinasId)) {
            $query->where('cabang_dinas_id', $cabangDinasId);
        }

        if (!empty($search)) {
            $query->like('guru_nama', $search);
        }

        return $query->orderBy('created_at', 'DESC')->paginate($perPage, 'page_status01');
    }

    /**
     * Mendapatkan usulan dengan dokumen pengiriman terbaru (status tertentu) dengan pagination.
     *
     * @param string $status
     * @param string|null $cabangDinasId
     * @param int $perPage
     * @param string $search
     * @return array
     */
    public function getUsulanWithDokumenPaginated($status, $cabangDinasId = null, $perPage = 10, $search = '', $statusCabdin = '')
    {
        $query = $this->select(
                'usulan.*,
                pu.dokumen_rekomendasi,
                pu.status_usulan_cabdin,
                pu.catatan'
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
                'pu.nomor_usulan = usulan.nomor_usulan',
                'left'
            )
            ->where('usulan.status', $status);

        if (!empty($cabangDinasId)) {
            $query->where('usulan.cabang_dinas_id', $cabangDinasId);
        }

        if (!empty($search)) {
            $query->like('usulan.guru_nama', $search);
        }

        if (!empty($statusCabdin)) {
            $query->where('pu.status_usulan_cabdin', $statusCabdin);
        }

        return $query->orderBy('usulan.created_at', 'DESC')->paginate($perPage, 'page_status02');
    }


    /**
     * Menghitung jumlah usulan berdasarkan status dan cabang dinas (dengan filter pencarian).
     *
     * @param string $status
     * @param string|null $cabangDinasId
     * @param string $search
     * @return int
     */
    public function countUsulanByStatus($status, $cabangDinasId = null, $search = '')
    {
        $query = $this->where('status', $status);

        if (!empty($cabangDinasId)) {
            $query->where('cabang_dinas_id', $cabangDinasId);
        }

        if (!empty($search)) {
            $query->like('guru_nama', $search);
        }

        return $query->countAllResults();
    }

    /**
     * Menghitung jumlah usulan dengan dokumen pengiriman (status tertentu) dengan filter pencarian.
     *
     * @param string $status
     * @param string|null $cabangDinasId
     * @param string $search
     * @return int
     */
    public function countUsulanWithDokumen($status, $cabangDinasId = null, $search = '')
    {
        $query = $this->db->table('usulan')
            ->join(
                '(SELECT nomor_usulan, MAX(id) AS max_id
                  FROM pengiriman_usulan
                  GROUP BY nomor_usulan
                ) pu',
                'pu.nomor_usulan = usulan.nomor_usulan',
                'left'
            )
            ->where('usulan.status', $status);

        if (!empty($cabangDinasId)) {
            $query->where('usulan.cabang_dinas_id', $cabangDinasId);
        }

        if (!empty($search)) {
            $query->like('usulan.guru_nama', $search);
        }

        return $query->countAllResults();
    }
}