<?php

namespace App\Controllers;

use App\Models\UsulanStatusHistoryModel;
use App\Models\UsulanModel;
use App\Models\SkMutasiModel;
use App\Models\RekomkadisModel;
use App\Models\PengirimanUsulanModel;
use CodeIgniter\Controller;

class LacakUsulanController extends Controller
{
    public function index()
    {
        return view('lacak_mutasi'); // Menampilkan halaman landing page baru
    }


    public function search()
    {
        $nomorUsulan = $this->request->getPost('nomor_usulan');
        $nip = $this->request->getPost('nip');

        $usulanModel = new UsulanModel();
        
        // Ambil data usulan lengkap (termasuk email dan no_hp)
        $usulan = $usulanModel->select('id, id_rekomkadis, guru_nama, guru_nip, guru_nik, email, no_hp, sekolah_asal, google_drive_link, sekolah_tujuan, created_at, nomor_usulan, status')
                              ->where('nomor_usulan', $nomorUsulan)
                              ->where('guru_nip', $nip)
                              ->first();

        if (!$usulan) {
            return redirect()->to('/lacak-mutasi')->with('error', 'Nomor usulan atau NIP tidak ditemukan!');
        }

        // CEK APAKAH EMAIL DAN NO_HP SUDAH TERISI
        $statusUsulan = $usulan['status'];
        $emailKosong = empty($usulan['email']);
        $noHpKosong = empty($usulan['no_hp']);

        // Jika usulan status 01-06 DAN (email kosong ATAU no_hp kosong)
        if (in_array($statusUsulan, ['01', '02', '03', '04', '05', '06']) && ($emailKosong || $noHpKosong)) {
            // Simpan data ke session untuk digunakan di form update
            session()->set('lacak_temp', [
                'id_usulan' => $usulan['id'],
                'nomor_usulan' => $usulan['nomor_usulan'],
                'nip' => $usulan['guru_nip'],
                'nama' => $usulan['guru_nama']
            ]);
            
            // Tampilkan halaman form email/no_hp
            return view('lacak_update_kontak', [
                'nomorUsulan' => $usulan['nomor_usulan'],
                'nip' => $usulan['guru_nip'],
                'nama' => $usulan['guru_nama'],
                'email' => $usulan['email'],
                'no_hp' => $usulan['no_hp']
            ]);
        }

        // Jika semua syarat terpenuhi, lanjutkan ke hasil lacak
        return $this->tampilkanHasilLacak($usulan);
    }

    /**
     * Method untuk menyimpan update email/no_hp
     */
    public function updateKontak()
    {
        $nomorUsulan = $this->request->getPost('nomor_usulan');
        $nip = $this->request->getPost('nip');
        $email = $this->request->getPost('email');
        $no_hp = $this->request->getPost('no_hp');

        // Validasi input
        if (empty($email) || empty($no_hp)) {
            return redirect()->back()->with('error', 'Email dan No. HP harus diisi!');
        }

        $usulanModel = new UsulanModel();
        
        // Cari usulan berdasarkan nomor dan nip
        $usulan = $usulanModel->where('nomor_usulan', $nomorUsulan)
                            ->where('guru_nip', $nip)
                            ->first();

        if (!$usulan) {
            return redirect()->to('/lacak-mutasi')->with('error', 'Data usulan tidak ditemukan!');
        }

        // Update email dan no_hp
        $usulanModel->update($usulan['id'], [
            'email' => $email,
            'no_hp' => $no_hp
        ]);

        // ✅ PERBAIKAN: Set flashdata dengan nilai yang benar
        session()->setFlashdata('kontak_success', true);
        session()->setFlashdata('kontak_email', $email);
        session()->setFlashdata('kontak_hp', $no_hp);
        session()->setFlashdata('kontak_nomor', $nomorUsulan);
        session()->setFlashdata('kontak_nip', $nip);

        // Redirect ke halaman hasil lacak
        return redirect()->to("/lacak-mutasi/hasil/$nomorUsulan/$nip");
    }

    /**
     * Method untuk menampilkan hasil lacak setelah update kontak
     */
    public function hasil($nomorUsulan, $nip)
    {
        $usulanModel = new UsulanModel();
        
        $usulan = $usulanModel->select('id, id_rekomkadis, guru_nama, guru_nip, guru_nik, email, no_hp, sekolah_asal, google_drive_link, sekolah_tujuan, created_at, nomor_usulan, status')
                            ->where('nomor_usulan', $nomorUsulan)
                            ->where('guru_nip', $nip)
                            ->first();

        if (!$usulan) {
            return redirect()->to('/lacak-mutasi')->with('error', 'Data usulan tidak ditemukan!');
        }

        return $this->tampilkanHasilLacak($usulan);
    }

