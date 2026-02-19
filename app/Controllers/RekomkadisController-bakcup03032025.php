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
        $RekomperPage = 9; // Pagination khusus untuk tabel 05.2 (Daftar Surat Rekomendasi Kadis)
        $UsulanperPage = $this->request->getGet('perPageUsulan') ?? 25; // Default tampil 25 data
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

        // **1️⃣ Pengambilan Data untuk Tabel 05.2: Daftar Surat Rekomendasi Kadis**
        $daftarSurat = $this->rekomkadisModel
            ->select('rekom_kadis.*, COUNT(usulan.id) as terkait')
            ->join('usulan', 'usulan.id_rekomkadis = rekom_kadis.id', 'left')
            ->groupBy('rekom_kadis.id')
            ->orderBy('rekom_kadis.id', 'DESC')
            ->paginate($RekomperPage, 'rekom_surat_pagination');

        $pagerSurat = $this->rekomkadisModel->pager;

        // **2️⃣ Pengambilan Data untuk Tabel 05.3: Usulan (Belum Terbit Rekom)**
        $queryBelumTerkait = $this->usulanDiterimaModel
            ->where('id_rekomkadis', null)
            ->where('status', '04'); // Tambahkan filter status 04

        if ($role === 'dinas') {
            $queryBelumTerkait->whereIn('cabang_dinas_id', $cabangDinasIds);
        }

        $usulanBelumTerkait = $queryBelumTerkait->findAll();

        // **3️⃣ Pengambilan Data untuk Tabel 05.4: Usulan (Telah Terbit Rekom)**
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

        // **4️⃣ Kirim Data ke View**
        $data = [
            'daftarSurat' => $daftarSurat,
            'pagerSurat' => $pagerSurat,
            'usulanBelumTerkait' => $usulanBelumTerkait,
            'usulanTerkait' => $usulanTerkait,
            'pagerUsulan' => $pagerUsulan,
            'perPageUsulan' => $UsulanperPage,
            'keywordUsulan' => $keyword
        ];

        return view('rekomkadis/index', $data);
    }

    public function store()
    {
        $nomorRekomkadis = $this->request->getPost('nomor_rekomkadis');
        $tanggalRekomkadis = $this->request->getPost('tanggal_rekomkadis');
        $perihalRekomkadis = $this->request->getPost('perihal_rekomkadis');
        $fileRekomkadis = $this->request->getFile('file_rekomkadis');

        if (!$fileRekomkadis->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }

        if ($fileRekomkadis->getExtension() !== 'pdf') {
            return redirect()->back()->with('error', 'File yang diunggah harus berformat PDF.');
        }

        if ($fileRekomkadis->getSize() > 10485760) { // 10 MB = 10.485.760 byte
            return redirect()->back()->with('error', 'Ukuran file tidak boleh lebih dari 10 MB.');
        }

        $timestamp = date('Y-m-d_H-i-s'); // Format: Tahun-Bulan-Hari_Jam-Menit-Detik
        $fileName = $timestamp . '-rekom_kadis.pdf';

        try {
            $fileRekomkadis->move(WRITEPATH . 'uploads/rekom_kadis', $fileName);

            $this->rekomkadisModel->insert([
                'nomor_rekomkadis' => $nomorRekomkadis,
                'tanggal_rekomkadis' => $tanggalRekomkadis,
                'perihal_rekomkadis' => $perihalRekomkadis,
                'file_rekomkadis' => $fileName,
            ]);

            return redirect()->to('/rekomkadis')->with('success', 'Surat rekomendasi berhasil ditambahkan.');
        } 
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunggah file: ' . $e->getMessage());
        }
    }


    public function delete($id)
    {
        $rekom = $this->rekomkadisModel->find($id);

        if (!$rekom) {
            return redirect()->to('/rekomkadis')->with('error', 'Data tidak ditemukan.');
        }
        $filePath = WRITEPATH . 'uploads/rekom_kadis/' . $rekom['file_rekomkadis'];
        if (file_exists($filePath)) {
            unlink($filePath); // Hapus file
        }
        $this->rekomkadisModel->delete($id);

        return redirect()->to('/rekomkadis')->with('success', 'Data berhasil dihapus.');
    }

    public function updaterekomkadis($id)
    {
        //log_message('debug', 'ID received in updaterekomkadis: ' . $id);
        $this->validate([
            'nomor_rekomkadis' => 'required',
            'tanggal_rekomkadis' => 'required|valid_date',
            'perihal_rekomkadis' => 'required',
            'file_rekomkadis' => 'permit_empty|uploaded[file_rekomkadis]|mime_in[file_rekomkadis,application/pdf]|max_size[file_rekomkadis,10240]',
        ]);

        $rekom = $this->rekomkadisModel->find($id);

        if (!$rekom) {
            return redirect()->to('/rekomkadis')->with('error', 'Data tidak ditemukan.');
        }

        $fileRekomkadis = $this->request->getFile('file_rekomkadis');
        $fileName = $rekom['file_rekomkadis']; // Gunakan file lama jika tidak ada file baru

        if ($fileRekomkadis && $fileRekomkadis->isValid()) {
            // Hapus file lama jika ada file baru
            $oldFilePath = WRITEPATH . 'uploads/rekom_kadis/' . $fileName;
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }

            $timestamp = date('Y-m-d_H-i-s'); // Format: Tahun-Bulan-Hari_Jam-Menit-Detik
            $fileName = $timestamp . '-rekom_kadis.pdf';

            $fileRekomkadis->move(WRITEPATH . 'uploads/rekom_kadis', $fileName);
        }

        $this->rekomkadisModel->update($id, [
            'nomor_rekomkadis' => $this->request->getPost('nomor_rekomkadis'),
            'tanggal_rekomkadis' => $this->request->getPost('tanggal_rekomkadis'),
            'perihal_rekomkadis' => $this->request->getPost('perihal_rekomkadis'),
            'file_rekomkadis' => $fileName,
        ]);

        return redirect()->to('/rekomkadis')->with('success', 'Data berhasil diperbarui.');
    }

    public function sematkan()
    {
        $idUsulan = $this->request->getPost('id_usulan');
        $idRekom = $this->request->getPost('id_rekomkadis');

        // **Validasi input**
        if (!$idUsulan || !$idRekom) {
            return redirect()->to('/rekomkadis')->with('error', 'Data tidak lengkap.');
        }

        // **Ambil nomor usulan dari database**
        $usulan = $this->usulanDiterimaModel->find($idUsulan);
        if (!$usulan) {
            return redirect()->to('/rekomkadis')->with('error', 'Usulan tidak ditemukan.');
        }

        $nomorUsulan = $usulan['nomor_usulan'];

        // **Update usulan dengan rekomendasi yang dipilih**
        $this->usulanDiterimaModel->update($idUsulan, [
            'id_rekomkadis' => $idRekom,
            'status' => '05',
        ]);

        // **Simpan riwayat status**
        $this->statusHistoryModel->insert([
            'nomor_usulan' => $nomorUsulan,
            'status' => '05',
            'catatan_history' => 'Penerbitan surat rekomendasi Kepala Dinas',
        ]);

        return redirect()->to('/rekomkadis')->with('success', 'Rekomendasi berhasil disematkan!');
    }

    public function batalrekomdis()
    {
        $input = $this->request->getJSON(true);
        $nomorUsulan = $input['nomor_usulan'];

        // **Pastikan nomor usulan valid dan masih berstatus 05**
        $usulan = $this->usulanDiterimaModel->where('nomor_usulan', $nomorUsulan)
                                            ->where('status', '05') // Hanya yang berstatus 05 bisa dibatalkan
                                            ->first();

        if (!$usulan) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Usulan tidak ditemukan atau sudah dibatalkan.'
            ]);
        }

        // **Update usulan: hapus id_rekomkadis dan kembalikan status ke 04**
        $this->usulanDiterimaModel->update($usulan['id'], [
            'id_rekomkadis' => null,
            'status' => '04',
        ]);

        // **Tambahkan riwayat pembatalan rekomendasi**
        $this->statusHistoryModel->insert([
            'nomor_usulan' => $nomorUsulan,
            'status' => '04',
            'catatan_history' => 'Surat rekomendasi Kepala Dinas (Dibatalkan)',
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Rekomendasi berhasil dibatalkan!'
        ]);
    }

}
