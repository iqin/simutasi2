<?php

namespace App\Controllers;

use App\Models\SaranMutasiModel;
use CodeIgniter\Controller;

class SaranController extends Controller
{
    public function index()
    {
        $saranModel = new SaranMutasiModel();
        
        // Ambil jumlah per halaman dari URL (default 10)
        $perPage = $this->request->getVar('per_page') ?: 10;
        
        // Ambil filter status dari URL
        $statusFilter = $this->request->getVar('status');
    
        // Query dasar dengan JOIN ke tabel `usulan`
        $query = $saranModel->select('saran_mutasi.*, usulan.guru_nama')
                            ->join('usulan', 'usulan.nomor_usulan = saran_mutasi.nomor_usulan', 'left')
                            ->orderBy('saran_mutasi.created_at', 'DESC');
    
        // Jika ada filter status, tambahkan ke query
        if (!empty($statusFilter)) {
            $query->where('saran_mutasi.status', $statusFilter);
        }
    
        // Pastikan `paginate` digunakan untuk pagination
        $data = [
            'saran' => $query->paginate($perPage, 'saran'),
            'pager' => $saranModel->pager,  
            'perPage' => $perPage,
            'selectedStatus' => $statusFilter,
        ];
    
        return view('kotak-saran/index', $data);
    }
    

    public function balas($id)
    {
        if (!in_array(session()->get('role'), ['admin', 'kabid'])) {
            return redirect()->to('/kotak-saran')->with('error', 'Anda tidak memiliki izin!');
        }
    
        $saranModel = new SaranMutasiModel();
        $data['saran'] = $saranModel->select('saran_mutasi.*, usulan.guru_nama')
                                    ->join('usulan', 'usulan.nomor_usulan = saran_mutasi.nomor_usulan', 'left')
                                    ->where('saran_mutasi.id', $id)
                                    ->first();
    
        if (!$data['saran']) {
            return redirect()->to('/kotak-saran')->with('error', 'Saran tidak ditemukan!');
        }
    
        return view('kotak-saran/balas', $data);
    }
    

    public function submitBalasan($id)
    {
        if (!in_array(session()->get('role'), ['admin', 'kabid'])) {
            return redirect()->to('/kotak-saran')->with('error', 'Anda tidak memiliki izin untuk membalas!');
        }
    
        $saranModel = new SaranMutasiModel();
        $balasan = esc($this->request->getPost('balasan')); // Mencegah XSS
    
        $saran = $saranModel->where('id', $id)->first();
    
        if (!$saran) {
            return redirect()->to('/kotak-saran')->with('error', 'Saran tidak ditemukan!');
        }
    
        // Simpan balasan
        $saranModel->update($id, [
            'balasan' => $balasan,
            'status' => 'Sudah Dibalas'
        ]);
    
        return redirect()->to('/kotak-saran')->with('success', 'Balasan berhasil dikirim!');
    }

    public function delete($id)
    {
        $saranModel = new SaranMutasiModel();

        // Periksa apakah data ada
        $saran = $saranModel->find($id);
        if (!$saran) {
            return redirect()->to('/kotak-saran')->with('error', 'Saran tidak ditemukan!');
        }

        // Hapus data
        $saranModel->delete($id);

        return redirect()->to('/kotak-saran')->with('success', 'Saran berhasil dihapus!');
    }

}