    /**
     * Method untuk menampilkan hasil lacak
     */
    private function tampilkanHasilLacak($usulan)
    {
        $nomorUsulan = $usulan['nomor_usulan'];
        
        $historyModel = new UsulanStatusHistoryModel();
        $skMutasiModel = new SkMutasiModel();
        $rekomKadisModel = new RekomkadisModel();
        $pengirimanUsulanModel = new PengirimanUsulanModel();

        $results = $historyModel->where('nomor_usulan', $nomorUsulan)
                                ->orderBy('updated_at', 'DESC')
                                ->findAll();

        $skMutasi = $skMutasiModel->where('nomor_usulan', $nomorUsulan)->first();
        $fileSK = $skMutasi ? $skMutasi['file_skmutasi'] : null;
        $jenisMutasi = $skMutasi ? $skMutasi['jenis_mutasi'] : null;

        $fileRekomKadis = null;
        if (!empty($usulan['id_rekomkadis'])) {
            $rekomKadis = $rekomKadisModel->select('file_rekomkadis')
                                          ->where('id', $usulan['id_rekomkadis'])
                                          ->first();
            $fileRekomKadis = $rekomKadis ? $rekomKadis['file_rekomkadis'] : null;
        }

        $pengirimanUsulan = $pengirimanUsulanModel->where('nomor_usulan', $nomorUsulan)->first();
        $fileDokumenRekomendasi = $pengirimanUsulan ? $pengirimanUsulan['dokumen_rekomendasi'] : null;

        $googleDriveLink = !empty($usulan['google_drive_link']) ? $usulan['google_drive_link'] : null;

        // Buat token
        $tokenSK = $fileSK ? hash_hmac('sha256', $nomorUsulan . $fileSK, 'secret_key') : null;
        if ($tokenSK) {
            session()->set("token_sk_$nomorUsulan", $tokenSK);
        }

        $tokenRekom = $fileRekomKadis ? hash_hmac('sha256', $nomorUsulan . $fileRekomKadis, 'secret_key') : null;
        if ($tokenRekom) {
            session()->set("token_rekom_$nomorUsulan", $tokenRekom);
        }

        $tokenDokumenRekom = $fileDokumenRekomendasi ? hash_hmac('sha256', $nomorUsulan . $fileDokumenRekomendasi, 'secret_key') : null;
        if ($tokenDokumenRekom) {
            session()->set("token_dokumen_rekom_$nomorUsulan", $tokenDokumenRekom);
        }

        return view('hasil_lacak_mutasi', [
            'pengirimanUsulan' => $pengirimanUsulan,
            'nomorUsulan'   => $usulan['nomor_usulan'],
            'namaGuru'      => $usulan['guru_nama'],
            'nipGuru'       => $usulan['guru_nip'],
            'sekolahAsal'   => $usulan['sekolah_asal'],
            'sekolahTujuan' => $usulan['sekolah_tujuan'],
            'tanggalUsulan' => $usulan['created_at'],
            'results'       => $results,
            'fileSK'        => $fileSK,
            'fileRekomKadis' => $fileRekomKadis,
            'fileDokumenRekom' => $fileDokumenRekomendasi,            
            'jenisMutasi'   => $jenisMutasi,
            'tokenSK'       => $tokenSK,
            'tokenRekom'    => $tokenRekom,
            'tokenDokumenRekom' => $tokenDokumenRekom,
            'googleDriveLink' => $googleDriveLink,
            'email'         => $usulan['email'],
            'no_hp'         => $usulan['no_hp']
        ]);
    }

