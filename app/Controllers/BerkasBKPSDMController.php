<?php

namespace App\Controllers;

use App\Models\BerkasBKPSDMModel;
use CodeIgniter\Controller;

class BerkasBKPSDMController extends BaseController
{
    protected $usulanModel;

    public function __construct()
    {
        // Cek apakah user adalah operator, jika iya redirect ke dashboard
        if (session()->get('role') == 'operator') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak.')->send();
            exit();
        }
    
        // Inisialisasi model
        $this->berkasModel = new BerkasBKPSDMModel();
    }

  public function index()
    {
        $role = session()->get('role');
        $userId = session()->get('id');

        $perPage = $this->request->getGet('perPage') ?? 25;
        $perPageBerkasDikirim = $this->request->getGet('perPageBerkasDikirim') ?? 25;

        // TAMBAHAN: ambil parameter pencarian
        $searchSiap = $this->request->getGet('search_siap');
        $searchSudah = $this->request->getGet('search_sudah');

        $data = [
            'usulanSiapKirim' => $this->berkasModel->getSiapKirim($role, $userId, $perPage, $searchSiap),
            'pagerSiapKirim' => $this->berkasModel->pager,
            'usulanSudahDikirim' => $this->berkasModel->getSudahDikirim($role, $userId, $perPageBerkasDikirim, $searchSudah),
            'pagerSudahDikirim' => $this->berkasModel->pager,
            'perPageBerkasDikirim' => $perPageBerkasDikirim,
            // TAMBAHAN: kirim ke view
            'searchSiap' => $searchSiap,
            'searchSudah' => $searchSudah,
            'perPage' => $perPage,
        ];

        return view('berkasbkpsdm/index', $data);
    }



    public function kirimKeBKPSDM()
    {
        if ($this->request->isAJAX()) {
            $nomorUsulanList = $this->request->getJSON(true)['nomor_usulan'];

            if (empty($nomorUsulanList)) {
                return $this->response->setJSON(['error' => 'Tidak ada data yang dikirim.'])->setStatusCode(400);
            }

            $db = \Config\Database::connect();
            $currentDateTime = date('Y-m-d H:i:s');
            $currentDateFormatted = date('d-m-Y H:i:s');

            try {
                $db->transStart();
                $dataDikirim = []; // Menampung daftar rincian
                
                foreach ($nomorUsulanList as $nomorUsulan) {
                    $db->table('usulan')
                        ->where('nomor_usulan', $nomorUsulan)
                        ->update([
                            'status' => '06',
                            'kirimbkpsdm' => '1',
                            'tglkirimbkpsdm' => $currentDateTime
                        ]);

                    $usulan = $db->table('usulan')
                                ->select('nomor_usulan, guru_nama, sekolah_asal, sekolah_tujuan')
                                ->where('nomor_usulan', $nomorUsulan)
                                ->get()
                                ->getRowArray();
                    

                    $dataDikirim[] = "{$usulan['nomor_usulan']} - {$usulan['guru_nama']} - {$usulan['sekolah_asal']}";

                    // Tambahkan ke tabel usulan_status_history
                    $db->table('usulan_status_history')
                        ->insert([
                            'nomor_usulan' => $nomorUsulan,
                            'status' => '06',
                            'catatan_history' => 'Berkas Mutasi dikirim ke Badan Kepegawaian Aceh (BKA).'
                        ]);
                }

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception("Gagal menyimpan data.");
                }

                // 🔔 Kirim notifikasi email untuk setiap usulan
                $usulanModel = new \App\Models\UsulanModel();
                foreach ($nomorUsulanList as $nomorUsulan) {
                    $usulanData = $usulanModel->where('nomor_usulan', $nomorUsulan)->first();
                    if ($usulanData && !empty($usulanData['email'])) {
                        helper('phpmailer');
                        $jenisLabel = match ($usulanData['jenis_usulan']) {
                            'mutasi_tetap' => 'Mutasi',
                            'nota_dinas'   => 'Nota Dinas',
                            'perpanjangan_nota_dinas' => 'Perpanjangan Nota Dinas',
                            default => $usulanData['jenis_usulan']
                        };

                        $pesanUtama = "Berkas <strong>{$jenisLabel}</strong> Anda dengan nomor <strong>{$nomorUsulan}</strong> telah dikirim ke Badan Kepegawaian Aceh (BKA) untuk diproses lebih lanjut.";
                        $statusText = "Berkas {$jenisLabel} anda telah dikirim ke Badan Kepegawaian Aceh (BKA)";
                        $link = base_url('lacak-mutasi');
                        $subject = "SIMUTASI 06 - Berkas {$jenisLabel} Anda Telah Dikirim ke BKA";

                        $message = getEmailTemplate(
                            $usulanData['guru_nama'],
                            $jenisLabel,
                            $nomorUsulan,
                            $pesanUtama,
                            $statusText,
                            $link
                        );

                        send_email_phpmailer($usulanData['email'], $subject, $message);
                    }
                }

            return $this->response->setJSON([
                'message' => "Tanggal $currentDateFormatted, sebanyak " . count($nomorUsulanList) . " berkas Mutasi telah tercatat di sistem (dikirimkan ke BKA)",
                'daftar_rincian' => $dataDikirim
            ]);

            } catch (\Exception $e) {
                return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
            }
        }

    }



}
