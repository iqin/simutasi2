<?php

namespace App\Controllers\akun;

use App\Controllers\BaseController;
use App\Models\UserModel;

class KabidGTKController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        // Cek apakah user adalah admin, jika bukan redirect ke dashboard
        if (session()->get('role') !== 'admin') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak.')->send();
            exit();
        }
    
        // Inisialisasi model
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $perPage = $this->request->getVar('per_page') ?: 5; // Default jumlah data per halaman

        // Ambil data pengguna dengan role 'kabid' menggunakan paginate
        $users = $this->userModel->where('role', 'kabid')->paginate($perPage, 'users');

        // Kirim data ke view
        $data = [
            'users' => $users,
            'pager' => $this->userModel->pager, // Tambahkan pager untuk pagination
            'perPage' => $perPage, // Tambahkan variabel perPage ke view
        ];

        return view('kabidgtk/index', $data);
    }

    public function create()
    {
        return view('kabidgtk/create');
    }

    public function store()
    {
        $data = [
            'username' => $this->request->getPost('username'),
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'no_hp' => $this->request->getPost('no_hp'),
            'status' => $this->request->getPost('status') ?: 'enable',            
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'kabid',
        ];
        $this->userModel->save($data);
        session()->setFlashdata('success', 'Kabid GTK berhasil ditambahkan!');
        return redirect()->to('/kabidgtk');
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        return view('kabidgtk/edit', ['user' => $user]);
    }

    public function update($id)
    {
        $data = [
            'username' => $this->request->getPost('username'),
            'nama' => $this->request->getPost('nama'),            
            'email' => $this->request->getPost('email'),
            'no_hp' => $this->request->getPost('no_hp'),
            'status' => $this->request->getPost('status'), 
        ];

        if (!empty($this->request->getPost('password'))) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);
        session()->setFlashdata('success', 'Kabid GTK berhasil diperbarui!');
        return redirect()->to('/kabidgtk');
    }

    public function delete($id)
    {
        $this->userModel->delete($id);
        session()->setFlashdata('success', 'Kabid GTK berhasil dihapus!');
        return redirect()->to('/kabidgtk');        
    }

    public function enable($id)
    {
        $this->userModel->update($id, ['status' => 'enable']);
        session()->setFlashdata('success', 'User berhasil diaktifkan!');
        return redirect()->to('/kabidgtk');  
    }

    public function disable($id)
    {
        $this->userModel->update($id, ['status' => 'disable']);
        session()->setFlashdata('success', 'User berhasil dinonaktifkan!');
        return redirect()->to('/kabidgtk');          
    }

}
