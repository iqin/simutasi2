<?php

namespace App\Controllers;

use App\Models\TelaahBerkasModel;

class TelaahController extends BaseController
{
    protected $telaahBerkasModel;
    protected $db;
    public function __construct()
    {
        // Cek apakah user adalah operator, jika iya redirect ke dashboard
        if (session()->get('role') == 'operator') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak.')->send();
            exit();
        }
    
        // Inisialisasi model
        $this->telaahBerkasModel = new TelaahBerkasModel();
        $this->db = \Config\Database::connect();  // ✅ Sekarang properti resmi, bukan dynamic

    }

    public function index()
    {
        $role = session()->get('role');
        $userId = session()->get('id'); // Ambil ID pengguna dari session
        $perPage = $this->request->getVar('perPage') ?: 50; // default 50

        // Ambil parameter pencarian
        $searchMenunggu = $this->request->getGet('search_menunggu');
        $searchDitelaah = $this->request->getGet('search_ditelaah');
        $statusFilter = $this->request->getGet('status_filter');


        $db = \Config\Database::connect();
        $cabangDinasIds = [];

        // Jika role adalah "dinas", ambil daftar cabang dinas yang menjadi hak aksesnya
        if ($role === 'dinas') {
            $cabangDinasQuery = $db->table('operator_cabang_dinas')
                ->select('cabang_dinas_id')
                ->where('user_id', $userId)
                ->get()
                ->getResultArray();

            $cabangDinasIds = array_column($cabangDinasQuery, 'cabang_dinas_id');

            if (empty($cabangDinasIds)) {
                $cabangDinasIds = [0]; // Default untuk menghindari error jika tidak ada hak akses
            }
        }

        // **1️⃣ Query Menunggu Telaah**
        $queryMenunggu = $this->telaahBerkasModel
            ->select('pengiriman_usulan.nomor_usulan, usulan.jenis_usulan, usulan.guru_nama, usulan.guru_nip, usulan.guru_nik, 
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

            if (!empty($searchMenunggu)) {
                $queryMenunggu->like('usulan.guru_nama', $searchMenunggu);
            }

        // **Filter hanya untuk Role DINAS** (kabid & admin melihat semua data)
        if ($role === 'dinas') {
            $queryMenunggu->whereIn('usulan.cabang_dinas_id', $cabangDinasIds);
        }

        $usulanMenunggu = $queryMenunggu->paginate($perPage, 'usulanMenunggu');
        $pagerMenunggu = $this->telaahBerkasModel->pager;

        // **2️⃣ Query Sudah Ditelaah**
        $queryDitelaah = $this->telaahBerkasModel
            ->select('pengiriman_usulan.nomor_usulan, usulan.jenis_usulan, usulan.guru_nama, usulan.guru_nip, usulan.guru_nik, 
                    usulan.sekolah_asal, usulan.sekolah_tujuan, usulan.alasan, usulan.google_drive_link, usulan.created_at, 
                    cabang_dinas.id as cabang_dinas_id, cabang_dinas.nama_cabang, 
                    pengiriman_usulan.status_telaah, pengiriman_usulan.updated_at_telaah, 
                    pengiriman_usulan.dokumen_rekomendasi, pengiriman_usulan.operator, 
                    pengiriman_usulan.no_hp, pengiriman_usulan.status_usulan_cabdin, pengiriman_usulan.created_at AS tanggal_dikirim, 
                    pengiriman_usulan.updated_at AS tanggal_update, pengiriman_usulan.catatan_telaah')
            ->join('usulan', 'pengiriman_usulan.nomor_usulan = usulan.nomor_usulan', 'inner')
            ->join('cabang_dinas', 'usulan.cabang_dinas_id = cabang_dinas.id', 'left')
            ->where('pengiriman_usulan.status_telaah !=', NULL)
            ->orderBy('pengiriman_usulan.updated_at_telaah', 'DESC');

            if (!empty($searchDitelaah)) {
                $queryDitelaah->like('usulan.guru_nama', $searchDitelaah);
            }

            if (!empty($statusFilter)) {
                $queryDitelaah->where('pengiriman_usulan.status_telaah', $statusFilter);
            }

        // **Filter hanya untuk Role DINAS** (kabid & admin melihat semua data)
        if ($role === 'dinas') {
            $queryDitelaah->whereIn('usulan.cabang_dinas_id', $cabangDinasIds);
        }

        $usulanDitelaah = $queryDitelaah->paginate($perPage, 'usulanDitelaah');
        $pagerDitelaah = $this->telaahBerkasModel->pager;

        // **3️⃣ Data untuk View**
        //$readonly = ($role === 'dinas'); // Role dinas hanya bisa melihat (readonly)
        $isKadisdik = ($userId == 161); //disesuaikan dengan id user kadisdik
        $readonly  = ($role === 'dinas' || $isKadisdik);
      
        $data = [
            'usulanMenunggu' => $usulanMenunggu,
            'pagerMenunggu' => $pagerMenunggu,
            'usulanDitelaah' => $usulanDitelaah,
            'pagerDitelaah' => $pagerDitelaah,
            'perPage' => $perPage,
            'searchMenunggu' => $searchMenunggu,
            'searchDitelaah' => $searchDitelaah, 
            'statusFilter' => $statusFilter,           
            'readonly' => $readonly, // ✅ Role dinas readonly, lainnya tidak
        ];

        return view('telaah/index', $data);
    }
    public function update()
    {
        $request = $this->request->getJSON(true);

        // Validasi data request
        if (!isset($request['nomor_usulan'], $request['status_telaah'])) {
            return $this->response->setJSON(['error' => 'Data tidak lengkap.'])->setStatusCode(400);
        }

        $nomorUsulan = $request['nomor_usulan'];
        $statusTelaah = $request['status_telaah'];
        $catatanTelaah = $request['catatan_telaah'] ?? '';

            // Cek Duplikasi..
        $existing = $this->db->table('pengiriman_usulan')
            ->select('status_telaah')
            ->where('nomor_usulan', $nomorUsulan)
            ->get()
            ->getRow();

        if ($existing && $existing->status_telaah !== null) {
            return $this->response->setJSON([
                'error' => 'Usulan ini sudah pernah ditelaah. Silakan refresh halaman.'
            ])->setStatusCode(400);
        }

        $statusUsulan = ($statusTelaah === 'Disetujui') ? '04' : '02';

        // Tentukan catatan history berdasarkan status telaah
        $catatanHistory = ($statusTelaah === 'Disetujui')
            ? "Telaah Usulan oleh Kepala Bidang GTK (Disetujui). " . $catatanTelaah
            : "Telaah Usulan oleh Kepala Bidang GTK (Ditolak). " . $catatanTelaah;

        try {
            // Mulai transaksi
            $this->db->transStart();

            // Update tabel pengiriman_usulan
            $this->db->table('pengiriman_usulan')
                ->where('nomor_usulan', $nomorUsulan)
                ->update([
                    'status_telaah' => $statusTelaah,
                    'catatan_telaah' => $catatanTelaah,
                    'updated_at_telaah' => date('Y-m-d H:i:s'),
                ]);

            // Update tabel usulan
            $this->db->table('usulan')
                ->where('nomor_usulan', $nomorUsulan)
                ->update(['status' => $statusUsulan]);

            // Tambahkan ke tabel usulan_status_history
            $this->db->table('usulan_status_history')
                ->insert([
                    'nomor_usulan' => $nomorUsulan,
                    'status' => $statusUsulan,
                    'catatan_history' => $catatanHistory,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            // Akhiri transaksi
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi gagal.');
            }
            // 🔔 Kirim notifikasi email
            $usulanModel = new \App\Models\UsulanModel();
            $usulan = $usulanModel->where('nomor_usulan', $nomorUsulan)->first();
            if ($usulan && !empty($usulan['email'])) {
                helper('phpmailer');
                $jenisLabel = match ($usulan['jenis_usulan']) {
                    'mutasi_tetap' => 'Mutasi',
                    'nota_dinas'   => 'Nota Dinas',
                    'perpanjangan_nota_dinas' => 'Perpanjangan Nota Dinas',
                    default => $usulan['jenis_usulan']
                };

                $catatanTampil = !empty($catatanTelaah) ? htmlspecialchars($catatanTelaah) : '-';
                $link = base_url('lacak-mutasi');

                if ($statusTelaah === 'Disetujui') {
                    $pesanUtama = "Usulan <strong>{$jenisLabel}</strong> Anda dengan nomor <strong>{$nomorUsulan}</strong> telah <strong>Disetujui</strong> oleh Kepala Bidang GTK.<br><br><strong>Catatan:</strong> {$catatanTampil}";
                    $statusText = "04 - Usulan {$jenisLabel} Anda Disetujui Kabid GTK dan saat ini sedang menunggu Rekomendasi Kepala Dinas";
                    $subject = "SIMUTASI 04 - Usulan {$jenisLabel} Anda Disetujui Kabid GTK";
                } else {
                    $pesanUtama = "Usulan <strong>{$jenisLabel}</strong> Anda dengan nomor <strong>{$nomorUsulan}</strong> <strong>Ditolak</strong> oleh Kepala Bidang GTK.<br><br><strong>Catatan:</strong> {$catatanTampil}<br><br>Silakan hubungi operator Cabang Dinas untuk informasi lebih lanjut.";
                    $statusText = "02 - Usulan {$jenisLabel} Anda Ditolak Kabid GTK.";
                    $subject = "SIMUTASI 02 - Usulan {$jenisLabel} Anda Ditolak Kabid GTK";
                }

                $message = getEmailTemplate(
                    $usulan['guru_nama'],
                    $jenisLabel,
                    $nomorUsulan,
                    $pesanUtama,
                    $statusText,
                    $link
                );

                send_email_phpmailer($usulan['email'], $subject, $message);
            }

            return $this->response->setJSON(['message' => 'Status telaah berhasil diperbarui.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
        }
    }

    public function batalkan()
    {
        log_message('debug', '[BATALKAN] Fungsi dipanggil');

        $json = $this->request->getJSON();
        log_message('debug', '[BATALKAN] Request JSON: ' . json_encode($json));

        $nomor_usulan = $json->nomor_usulan ?? null;
        log_message('debug', '[BATALKAN] Nomor usulan diterima: ' . var_export($nomor_usulan, true));

        if (!$nomor_usulan) {
            log_message('error', '[BATALKAN] Nomor usulan kosong!');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nomor usulan tidak ditemukan.'
            ]);
        }

        try {
            $db = \Config\Database::connect();

            // Reset status telaah
            $db->table('pengiriman_usulan')
                ->where('nomor_usulan', $nomor_usulan)
                ->update([
                    'status_telaah' => null,
                    'catatan_telaah' => null,
                    'updated_at_telaah' => null,
                    'status_usulan_cabdin' => 'Terkirim'
                ]);
            log_message('debug', '[BATALKAN] Update pengiriman_usulan OK');

            // Update status usulan ke 03
            $db->table('usulan')
                ->where('nomor_usulan', $nomor_usulan)
                ->update(['status' => '03']);
            log_message('debug', '[BATALKAN] Update usulan ke status 03 OK');

            // Hapus riwayat status 04
            $db->table('usulan_status_history')
                ->where('nomor_usulan', $nomor_usulan)
                ->where('status', '04')
                ->delete();
            log_message('debug', '[BATALKAN] Hapus status history 04 OK');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Telaah berhasil dibatalkan.'
            ]);
        } catch (\Exception $e) {
            log_message('error', '[BATALKAN] ERROR: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ]);
        }
    }








}
