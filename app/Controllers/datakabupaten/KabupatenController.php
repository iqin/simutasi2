<?php

namespace App\Controllers\datakabupaten;

use App\Controllers\BaseController;
use App\Models\KabupatenModel;

class KabupatenController extends BaseController
{
    protected $kabupatenModel;

    public function __construct()
    {
        // Cek apakah user adalah admin, jika bukan redirect ke dashboard
        if (session()->get('role') !== 'admin') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak.')->send();
            exit();
        }
    
        // Inisialisasi model
        $this->kabupatenModel = new KabupatenModel();
    }

    public function index()
    {
        $perPage = $this->request->getVar('per_page') ?: 10; // Default jumlah data per halaman
        $currentPage = $this->request->getVar('page') ?: 1;

        $data = [
            'kabupaten' => $this->kabupatenModel->orderBy('id_kab', 'DESC')->paginate($perPage, 'kabupaten'),
            'pager' => $this->kabupatenModel->pager,
            'perPage' => $perPage,
        ];

        return view('datakabupaten/index', $data);
    }

    public function create()
    {
        return view('datakabupaten/create');
    }

    public function store()
    {
        $this->kabupatenModel->save([
            'nama_kab' => $this->request->getPost('nama_kab'),
            'nama_ibukotakab' => $this->request->getPost('nama_ibukotakab'),
        ]);

        return redirect()->to('/kabupaten')->with('success', 'Kabupaten berhasil ditambahkan!');
    }


    public function edit($id)
    {
        $data = [
            'kabupaten' => $this->kabupatenModel->find($id),
        ];
        return view('datakabupaten/edit', $data);
    }

    public function update($id)
    {
        $this->kabupatenModel->update($id, [
            'nama_kab' => $this->request->getPost('nama_kab'),
            'nama_ibukotakab' => $this->request->getPost('nama_ibukotakab'),
        ]);

        return redirect()->to('/kabupaten')->with('success', 'Kabupaten berhasil diperbarui!');
    }

    public function delete($id)
    {
        $this->kabupatenModel->delete($id);
        return redirect()->to('/kabupaten')->with('success', 'Kabupaten berhasil dihapus!');
    }
}