    public function downloadSK($nomorUsulan, $token)
    {
        $skMutasiModel = new SkMutasiModel();

        // 🔹 Ambil data file berdasarkan nomor usulan
        $fileData = $skMutasiModel->where('nomor_usulan', $nomorUsulan)->first();

        if (!$fileData || empty($fileData['file_skmutasi'])) {
            return redirect()->to('/lacak-mutasi')->with('error', 'File tidak ditemukan.');
        }

        // 🔹 Ambil token yang tersimpan di sesi
        $sessionToken = session()->get("token_sk_$nomorUsulan");

        // 🔹 Validasi token
        $expectedToken = hash_hmac('sha256', $nomorUsulan . $fileData['file_skmutasi'], 'secret_key');
        if ($sessionToken !== $token || $expectedToken !== $token) {
            return redirect()->to('/lacak-mutasi')->with('error', 'Akses tidak valid.');
        }

        // 🔹 Path ke file
        $filePath = WRITEPATH . 'uploads/sk_mutasi/' . $fileData['file_skmutasi'];

        if (!file_exists($filePath)) {
            return redirect()->to('/lacak-mutasi')->with('error', 'File tidak tersedia.');
        }

        return $this->response->download($filePath, null)->setFileName($fileData['file_skmutasi']);
    }

    public function downloadRekomKadis($nomorUsulan, $token)
    {
        $rekomKadisModel = new RekomKadisModel();
        $usulanModel = new UsulanModel();

        // 🔹 Ambil ID Rekom Kadis dari tabel usulan
        $usulan = $usulanModel->select('id_rekomkadis')->where('nomor_usulan', $nomorUsulan)->first();
        if (!$usulan || empty($usulan['id_rekomkadis'])) {
            return redirect()->to('/lacak-mutasi')->with('error', 'File tidak ditemukan.');
        }

        // 🔹 Ambil data file rekom kadis berdasarkan ID rekom kadis
        $fileData = $rekomKadisModel->select('file_rekomkadis')
                                    ->where('id', $usulan['id_rekomkadis'])
                                    ->first();

        if (!$fileData || empty($fileData['file_rekomkadis'])) {
            return redirect()->to('/lacak-mutasi')->with('error', 'File tidak ditemukan.');
        }

        // 🔹 Ambil token yang tersimpan di sesi
        $sessionToken = session()->get("token_rekom_$nomorUsulan");

        // 🔹 Validasi token
        $expectedToken = hash_hmac('sha256', $nomorUsulan . $fileData['file_rekomkadis'], 'secret_key');
        if ($sessionToken !== $token || $expectedToken !== $token) {
            return redirect()->to('/lacak-mutasi')->with('error', 'Akses tidak valid.');
        }

        // 🔹 Path ke file
        $filePath = WRITEPATH . 'uploads/rekom_kadis/' . $fileData['file_rekomkadis'];

        if (!file_exists($filePath)) {
            return redirect()->to('/lacak-mutasi')->with('error', 'File tidak tersedia.');
        }

        return $this->response->download($filePath, null)->setFileName($fileData['file_rekomkadis']);
    }

    public function downloadDokumenRekom($nomorUsulan, $token)
    {
        $pengirimanUsulanModel = new PengirimanUsulanModel();

        $fileData = $pengirimanUsulanModel->where('nomor_usulan', $nomorUsulan)->first();

        if (!$fileData || empty($fileData['dokumen_rekomendasi'])) {
            return redirect()->to('/lacak-mutasi')->with('error', 'File tidak ditemukan.');
        }

        $sessionToken = session()->get("token_dokumen_rekom_$nomorUsulan");
        $expectedToken = hash_hmac('sha256', $nomorUsulan . $fileData['dokumen_rekomendasi'], 'secret_key');

        if ($sessionToken !== $token || $expectedToken !== $token) {
            return redirect()->to('/lacak-mutasi')->with('error', 'Akses tidak valid.');
        }

        $filePath = WRITEPATH . 'uploads/rekomendasi/' . $fileData['dokumen_rekomendasi'];

        if (!file_exists($filePath)) {
            return redirect()->to('/lacak-mutasi')->with('error', 'File tidak tersedia.');
        }

        return $this->response->download($filePath, null)->setFileName($fileData['dokumen_rekomendasi']);
    }

    //chatbox saran
    public function submitSaran()
    {
        // Cek apakah request berasal dari AJAX secara manual
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Request tidak valid.']);
        }
    
        // Ambil data dari request
        $nomorUsulan = $this->request->getPost('nomor_usulan');
        $email = $this->request->getPost('email');
        $saran = $this->request->getPost('saran');
    
        if (empty($email) || empty($saran)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Email dan saran harus diisi!']);
        }
    
        // Simpan ke database
        $saranModel = new \App\Models\SaranMutasiModel();
        $saranModel->save([
            'nomor_usulan' => $nomorUsulan,
            'email' => $email,
            'saran' => $saran
        ]);
    
        return $this->response->setJSON(['status' => 'success', 'message' => 'Terima kasih atas saran Anda!']);
    }
    
    

}