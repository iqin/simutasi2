<?php

namespace App\Controllers;

use App\Models\UsulanModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $usulanModel = new UsulanModel();
        $tahunSaatIni = date('Y');

        // Data Statistik
        $totalUsulan = $usulanModel->countAll(); // Semua data tanpa filter tahun
        $usulanBelumDikirim = $usulanModel->where('status', 01)->where("YEAR(created_at)", $tahunSaatIni)->countAllResults();

         // Query untuk mendapatkan data terkirim dari pengiriman_usulan yang ada di usulan dengan status 02
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT 
                SUM(CASE WHEN pengiriman_usulan.status_usulan_cabdin = 'Terkirim' THEN 1 ELSE 0 END) AS total_terkirim,
                SUM(CASE WHEN pengiriman_usulan.status_usulan_cabdin = 'Lengkap' THEN 1 ELSE 0 END) AS total_lengkap,
                SUM(CASE WHEN pengiriman_usulan.status_usulan_cabdin = 'TdkLengkap' THEN 1 ELSE 0 END) AS total_tdk_lengkap,
                SUM(CASE WHEN pengiriman_usulan.status_telaah = 'Disetujui' THEN 1 ELSE 0 END) AS telaah_disetujui,
                SUM(CASE WHEN pengiriman_usulan.status_telaah = 'Ditolak' THEN 1 ELSE 0 END) AS telaah_ditolak
            FROM pengiriman_usulan
            JOIN usulan ON usulan.nomor_usulan = pengiriman_usulan.nomor_usulan
            WHERE YEAR(usulan.created_at) = ?
        ", [$tahunSaatIni]);

        $result = $query->getRowArray();
        $totalVerifDinas = ($result['total_lengkap'] ?? 0) + ($result['total_tdk_lengkap'] ?? 0);
        $totalTelaahKabid = ($result['telaah_disetujui'] ?? 0) + ($result['telaah_ditolak'] ?? 0);
        $totalUsulanCabdin = ($result['total_terkirim'] ?? 0) + ($usulanBelumDikirim ?? 0);
        
        $queryRekom = $db->query("
                SELECT 
                    SUM(CASE WHEN pengiriman_usulan.status_telaah = 'Disetujui' AND usulan.id_rekomkadis IS NOT NULL THEN 1 ELSE 0 END) AS rekom_kadis_ada,
                    SUM(CASE WHEN pengiriman_usulan.status_telaah = 'Disetujui' AND usulan.id_rekomkadis IS NULL THEN 1 ELSE 0 END) AS rekom_kadis_belum
                FROM pengiriman_usulan
                JOIN usulan ON usulan.nomor_usulan = pengiriman_usulan.nomor_usulan
                WHERE YEAR(usulan.created_at) = ?
            ", [$tahunSaatIni]);

         $rekomResult = $queryRekom->getRowArray();
         $RekomKadisAda = $rekomResult['rekom_kadis_ada'] ?? 0;
         $RekomKadisBelum = $rekomResult['rekom_kadis_belum'] ?? 0;
         $rekomKadis = $RekomKadisAda + $RekomKadisBelum;

        $queryBKPSDM = $db->query("
                SELECT 
                    SUM(CASE WHEN usulan.id_rekomkadis IS NOT NULL AND usulan.kirimbkpsdm IS NOT NULL THEN 1 ELSE 0 END) AS kirim_bka_sudah,
                    SUM(CASE WHEN usulan.id_rekomkadis IS NOT NULL AND usulan.kirimbkpsdm IS NULL THEN 1 ELSE 0 END) AS kirim_bka_belum
                FROM usulan
                WHERE YEAR(usulan.created_at) = ?
            ", [$tahunSaatIni]);

            $bkpsdmResult = $queryBKPSDM->getRowArray();

            // Menghitung total usulan yang dikirim ke BKPSDM
            $KirimBKASudah = $bkpsdmResult['kirim_bka_sudah'] ?? 0;
            $KirimBKABelum = $bkpsdmResult['kirim_bka_belum'] ?? 0;
            $dikirimBKPSDM = $KirimBKASudah + $KirimBKABelum;

        $querySK = $db->query("
                SELECT 
                    SUM(CASE WHEN usulan.kirimbkpsdm IS NOT NULL AND sk_mutasi.jenis_mutasi = 'Nota Dinas' THEN 1 ELSE 0 END) AS nota_dinas,
                    SUM(CASE WHEN usulan.kirimbkpsdm IS NOT NULL AND sk_mutasi.jenis_mutasi = 'SK Mutasi' THEN 1 ELSE 0 END) AS sk_mutasi,
                    SUM(CASE WHEN usulan.kirimbkpsdm IS NOT NULL AND sk_mutasi.nomor_usulan IS NULL THEN 1 ELSE 0 END) AS belum_terbit
                FROM usulan
                LEFT JOIN sk_mutasi ON usulan.nomor_usulan = sk_mutasi.nomor_usulan
                WHERE YEAR(usulan.created_at) = ?
            ", [$tahunSaatIni]);

            $skResult = $querySK->getRowArray();
            $NotaDinas = $skResult['nota_dinas'] ?? 0;
            $SKMutasi = $skResult['sk_mutasi'] ?? 0;
            $BelumTerbit = $skResult['belum_terbit'] ?? 0;
            $terbitSK = $NotaDinas + $SKMutasi + $BelumTerbit;


        $data = [
            'total_usulan' => $totalUsulan,
            'usulan_belum_dikirim' => $usulanBelumDikirim,
            'total_usulan_cabdin' => $totalUsulanCabdin,
            'rekom_kadis' => $rekomKadis,
            'tahun_saat_ini' => $tahunSaatIni,
            'total_terkirim' => $result['total_terkirim'] ?? 0,
            'total_lengkap' => $result['total_lengkap'] ?? 0,
            'total_tdk_lengkap' => $result['total_tdk_lengkap'] ?? 0,
            'total_verif_dinas' => $totalVerifDinas,
            'telaah_disetujui' => $result['telaah_disetujui'] ?? 0,
            'telaah_ditolak' => $result['telaah_ditolak'] ?? 0,
            'total_telaah_kabid' => $totalTelaahKabid,
            'rekom_kadis' => $rekomKadis,
            'rekom_kadis_ada' => $RekomKadisAda,
            'rekom_kadis_belum' => $RekomKadisBelum,
            'dikirim_bkpsdm' => $dikirimBKPSDM,
            'kirim_bka_sudah' => $KirimBKASudah,
            'kirim_bka_belum' => $KirimBKABelum,
            'terbit_sk' => $terbitSK,
            'nota_dinas' => $NotaDinas,
            'sk_mutasi' => $SKMutasi,
            'belum_terbit' => $BelumTerbit
            ];

        return view('dashboard', $data);
    }

    public function getDetailUsulanDikirim()
    {
        $tahun = $this->request->getGet('year');

        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT 
                COUNT(*) AS total_terkirim,
                SUM(CASE WHEN pengiriman_usulan.status_usulan_cabdin = 'Lengkap' THEN 1 ELSE 0 END) AS total_lengkap,
                SUM(CASE WHEN pengiriman_usulan.status_usulan_cabdin = 'TdkLengkap' THEN 1 ELSE 0 END) AS total_tdk_lengkap
            FROM pengiriman_usulan
            JOIN usulan ON usulan.nomor_usulan = pengiriman_usulan.nomor_usulan
            WHERE YEAR(usulan.created_at) = ?
        ", [$tahun]);

        $result = $query->getRowArray();

        return $this->response->setJSON($result);
    }


    public function getChartData()
    {
        $tahun = $this->request->getGet('year');
        $db = \Config\Database::connect();

        // Query jumlah usulan per bulan
        $queryUsulan = $db->query("
            SELECT MONTH(created_at) as bulan, COUNT(usulan.id) as total
            FROM usulan
            WHERE YEAR(created_at) = ?
            GROUP BY MONTH(created_at)
            ORDER BY MONTH(created_at)", [$tahun]);

        $usulan_per_bulan = array_fill(0, 12, 0); // Set default 0 untuk semua bulan

        foreach ($queryUsulan->getResultArray() as $row) {
            $usulan_per_bulan[$row['bulan'] - 1] = (int) $row['total'];
        }

        // Query data untuk Pie Chart dari relasi usulan & pengiriman_usulan
        $queryPie = $db->query("
            SELECT 
                SUM(CASE WHEN pengiriman_usulan.status_telaah = 'Disetujui' THEN 1 ELSE 0 END) as disetujui,
                SUM(CASE WHEN pengiriman_usulan.status_telaah = 'Ditolak' THEN 1 ELSE 0 END) as ditolak,
                SUM(CASE WHEN pengiriman_usulan.status_telaah IS NULL THEN 1 ELSE 0 END) as dalam_proses
            FROM usulan
            LEFT JOIN pengiriman_usulan ON usulan.nomor_usulan = pengiriman_usulan.nomor_usulan
            WHERE YEAR(usulan.created_at) = ?", [$tahun]);

        $pieData = $queryPie->getRowArray();

        return $this->response->setJSON([
            'labels_bulan' => [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ],
            'usulan_per_bulan' => $usulan_per_bulan,
            'labels_pie' => ["Dalam Proses", "Disetujui", "Ditolak"],
            'data_pie' => [
                (int) $pieData['dalam_proses'],
                (int) $pieData['disetujui'],
                (int) $pieData['ditolak']
            ]
        ]);
    }


    public function getAvailableYears()
    {
        $usulanModel = new UsulanModel();

        // Ambil daftar tahun dari data usulan
        $years = $usulanModel->select("DISTINCT YEAR(created_at) as tahun")
            ->orderBy("tahun", "DESC")
            ->findAll();

        $availableYears = array_map(function ($row) {
            return $row['tahun'];
        }, $years);

        return $this->response->setJSON($availableYears);
    }

    public function getLatestUsulan()
    {
        $usulanModel = new UsulanModel();

        $latestUsulan = $usulanModel->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        return $this->response->setJSON($latestUsulan);
    }



}
