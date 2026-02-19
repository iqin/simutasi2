<?php

namespace App\Controllers\akun;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CabangDinasModel;
use App\Models\OperatorCabangDinasModel;

class OperatorDinasController extends BaseController
{
    protected $userModel;
    protected $cabangDinasModel;
    protected $operatorCabangDinasModel;    

    public function __construct()
    {
        // Cek apakah user memiliki role yang diperbolehkan (admin atau kabid)
        $allowedRoles = ['admin', 'kabid'];
        if (!in_array(session()->get('role'), $allowedRoles)) {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak.')->send();
            exit();
        }

        // Inisialisasi model yang digunakan
        $this->userModel = new UserModel();
        $this->cabangDinasModel = new CabangDinasModel();
        $this->operatorCabangDinasModel = new OperatorCabangDinasModel();        
    }

    public function index()
    {
        $perPage = $this->request->getVar('per_page') ?: 5; // Default jumlah data per halaman

        // Ambil data pengguna dengan role 'dinas' menggunakan paginate
        $users = $this->userModel->where('role', 'dinas')->paginate($perPage, 'users');

        // Ambil data cabang dinas untuk setiap user
        $db = \Config\Database::connect();
        foreach ($users as &$user) {
            $assignedCabangDinas = $db->table('operator_cabang_dinas')
                ->select('nama_cabang')
                ->join('cabang_dinas', 'cabang_dinas.id = operator_cabang_dinas.cabang_dinas_id')
                ->where('operator_cabang_dinas.user_id', $user['id'])
                ->get()
                ->getResultArray();
            $user['hak_akses'] = array_column($assignedCabangDinas, 'nama_cabang'); // Tambahkan data hak akses
        }

            // Kirim data ke view
        $data = [
            'users' => $users,
            'pager' => $this->userModel->pager, // Tambahkan pager untuk pagination
            'perPage' => $perPage, // Tambahkan variabel perPage ke view            
        ];

        return view('operatordinas/index', $data);
    }

    public function create()
    {
        $data = [
            'cabang_dinas' => $this->cabangDinasModel->findAll(),
        ];        
        return view('operatordinas/create', $data);
    }

public function store()
{
    $db = \Config\Database::connect();

    // Validasi apakah username sudah ada
    $username = $this->request->getPost('username');
    $existingUser = $db->table('users')->where('username', $username)->get()->getRow();

    if ($existingUser) {
        // Jika username sudah digunakan, tampilkan pesan error
        return redirect()->back()->withInput()->with('error', 'Username sudah digunakan! Silakan pilih username lain.');
    }

    // Data utama user
    $userData = [
        'username' => $username,
        'nama' => $this->request->getPost('nama'),
        'email' => $this->request->getPost('email'),
        'no_hp' => $this->request->getPost('no_hp'),
        'status' => $this->request->getPost('status') ?: 'enable',
        'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        'role' => 'dinas',
    ];

    // Simpan data user ke tabel users
    $this->userModel->save($userData);
    $userId = $this->userModel->getInsertID(); // Ambil ID user baru

    // Ambil cabang dinas yang dipilih
    $cabangDinasIds = $this->request->getPost('cabang_dinas');

    // Simpan data ke tabel operator_cabang_dinas
    if (!empty($cabangDinasIds)) {
        $operatorCabangDinasData = [];
        foreach ($cabangDinasIds as $cabangDinasId) {
            $operatorCabangDinasData[] = [
                'user_id' => $userId,
                'cabang_dinas_id' => $cabangDinasId,
            ];
        }

        $db->table('operator_cabang_dinas')->insertBatch($operatorCabangDinasData);
    }
    session()->setFlashdata('success', 'Operator Dinas berhasil ditambahkan!');
    return redirect()->to('/operatordinas');
}


    public function edit($id)
    {
    // Ambil data user
    $user = $this->userModel->find($id);
    // Ambil semua cabang dinas
    $cabangDinasModel = new \App\Models\CabangDinasModel();
    $cabang_dinas = $cabangDinasModel->findAll();

    // Ambil data cabang dinas yang dikelola user
    $db = \Config\Database::connect();
    $assignedCabangDinas = $db->table('operator_cabang_dinas')
        ->where('user_id', $id)
        ->get()
        ->getResultArray();

    // Ambil ID cabang dinas yang sudah dikelola user
    $assignedCabangIds = array_column($assignedCabangDinas, 'cabang_dinas_id');

    // Kirim data ke view
    return view('operatordinas/edit', [
        'user' => $user,
        'cabang_dinas' => $cabang_dinas,
        'assignedCabangIds' => $assignedCabangIds,
    ]);
    }

    public function update($id)
    {
    // Update data utama user
    $userData = [
        'username' => $this->request->getPost('username'),
        'nama' => $this->request->getPost('nama'),
        'email' => $this->request->getPost('email'),
        'no_hp' => $this->request->getPost('no_hp'),
        'status' => $this->request->getPost('status'),
    ];

    if (!empty($this->request->getPost('password'))) {
        $userData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
    }

    $this->userModel->update($id, $userData);

    // Perbarui data relasi cabang dinas
    $cabangDinasIds = $this->request->getPost('cabang_dinas');
    $db = \Config\Database::connect();
    $db->table('operator_cabang_dinas')->where('user_id', $id)->delete(); // Hapus data lama

    if (!empty($cabangDinasIds)) {
        $operatorCabangDinasData = [];
        foreach ($cabangDinasIds as $cabangDinasId) {
            $operatorCabangDinasData[] = [
                'user_id' => $id,
                'cabang_dinas_id' => $cabangDinasId,
            ];
        }
        $db->table('operator_cabang_dinas')->insertBatch($operatorCabangDinasData); // Masukkan data baru
    }
        session()->setFlashdata('success', 'Operator Dinas berhasil diperbarui!');
        return redirect()->to('/operatordinas');
    }

    public function delete($id)
    {
        $this->userModel->delete($id);
        session()->setFlashdata('success', 'Operator Dinas berhasil dihapus!');
        return redirect()->to('/operatordinas');
    }
    
        public function enable($id)
    {
        $this->userModel->update($id, ['status' => 'enable']);
        session()->setFlashdata('success', 'User berhasil diaktifkan!');
        return redirect()->to('/operatordinas');        
    }

    public function disable($id)
    {
        $this->userModel->update($id, ['status' => 'disable']);
        session()->setFlashdata('success', 'User berhasil dinonaktifkan!');
        return redirect()->to('/operatordinas');           
    }
}
