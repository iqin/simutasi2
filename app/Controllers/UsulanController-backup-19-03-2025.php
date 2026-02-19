<?php

namespace App\Controllers;

require_once APPPATH . 'ThirdParty/dompdf/autoload.inc.php';
use Dompdf\Dompdf; // Menggunakan namespace Dompdf


use App\Models\UsulanDriveModel;
use App\Models\UsulanModel;
use App\Models\UsulanStatusHistoryModel;
use App\Models\PengirimanUsulanModel;
use App\Models\KabupatenModel;
use App\Models\SekolahModel;
use App\Models\CabangDinasModel;

class UsulanController extends BaseController
{
    protected $usulanModel;
    protected $historyModel;
    protected $pengirimanModel;

    public function __construct()
    {
        $this->usulanModel = new UsulanModel();
        $this->historyModel = new UsulanStatusHistoryModel();
        $this->pengirimanModel = new PengirimanUsulanModel();
    }

    public function index()
    {
        $role = session()->get('role');
        $userId = session()->get('id');
        $perPage = $this->request->getVar('per_page') ?: 10;
        $searchNIP = $this->request->getVar('nip');

        $query = $this->usulanModel->orderBy('created_at', 'DESC');

        // 🔹 Cek Role dan Filter Data Sesuai Role
        if ($role === 'dinas') {
            // Dinas hanya melihat data berdasarkan cabang dinas user (tanpa tambah/edit/hapus)
            $operatorModel = new \App\Models\OperatorCabangDinasModel();
            $operator = $operatorModel->where('user_id', $userId)->first();

            if ($operator && isset($operator['cabang_dinas_id'])) {
                $query->where('cabang_dinas_id', $operator['cabang_dinas_id']);
            } else {
                return redirect()->to('/dashboard')->with('error', 'Cabang dinas tidak ditemukan.');
            }
        } elseif ($role === 'operator') {
            // Operator hanya bisa melihat data sesuai cabang dinasnya
            $operatorModel = new \App\Models\OperatorCabangDinasModel();
            $operator = $operatorModel->where('user_id', $userId)->first();

            if ($operator && isset($operator['cabang_dinas_id'])) {
                $query->where('cabang_dinas_id', $operator['cabang_dinas_id']);
            } else {
                return redirect()->to('/dashboard')->with('error', 'Cabang dinas tidak ditemukan.');
            }
        }

        // 🔹 Jika ada pencarian berdasarkan NIP
        if (!empty($searchNIP)) {
            $query = $query->like('guru_nip', $searchNIP);
        }

        // 🔹 Ambil Data Usulan dengan Pagination
        $usulanData = $query->paginate($perPage, 'usulan');

        // 🔹 Tambahkan Status Telaah ke Setiap Data
        $pengirimanUsulanModel = new \App\Models\PengirimanUsulanModel();
        foreach ($usulanData as &$row) {
            $pengiriman = $pengirimanUsulanModel->where('nomor_usulan', $row['nomor_usulan'])->first();
            $row['status_usulan_cabdin'] = $pengiriman['status_usulan_cabdin'] ?? null;
            $row['status_telaah'] = $pengiriman['status_telaah'] ?? null;
        }

        // 🔹 Tentukan Hak Akses Readonly untuk Role Kabid
        $readonly = ($role === 'kabid');

        // 🔹 Kirim Data ke View
        $data = [
            'usulan' => $usulanData,
            'pager' => $this->usulanModel->pager,
            'perPage' => $perPage,
            'searchNIP' => $searchNIP,
            'role' => $role, // Kirim role ke view untuk tampilan dinamis
            'readonly' => $readonly,
        ];

        return view('usulan/index', $data);
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
    
   
    public function getHistory($nomor_usulan)
    {
        // Ambil data dari tabel usulan_status_history
        $history = $this->historyModel->where('nomor_usulan', $nomor_usulan)
                                      ->orderBy('updated_at', 'ASC') // Urutkan dari yang terlama
                                      ->findAll();

        // Kembalikan data dalam format JSON
        return $this->response->setJSON($history);
    }


    public function create()
    {
        $role = session()->get('role');
        $userId = session()->get('id');
    
        // Cegah role 'dinas' mengakses halaman ini
        if ($role === 'dinas') {
            return redirect()->to('/usulan')->with('error', 'Anda tidak memiliki izin untuk menambah usulan.');
        }
    
        // Inisialisasi model
        $kabupatenModel = new \App\Models\KabupatenModel();
        $cabangDinasModel = new \App\Models\CabangDinasModel();
        $cabangDinasKabupatenModel = new \App\Models\CabangDinasKabupatenModel();
        $sekolahModel = new \App\Models\SekolahModel();    
        $operatorModel = new \App\Models\OperatorCabangDinasModel();
    
        // Ambil semua kabupaten untuk pilihan tujuan
        $kabupatenListTujuan = $kabupatenModel->findAll();
    
        // Inisialisasi array data untuk dikirim ke view
        $data = [
            'kabupaten_asal_id' => null,
            'kabupaten_asal_nama' => '',
            'cabang_dinas_asal_id' => null,
            'cabang_dinas_asal_nama' => '',
            'sekolahAsalList' => [],
            'kabupatenListAsal' => [],
            'kabupatenListTujuan' => $kabupatenListTujuan, // Semua kabupaten untuk tujuan
            'is_operator' => ($role === 'operator'),
        ];
    
        // Jika role adalah operator, ambil otomatis Kabupaten Asal & Cabang Dinas Asal berdasarkan user
        if ($role === 'operator') {
            $operator = $operatorModel->where('user_id', $userId)->first();
    
            if ($operator) {
                // Ambil daftar kabupaten yang terkait dengan cabang dinas operator
                $kabupatenOperatorList = $cabangDinasKabupatenModel
                    ->where('cabang_dinas_id', $operator['cabang_dinas_id'])
                    ->findAll();
    
                // Ambil ID kabupaten yang ditemukan
                $kabupatenIds = array_column($kabupatenOperatorList, 'kabupaten_id');
    
                // Ambil daftar kabupaten berdasarkan ID yang sudah difilter
                $kabupatenListAsal = $kabupatenModel->whereIn('id_kab', $kabupatenIds)->findAll();
    
                // Set data untuk tampilan form
                $data['kabupatenListAsal'] = $kabupatenListAsal;
            }
        }
    
        return view('usulan/create', $data);
    }
    
    

    public function uploadBerkas($nomorUsulan)
    {
        // Periksa apakah nomor usulan valid
        $usulan = $this->usulanModel->where('nomor_usulan', $nomorUsulan)->first();

        if (!$usulan) {
            return redirect()->to('/usulan')->with('error', 'Nomor usulan tidak ditemukan.');
        }

        // Kirim data ke view
        $data = [
            'nomor_usulan' => $nomorUsulan,
            'usulan' => $usulan,
        ];

        return view('usulan/upload_berkas', $data);
    }

    public function getCabangDinas($kabupatenId)
    {
        $db = \Config\Database::connect(); // Koneksi database secara langsung
        $cabangDinas = $db->table('cabang_dinas_kabupaten')
                          ->join('cabang_dinas', 'cabang_dinas.id = cabang_dinas_kabupaten.cabang_dinas_id')
                          ->where('kabupaten_id', $kabupatenId)
                          ->get()
                          ->getRowArray();

        if (!$cabangDinas) {
            return $this->response->setJSON([
                'id' => null,
                'nama_cabang' => "Cabang Dinas tidak ditemukan"
            ]);
        }

        return $this->response->setJSON([
            'id' => $cabangDinas['cabang_dinas_id'],
            'nama_cabang' => $cabangDinas['nama_cabang']
        ]);
    }

    public function getSekolah($kabupatenId)
    {
        $db = \Config\Database::connect(); // Koneksi database
        $sekolahList = $db->table('data_sekolah')
                          ->where('kabupaten_id', $kabupatenId)
                          ->orderBy('jenjang', 'ASC') // Urutkan berdasarkan jenjang (SLB, SMA, SMK)
                          ->orderBy('nama_sekolah', 'ASC') // Urutkan berdasarkan nama sekolah A-Z
                          ->get()
                          ->getResultArray();

        if (empty($sekolahList)) {
            return $this->response->setJSON([
                'error' => 'Tidak ada sekolah ditemukan untuk kabupaten ini'
            ]);
        }

        return $this->response->setJSON($sekolahList);
    }
    
    public function storeDataGuru()
    {
        $db = \Config\Database::connect();

        // Mulai transaksi database
        $db->transBegin();

        try {
            // Ambil data dari input form
            $guruNama = $this->request->getPost('guru_nama');
            $guruNip = $this->request->getPost('guru_nip');
            $guruNik = $this->request->getPost('guru_nik');
            $alasan = $this->request->getPost('alasan');
            $cabangDinasAsalId = $this->request->getPost('cabang_dinas_asal_id');
            $sekolahAsal = $this->request->getPost('sekolah_asal_nama');
            $sekolahTujuan = $this->request->getPost('sekolah_tujuan_nama');

            // Pastikan data sekolah dan cabang dinas tidak kosong
            if (empty($sekolahAsal) || empty($sekolahTujuan) || empty($cabangDinasAsalId)) {
                throw new \Exception('Data sekolah atau cabang dinas tidak ditemukan.');
            }

            // Generate nomor usulan
            $kodeCabang = $this->getKodeCabangDinas($cabangDinasAsalId);
            $tanggal = date('Ymd');
            $lastUsulan = $this->usulanModel
                ->select('nomor_usulan')
                ->like('nomor_usulan', "{$kodeCabang}{$tanggal}", 'after')
                ->orderBy('nomor_usulan', 'DESC')
                ->first();

            $nomorUrut = ($lastUsulan) ? sprintf('%04d', (int) substr($lastUsulan['nomor_usulan'], -4) + 1) : '0001';
            $nomorUsulan = "{$kodeCabang}{$tanggal}{$nomorUrut}";

            // Simpan data ke database
            $this->usulanModel->save([
                'guru_nama'        => $guruNama,
                'guru_nik'         => $guruNik,
                'guru_nip'         => $guruNip,
                'sekolah_asal'     => $sekolahAsal,
                'sekolah_tujuan'   => $sekolahTujuan,
                'alasan'           => $alasan,
                'cabang_dinas_id'  => $cabangDinasAsalId,
                'nomor_usulan'     => $nomorUsulan,
                'status'           => '01',
            ]);

            // Simpan riwayat status
            $this->addStatusHistory($nomorUsulan, '01', 'Input data guru dan sekolah oleh Cabang Dinas');

            // Commit transaksi jika sukses
            $db->transCommit();

            // Simpan nomor usulan ke session
            session()->set('nomor_usulan', $nomorUsulan);

            // Redirect ke tahap 2 (upload berkas)
            return redirect()->to("/usulan/upload-berkas/{$nomorUsulan}")->with('success', 'Data guru dan sekolah berhasil disimpan. Silakan lanjut ke upload berkas.');

        } catch (\Exception $e) {
            // Rollback jika ada kesalahan
            $db->transRollback();
            log_message('error', 'Gagal menyimpan data guru: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan data. Silakan coba lagi.')->withInput();
        }
    }

    

    public function storeDriveLinks($nomor_usulan)
    {
        $db = \Config\Database::connect();
        $usulanDriveModel = new \App\Models\UsulanDriveModel(); 
    
        // Cek apakah nomor usulan valid
        $usulan = $this->usulanModel->where('nomor_usulan', $nomor_usulan)->first();
        if (!$usulan) {
            return redirect()->to('/usulan')->with('error', 'Nomor usulan tidak valid.');
        }
    
        // Mulai transaksi database
        $db->transBegin();
    
        try {
            // Ambil tautan berkas dari form
            $googleDriveLinks = $this->request->getPost('google_drive_link');
    
            // Validasi jumlah link yang harus 20
            if (count($googleDriveLinks) < 20) {
                throw new \Exception('Semua tautan berkas harus diisi.');
            }
    
            $dataBerkas = [];
            $timestamp = date('Y-m-d H:i:s');
    
            for ($i = 0; $i < 20; $i++) {
                $dataBerkas[] = [
                    'nomor_usulan' => $nomor_usulan,
                    'drive_link'   => isset($googleDriveLinks[$i]) ? $googleDriveLinks[$i] : '', // Bisa kosong jika opsional
                    'created_at'   => $timestamp,
                ];
            }
    
            // Hapus data lama sebelum menyimpan yang baru (jika ada)
            $usulanDriveModel->where('nomor_usulan', $nomor_usulan)->delete();
    
            // Simpan semua data ke database dalam satu operasi batch insert
            $usulanDriveModel->insertBatch($dataBerkas);
    
            // Commit transaksi jika semua berhasil
            $db->transCommit();
    
        // Set flashdata untuk SweetAlert
        session()->setFlashdata('success', 'Berkas berhasil diunggah dan disimpan!');
        session()->setFlashdata('nomor_usulan', $nomorUsulan); // Simpan nomor usulan untuk cetak resi

        return redirect()->to('/usulan/upload-berkas/' . $nomorUsulan);
    
        } catch (\Exception $e) {
            // Rollback jika terjadi kesalahan
            $db->transRollback();
            log_message('error', 'Gagal menyimpan berkas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan berkas. Silakan coba lagi.');
        }
    }
      

    public function editUsulan($id)
    {
        // Ambil data usulan dengan JOIN ke tabel yang terkait
        $usulan = $this->usulanModel
            ->select('usulan.*, 
                    sekolah_asal.nama_sekolah as sekolah_asal_nama,
                    sekolah_tujuan.nama_sekolah as sekolah_tujuan_nama,
                    kab_asal.id_kab as kabupaten_asal_id,
                    kab_asal.nama_kab as kabupaten_asal_nama,
                    kab_tujuan.id_kab as kabupaten_tujuan_id,
                    kab_tujuan.nama_kab as kabupaten_tujuan_nama,
                    cd_asal.id AS cabang_dinas_asal_id,
                    cd_asal.nama_cabang as cabang_dinas_asal_nama,
                    cd_tujuan.id AS cabang_dinas_tujuan_id,
                    cd_tujuan.nama_cabang as cabang_dinas_tujuan_nama')
            ->join('data_sekolah as sekolah_asal', 'usulan.sekolah_asal = sekolah_asal.nama_sekolah', 'left')
            ->join('data_sekolah as sekolah_tujuan', 'usulan.sekolah_tujuan = sekolah_tujuan.nama_sekolah', 'left')
            ->join('kabupaten as kab_asal', 'sekolah_asal.kabupaten_id = kab_asal.id_kab', 'left')
            ->join('kabupaten as kab_tujuan', 'sekolah_tujuan.kabupaten_id = kab_tujuan.id_kab', 'left')
            ->join('cabang_dinas_kabupaten as cdk_asal', 'kab_asal.id_kab = cdk_asal.kabupaten_id', 'left')
            ->join('cabang_dinas as cd_asal', 'cdk_asal.cabang_dinas_id = cd_asal.id', 'left')
            ->join('cabang_dinas_kabupaten as cdk_tujuan', 'kab_tujuan.id_kab = cdk_tujuan.kabupaten_id', 'left')
            ->join('cabang_dinas as cd_tujuan', 'cdk_tujuan.cabang_dinas_id = cd_tujuan.id', 'left')
            ->where('usulan.id', $id)
            ->first();
    
        if (!$usulan) {
            return redirect()->to('/usulan')->with('error', 'Data usulan tidak ditemukan.');
        }
    
        $kabupatenModel = new KabupatenModel();
        $kabupatenList = $kabupatenModel->findAll();
    
        return view('usulan/editusulan', [
            'usulan' => $usulan,
            'kabupatenList' => $kabupatenList
        ]);
    }

    public function editBerkas($nomor_usulan)
    {
        // Ambil data usulan berdasarkan nomor usulan
        $usulan = $this->usulanModel->where('nomor_usulan', $nomor_usulan)->first();

        if (!$usulan) {
            return redirect()->to('/usulan')->with('error', 'Data usulan tidak ditemukan.');
        }

        // Ambil data berkas dari tabel usulan_drive_links
        $usulanDriveModel = new \App\Models\UsulanDriveModel();
        $driveLinks = $usulanDriveModel->where('nomor_usulan', $nomor_usulan)->orderBy('id', 'ASC')->findAll();

        // Pastikan 20 slot tersedia (jika kurang, isi dengan kosong)
        $usulan_drive_links = array_fill(0, 20, '');
        foreach ($driveLinks as $index => $link) {
            if ($index < 20) {
                $usulan_drive_links[$index] = $link['drive_link'];
            }
        }

        return view('usulan/editberkas', [
            'usulan' => $usulan,
            'nomor_usulan' => $nomor_usulan, // Kirim nomor usulan ke view agar bisa digunakan
            'usulan_drive_links' => $usulan_drive_links
        ]);
    }

    public function updateUsulan($id)
    {
        try {
            // Ambil data usulan berdasarkan ID
            $usulan = $this->usulanModel->find($id);
    
            if (!$usulan) {
                return redirect()->to('/usulan')->with('error', 'Data usulan tidak ditemukan.');
            }
    
            // Ambil ID sekolah tujuan dari inputan
            $sekolahTujuanId = $this->request->getPost('sekolah_tujuan');
    
            // Ambil nama sekolah berdasarkan ID
            $sekolahModel = new \App\Models\SekolahModel();
            $sekolahTujuan = $sekolahModel->where('id', $sekolahTujuanId)->first();
    
            if (!$sekolahTujuan) {
                return redirect()->to('/usulan/edit-usulan/' . $id)->with('error', 'Sekolah tujuan tidak ditemukan.');
            }
    
            // Data yang akan diperbarui
            $dataUpdate = [
                'guru_nama' => $this->request->getPost('guru_nama'),
                'sekolah_tujuan' => $sekolahTujuan['nama_sekolah'], // Simpan nama sekolah tujuan, bukan ID
                'alasan' => $this->request->getPost('alasan'),
            ];
    
            // Lakukan update data usulan
            $this->usulanModel->update($id, $dataUpdate);
    
            return redirect()->to('/usulan/edit-usulan/' . $id)
                ->with('success', 'Data usulan berhasil diperbarui.');
    
        } catch (\Exception $e) {
            return redirect()->to('/usulan/edit-usulan/' . $id)->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateDriveLinks($nomor_usulan)
    {
        $db = \Config\Database::connect();
        $usulanDriveModel = new \App\Models\UsulanDriveModel();

        // Cek apakah nomor usulan valid
        $usulan = $this->usulanModel->where('nomor_usulan', $nomor_usulan)->first();
        if (!$usulan) {
            session()->setFlashdata('error', 'Nomor usulan tidak valid.');
            return redirect()->to('/usulan/edit-berkas/' . $nomor_usulan);
        }

        // Mulai transaksi database
        $db->transBegin();

        try {
            // Ambil tautan berkas dari form
            $googleDriveLinks = $this->request->getPost('google_drive_link');

            // Validasi jumlah link yang harus 20
            if (count($googleDriveLinks) < 20) {
                throw new \Exception('Semua tautan berkas harus diisi.');
            }

            $dataBerkas = [];
            $timestamp = date('Y-m-d H:i:s');

            for ($i = 0; $i < 20; $i++) {
                $dataBerkas[] = [
                    'nomor_usulan' => $nomor_usulan,
                    'drive_link'   => isset($googleDriveLinks[$i]) ? $googleDriveLinks[$i] : '',
                    'updated_at'   => $timestamp,
                ];
            }

            // Hapus data lama sebelum menyimpan yang baru
            $usulanDriveModel->where('nomor_usulan', $nomor_usulan)->delete();

            // Simpan semua data ke database dalam satu operasi batch insert
            $usulanDriveModel->insertBatch($dataBerkas);

            // Commit transaksi jika semua berhasil
            $db->transCommit();

            // ✅ Set flashdata untuk SweetAlert
            session()->setFlashdata('success', 'Berkas berhasil diperbarui!');

            return redirect()->to('/usulan/edit-berkas/' . $nomor_usulan);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Gagal memperbarui berkas: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal memperbarui berkas. Silakan coba lagi.');
            return redirect()->to('/usulan/edit-berkas/' . $nomor_usulan);
        }
    }

    public function generateResi($nomorUsulan)
    {
        $usulan = $this->usulanModel
            ->select([
                'usulan.nomor_usulan',
                'usulan.guru_nama AS nama_guru',
                'usulan.guru_nip AS nip',
                'usulan.guru_nik AS nik',                
                'usulan.sekolah_asal',
                'usulan.sekolah_tujuan',
                'usulan.alasan AS alasan_mutasi',
                'usulan.created_at AS tanggal_usulan',
                'cabang_dinas.nama_cabang'
            ])
            ->join('cabang_dinas', 'usulan.cabang_dinas_id = cabang_dinas.id', 'left')
            ->where('usulan.nomor_usulan', trim($nomorUsulan))
            ->first();

        if (!$usulan) {
            return redirect()->to('/usulan')->with('error', 'Data usulan tidak ditemukan.');
        }

        // Data yang dikirim ke view
        $data = [
            'usulan' => $usulan,
            'tanggal_cetak' => date('d-m-Y'),
        ];

        // Load View ke DomPDF
        $html = view('usulan/pdf_resi', $data);
        $dompdf = new \Dompdf\Dompdf();

        // Aktifkan opsi agar Dompdf lebih cepat dan ringan
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->set_option('defaultFont', 'Arial');
        $dompdf->set_option('isHtml5ParserEnabled', true);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        ob_end_clean(); // Mencegah error buffer output

        header("Content-Type: application/pdf");
        header("Content-Disposition: inline; filename=\"resi_usulan.pdf\"");
        echo $dompdf->output();
        exit;
    }

    public function checkNipNik()
    {
        $nip = $this->request->getGet('nip');
        $nik = $this->request->getGet('nik');
    
        $exists = $this->usulanModel
            ->groupStart()
                ->where('guru_nip', $nip)
                ->orWhere('guru_nik', $nik)
            ->groupEnd()
            ->whereNotIn('status', ['08'])
            ->countAllResults() > 0;
    
        return $this->response->setJSON(['exists' => $exists]);
    }

    // Fungsi untuk mendapatkan kode cabang dinas berdasarkan ID
    private function getKodeCabangDinas($cabangDinasId)
    {
        $cabangDinasModel = new \App\Models\CabangDinasModel();
        $cabangDinas = $cabangDinasModel->find($cabangDinasId);
        return $cabangDinas['kode_cabang'];
    }

    // Fungsi untuk menyimpan riwayat status ke tabel usulan_status_history
    private function addStatusHistory($nomorUsulan, $status, $catatan = null)
    {
        $statusHistoryModel = new \App\Models\UsulanStatusHistoryModel();
        $statusHistoryModel->save([
            'nomor_usulan' => $nomorUsulan,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
            'catatan_history' => $catatan,
        ]);
    }

    public function konfirmasiCetak($nomorUsulan)
    {
        if (!$nomorUsulan) {
            return redirect()->to('/usulan')->with('error', 'Nomor usulan tidak valid.');
        }

        return view('usulan/konfirmasi_cetak', ['nomor_usulan' => $nomorUsulan]);
    }

    public function delete($id)
    {
        $usulan = $this->usulanModel->find($id);

        if (!$usulan) {
            log_message('error', "Gagal menghapus: Data usulan dengan ID {$id} tidak ditemukan.");
            return redirect()->to('/usulan')->with('error', 'Data usulan tidak ditemukan.');
        }

        $nomorUsulan = $usulan['nomor_usulan'];

        $db = \Config\Database::connect();
        $db->transStart(); // Mulai transaksi

        try {
            // Hapus semua berkas terkait di tabel usulan_drive_links
            $deletedBerkas = $db->table('usulan_drive_links')->where('nomor_usulan', $nomorUsulan)->delete();
            if (!$deletedBerkas) {
                log_message('error', "Gagal menghapus: Data di tabel `usulan_drive_links` untuk nomor usulan {$nomorUsulan} tidak ditemukan atau gagal dihapus.");
            }

            // Hapus data di tabel lain yang mungkin terkait
            $db->table('usulan_status_history')->where('nomor_usulan', $nomorUsulan)->delete();
            $db->table('pengiriman_usulan')->where('nomor_usulan', $nomorUsulan)->delete();

            // Setelah itu, hapus data usulan
            $this->usulanModel->delete($id);

            $db->transComplete(); // Selesaikan transaksi

            if ($db->transStatus() === false) {
                log_message('error', "Gagal menghapus: Terjadi kesalahan dalam transaksi database.");
                throw new \Exception('Gagal menghapus data.');
            }

            return redirect()->to('/usulan')->with('success', 'Data usulan berhasil dihapus.');
        } catch (\Exception $e) {
            $db->transRollback(); // Kembalikan transaksi jika terjadi error
            log_message('error', 'Gagal menghapus usulan: ' . $e->getMessage());
            return redirect()->to('/usulan')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function deletetolak($id)
    {
        $pengirimanModel = new \App\Models\PengirimanUsulanModel();
        $usulanModel = new \App\Models\UsulanModel();

        $usulan = $usulanModel->find($id);
        $pengiriman = $pengirimanModel->where('nomor_usulan', $usulan['nomor_usulan'])->first();

        if ($pengiriman) {
            // Hapus file PDF jika ada
            if (!empty($pengiriman['dokumen_rekomendasi'])) {
                $filePath = WRITEPATH . 'uploads/rekomendasi/' . $pengiriman['dokumen_rekomendasi'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $pengirimanModel->where('nomor_usulan', $usulan['nomor_usulan'])->delete();
        }
        $this->usulanModel->delete($id);
        session()->setFlashdata('success', 'Usulan dan dokumen terkait berhasil dihapus!');
        return redirect()->to('/usulan');
    }

    public function revisi($nomorUsulan)
    {
        // Cari data usulan berdasarkan nomor_usulan
        $usulan = $this->usulanModel->where('nomor_usulan', $nomorUsulan)->first();

        if (!$usulan) {
            // Jika tidak ditemukan, kembalikan ke halaman daftar usulan dengan pesan error
            return redirect()->to('/usulan')->with('error', 'Usulan tidak ditemukan.');
        }

        // Kirim data ke view revisi
        return view('usulan/revisi', ['usulan' => $usulan]);
    }
    public function updateRevisi($id) 
    {
        // Pastikan usulan ada di database
        $usulan = $this->usulanModel->find($id);
        if (!$usulan) {
            return redirect()->to('/usulan')->with('error', 'Data usulan tidak ditemukan.');
        }
    
        // Ambil nomor usulan berdasarkan ID
        $nomorUsulan = $usulan['nomor_usulan'];
    
        // Ambil daftar tautan dari form input
        $driveLinks = $this->request->getPost('google_drive_link');
    
        // Pastikan data dikirimkan dan dalam bentuk array
        if (!$driveLinks || !is_array($driveLinks)) {
            return redirect()->back()->with('error', 'Data tautan berkas tidak valid.');
        }
    
        // Pastikan jumlah data tetap 20 (jika kurang, tambahkan kosong)
        $driveLinks = array_pad($driveLinks, 20, ""); 
    
        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transBegin();
    
        try {
            $usulanDriveModel = new \App\Models\UsulanDriveModel();
    
            // Ambil data lama dari database berdasarkan nomor usulan
            $existingLinks = $usulanDriveModel->where('nomor_usulan', $nomorUsulan)->orderBy('id', 'ASC')->findAll();
    
            // Pastikan jumlah data lama juga 20 agar sesuai dengan data baru
            $existingLinks = array_pad($existingLinks, 20, ['drive_link' => '', 'id' => null]);
    
            // Persiapkan data untuk update atau insert
            foreach ($driveLinks as $index => $link) {
                $data = [
                    'nomor_usulan' => $nomorUsulan,
                    'drive_link'   => trim($link), // Bersihkan input dari spasi ekstra
                    'created_at'   => date('Y-m-d H:i:s')
                ];
    
                if (!empty($existingLinks[$index]['id'])) {
                    // Jika ID ada, update data lama
                    $usulanDriveModel->update($existingLinks[$index]['id'], $data);
                } else {
                    // Jika tidak ada, insert data baru
                    $usulanDriveModel->insert($data);
                }
            }
    
            // Commit transaksi jika semua berhasil
            $db->transCommit();
    
            // Set pesan sukses
            session()->setFlashdata('success', 'Revisi berhasil disimpan. Silahkan melanjutkan proses pengiriman melalui Menu Pengiriman Usulan.');
            
            return redirect()->to('/usulan');
    
        } catch (\Exception $e) {
            // Rollback jika terjadi kesalahan
            $db->transRollback();
            log_message('error', 'Gagal menyimpan revisi: ' . $e->getMessage());
            return redirect()->to('/usulan/revisi/' . $id)->with('error', 'Terjadi kesalahan saat menyimpan revisi.');
        }
    }
    
    
    


}
