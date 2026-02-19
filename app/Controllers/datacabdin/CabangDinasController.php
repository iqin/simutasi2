<?php

namespace App\Controllers\datacabdin;

use App\Controllers\BaseController;
use App\Models\CabangDinasModel;
use App\Models\KabupatenModel;
use App\Models\CabangDinasKabupatenModel;

class CabangDinasController extends BaseController
{
    protected $cabangDinasModel;
    protected $kabupatenModel;
    protected $cabangDinasKabupatenModel;

    public function __construct()
    {
        // Cek apakah user adalah admin, jika bukan redirect ke dashboard
        if (session()->get('role') !== 'admin') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak.')->send();
            exit();
        }
    
        // Inisialisasi model
        $this->cabangDinasModel = new CabangDinasModel();
        $this->kabupatenModel = new KabupatenModel();
        $this->cabangDinasKabupatenModel = new CabangDinasKabupatenModel();
    }

    public function index()
    {
        $perPage = $this->request->getVar('per_page') ?: 10;

        $query = $this->cabangDinasModel->select('cabang_dinas.*, GROUP_CONCAT(kabupaten.nama_kab SEPARATOR ", ") as kabupaten_wilayah')
                                        ->join('cabang_dinas_kabupaten', 'cabang_dinas_kabupaten.cabang_dinas_id = cabang_dinas.id', 'left')
                                        ->join('kabupaten', 'kabupaten.id_kab = cabang_dinas_kabupaten.kabupaten_id', 'left')
                                        ->groupBy('cabang_dinas.id')
                                        ->orderBy('cabang_dinas.id', 'DESC');

        $data = [
            'cabang_dinas' => $query->paginate($perPage, 'cabang_dinas'),
            'pager' => $this->cabangDinasModel->pager,
            'perPage' => $perPage
        ];

        return view('datacabdin/index', $data);
    }

    public function create()
    {
        // Ambil kode_cabang terbesar dari database
        $lastCabang = $this->cabangDinasModel->select('kode_cabang')
                                             ->orderBy('kode_cabang', 'DESC')
                                             ->first();

        $lastNumber = 0;
        if ($lastCabang) {
            preg_match('/CD(\d+)/', $lastCabang['kode_cabang'], $matches);
            $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
        }

        // Generate kode_cabang baru berdasarkan angka terbesar
        $newKodeCabang = 'CD' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        $data = [
            'kabupaten' => $this->kabupatenModel->findAll(), // Mengambil daftar kabupaten
            'newKodeCabang' => $newKodeCabang, // Mengirim kode_cabang otomatis
        ];

        return view('datacabdin/create', $data);
    }

    public function store()
    {
        $cabangDinasId = $this->cabangDinasModel->insert([
            'kode_cabang' => $this->request->getPost('kode_cabang'),
            'nama_cabang' => $this->request->getPost('nama_cabang'),
        ]);

        $kabupatenIds = $this->request->getPost('id_kab'); // Ambil daftar kabupaten yang dipilih
        if (!empty($kabupatenIds)) {
            foreach ($kabupatenIds as $kabupatenId) {
                $this->cabangDinasKabupatenModel->save([
                    'cabang_dinas_id' => $cabangDinasId,
                    'kabupaten_id' => $kabupatenId
                ]);
            }
        }

        return redirect()->to('/cabang-dinas')->with('success', 'Cabang Dinas berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $kabupatenModel = new \App\Models\KabupatenModel();
        $cabangDinasKabupatenModel = new \App\Models\CabangDinasKabupatenModel();

        $cabangDinas = $this->cabangDinasModel->find($id);
        if (!$cabangDinas) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Cabang Dinas dengan ID $id tidak ditemukan.");
        }

        $selectedKabupaten = array_column(
            $cabangDinasKabupatenModel->where('cabang_dinas_id', $id)->findAll(),
            'kabupaten_id'
        );

        $data = [
            'cabang_dinas' => $cabangDinas,
            'kabupaten' => $kabupatenModel->findAll(),
            'selected_kabupaten' => $selectedKabupaten,
        ];

        return view('datacabdin/edit', $data);
    }

    public function update($id)
    {
        // Pastikan data cabang dinas ada
        $cabangDinas = $this->cabangDinasModel->find($id);
        if (!$cabangDinas) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Cabang Dinas dengan ID $id tidak ditemukan.");
        }

        // Update data utama cabang dinas
        $this->cabangDinasModel->update($id, [
            'kode_cabang' => $this->request->getPost('kode_cabang'),
            'nama_cabang' => $this->request->getPost('nama_cabang'),
        ]);

        // Hapus data wilayah kabupaten yang lama
        $this->cabangDinasKabupatenModel->where('cabang_dinas_id', $id)->delete();

        // Simpan ulang data wilayah kabupaten yang baru
        $kabupatenIds = $this->request->getPost('id_kab');
        if (!empty($kabupatenIds)) {
            foreach ($kabupatenIds as $kabupatenId) {
                $this->cabangDinasKabupatenModel->save([
                    'cabang_dinas_id' => $id,
                    'kabupaten_id' => $kabupatenId
                ]);
            }
        }

        return redirect()->to('/cabang-dinas')->with('success', 'Cabang Dinas berhasil diperbarui!');
    }

    public function delete($id)
    {
        // Pastikan ID yang diberikan valid
        $cabangDinas = $this->cabangDinasModel->find($id);

        if (!$cabangDinas) {
            return redirect()->to('/cabang-dinas')->with('error', 'Data tidak ditemukan!');
        }

        // Hapus data dari database
        $this->cabangDinasModel->delete($id);

        return redirect()->to('/cabang-dinas')->with('success', 'Cabang Dinas berhasil dihapus!');
    }




}
