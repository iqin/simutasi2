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
        $perPage = $this->request->getVar('perPage') ?: 10;
        $statusFilter = $this->request->getGet('status_filter');

        // Ambil parameter pencarian untuk masing-masing tabel
        $searchMenunggu = $this->request->getGet('search_menunggu');
        $searchDiverifikasi = $this->request->getGet('search_diverifikasi');

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

         // Panggil model dengan menyertakan parameter pencarian
        $usulanMenunggu = $this->verifikasiBerkasModel->getUsulanByStatus(
            'Terkirim', $cabangDinasIds, $perPage, 'page_status03', $searchMenunggu
            );
        $pagerMenunggu = $this->verifikasiBerkasModel->pager;

        // Ambil daftar usulan yang sudah diverifikasi dengan menyertakan parameter pencarian
        $usulanDiverifikasi = $this->verifikasiBerkasModel->getUsulanWithDokumenPaginated(
            ['Lengkap', 'TdkLengkap'], $cabangDinasIds, $perPage, 'page_status04', $searchDiverifikasi, $statusFilter
            );
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
            'perPage' => $perPage,
            'searchMenunggu' => $searchMenunggu,
            'searchDiverifikasi' => $searchDiverifikasi,
            'statusFilter' => $statusFilter,
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

        // 🔔 KIRIM EMAIL NOTIFIKASI KE GURU
        helper('phpmailer');
        $usulanModel = new \App\Models\UsulanModel();
        $usulan = $usulanModel->where('nomor_usulan', $nomorUsulan)->first();

        if ($usulan && !empty($usulan['email'])) {
            $jenisLabel = match ($usulan['jenis_usulan']) {
                'mutasi_tetap' => 'Mutasi',
                'nota_dinas'   => 'Nota Dinas',
                'perpanjangan_nota_dinas' => 'Perpanjangan Nota Dinas',
                default => $usulan['jenis_usulan']
            };

            $catatanTampil = !empty($catatan) ? htmlspecialchars($catatan) : '-';

            if ($status === 'Lengkap') {
                $pesanUtama = "Berkas usulan <strong>{$jenisLabel}</strong> Anda dengan nomor <strong>{$nomorUsulan}</strong> telah diverifikasi dan dinyatakan <strong>Lengkap</strong> oleh Dinas Provinsi. Selanjutnya akan diproses oleh Kabid GTK.";
                $statusText = "03 - Verifikasi Berkas {$jenisLabel} Lengkap – Menunggu Telaah Kabid GTK";
                $subject = "SIMUTASI 03 - Verifikasi Berkas {$jenisLabel} Anda dinyatakan Lengkap";
            } else {
                $pesanUtama = "Berkas usulan <strong>{$jenisLabel}</strong> Anda dengan nomor <strong>{$nomorUsulan}</strong> dinyatakan <strong>Tidak Lengkap</strong> oleh Dinas Provinsi.<br><br><strong>Catatan:</strong> {$catatanTampil}<br><br>Silakan hubungi operator Cabang Dinas untuk informasi lebih lanjut.";
                $statusText = "02 - Verifikasi Berkas Tidak Lengkap";
                $subject = "SIMUTASI 02 - Verifikasi Berkas {$jenisLabel} Anda dinyatakan Tidak Lengkap";
            }

            $link = base_url('lacak-mutasi');

            $message = getEmailTemplate(
                $usulan['guru_nama'],
                $jenisLabel,
                $nomorUsulan,
                $pesanUtama,
                $statusText,
                $link
            );

            send_email_phpmailer($usulan['email'], $subject, $message);
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
