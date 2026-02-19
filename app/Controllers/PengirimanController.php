<?php

namespace App\Controllers;

use App\Models\FilterCabdinUsulanModel; 
use App\Models\PengirimanUsulanModel;
use App\Models\OperatorCabangDinasModel;
use App\Models\UsulanStatusHistoryModel;
//use App\Models\UsulanModel;

class PengirimanController extends BaseController
{
    protected $pengirimanModel;
    protected $filterCabdinModel;

    public function __construct()
    {
        $this->pengirimanModel = new PengirimanUsulanModel();
        $this->filterCabdinModel = new FilterCabdinUsulanModel();
        helper('custom');

    }

    public function index()
    {
        $role = session()->get('role'); 
        $userId = session()->get('id'); 
        $perPage = 10;

        $status01Usulan = [];
        $status02Usulan = [];
        $totalStatus01 = 0;
        $totalStatus02 = 0;

        if ($role === 'dinas') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        if ($role === 'admin') {
            $status01Usulan = $this->filterCabdinModel->getUsulanByStatus('01', null, $perPage);
            $status02Usulan = $this->filterCabdinModel->getUsulanWithDokumenPaginated('02', null, $perPage);
            $totalStatus01 = $this->filterCabdinModel->countUsulanByStatus('01');
            $totalStatus02 = $this->filterCabdinModel->countUsulanWithDokumen('02');
            $readonly = false;
        } elseif ($role === 'operator') {
            $operatorModel = new OperatorCabangDinasModel();
            $operator = $operatorModel->where('user_id', $userId)->first();
            if ($operator && isset($operator['cabang_dinas_id'])) {
                $cabangDinasId = $operator['cabang_dinas_id'];
                $status01Usulan = $this->filterCabdinModel->getUsulanByStatus('01', $cabangDinasId, $perPage);
                $status02Usulan = $this->filterCabdinModel->getUsulanWithDokumenPaginated('02', $cabangDinasId, $perPage);
                $totalStatus01 = $this->filterCabdinModel->countUsulanByStatus('01', $cabangDinasId);
                $totalStatus02 = $this->filterCabdinModel->countUsulanWithDokumen('02', $cabangDinasId);
                $readonly = false;
            } else {
                return redirect()->to('/dashboard')->with('error', 'Cabang dinas tidak ditemukan.');
            }
        } elseif ($role === 'kabid') {
            $status01Usulan = $this->filterCabdinModel->getUsulanByStatus('01', null, $perPage);
            $status02Usulan = $this->filterCabdinModel->getUsulanWithDokumenPaginated('02', null, $perPage);
            $totalStatus01 = $this->filterCabdinModel->countUsulanByStatus('01');
            $totalStatus02 = $this->filterCabdinModel->countUsulanWithDokumen('02');
            $readonly = true;
        }

        $data = [
            'status01Usulan' => $status01Usulan,
            'status02Usulan' => $status02Usulan,
            'pager' => $this->filterCabdinModel->pager, // Pastikan `pager` dikirim ke view
            'perPage' => $perPage,
            'readonly' => $readonly,
        ];

        return view('pengiriman/index', $data);
    }



public function updateStatus()
{
    $nomorUsulan = $this->request->getPost('nomor_usulan');
    $noHp = $this->request->getPost('no_hp');
    $dokumenRekomendasi = $this->request->getFile('dokumen_rekomendasi');

    // Ambil nama operator dari session
    $operatorName = session()->get('nama');
    if (!$operatorName) {
        return redirect()->back()->with('error', 'Nama operator tidak ditemukan di session.');
    }

    // Validasi nomor HP wajib diisi
    if (!$noHp) {
        return redirect()->back()->with('error', 'Nomor HP wajib diisi.');
    }

    // Validasi dokumen hanya PDF dan ukuran maksimal 1 MB
    if ($dokumenRekomendasi && $dokumenRekomendasi->isValid()) {
        if ($dokumenRekomendasi->getExtension() !== 'pdf') {
            return redirect()->back()->with('error', 'File yang diunggah harus berformat PDF.');
        }


        if ($dokumenRekomendasi->getSize() > 1048576) { // 1 MB = 1.048.576 byte
            return redirect()->back()->with('error', 'Ukuran file tidak boleh lebih dari 1 MB.');
        }

        // Atur nama file sesuai format <nomor_usulan>-<rekomendasicabdin>.pdf
        $dokumenName = $nomorUsulan . '-rekomendasicabdin.pdf';
        $dokumenRekomendasi->move(WRITEPATH . 'uploads/rekomendasi/', $dokumenName);

        // Simpan data ke tabel pengiriman_usulan
        $this->pengirimanModel->insert([
            'nomor_usulan' => $nomorUsulan,
            'dokumen_rekomendasi' => $dokumenName,
            'operator' => $operatorName,
            'no_hp' => $noHp,
            'status_usulan_cabdin' => 'Terkirim',
            'catatan' => '-',         
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Ambil jenis usulan berdasarkan nomor_usulan
        $usulanData = $this->filterCabdinModel
            ->where('nomor_usulan', $nomorUsulan)
            ->select('jenis_usulan')
            ->first();

        $jenis = $usulanData['jenis_usulan'] ?? '';
        $catatanMap = [
            'mutasi_tetap' => 'Berkas usulan Mutasi telah dikirim ke Dinas Provinsi',
            'nota_dinas' => 'Berkas usulan Nota Dinas telah dikirim ke Dinas Provinsi',
            'perpanjangan_nota_dinas' => 'Berkas usulan Perpanjangan Nota Dinas telah dikirim ke Dinas Provinsi',
        ];

        $catatanHistory = $catatanMap[$jenis] ?? 'Berkas usulan telah dikirim ke Dinas Provinsi';

        // Tambahkan riwayat status ke tabel usulan_status_history
        $statusHistoryModel = new UsulanStatusHistoryModel();
        $statusHistoryModel->save([
            'nomor_usulan' => $nomorUsulan,
            'status' => '02',
            'updated_at' => date('Y-m-d H:i:s'),
            'catatan_history' => $catatanHistory,
        ]);


        // Update status di tabel usulan menggunakan FilterCabdinUsulanModel
        $this->filterCabdinModel
            ->where('nomor_usulan', $nomorUsulan)
            ->set(['status' => '02', 'updated_at' => date('Y-m-d H:i:s')])
            ->update();

        session()->setFlashdata('success', 'Usulan berhasil dikirim.');
        return redirect()->to('/pengiriman');
    }

    return redirect()->back()->with('error', 'Dokumen rekomendasi gagal diunggah.');
}



}
