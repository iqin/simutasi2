<?php

namespace App\Controllers;

use App\Models\UsulanModel;
use App\Models\UsulanDriveModel;
use App\Models\UsulanStatusHistoryModel;
use App\Models\PengirimanUsulanModel;

class RevisiUsulanController extends BaseController
{
    protected $usulanModel;
    protected $driveLinksModel;
    protected $statusHistoryModel;
    protected $pengirimanModel;

    public function __construct()
    {
        $this->usulanModel        = new UsulanModel();
        $this->driveLinksModel    = new UsulanDriveModel();
        $this->statusHistoryModel = new UsulanStatusHistoryModel();
        $this->pengirimanModel    = new PengirimanUsulanModel();
    }

    /**
     * Tampilkan halaman revisi berdasarkan nomor usulan
     */
    public function index($nomorUsulan)
    {
        $usulan = $this->usulanModel->where('nomor_usulan', $nomorUsulan)->first();

        if (!$usulan) {
            return redirect()->to('/usulan')->with('error', 'Data usulan tidak ditemukan.');
        }

        $driveLinks = $this->driveLinksModel
            ->where('nomor_usulan', $nomorUsulan)
            ->orderBy('id', 'ASC')
            ->findAll();

        return view('usulan/revisi', [
            'usulan'              => $usulan,
            'usulan_drive_links'  => array_column($driveLinks, 'drive_link')
        ]);
    }

    /**
     * Proses penyimpanan hasil revisi link drive
     */
    public function store()
    {
        $nomorUsulan = $this->request->getPost('nomor_usulan');
        $dokumenList = $this->request->getPost('dokumen');

        if (!$nomorUsulan || !$dokumenList) {
            return redirect()->back()->with('error', 'Data tidak lengkap.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Data lama
            $oldDocs = $this->driveLinksModel
                ->where('nomor_usulan', $nomorUsulan)
                ->findAll();

            foreach ($dokumenList as $dok) {
                $jenis    = $dok['jenis'] ?? null;
                $linkBaru = trim($dok['link'] ?? '');

                if (!$jenis) continue;

                $existing = null;
                foreach ($oldDocs as $od) {
                    if ($od['jenis_dokumen'] === $jenis) {
                        $existing = $od;
                        break;
                    }
                }

                if ($existing) {
                    if ($linkBaru && $linkBaru !== $existing['drive_link']) {
                        $this->driveLinksModel->update($existing['id'], [
                            'drive_link' => $linkBaru,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                } else {
                    if ($linkBaru) {
                        $this->driveLinksModel->insert([
                            'nomor_usulan'   => $nomorUsulan,
                            'jenis_dokumen'  => $jenis,
                            'drive_link'     => $linkBaru,
                            'created_at'     => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

            // Update status usulan ke 01
            $this->usulanModel->where('nomor_usulan', $nomorUsulan)
                ->set(['status' => '01', 'updated_at' => date('Y-m-d H:i:s')])
                ->update();

            // Tambah history
            $this->statusHistoryModel->insert([
                'nomor_usulan'    => $nomorUsulan,
                'status'          => '01',
                'catatan_history' => 'Revisi berkas usulan oleh cabang dinas',
                'updated_at'      => date('Y-m-d H:i:s')
            ]);

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal.');
            }

            return redirect()->to('/usulan')->with('success', 'Revisi usulan berhasil disimpan.');
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Gagal revisi usulan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan revisi.');
        }
    }
    /**
     * Awal proses revisi (hapus pengiriman + file, reset status ke 01)
     */
public function startRevisi($nomorUsulan)
{
    if (!$nomorUsulan) {
        return redirect()->to('/usulan')->with('error', 'Nomor usulan tidak valid.');
    }

    // Cari usulan
    $usulan = $this->usulanModel->where('nomor_usulan', $nomorUsulan)->first();
    if (!$usulan) {
        return redirect()->to('/usulan')->with('error', 'Data usulan tidak ditemukan.');
    }

    // Cari & hapus data pengiriman
    $pengiriman = $this->pengirimanModel->where('nomor_usulan', $nomorUsulan)->first();
    if ($pengiriman) {
        if (!empty($pengiriman['dokumen_rekomendasi'])) {
            $filePath = WRITEPATH . 'uploads/rekomendasi/' . $pengiriman['dokumen_rekomendasi'];
            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }
        $this->pengirimanModel->where('nomor_usulan', $nomorUsulan)->delete();
    }

    // Update status usulan kembali ke 01
    $this->usulanModel->update($usulan['id'], ['status' => '01']);
/*
    // Tambahkan riwayat
    $this->statusHistoryModel->insert([
        'nomor_usulan'    => $nomorUsulan,
        'status'          => '01',
        'catatan_history' => 'Data usulan dilakukan revisi oleh Cabang Dinas',
        'updated_at'      => date('Y-m-d H:i:s'),
    ]);
*/
    // ✅ Redirect ke form revisi (pakai nomor_usulan)
    return redirect()->to("/usulan/revisi/" . $usulan['nomor_usulan'])
        ->with('success', 'Revisi dimulai. Data pengiriman & file terkait dihapus, status kembali ke awal.');
}


}
