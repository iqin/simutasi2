<?php

namespace App\Controllers;

use App\Models\UsulanModel;
use App\Models\SkMutasiModel;
use CodeIgniter\Controller;

class SkMutasiController extends Controller
{
    protected $usulanModel;
    protected $skMutasiModel;
    
    public function __construct()
    {
        // Cek apakah user adalah operator, jika iya redirect ke dashboard
        if (session()->get('role') == 'operator') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak.')->send();
            exit();
        }
    
        // Inisialisasi model
        $this->usulanModel = new UsulanModel();
        $this->skMutasiModel = new SkMutasiModel();
    }

    public function index()
    {
        $role   = session()->get('role');
        $userId = session()->get('id');

        $db = \Config\Database::connect();
        $cabangDinasIds = [];

        if ($role === 'dinas') {
            $cabangDinasQuery = $db->table('operator_cabang_dinas')
                ->select('cabang_dinas_id')
                ->where('user_id', $userId)
                ->get()->getResultArray();
            $cabangDinasIds = array_column($cabangDinasQuery, 'cabang_dinas_id');
        }

        // per halaman
        $perPageKiri = (int)($this->request->getGet('perPageKiri') ?: 10);
        $perPageSk   = (int)($this->request->getGet('perPageSk')   ?: 25); // <- sinkron dg nama select di view

        // ====== TABEL KIRI: usulan status 05/06 ======
        $queryKiri = $this->usulanModel
            ->select('
                usulan.nomor_usulan,
                usulan.guru_nama,
                usulan.guru_nip,
                usulan.sekolah_asal,
                usulan.sekolah_tujuan,
                usulan.jenis_usulan,
                usulan.status,
                usulan.created_at
            ')
            ->whereIn('usulan.status', ['05','06'])
            ->orderBy('usulan.created_at', 'ASC');

        // hitung apakah nomor_usulan tsb sudah punya ND (ND / PND)
        $ndRows = $db->table('sk_mutasi')
            ->select('nomor_usulan')
            ->whereIn('jenis_mutasi', ['Nota Dinas', 'Nota Dinas Perpanjangan'])
            ->groupBy('nomor_usulan')
            ->get()->getResultArray();

        $hasNdByUsulan = [];
        foreach ($ndRows as $r) {
            $hasNdByUsulan[$r['nomor_usulan']] = true;
        }

        $usulanKiri = $queryKiri->paginate($perPageKiri, 'usulanKiri');
        $pagerKiri  = $this->usulanModel->pager;

        // ====== TABEL KANAN: ambil dari sk_mutasi (dokumen yang sudah ada) ======
        $usulanKanan = $this->skMutasiModel
            ->select('
                sk_mutasi.*,
                usulan.guru_nama,
                usulan.guru_nip,
                usulan.sekolah_asal,
                usulan.sekolah_tujuan,
                usulan.jenis_usulan
            ')
            ->join('usulan', 'usulan.nomor_usulan = sk_mutasi.nomor_usulan', 'left')
            // TIDAK ada filter usulan.status = 07
            ->orderBy('sk_mutasi.created_at', 'DESC')
            ->paginate($perPageSk, 'usulanKanan');

        $pagerKanan = $this->skMutasiModel->pager;

        return view('skmutasi/index', [
            'usulanKiri'     => $usulanKiri,
            'pagerKiri'      => $pagerKiri,
            'usulanKanan'    => $usulanKanan,
            'pagerKanan'     => $pagerKanan,
            'perPageKiri'    => $perPageKiri,
            'perPageKanan'   => $perPageSk,     // variabel yg dipakai di view
            'hasNdByUsulan'  => $hasNdByUsulan,
        ]);
    }

    public function upload()
    {
        $nomorUsulan  = $this->request->getPost('nomor_usulan');
        $jenisUsulan  = $this->request->getPost('jenis_usulan');     // mutasi_tetap | nota_dinas | perpanjangan_nota_dinas
        $statusUsulan = $this->request->getPost('status_usulan');    // 05 | 06
        $jenisMutasi  = $this->request->getPost('jenis_mutasi');     // SK Mutasi | Nota Dinas | Nota Dinas Perpanjangan
        $nomorSK      = $this->request->getPost('nomor_skmutasi');
        $tanggalSK    = $this->request->getPost('tanggal_skmutasi');
        $file         = $this->request->getFile('file_skmutasi');

        // ---------- Validasi file ----------
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }
        if (strtolower($file->getClientMimeType()) !== 'application/pdf') {
            return redirect()->back()->with('error', 'File harus PDF.');
        }
        if ($file->getSize() > 1024 * 1024) { // 1 MB
            return redirect()->back()->with('error', 'Ukuran file maks. 1 MB.');
        }

        // ---------- Normalisasi label dokumen ----------
        // Untuk usulan ND/PND kita paksa label dokumennya agar konsisten
        if ($jenisUsulan === 'perpanjangan_nota_dinas') {
            $jenisMutasi = 'Nota Dinas Perpanjangan';
        } elseif ($jenisUsulan === 'nota_dinas') {
            $jenisMutasi = 'Nota Dinas';
        }

        // ---------- Validasi logika bisnis ----------
        if ($jenisUsulan === 'mutasi_tetap') {
            // Cek apakah untuk usulan ini sudah ada ND (reguler / perpanjangan)
            $sudahAdaNd = $this->skMutasiModel
                ->where('nomor_usulan', $nomorUsulan)
                ->whereIn('jenis_mutasi', ['Nota Dinas', 'Nota Dinas Perpanjangan'])
                ->first();

            if ($statusUsulan === '05') {
                // Pada 05 hanya boleh unggah ND, dan hanya sekali per usulan
                if ($jenisMutasi !== 'Nota Dinas') {
                    return redirect()->back()->with('error', 'Pada status 05 hanya boleh unggah Nota Dinas.');
                }
                if ($sudahAdaNd) {
                    return redirect()->back()->with('error', 'Nota Dinas sudah pernah diunggah untuk usulan ini.');
                }
            } elseif ($statusUsulan === '06') {
                // Pada 06 boleh SK Mutasi. ND juga boleh jika BELUM ada.
                if ($jenisMutasi === 'Nota Dinas' && $sudahAdaNd) {
                    return redirect()->back()->with('error', 'Nota Dinas sudah pernah diunggah untuk usulan ini.');
                }
            } else {
                return redirect()->back()->with('error', 'Status usulan tidak valid untuk Mutasi Tetap.');
            }
        } else {
            // Usulan ND/PND selalu harus dokumen ND (reguler atau perpanjangan)
            if (!in_array($jenisMutasi, ['Nota Dinas', 'Nota Dinas Perpanjangan'], true)) {
                return redirect()->back()->with('error', 'Jenis dokumen harus Nota Dinas.');
            }
        }

        // ---------- Simpan file fisik ----------
        $tanggalFormatted = date('Ymd', strtotime($tanggalSK));
        $fileName   = "{$nomorUsulan}-{$tanggalFormatted}.pdf";
        $uploadPath = WRITEPATH . 'uploads/sk_mutasi';
        if (!is_dir($uploadPath)) {
            @mkdir($uploadPath, 0775, true);
        }
        $file->move($uploadPath, $fileName, true);

        // ---------- UPSERT ke sk_mutasi (per kombinasi nomor_usulan + jenis_mutasi) ----------
        $existing = $this->skMutasiModel
            ->where('nomor_usulan', $nomorUsulan)
            ->where('jenis_mutasi', $jenisMutasi)
            ->first();

        $dataSave = [
            'nomor_usulan'     => $nomorUsulan,
            'jenis_mutasi'     => $jenisMutasi,               // 'SK Mutasi' | 'Nota Dinas' | 'Nota Dinas Perpanjangan'
            'nomor_skmutasi'   => $nomorSK,
            'tanggal_skmutasi' => $tanggalSK,
            'file_skmutasi'    => $fileName,
        ];

        if ($existing) {
            // Hapus file lama jika berbeda
            if (!empty($existing['file_skmutasi']) && $existing['file_skmutasi'] !== $fileName) {
                $oldPath = $uploadPath . '/' . $existing['file_skmutasi'];
                if (is_file($oldPath)) { @unlink($oldPath); }
            }
            $dataSave['updated_at'] = date('Y-m-d H:i:s');
            $this->skMutasiModel->update($existing['id_skmutasi'], $dataSave);
        } else {
            try {
                $dataSave['created_at'] = date('Y-m-d H:i:s');
                $this->skMutasiModel->insert($dataSave);
            } catch (\Throwable $e) {
                if (strpos($e->getMessage(), '1062') !== false) {
                    return redirect()->back()->with(
                        'error',
                        'Gagal menyimpan dokumen: terdeteksi indeks unik pada "nomor_usulan". ' .
                        'Ubah indeks unik tabel sk_mutasi menjadi (nomor_usulan, jenis_mutasi).'
                    );
                }
                throw $e;
            }
        }

        $db = \Config\Database::connect();

        // ---------- RIWAYAT: ND pada Mutasi Tetap (status 05/06) ----------
        // Saat Mutasi Tetap mengunggah ND, catat di history dengan status berjalan (05/06).
        if ($jenisUsulan === 'mutasi_tetap' && $jenisMutasi === 'Nota Dinas') {
            $existNdHist = $db->table('usulan_status_history')
                ->where('nomor_usulan', $nomorUsulan)
                ->where('status', $statusUsulan) // 05 atau 06
                ->where('catatan_history', 'Nota Dinas (dapat diunduh)')
                ->countAllResults();

            if (!$existNdHist) {
                $db->table('usulan_status_history')->insert([
                    'nomor_usulan'    => $nomorUsulan,
                    'status'          => $statusUsulan, // tetap 05/06
                    'catatan_history' => 'Nota Dinas (dapat diunduh)',
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // ---------- Update status usulan + history selesai (07) bila perlu ----------
        $setTo07 = false;
        if ($jenisUsulan === 'mutasi_tetap') {
            // Mutasi Tetap selesai (07) hanya jika upload SK Mutasi
            if ($jenisMutasi === 'SK Mutasi') {
                $setTo07 = true;
            }
        } else {
            // ND reguler / PND → selesai (07)
            $setTo07 = true;
        }

        if ($setTo07) {
            $this->usulanModel->where('nomor_usulan', $nomorUsulan)
                            ->set(['status' => '07'])
                            ->update();

            // Tambah history 07 (hindari duplikasi)
            $already07 = $db->table('usulan_status_history')
                ->where('nomor_usulan', $nomorUsulan)
                ->where('status', '07')
                ->countAllResults();

            if (!$already07) {
                $catatan = match ($jenisMutasi) {
                    'SK Mutasi'               => 'SK Mutasi (dapat diunduh)',
                    'Nota Dinas Perpanjangan' => 'Nota Dinas Perpanjangan (dapat diunduh)',
                    default                   => 'Nota Dinas (dapat diunduh)',
                };
                $db->table('usulan_status_history')->insert([
                    'nomor_usulan'    => $nomorUsulan,
                    'status'          => '07',
                    'catatan_history' => $catatan,
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);
            }
        }

        return redirect()->to('/skmutasi')->with('success', "$jenisMutasi berhasil diunggah.");
    }



    public function update()
    {
        $idSkMutasi = $this->request->getPost('id_skmutasi');
        $nomorUsulan = $this->request->getPost('nomor_usulan');
        $jenisMutasi = $this->request->getPost('jenis_mutasi'); // SK Mutasi / Nota Dinas
        $nomorSK = $this->request->getPost('nomor_skmutasi');
        $tanggalSK = $this->request->getPost('tanggal_skmutasi');
        $file = $this->request->getFile('file_skmutasi');

        // **Ambil data lama dari database**
        $existingData = $this->skMutasiModel->where('id_skmutasi', $idSkMutasi)->first();
        if (!$existingData) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // **Format nama file baru**
        $tanggalFormatted = date('Ymd', strtotime($tanggalSK));
        $fileName = "{$nomorUsulan}-{$tanggalFormatted}.pdf"; // Format baru

        // **Direktori penyimpanan**
        $uploadPath = WRITEPATH . 'uploads/sk_mutasi';
        $fileUpdated = false;

        // **Jika ada file baru yang diunggah**
        if ($file && $file->isValid() && $file->getMimeType() === 'application/pdf') {
            if ($file->getSize() > 1024 * 1024) { // Maksimal 1MB
                return redirect()->back()->with('error', 'Ukuran file tidak boleh lebih dari 1 MB.');
            }

            // **Hapus file lama jika ada**
            $oldFilePath = $uploadPath . '/' . $existingData['file_skmutasi'];
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }

            // **Pindahkan file baru**
            $file->move($uploadPath, $fileName);
            $fileUpdated = true;
        } else {
            // **Gunakan nama file lama jika tidak diunggah file baru**
            $fileName = $existingData['file_skmutasi'];
        }

        // **Update data di database**
        $updateData = [
            'jenis_mutasi' => $jenisMutasi,
            'nomor_skmutasi' => $nomorSK,
            'tanggal_skmutasi' => $tanggalSK,
            'file_skmutasi' => $fileName,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->skMutasiModel->update($idSkMutasi, $updateData);

        // **Update catatan history berdasarkan jenis mutasi & apakah file diperbarui**
        if ($fileUpdated) {
            $catatanHistory = ($jenisMutasi === 'SK Mutasi') 
                ? 'SK Mutasi diperbaharui (dapat diunduh)' 
                : 'Nota Dinas diperbaharui (dapat diunduh)';
        } else {
            $catatanHistory = ($jenisMutasi === 'SK Mutasi') 
                ? 'SK Mutasi diperbaharui (tanpa perubahan file)' 
                : 'Nota Dinas diperbaharui (tanpa perubahan file)';
        }

       // **Cek apakah nomor_usulan dengan status = 07 sudah ada di tabel usulan_status_history**
        $db = \Config\Database::connect();
        $existingHistory = $db->table('usulan_status_history')
                              ->where('nomor_usulan', $nomorUsulan)
                              ->where('status', '07')
                              ->get()
                              ->getRowArray();

        if ($existingHistory) {
            // **Jika sudah ada, lakukan UPDATE**
            $db->table('usulan_status_history')
               ->where('nomor_usulan', $nomorUsulan)
               ->where('status', '07')
               ->update(['catatan_history' => $catatanHistory]);
        } else {
            // **Jika belum ada, lakukan INSERT**
            $db->table('usulan_status_history')->insert([
                'nomor_usulan' => $nomorUsulan,
                'status' => '07',
                'catatan_history' => $catatanHistory
            ]);
        }

        return redirect()->to('/skmutasi')->with('success', 'Data SK Mutasi berhasil diperbarui.');
    }

    // SkMutasiController.php

    private function respondJson(bool $ok, string $message, array $extra = [])
    {
        return $this->response
            ->setContentType('application/json')
            ->setStatusCode(200)
            ->setJSON(array_merge(['success' => $ok, 'message' => $message], $extra));
    }

    public function delete($idSkMutasi)
    {
        $db = \Config\Database::connect();

        // 1) Ambil baris dokumen
        $row = $this->skMutasiModel
            ->where('id_skmutasi', $idSkMutasi)
            ->first();

        if (!$row) {
            return $this->respondJson(false, 'Data dokumen tidak ditemukan.');
        }

        $nomorUsulan = $row['nomor_usulan'];

        // 2) Hapus file fisik (jika ada)
        $filePath = WRITEPATH . 'uploads/sk_mutasi/' . $row['file_skmutasi'];
        if (is_file($filePath)) {
            @unlink($filePath);
        }

        // 3) Hapus baris dokumen
        $this->skMutasiModel->delete($idSkMutasi);

        // 4) Hitung sisa dokumen untuk nomor_usulan ini
        $remaining = $this->skMutasiModel
            ->where('nomor_usulan', $nomorUsulan)
            ->findAll();

        $hasSk   = false;
        $hasNd   = false;
        $hasPnd  = false;

        foreach ($remaining as $r) {
            if ($r['jenis_mutasi'] === 'SK Mutasi') $hasSk  = true;
            if ($r['jenis_mutasi'] === 'Nota Dinas') $hasNd  = true;
            if ($r['jenis_mutasi'] === 'Nota Dinas Perpanjangan') $hasPnd = true;
        }

        // 5) Tentukan status baru berdasarkan jenis usulan
        $usulan = $this->usulanModel->where('nomor_usulan', $nomorUsulan)->first();
        $jenisUsulan = $usulan['jenis_usulan'] ?? null;

        // default: jika tidak ada dokumen sisa -> kembali ke 06
        $newStatus = '06';

        if ($jenisUsulan === 'mutasi_tetap') {
            // Mutasi Tetap selesai (07) hanya kalau ada SK Mutasi
            $newStatus = $hasSk ? '07' : '06';
        } else {
            // ND / Perpanjangan ND selesai (07) bila masih ada dokumen ND/PND
            $newStatus = (!empty($remaining)) ? '07' : '06';
        }

        // 6) Update status usulan
        $this->usulanModel->where('nomor_usulan', $nomorUsulan)
            ->set(['status' => $newStatus])
            ->update();

        // 7) Sinkronkan riwayat status (07)
        if ($newStatus === '07') {
            // Pilih catatan yang paling “kuat”
            $catatan = 'Nota Dinas (dapat diunduh)';
            if ($hasSk) {
                $catatan = 'SK Mutasi (dapat diunduh)';
            } elseif ($hasPnd) {
                $catatan = 'Nota Dinas Perpanjangan (dapat diunduh)';
            }

            $exist07 = $db->table('usulan_status_history')
                ->where('nomor_usulan', $nomorUsulan)
                ->where('status', '07')
                ->get()->getRowArray();

            if ($exist07) {
                $db->table('usulan_status_history')
                    ->where('nomor_usulan', $nomorUsulan)
                    ->where('status', '07')
                    ->update([
                        'catatan_history' => $catatan,
                        'updated_at'      => date('Y-m-d H:i:s'),
                    ]);
            } else {
                $db->table('usulan_status_history')->insert([
                    'nomor_usulan'    => $nomorUsulan,
                    'status'          => '07',
                    'catatan_history' => $catatan,
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);
            }
        } else {
            // Tidak selesai lagi -> hapus history 07
            $db->table('usulan_status_history')
                ->where('nomor_usulan', $nomorUsulan)
                ->where('status', '07')
                ->delete();
        }

        return $this->respondJson(true, 'Berkas berhasil dihapus.');
    }



}
