<?php

namespace App\Controllers;

use App\Models\TelaahBerkasModel;

class TelaahController extends BaseController
{
    protected $telaahBerkasModel;

    public function __construct()
    {
        $this->telaahBerkasModel = new TelaahBerkasModel();
        $this->db = \Config\Database::connect();

    }

    public function index()
    {
        $role = session()->get('role');
        $userId = session()->get('id'); // Ambil ID pengguna dari session
        $perPage = 50;

        // Jika role adalah operator, redirect ke dashboard
        if ($role === 'operator') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $db = \Config\Database::connect();
        $cabangDinasIds = [];

        // Jika role adalah "dinas", ambil daftar cabang dinas yang menjadi hak aksesnya
        if ($role === 'dinas') {
            $cabangDinasQuery = $db->table('operator_cabang_dinas')
                ->select('cabang_dinas_id')
                ->where('user_id', $userId)
                ->get()
                ->getResultArray();

            $cabangDinasIds = array_column($cabangDinasQuery, 'cabang_dinas_id');

            if (empty($cabangDinasIds)) {
                $cabangDinasIds = [0]; // Default untuk menghindari error jika tidak ada hak akses
            }
        }

        // Ambil data usulan menunggu telaah dari model
        $usulanMenunggu = $this->telaahBerkasModel->getUsulanMenungguTelaah($role, $cabangDinasIds, $perPage);
        $pagerMenunggu = $this->telaahBerkasModel->pager;

        // Ambil data usulan sudah ditelaah dari model
        $usulanDitelaah = $this->telaahBerkasModel->getUsulanSudahDitelaah($role, $cabangDinasIds, $perPage);
        $pagerDitelaah = $this->telaahBerkasModel->pager;

        // Data untuk View
        $readonly = ($role === 'dinas'); // Role dinas hanya bisa melihat (readonly)

        $data = [
            'usulanMenunggu' => $usulanMenunggu,
            'pagerMenunggu' => $pagerMenunggu,
            'usulanDitelaah' => $usulanDitelaah,
            'pagerDitelaah' => $pagerDitelaah,
            'perPage' => $perPage,
            'readonly' => $readonly, // Role dinas readonly, lainnya tidak
        ];

        return view('telaah/index', $data);
    }

    public function update()
    {
        $request = $this->request->getJSON(true);

        // Validasi data request
        if (!isset($request['nomor_usulan'], $request['status_telaah'])) {
            return $this->response->setJSON(['error' => 'Data tidak lengkap.'])->setStatusCode(400);
        }

        $nomorUsulan = $request['nomor_usulan'];
        $statusTelaah = $request['status_telaah'];
        $catatanTelaah = $request['catatan_telaah'] ?? '';
        $statusUsulan = ($statusTelaah === 'Disetujui') ? '04' : '02';

        // Tentukan catatan history berdasarkan status telaah
        $catatanHistory = ($statusTelaah === 'Disetujui')
            ? "Telaah Usulan oleh Kepala Bidang GTK (Disetujui). " . $catatanTelaah
            : "Telaah Usulan oleh Kepala Bidang GTK (Ditolak). " . $catatanTelaah;

        try {
            // Mulai transaksi
            $this->db->transStart();

            // Update tabel pengiriman_usulan
            $this->db->table('pengiriman_usulan')
                ->where('nomor_usulan', $nomorUsulan)
                ->update([
                    'status_telaah' => $statusTelaah,
                    'catatan_telaah' => $catatanTelaah,
                    'updated_at_telaah' => date('Y-m-d H:i:s'),
                ]);

            // Update tabel usulan
            $this->db->table('usulan')
                ->where('nomor_usulan', $nomorUsulan)
                ->update(['status' => $statusUsulan]);

            // Tambahkan ke tabel usulan_status_history
            $this->db->table('usulan_status_history')
                ->insert([
                    'nomor_usulan' => $nomorUsulan,
                    'status' => $statusUsulan,
                    'catatan_history' => $catatanHistory,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            // Akhiri transaksi
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi gagal.');
            }

            return $this->response->setJSON(['success' => 'Status telaah berhasil diperbarui.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
        }
    }
    




}
