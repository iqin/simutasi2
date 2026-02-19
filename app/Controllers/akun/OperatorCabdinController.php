<?php
namespace App\Controllers\akun;

use App\Controllers\BaseController;
use App\Models\UserModel;

class OperatorCabdinController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        // Daftar role yang diizinkan
        $allowedRoles = ['admin', 'kabid', 'dinas'];

        // Cek apakah role user termasuk dalam daftar yang diizinkan
        if (!in_array(session()->get('role'), $allowedRoles)) {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak.')->send();
            exit();
        }

        // Inisialisasi model yang digunakan    
        $this->userModel = new UserModel();
    }

public function index()
{
    $perPage = $this->request->getVar('per_page') ?: 5;
    $operatordinasId = session()->get('id'); // ID user operatordinas dari sesi

    // Ambil ID cabang dinas yang menjadi hak akses operatordinas
    $db = \Config\Database::connect();
    $accessibleCabangIds = $db->table('operator_cabang_dinas')
        ->select('cabang_dinas_id')
        ->where('user_id', $operatordinasId)
        ->get()
        ->getResultArray();

    $accessibleCabangIds = array_column($accessibleCabangIds, 'cabang_dinas_id');

    // Jika $accessibleCabangIds kosong, gunakan array default untuk menghindari error
    if (empty($accessibleCabangIds)) {
        $accessibleCabangIds = [0]; // Nilai default yang tidak valid
    }

    // Ambil data operator cabdin
$users = $this->userModel
    ->select('users.*, cabang_dinas.nama_cabang')
    ->join('operator_cabang_dinas', 'users.id = operator_cabang_dinas.user_id', 'inner') // Ganti 'left' dengan 'inner'
    ->join('cabang_dinas', 'operator_cabang_dinas.cabang_dinas_id = cabang_dinas.id', 'inner')
    ->whereIn('cabang_dinas.id', $accessibleCabangIds)
    ->where('users.role', 'operator')
    ->paginate($perPage, 'users');


    $data = [
        'users' => $users,
        'pager' => $this->userModel->pager,
        'perPage' => $perPage,
    ];

    return view('operatorcabdin/index', $data);
}



    public function create()
    {
        $operatordinasId = session()->get('id'); // Ambil ID OperatorDinas dari sesi

        // Ambil cabang dinas yang menjadi hak akses OperatorDinas
        $db = \Config\Database::connect();
        $accessibleCabangDinas = $db->table('operator_cabang_dinas')
            ->select('cabang_dinas.id, cabang_dinas.nama_cabang')
            ->join('cabang_dinas', 'operator_cabang_dinas.cabang_dinas_id = cabang_dinas.id')
            ->where('operator_cabang_dinas.user_id', $operatordinasId)
            ->get()
            ->getResultArray();

        // Kirim data cabang dinas ke view
        $data = [
            'cabang_dinas' => $accessibleCabangDinas, // Data cabang dinas yang akan tampil di dropdown
        ];

        return view('operatorcabdin/create', $data);
    }


    public function store()
    {
        $operatordinasId = session()->get('id'); // ID OperatorDinas dari sesi

        // Validasi cabang dinas yang dipilih
        $cabangDinasId = $this->request->getPost('cabang_dinas');
        $db = \Config\Database::connect();
        $accessibleCabangDinas = $db->table('operator_cabang_dinas')
            ->select('cabang_dinas_id')
            ->where('user_id', $operatordinasId)
            ->where('cabang_dinas_id', $cabangDinasId)
            ->get()
            ->getRowArray();

        // Validasi tambahan: Periksa apakah cabang dinas yang dipilih valid
        if (!$accessibleCabangDinas) {
            return redirect()->back()->with('error', 'Cabang dinas yang dipilih tidak valid!');
        }

        // Validasi tambahan: Periksa apakah username sudah ada
        $username = $this->request->getPost('username');
        $existingUser = $db->table('users')->where('username', $username)->get()->getRow();

        if ($existingUser) {
            // Jika username sudah digunakan, tampilkan pesan error
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan! Silakan pilih username lain.');
        }

        // Simpan data OperatorCabdin
        $data = [
            'username' => $username,
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'no_hp' => $this->request->getPost('no_hp'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'status' => $this->request->getPost('status') ?: 'enable',
            'role' => 'operator',
        ];
        $this->userModel->save($data);

        // Simpan relasi dengan cabang dinas
        $newOperatorCabdinId = $this->userModel->insertID();
        $db->table('operator_cabang_dinas')->insert([
            'user_id' => $newOperatorCabdinId,
            'cabang_dinas_id' => $cabangDinasId,
        ]);
        session()->setFlashdata('success', 'Operator Cabdin berhasil ditambahkan!');
        return redirect()->to('/operatorcabdin');
    }





    public function edit($id)
    {
        $operatordinasId = session()->get('id'); // ID OperatorDinas dari sesi

        // Ambil data OperatorCabdin berdasarkan ID
        $user = $this->userModel->find($id);

        // Validasi: Pastikan OperatorCabdin ada
        if (!$user || $user['role'] !== 'operator') {
            return redirect()->to('/operatorcabdin')->with('error', 'Operator Cabdin tidak ditemukan!');
        }

        // Ambil hak akses cabang dinas OperatorDinas
        $db = \Config\Database::connect();
        $accessibleCabangDinas = $db->table('operator_cabang_dinas')
            ->select('cabang_dinas.id, cabang_dinas.nama_cabang')
            ->join('cabang_dinas', 'operator_cabang_dinas.cabang_dinas_id = cabang_dinas.id')
            ->where('operator_cabang_dinas.user_id', $operatordinasId)
            ->get()
            ->getResultArray();

        // Ambil cabang dinas yang sudah terhubung dengan OperatorCabdin
        $selectedCabangDinas = $db->table('operator_cabang_dinas')
            ->select('cabang_dinas_id')
            ->where('user_id', $id)
            ->get()
            ->getResultArray();

        $selectedCabangDinas = array_column($selectedCabangDinas, 'cabang_dinas_id');

        $data = [
            'user' => $user,
            'cabang_dinas' => $accessibleCabangDinas,
            'selected_cabang_dinas' => $selectedCabangDinas,
        ];

        return view('operatorcabdin/edit', $data);
    }


    public function update($id)
    {
        // Ambil data dari form
        $data = [
            'username' => $this->request->getPost('username'),
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'no_hp' => $this->request->getPost('no_hp'),
            'status' => $this->request->getPost('status'),
        ];

        // Jika password diisi, maka perbarui
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        // Update data user di tabel users
        $this->userModel->update($id, $data);

        session()->setFlashdata('success', 'Operator Cabdin berhasil diperbarui!');
        return redirect()->to('/operatorcabdin');
    }


    public function delete($id)
    {
        $this->userModel->delete($id);
        session()->setFlashdata('success', 'Operator Cabdin berhasil dihapus!');
        return redirect()->to('/operatorcabdin');
    }


    public function enable($id)
    {
        $this->userModel->update($id, ['status' => 'enable']);
        session()->setFlashdata('success', 'User berhasil diaktifkan!');
        return redirect()->to('/operatorcabdin');        
    }

    public function disable($id)
    {
        $this->userModel->update($id, ['status' => 'disable']);
        session()->setFlashdata('success', 'User berhasil dinonaktifkan!');
        return redirect()->to('/operatorcabdin');             
    }
}
