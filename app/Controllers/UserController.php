<?php
namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function updatePassword()
    {
        $session = session();
        $userId = $session->get('id');

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        $user = $this->userModel->find($userId);
        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->back()->with('error', 'Password lama salah!');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'Password baru dan konfirmasi tidak cocok!');
        }

        $this->userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);

        return redirect()->back()->with('success', 'Password berhasil diperbarui!');
    }

    public function changePassword()
    {
        return view('user/ubah_password');
    }

    private function getRoleName($role)
    {
        $roles = [
            'admin' => 'Administrator',
            'kabid' => 'Kabid. GTK Dinas Pendidikan Aceh',
            'dinas' => 'Operator Dinas Pendidikan Aceh',
            'operator' => 'Operator Cabang Dinas'
        ];

        return $roles[$role] ?? 'Unknown Role';
    }

    private function getInstansiName($role)
    {
        if ($role === 'admin') {
            return '-';
        } elseif (in_array($role, ['kabid', 'dinas'])) {
            return 'Dinas Pendidikan Aceh';
        } elseif ($role === 'operator') {
            return 'Cabang Dinas';
        }

        return 'Unknown Instansi';
    }

    public function profile()
    {
        $session = session();
        $userId = $session->get('id');

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        $hakAkses = [];
        if ($user['role'] === 'operator') {
            $db = \Config\Database::connect();
            $query = $db->table('operator_cabang_dinas')
                ->select('cabang_dinas.nama_cabang')
                ->join('cabang_dinas', 'operator_cabang_dinas.cabang_dinas_id = cabang_dinas.id')
                ->where('operator_cabang_dinas.user_id', $userId)
                ->get();

            $hakAkses = array_column($query->getResultArray(), 'nama_cabang');
        }

        return view('user/profile', [
            'user' => $user,
            'roleName' => $this->getRoleName($user['role']),
            'instansi' => $this->getInstansiName($user['role']),
            'hakAkses' => $hakAkses,
        ]);
    }

    public function updateProfile()
    {
        $session = session();
        $userId = $session->get('id');

        $this->userModel->update($userId, [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'no_hp' => $this->request->getPost('no_hp'),
        ]);

        return redirect()->to('/user/profile')->with('success', 'Profil berhasil diperbarui!');
    }
}
