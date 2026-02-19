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
     * Mendapatkan usulan berdasarkan status dan cabang dinas.
     *
     * @param string $status
     * @param string|null $cabangDinasId
     * @return array
     */
    public function getUsulanByStatus($status, $cabangDinasId = null, $limit = 10, $offset = 0)
    {
        $query = $this->where('status', $status);

        if ($cabangDinasId) {
            $query->where('cabang_dinas_id', $cabangDinasId); // Filter berdasarkan cabang dinas
        }

        return $query->orderBy('created_at', 'DESC')->findAll($limit, $offset);
    }
    
    /* Kode ini diubah karena ada bug duplikasi data walaupun databsenya normal

      public function getUsulanWithDokumenPaginated($status, $cabangDinasId = null, $perPage = 10)
      {
          $query = $this->select('usulan.*, pengiriman_usulan.dokumen_rekomendasi, pengiriman_usulan.status_usulan_cabdin, pengiriman_usulan.catatan')
              ->join('pengiriman_usulan', 'pengiriman_usulan.nomor_usulan = usulan.nomor_usulan', 'left')
              ->where('usulan.status', $status);

          if ($cabangDinasId) {
              $query->where('usulan.cabang_dinas_id', $cabangDinasId);
          }

          return $query->paginate($perPage, 'page_status02'); // Menggunakan pagination bawaan CI4
      }
  Berikut kode penggantinya:
  */
    public function getUsulanWithDokumenPaginated($status, $cabangDinasId = null, $perPage = 10)
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

      if ($cabangDinasId) {
        $query->where('usulan.cabang_dinas_id', $cabangDinasId);
      }

      return $query->paginate($perPage, 'page_status02');
    }


    public function countUsulanByStatus($status, $cabangDinasId = null)
    {
        $query = $this->where('status', $status);

        if ($cabangDinasId) {
            $query->where('cabang_dinas_id', $cabangDinasId);
        }

        return $query->countAllResults();
    }

  /*Kode ini juga diubah karena sempat bug double usulan walaupun di databse normal
  
    public function countUsulanWithDokumen($status, $cabangDinasId = null)
    {
        $query = $this->db->table($this->table)
            ->join('pengiriman_usulan', 'pengiriman_usulan.nomor_usulan = usulan.nomor_usulan', 'left')
            ->where('usulan.status', $status);

        if ($cabangDinasId) {
            $query->where('usulan.cabang_dinas_id', $cabangDinasId);
        }

        return $query->countAllResults();
    }
    
    Berikut kode penggantinya:
    */
  	public function countUsulanWithDokumen($status, $cabangDinasId = null)
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

        if ($cabangDinasId) {
            $query->where('usulan.cabang_dinas_id', $cabangDinasId);
        }

        return $query->countAllResults();
    }

    
}
