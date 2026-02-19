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

        $data = [
            'usulanSiapKirim' => $this->berkasModel->getSiapKirim($role, $userId, $perPage),
            'pagerSiapKirim' => $this->berkasModel->pager,
            'usulanSudahDikirim' => $this->berkasModel->getSudahDikirim($role, $userId, $perPageBerkasDikirim),
            'pagerSudahDikirim' => $this->berkasModel->pager,
            'perPageBerkasDikirim' => $perPageBerkasDikirim,
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
