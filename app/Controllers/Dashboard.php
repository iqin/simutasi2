<?php

namespace App\Controllers;

use App\Models\UsulanModel;
use App\Models\OperatorCabangDinasModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $usulanModel = new UsulanModel();
        $tahunSaatIni = date('Y');
        $tahunTerpilih = $this->request->getGet('tahun') ?? 'semua';

        // Ambil daftar tahun dari tabel usulan
        $tahunList = $usulanModel->select("DISTINCT YEAR(created_at) as tahun")
            ->orderBy("tahun", "DESC")
            ->findAll();
        $availableYears = array_column($tahunList, 'tahun');

        // ==================== FILTER BERDASARKAN ROLE ====================
        $role = session()->get('role');
        $userId = session()->get('id');
        $cabangDinasIds = [];

        if ($role === 'operator' || $role === 'dinas') {
            $operatorModel = new OperatorCabangDinasModel();
            $cabangRows = $operatorModel->where('user_id', $userId)->findAll();
            $cabangDinasIds = array_column($cabangRows, 'cabang_dinas_id');
            if (empty($cabangDinasIds)) {
                $cabangDinasIds = [0]; // agar tidak muncul data
            }
        }

        $db = \Config\Database::connect();

        // Subquery untuk pengiriman terbaru
        $latestPengirimanSub = "
            SELECT p1.*
            FROM pengiriman_usulan p1
            INNER JOIN (
                SELECT nomor_usulan, MAX(id) AS max_id
                FROM pengiriman_usulan
                GROUP BY nomor_usulan
            ) p2 ON p1.id = p2.max_id
        ";

        // Kondisi filter cabang
        $cabangCondition = '';
        $cabangBind = [];
        if (!empty($cabangDinasIds)) {
            $cabangCondition = "AND u.cabang_dinas_id IN (" . implode(',', array_fill(0, count($cabangDinasIds), '?')) . ")";
            $cabangBind = $cabangDinasIds;
        }

        // Kondisi filter tahun
        $tahunCondition = '';
        $tahunBind = [];
        if ($tahunTerpilih !== 'semua' && in_array($tahunTerpilih, $availableYears)) {
            $tahunCondition = "AND u.created_at >= ? AND u.created_at < ?";
            $tahunBind = [$tahunTerpilih . '-01-01', ($tahunTerpilih + 1) . '-01-01'];
        }

        // Gabungkan semua parameter binding
        $bindParams = array_merge($cabangBind, $tahunBind);
        $whereClause = "WHERE 1=1 $cabangCondition $tahunCondition";

        // ==================== QUERY STATISTIK UTAMA ====================
        $query = $db->query("
            SELECT 
                SUM(CASE WHEN lp.status_usulan_cabdin = 'Terkirim' THEN 1 ELSE 0 END) AS total_terkirim,
                SUM(CASE WHEN lp.status_usulan_cabdin = 'Lengkap' THEN 1 ELSE 0 END) AS total_lengkap,
                SUM(CASE WHEN lp.status_usulan_cabdin = 'TdkLengkap' THEN 1 ELSE 0 END) AS total_tdk_lengkap,
                SUM(CASE WHEN lp.status_telaah = 'Disetujui' THEN 1 ELSE 0 END) AS telaah_disetujui,
                SUM(CASE WHEN lp.status_telaah = 'Ditolak' THEN 1 ELSE 0 END) AS telaah_ditolak
            FROM ($latestPengirimanSub) lp
            INNER JOIN usulan u ON u.nomor_usulan = lp.nomor_usulan
            $whereClause
        ", $bindParams);

        $result = $query->getRowArray();

        // ==================== USULAN STATUS 01 (BELUM DIKIRIM) ====================
        $usulanBelumDikirimQuery = $usulanModel->where('status', '01');
        if (!empty($cabangDinasIds)) {
            $usulanBelumDikirimQuery->whereIn('cabang_dinas_id', $cabangDinasIds);
        }
        if ($tahunTerpilih !== 'semua' && in_array($tahunTerpilih, $availableYears)) {
            $usulanBelumDikirimQuery->where('YEAR(created_at)', $tahunTerpilih);
        }
        $usulanBelumDikirim = $usulanBelumDikirimQuery->countAllResults();

        // ==================== TOTAL USULAN (CARD PERTAMA) ====================
        $totalUsulanQuery = $usulanModel->select('id');
        if (!empty($cabangDinasIds)) {
            $totalUsulanQuery->whereIn('cabang_dinas_id', $cabangDinasIds);
        }
        if ($tahunTerpilih !== 'semua' && in_array($tahunTerpilih, $availableYears)) {
            $totalUsulanQuery->where('YEAR(created_at)', $tahunTerpilih);
        }
        $totalUsulanFiltered = $totalUsulanQuery->countAllResults();

        // Hitung turunan
        $totalVerifDinas = ($result['total_lengkap'] ?? 0) + ($result['total_tdk_lengkap'] ?? 0);
        $totalTelaahKabid = ($result['telaah_disetujui'] ?? 0) + ($result['telaah_ditolak'] ?? 0);
        $totalUsulanCabdin = ($result['total_terkirim'] ?? 0) + $usulanBelumDikirim;

        // ==================== REKOMENDASI KADIS ====================
        $queryRekom = $db->query("
            SELECT 
                SUM(CASE WHEN u.id_rekomkadis IS NOT NULL THEN 1 ELSE 0 END) AS rekom_kadis_ada,
                SUM(CASE WHEN u.id_rekomkadis IS NULL THEN 1 ELSE 0 END) AS rekom_kadis_belum
            FROM (
                SELECT lp.nomor_usulan
                FROM ($latestPengirimanSub) lp
                WHERE lp.status_telaah = 'Disetujui'
            ) lp2
            INNER JOIN usulan u ON u.nomor_usulan = lp2.nomor_usulan
            $whereClause
        ", $bindParams);

        $rekomResult = $queryRekom->getRowArray();
        $RekomKadisAda = $rekomResult['rekom_kadis_ada'] ?? 0;
        $RekomKadisBelum = $rekomResult['rekom_kadis_belum'] ?? 0;
        $rekomKadis = $RekomKadisAda + $RekomKadisBelum;

        // ==================== BKPSDM ====================
        $queryBKPSDM = $db->query("
            SELECT 
                SUM(CASE WHEN u.id_rekomkadis IS NOT NULL AND u.kirimbkpsdm IS NOT NULL THEN 1 ELSE 0 END) AS kirim_bka_sudah,
                SUM(CASE WHEN u.id_rekomkadis IS NOT NULL AND u.kirimbkpsdm IS NULL THEN 1 ELSE 0 END) AS kirim_bka_belum
            FROM usulan u
            $whereClause
        ", $bindParams);

        $bkpsdmResult = $queryBKPSDM->getRowArray();
        $KirimBKASudah = $bkpsdmResult['kirim_bka_sudah'] ?? 0;
        $KirimBKABelum = $bkpsdmResult['kirim_bka_belum'] ?? 0;
        $dikirimBKPSDM = $KirimBKASudah + $KirimBKABelum;

        // ==================== SK MUTASI ====================
        $querySK = $db->query("
            SELECT 
                SUM(CASE WHEN sk.jenis_mutasi = 'Nota Dinas' THEN 1 ELSE 0 END) AS nota_dinas,
                SUM(CASE WHEN sk.jenis_mutasi = 'SK Mutasi' THEN 1 ELSE 0 END) AS sk_mutasi
            FROM usulan u
            LEFT JOIN sk_mutasi sk ON u.nomor_usulan = sk.nomor_usulan
            $whereClause
        ", $bindParams);

        $skResult = $querySK->getRowArray();
        $NotaDinas   = $skResult['nota_dinas'] ?? 0;
        $SKMutasi    = $skResult['sk_mutasi'] ?? 0;
        $terbitSK    = $NotaDinas + $SKMutasi;

        // ==================== DATA GRAFIK PER BULAN ====================
        if ($tahunTerpilih === 'semua') {
            // Mode semua tahun: tampilkan per bulan dengan series per tahun
            $cabangFilterAll = '';
            $bindAll = [];
            if (!empty($cabangDinasIds)) {
                $cabangFilterAll = "WHERE u.cabang_dinas_id IN (" . implode(',', array_fill(0, count($cabangDinasIds), '?')) . ")";
                $bindAll = $cabangDinasIds;
            }
            $queryBulanAll = $db->query("
                SELECT YEAR(u.created_at) as tahun, MONTH(u.created_at) as bulan, COUNT(u.id) as total
                FROM usulan u
                $cabangFilterAll
                GROUP BY YEAR(u.created_at), MONTH(u.created_at)
                ORDER BY tahun, bulan
            ", $bindAll);
            $rows = $queryBulanAll->getResultArray();

            // Kelompokkan data per tahun
            $dataPerTahun = [];
            foreach ($rows as $row) {
                $tahun = $row['tahun'];
                $bulan = $row['bulan'];
                $total = (int)$row['total'];
                if (!isset($dataPerTahun[$tahun])) {
                    $dataPerTahun[$tahun] = array_fill(0, 12, 0);
                }
                $dataPerTahun[$tahun][$bulan - 1] = $total;
            }

            // Siapkan datasets untuk chart
            $datasets = [];
            $warna = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69', '#858796', '#3a3b45', '#2c3e50', '#fd7e14'];
            $i = 0;
            foreach ($dataPerTahun as $tahun => $dataBulan) {
                $datasets[] = [
                    'label' => (string)$tahun,
                    'data' => $dataBulan,
                    'backgroundColor' => $warna[$i % count($warna)],
                    'borderColor' => $warna[$i % count($warna)],
                    'borderWidth' => 1
                ];
                $i++;
            }
        } else {
            // Mode tahun tertentu: tampilkan satu series untuk tahun tersebut
            $queryBulan = $db->query("
                SELECT MONTH(u.created_at) as bulan, COUNT(u.id) as total
                FROM usulan u
                $whereClause
                GROUP BY MONTH(u.created_at)
                ORDER BY MONTH(u.created_at)
            ", $bindParams);
            $usulan_per_bulan = array_fill(0, 12, 0);
            foreach ($queryBulan->getResultArray() as $row) {
                $usulan_per_bulan[$row['bulan'] - 1] = (int) $row['total'];
            }
            $datasets = [
                [
                    'label' => 'Jumlah Usulan',
                    'data' => $usulan_per_bulan,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ]
            ];
        }

        $chartData = [
            'labels' => ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"],
            'datasets' => $datasets
        ];

        // ==================== PIE CHART TOTAL ====================
        $queryPie = $db->query("
            SELECT 
                SUM(CASE WHEN lp.status_telaah = 'Disetujui' THEN 1 ELSE 0 END) as disetujui,
                SUM(CASE WHEN lp.status_telaah = 'Ditolak' THEN 1 ELSE 0 END) as ditolak,
                SUM(CASE WHEN lp.status_telaah IS NULL THEN 1 ELSE 0 END) as dalam_proses
            FROM ($latestPengirimanSub) lp
            INNER JOIN usulan u ON u.nomor_usulan = lp.nomor_usulan
            $whereClause
        ", $bindParams);
        $pieData = $queryPie->getRowArray();

        // ==================== PIE CHART PER TAHUN (UNTUK TOOLTIP) ====================
        $cabangFilterPie = '';
        $bindPie = [];
        if (!empty($cabangDinasIds)) {
            $cabangFilterPie = "WHERE u.cabang_dinas_id IN (" . implode(',', array_fill(0, count($cabangDinasIds), '?')) . ")";
            $bindPie = $cabangDinasIds;
        }
        $queryPiePerTahun = $db->query("
            SELECT 
                YEAR(u.created_at) as tahun,
                SUM(CASE WHEN lp.status_telaah = 'Disetujui' THEN 1 ELSE 0 END) as disetujui,
                SUM(CASE WHEN lp.status_telaah = 'Ditolak' THEN 1 ELSE 0 END) as ditolak,
                SUM(CASE WHEN lp.status_telaah IS NULL THEN 1 ELSE 0 END) as dalam_proses
            FROM ($latestPengirimanSub) lp
            INNER JOIN usulan u ON u.nomor_usulan = lp.nomor_usulan
            $cabangFilterPie
            GROUP BY YEAR(u.created_at)
            ORDER BY tahun
        ", $bindPie);
        $piePerTahun = $queryPiePerTahun->getResultArray();

        $statusPerTahun = [];
        foreach ($piePerTahun as $row) {
            $statusPerTahun[$row['tahun']] = [
                'dalam_proses' => (int)$row['dalam_proses'],
                'disetujui' => (int)$row['disetujui'],
                'ditolak' => (int)$row['ditolak']
            ];
        }

        // ==================== USULAN TERBARU ====================
        $latestUsulanQuery = $usulanModel
            ->select('nomor_usulan, guru_nama, sekolah_asal, sekolah_tujuan, status')
            ->orderBy('created_at', 'DESC')
            ->limit(10);

        if (!empty($cabangDinasIds)) {
            $latestUsulanQuery->whereIn('cabang_dinas_id', $cabangDinasIds);
        }
        if ($tahunTerpilih !== 'semua' && in_array($tahunTerpilih, $availableYears)) {
            $latestUsulanQuery->where('YEAR(created_at)', $tahunTerpilih);
        }
        $latestUsulan = $latestUsulanQuery->findAll();

        // ==================== DATA UNTUK VIEW ====================
        $data = [
            'total_usulan_filtered' => $totalUsulanFiltered,
            'usulan_belum_dikirim'   => $usulanBelumDikirim,
            'total_usulan_cabdin'    => $totalUsulanCabdin,
            'tahun_saat_ini'         => $tahunSaatIni,
            'total_terkirim'         => $result['total_terkirim'] ?? 0,
            'total_lengkap'          => $result['total_lengkap'] ?? 0,
            'total_tdk_lengkap'      => $result['total_tdk_lengkap'] ?? 0,
            'total_verif_dinas'      => $totalVerifDinas,
            'telaah_disetujui'       => $result['telaah_disetujui'] ?? 0,
            'telaah_ditolak'         => $result['telaah_ditolak'] ?? 0,
            'total_telaah_kabid'     => $totalTelaahKabid,
            'rekom_kadis'            => $rekomKadis,
            'rekom_kadis_ada'        => $RekomKadisAda,
            'rekom_kadis_belum'      => $RekomKadisBelum,
            'dikirim_bkpsdm'         => $dikirimBKPSDM,
            'kirim_bka_sudah'        => $KirimBKASudah,
            'kirim_bka_belum'        => $KirimBKABelum,
            'terbit_sk'              => $terbitSK,
            'nota_dinas'             => $NotaDinas,
            'sk_mutasi'              => $SKMutasi,
            'availableYears'         => $availableYears,
            'tahunTerpilih'          => $tahunTerpilih,
            'chart_labels_bulan'     => ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"],
            'chart_labels_pie'       => ["Dalam Proses", "Disetujui", "Ditolak"],
            'chartData'              => $chartData,
            'chart_data_pie'         => [
                (int) ($pieData['dalam_proses'] ?? 0),
                (int) ($pieData['disetujui'] ?? 0),
                (int) ($pieData['ditolak'] ?? 0)
            ],
            'status_per_tahun'       => $statusPerTahun,
            'latest_usulan'          => $latestUsulan,
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
                "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
                "Jul", "Agu", "Sep", "Okt", "Nov", "Des"
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
            ->limit(10)
            ->findAll();

        return $this->response->setJSON($latestUsulan);
    }



}
