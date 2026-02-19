<?php

namespace App\Controllers;

use App\Models\RekomKadisModel;
use App\Models\UsulanModel;
use App\Models\UsulanStatusHistoryModel;
use CodeIgniter\Controller;

class SematkanController extends Controller
{
    protected $rekomModel;
    protected $usulanModel;
    protected $historyModel;

    public function __construct()
    {
        $this->rekomModel = new RekomKadisModel();
        $this->usulanModel = new UsulanModel();
        $this->historyModel = new UsulanStatusHistoryModel();
    }

    // Menampilkan halaman sematkan
    public function index($idUsulan)
    {
        $role = session()->get('role');
        if ($role === 'operator') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $usulan = $this->usulanModel->find($idUsulan);
        $perPage = $this->request->getGet('perPage') ?? 50; // Jumlah data per halaman
        $keyword = $this->request->getGet('search') ?? ''; // Kata kunci pencarian

        // Pastikan usulan ditemukan
        if (!$usulan) {
            return redirect()->to('/rekomkadis')->with('error', 'Usulan tidak ditemukan');
        }

        // Query untuk daftar rekomendasi dengan filter pencarian
        $query = $this->rekomModel;
        if (!empty($keyword)) {
            $query = $query->groupStart()
                           ->like('nomor_rekomkadis', $keyword)
                           ->orLike('perihal_rekomkadis', $keyword)
                           ->groupEnd();
        }

        // Pagination dengan filter pencarian
        $daftarRekom = $query->paginate($perPage, 'rekom_pagination');
        $pager = $this->rekomModel->pager; // Ambil pager untuk tampilan navigasi halaman

        $data = [
            'usulanTerpilih' => $usulan,
            'daftarRekom' => $daftarRekom,
            'pager' => $pager, // Kirim objek pager ke view untuk navigasi halaman
            'perPage' => $perPage,
            'keyword' => $keyword // Kirim nilai pencarian ke view agar tetap tampil
        ];

        return view('rekomkadis/sematkan', $data);
    }



    // Proses penyematan rekomendasi
    public function proses()
    {
        $usulanModel = new UsulanModel();
        $statusHistoryModel = new UsulanStatusHistoryModel();

        // Ambil data dari request JSON
        $input = $this->request->getJSON(true);
        $idUsulan = $input['idUsulan'];
        $idRekom = $input['idRekomkadis'];

        // Ambil nomor usulan
        $usulanData = $usulanModel->find($idUsulan);
        if (!$usulanData) {
            return $this->response->setJSON(['success' => false, 'message' => 'Usulan tidak ditemukan.']);
        }
        $nomorUsulan = $usulanData['nomor_usulan'];

        // Update usulan dengan kondisi where
        $usulanModel->where('id', $idUsulan)->set([
            'id_rekomkadis' => $idRekom,
            'status' => '05',
        ])->update();

        // Simpan riwayat status
        $statusHistoryModel->insert([
            'nomor_usulan' => $nomorUsulan,
            'status' => '05',
            'catatan_history' => 'Penerbitan surat rekomendasi Kepala Dinas',
        ]);


        // Kirim response JSON agar Fetch API tidak error
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Rekomendasi berhasil disematkan!'
        ]);
    }





}
