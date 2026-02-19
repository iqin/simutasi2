<?php

namespace App\Controllers;
use App\Models\UsulanDriveModel;
use App\Models\VerifikasiBerkasModel;
use CodeIgniter\Controller;

class VerifikasiController extends BaseController
{
    protected $verifikasiBerkasModel;

    public function __construct()
    {
        // Cek apakah user adalah operator, jika iya redirect ke dashboard
        if (session()->get('role') == 'operator') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak.')->send();
            exit();
        }
    
        // Inisialisasi model
        $this->verifikasiBerkasModel = new VerifikasiBerkasModel();
    }
    public function index()
    {
        $role = session()->get('role');
        $userId = session()->get('id');
        $perPage = 10;

        $db = \Config\Database::connect();
        $cabangDinasIds = [];

        if ($role === 'dinas') {
            // Ambil cabang dinas berdasarkan user jika role adalah dinas
            $cabangDinasIds = $db->table('operator_cabang_dinas')
                ->select('cabang_dinas_id')
                ->where('user_id', $userId)
                ->get()
                ->getResultArray();

            $cabangDinasIds = array_column($cabangDinasIds, 'cabang_dinas_id');

            if (empty($cabangDinasIds)) {
                $cabangDinasIds = [0]; // Nilai default untuk menghindari error
            }
        }

        // Ambil daftar usulan menunggu verifikasi
        $usulanMenunggu = $this->verifikasiBerkasModel->getUsulanByStatus('Terkirim', $cabangDinasIds, $perPage, 'page_status03');
        $pagerMenunggu = $this->verifikasiBerkasModel->pager;

        // Ambil daftar usulan yang sudah diverifikasi
        $usulanDiverifikasi = $this->verifikasiBerkasModel->getUsulanWithDokumenPaginated(['Lengkap', 'TdkLengkap'], $cabangDinasIds, $perPage, 'page_status04');
        $pagerDiverifikasi = $this->verifikasiBerkasModel->pager;

        // Kabid hanya bisa melihat (readonly)
        $readonly = ($role === 'kabid');

        // Data yang dikirim ke tampilan
        $data = [
            'usulanMenunggu' => $usulanMenunggu,
            'pagerMenunggu' => $pagerMenunggu,
            'usulanDiverifikasi' => $usulanDiverifikasi,
            'pagerDiverifikasi' => $pagerDiverifikasi,
            'readonly' => $readonly, // ✅ Kabid hanya bisa melihat
        ];

        return view('verifikasi/index', $data);
    }


    public function updateStatus()
    {
        $request = $this->request->getJSON(true); // Ambil data JSON dari client

        if (!isset($request['nomor_usulan'], $request['status'])) {
            return $this->response->setJSON(['error' => 'Data tidak lengkap.'])->setStatusCode(400);
        }

        $nomorUsulan = $request['nomor_usulan'];
        $status = $request['status'];
        $catatan = $request['catatan'] ?? '';

        $db = \Config\Database::connect();

        try {
            if ($status === 'TdkLengkap') {
                // Update tabel pengiriman_usulan
                $db->table('pengiriman_usulan')
                    ->where('nomor_usulan', $nomorUsulan)
                    ->update([
                        'status_usulan_cabdin' => 'TdkLengkap',
                        'catatan' => $catatan
                    ]);

                // Tambahkan ke tabel usulan_status_history
                $db->table('usulan_status_history')
                    ->insert([
                        'nomor_usulan' => $nomorUsulan,
                        'status' => '02', // Status tetap 02
                        'updated_at' => date('Y-m-d H:i:s'),
                        'catatan_history' => "Proses Verifikasi Berkas di Dinas Provinsi (TdkLengkap). $catatan",
                    ]);

            } elseif ($status === 'Lengkap') {
                // Update tabel pengiriman_usulan
                $db->table('pengiriman_usulan')
                    ->where('nomor_usulan', $nomorUsulan)
                    ->update([
                        'status_usulan_cabdin' => 'Lengkap',
                        'catatan' => $catatan
                    ]);

                // Update tabel usulan
                $db->table('usulan')
                    ->where('nomor_usulan', $nomorUsulan)
                    ->update([
                        'status' => '03', // Status berubah menjadi 03
                    ]);

                // Tambahkan ke tabel usulan_status_history
                $db->table('usulan_status_history')
                    ->insert([
                        'nomor_usulan' => $nomorUsulan,
                        'status' => '03',
                        'updated_at' => date('Y-m-d H:i:s'),
                        'catatan_history' => "Proses Verifikasi Berkas di Dinas Provinsi (Lengkap). $catatan",
                    ]);
            }

            return $this->response->setJSON(['success' => 'Verifikasi berhasil diperbarui.']);
        } 
        catch (\Exception $e) {
        return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
        }
    }

    public function getDriveLinks($nomor_usulan)
    {
        $db = \Config\Database::connect();
        $query = $db->table('usulan_drive_links')
                    ->select('id, nomor_usulan, drive_link') // Pastikan hanya mengambil kolom yang diperlukan
                    ->where('nomor_usulan', $nomor_usulan)
                    ->get();
    
        $data = $query->getResultArray(); // ✅ Mengambil semua data dalam bentuk array
    
        log_message('debug', '[DEBUG] Total data yang dikembalikan dari database: ' . count($data));
    
        return $this->response->setJSON(["total" => count($data), "data" => $data]);
    }
    

}
