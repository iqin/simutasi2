<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserLogModel;

class Auth extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function authenticate()
    {
      /*
        // 🔹 Cegah akses dengan GET
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/login');
        }
    */
        $session = session();
        $model = new UserModel();
        $logModel = new UserLogModel();
        
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        /* **1️⃣ Validasi reCAPTCHA**
        $recaptchaResponse = $this->request->getVar('g-recaptcha-response');
        $secretKey = '6LepasoqAAAAAP0H_15xqhh9RI3HLByT-fnXO1BX'; // 🔹 Gantilah dengan Secret Key dari Google
        $verifyURL = "https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}";

        $recaptcha = json_decode(file_get_contents($verifyURL));

        if (!$recaptcha->success) {
            $session->setFlashdata('error', 'Verifikasi reCAPTCHA gagal. Coba lagi.');
            return redirect()->to('/login');
        }
*/
        // **2️⃣ Cek apakah username ada di database**
        $user = $model->where('username', $username)->first();

        if ($user) {
            $maxAttempts = 5; // Batas percobaan login
            $lockoutTime = 600; // 10 menit dalam detik (600 detik)

            // **3️⃣ Cek apakah user berstatus "enable"**
            if ($user['status'] !== 'enable') {
                $session->setFlashdata('error', 'Akun Anda dinonaktifkan! Hubungi admin.');
                return redirect()->to('/login');
            }

            // **4️⃣ Cek apakah user telah mencapai batas percobaan login**
            if ($user['login_attempts'] >= $maxAttempts) {
                $lastAttemptTime = strtotime($user['last_attempt']);
                if ((time() - $lastAttemptTime) < $lockoutTime) {
                    $session->setFlashdata('error', 'Akun Anda terkunci! Coba lagi setelah 10 menit.');
                    return redirect()->to('/login');
                } else {
                    // **Reset login_attempts hanya jika sudah melewati batas waktu**
                    if ($user['login_attempts'] > 0) {
                        $model->set('login_attempts', 0)->where('id', $user['id'])->update();
                    }
                }
            }

            // **5️⃣ Cek password**
            if (password_verify($password, $user['password'])) {
                // **Reset login_attempts jika ada percobaan sebelumnya**
                if ($user['login_attempts'] > 0) {
                    $model->set('login_attempts', 0)->where('id', $user['id'])->update();
                }

                // ✅ **Regenerasi session ID untuk mencegah Session Hijacking**
                session()->regenerate();

                // ✅ **Simpan informasi sesi dengan keamanan tambahan**
                $sessionData = [
                    'id'        => $user['id'],
                    'username'  => $user['username'],
                    'nama'      => $user['nama'],
                    'role'      => $user['role'],
                    'ip_address'=> $this->request->getIPAddress(),
                    'user_agent'=> $this->request->getUserAgent()->getAgentString(),
                    'logged_in' => true
                ];
                session()->set($sessionData);

                // ✅ **Simpan log aktivitas login**
                $logModel->save([
                    'user_id'    => $user['id'],
                    'action'     => 'login',
                    'ip_address' => $sessionData['ip_address'],
                    'user_agent' => $sessionData['user_agent']
                ]);

                return redirect()->to('/dashboard');

            } else {
                // **6️⃣ Jika password salah, tingkatkan login_attempts**
                $newLoginAttempts = $user['login_attempts'] + 1;
                $currentTimestamp = date('Y-m-d H:i:s');

                if ($newLoginAttempts != $user['login_attempts'] || $currentTimestamp != $user['last_attempt']) {
                    $model->set([
                        'login_attempts' => $newLoginAttempts,
                        'last_attempt'   => $currentTimestamp
                    ])->where('id', $user['id'])->update();
                }

                $session->setFlashdata('error', 'Password salah!');
                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('error', 'Username tidak ditemukan!');
            return redirect()->to('/login');
        }
    }


    public function logout()
    {
        $session = session();
        $logModel = new UserLogModel();

        // ✅ Simpan log aktivitas logout sebelum sesi dihancurkan
        if ($session->has('id')) {
            $logModel->save([
                'user_id'    => $session->get('id'),
                'action'     => 'logout',
                'ip_address' => $session->get('ip_address'),
                'user_agent' => $session->get('user_agent')
            ]);
        }

        // ✅ Hancurkan sesi sepenuhnya untuk keamanan
        session()->destroy();

        // ✅ Redirect ke halaman login dengan pesan sukses
        session()->setFlashdata('success', 'Anda berhasil logout.');
        return redirect()->to('/login');
    }
}
