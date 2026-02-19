<?php

namespace App\Controllers;

use App\Models\RekomkadisModel;
use App\Models\UsulanDiterimaModel;
use App\Models\UsulanStatusHistoryModel;

class RekomkadisController extends BaseController
{
    protected $rekomkadisModel;
    protected $usulanDiterimaModel;
    protected $statusHistoryModel;

    public function __construct()
    {
        // Cek apakah user adalah operator, jika iya redirect ke dashboard
        if (session()->get('role') == 'operator') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak.')->send();
            exit();
        }
    
        // Inisialisasi model
        $this->rekomkadisModel = new RekomkadisModel();
        $this->usulanDiterimaModel = new UsulanDiterimaModel();
        $this->statusHistoryModel = new UsulanStatusHistoryModel();        
    }
    
    public function index()
    {
        $role = session()->get('role');
        $userId = session()->get('id');
        $UsulanperPage = $this->request->getGet('perPageUsulan') ?? 25; // Default tampil 25 data untuk tabel kanan
        $BelumTerkaitperPage = $this->request->getGet('perPageBelumTerkait') ?? 25; // Default tampil 25 data untuk tabel kiri
        $keyword = $this->request->getGet('searchUsulan') ?? ''; // Kata kunci pencarian
    
        $db = \Config\Database::connect();
        $cabangDinasIds = [];
    
        if ($role === 'dinas') {
            // Ambil daftar cabang dinas yang menjadi hak akses pengguna role dinas
            $cabangDinasIds = $db->table('operator_cabang_dinas')
                ->select('cabang_dinas_id')
                ->where('user_id', $userId)
                ->get()
                ->getResultArray();
            $cabangDinasIds = array_column($cabangDinasIds, 'cabang_dinas_id');
    
            if (empty($cabangDinasIds)) {
                $cabangDinasIds = [0]; // Nilai default untuk menghindari error jika tidak ada cabang dinas
            }
        }
    
        // **1️⃣ Pengambilan Data untuk Tabel 05.3: Usulan (Belum Terbit Rekom) dengan Pagination**
        $queryBelumTerkait = $this->usulanDiterimaModel
            ->where('id_rekomkadis', null)
            ->where('status', '04'); // Tambahkan filter status 04
    
        if ($role === 'dinas') {
            $queryBelumTerkait->whereIn('cabang_dinas_id', $cabangDinasIds);
        }
    
        $usulanBelumTerkait = $queryBelumTerkait->paginate($BelumTerkaitperPage, 'usulan_belum_terkait_pagination');
        $pagerBelumTerkait = $this->usulanDiterimaModel->pager;
    
        // **2️⃣ Pengambilan Data untuk Tabel 05.4: Usulan (Telah Terbit Rekom)**
        $queryUsulanTerkait = $this->usulanDiterimaModel
            ->select('usulan.*, rekom_kadis.nomor_rekomkadis, rekom_kadis.perihal_rekomkadis, rekom_kadis.tanggal_rekomkadis, rekom_kadis.file_rekomkadis')
            ->join('rekom_kadis', 'usulan.id_rekomkadis = rekom_kadis.id', 'left')
            ->where('usulan.id_rekomkadis IS NOT NULL')
            ->orderBy('usulan.id', 'DESC');
    
        if ($role === 'dinas') {
            $queryUsulanTerkait->whereIn('usulan.cabang_dinas_id', $cabangDinasIds);
        }
    
        $usulanTerkait = $queryUsulanTerkait->paginate($UsulanperPage, 'usulan_terkait_pagination');
        $pagerUsulan = $this->usulanDiterimaModel->pager;
    
        // **3️⃣ Kirim Data ke View**
        $data = [
            'usulanBelumTerkait' => $usulanBelumTerkait,
            'pagerBelumTerkait' => $pagerBelumTerkait,
            'usulanTerkait' => $usulanTerkait,
            'pagerUsulan' => $pagerUsulan,
            'perPageUsulan' => $UsulanperPage,
            'perPageBelumTerkait' => $BelumTerkaitperPage,
            'keywordUsulan' => $keyword
        ];
    
        return view('rekomkadis/index', $data);
    }

    public function uploadRekom()
    {
        $idUsulan = $this->request->getPost('id_usulan');
        $fileRekomkadis = $this->request->getFile('file_rekomkadis');

        // **Validasi input**
        if (!$idUsulan || !$fileRekomkadis->isValid()) {
            return redirect()->to('/rekomkadis')->with('error', 'Data tidak lengkap atau file tidak valid.');
        }

        // **Validasi file PDF & ukuran maksimum 10MB**
        if ($fileRekomkadis->getExtension() !== 'pdf' || $fileRekomkadis->getSize() > 10485760) {
            return redirect()->to('/rekomkadis')->with('error', 'File harus berformat PDF dan tidak lebih dari 10 MB.');
        }

        // **Cek apakah usulan sudah memiliki rekomendasi**
        $usulan = $this->usulanDiterimaModel->find($idUsulan);
        if (!$usulan || $usulan['id_rekomkadis'] !== null) {
            return redirect()->to('/rekomkadis')->with('error', 'Usulan sudah memiliki rekomendasi.');
        }

        // **Generate nama file baru menggunakan nomor usulan**
        $nomorUsulan = str_replace('/', '-', $usulan['nomor_usulan']); // Ganti '/' dengan '-' agar aman sebagai nama file
        $fileName = $nomorUsulan . '-rekomkadis.pdf';

        try {
            // **Pindahkan file ke folder upload**
            $fileRekomkadis->move(WRITEPATH . 'uploads/rekom_kadis', $fileName);

            // **Simpan rekomendasi di tabel `rekom_kadis`**
            $idRekom = $this->rekomkadisModel->insert([
                'nomor_rekomkadis' => 'REKOM-' . time(),
                'tanggal_rekomkadis' => date('Y-m-d'),
                'perihal_rekomkadis' => 'Perihal Otomatis',
                'file_rekomkadis' => $fileName,
            ], true); // Mengembalikan ID yang baru dibuat

            // **Update usulan dengan rekomendasi yang baru dibuat**
            $this->usulanDiterimaModel->update($idUsulan, [
                'id_rekomkadis' => $idRekom,
                'status' => '05',
            ]);

            // **Simpan riwayat perubahan status**
            $this->statusHistoryModel->insert([
                'nomor_usulan' => $usulan['nomor_usulan'],
                'status' => '05',
                'catatan_history' => 'Penerbitan surat rekomendasi Kepala Dinas',
            ]);

            return redirect()->to('/rekomkadis')->with('success', 'Rekomendasi berhasil diunggah.');
        } catch (\Exception $e) {
            return redirect()->to('/rekomkadis')->with('error', 'Gagal mengunggah file: ' . $e->getMessage());
        }
    }
    public function hapusRekom($nomorUsulan)
    {
        // **Cari usulan berdasarkan nomor usulan**
        $usulan = $this->usulanDiterimaModel->where('nomor_usulan', $nomorUsulan)->first();
    
        if (!$usulan || !$usulan['id_rekomkadis']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Usulan tidak memiliki rekomendasi atau tidak ditemukan.'
            ]);
        }
    
        $idRekomkadis = $usulan['id_rekomkadis'];
        $rekom = $this->rekomkadisModel->find($idRekomkadis);
    
        // **Hapus file rekomendasi jika ada**
        if ($rekom && !empty($rekom['file_rekomkadis'])) {
            $filePath = WRITEPATH . 'uploads/rekom_kadis/' . $rekom['file_rekomkadis'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    
        // **Hapus rekomendasi dari tabel rekom_kadis**
        $this->rekomkadisModel->delete($idRekomkadis);
    
        // **Reset id_rekomkadis di tabel usulan**
        $this->usulanDiterimaModel->update($usulan['id'], [
            'id_rekomkadis' => null,
            'status' => '04',
        ]);
    
        // **Simpan riwayat perubahan status**
        $this->statusHistoryModel->insert([
            'nomor_usulan' => $nomorUsulan,
            'status' => '04',
            'catatan_history' => 'Surat rekomendasi Kepala Dinas dibatalkan.',
        ]);
    
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Rekomendasi berhasil dihapus.'
        ]);
    }
    

}
