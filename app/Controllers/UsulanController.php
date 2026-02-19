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
    protected $db;

    public function __construct()
    {
        $this->usulanModel = new UsulanModel();
        $this->historyModel = new UsulanStatusHistoryModel();
        $this->pengirimanModel = new PengirimanUsulanModel();
         
        $this->db = \Config\Database::connect();
    }
    
    public function index()
    {
        $role = session()->get('role');
        $userId = session()->get('id');
        $perPage = $this->request->getVar('per_page') ?: 10;
        $searchNIP = $this->request->getVar('nip');
        $filterJenis = $this->request->getVar('jenis'); // ✅ Tambahan

      /*kode lama karena sempat duplikat list usulan di view, walaupun di database normal
        $query = $this->usulanModel
                ->select('usulan.*, pengiriman_usulan.catatan AS catatan_revisi') // ✅ ambil catatan revisi
                ->join('pengiriman_usulan', 'pengiriman_usulan.nomor_usulan = usulan.nomor_usulan', 'left')
                ->orderBy('usulan.created_at', 'DESC');
	 
     Kode penggantinya dibawah ini;
     */
      	$query = $this->usulanModel
        ->select('usulan.*, pu.catatan AS catatan_revisi')
        ->join(
            '(SELECT 
                p1.*
              FROM pengiriman_usulan p1
              INNER JOIN (
                  SELECT nomor_usulan, MAX(id) AS max_id
                  FROM pengiriman_usulan
                  GROUP BY nomor_usulan
              ) p2
              ON p1.id = p2.max_id
            ) pu',
            'pu.nomor_usulan = usulan.nomor_usulan',
            'left'
        )
        ->orderBy('usulan.created_at', 'DESC');
	
      
        // 🔹 Cek Role dan Filter Data Sesuai Role
        if ($role === 'dinas' || $role === 'operator') {
            $operatorModel = new \App\Models\OperatorCabangDinasModel();
            $operator = $operatorModel->where('user_id', $userId)->first();

            if ($operator && isset($operator['cabang_dinas_id'])) {
                $query->where('cabang_dinas_id', $operator['cabang_dinas_id']);
            } else {
                return redirect()->to('/dashboard')->with('error', 'Cabang dinas tidak ditemukan.');
            }
        }

        // 🔹 Filter berdasarkan NIP (jika ada)
        if (!empty($searchNIP)) {
            $query->like('guru_nip', $searchNIP);
        }

        // 🔹 Filter berdasarkan Jenis Usulan (jika ada)
        if (!empty($filterJenis)) {
            $query->where('jenis_usulan', $filterJenis);
        }

        // 🔹 Ambil Data Usulan dengan Pagination
        $usulanData = $query->paginate($perPage, 'usulan');

        // 🔹 Tambahkan Status Telaah & Cabdin (opsional)
        $pengirimanUsulanModel = new \App\Models\PengirimanUsulanModel();
        foreach ($usulanData as &$row) {
            $pengiriman = $pengirimanUsulanModel->where('nomor_usulan', $row['nomor_usulan'])->first();
            $row['status_usulan_cabdin'] = $pengiriman['status_usulan_cabdin'] ?? null;
            $row['status_telaah'] = $pengiriman['status_telaah'] ?? null;
        }

        // 🔹 Status readonly untuk role kabid
        $readonly = ($role === 'kabid');

        // 🔹 Kirim ke View
        $data = [
            'usulan' => $usulanData,
            'pager' => $this->usulanModel->pager,
            'perPage' => $perPage,
            'searchNIP' => $searchNIP,
            'filterJenis' => $filterJenis, // ✅ untuk dropdown jenis usulan
            'role' => $role,
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
                'nama_cabang' => "-"
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

    public function getBerkasSebelumnya()
    {
        $nik = $this->request->getGet('nik');
        $nip = $this->request->getGet('nip');
        $nomorUsulan = $this->request->getGet('current_nomor');

        if (!$nik || !$nip || !$nomorUsulan) {
            log_message('error', '❌ Parameter tidak lengkap: NIK/NIP/NomorUsulan kosong');
            return $this->response->setJSON([]);
        }

        // 🔍 Tidak perlu validasi status usulan
        $exists = $this->db->table('usulan')
            ->where('nomor_usulan', $nomorUsulan)
            ->countAllResults();

        if ($exists === 0) {
            log_message('warning', "⚠️ Nomor usulan tidak ditemukan di database → $nomorUsulan");
            return $this->response->setJSON([]);
        }

        // 🔎 Ambil 20 berkas berdasarkan ID ASC
        $result = $this->db->table('usulan_drive_links')
            ->where('nomor_usulan', $nomorUsulan)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();

        log_message('debug', "🔍 Jumlah baris ditemukan: " . count($result) . " untuk $nomorUsulan");

        // ✅ Isi 20 elemen (kosong jika belum ada)
        $links = array_fill(0, 20, '');
        foreach ($result as $index => $row) {
            if ($index < 20) {
                $links[$index] = $row->drive_link ?? '';
            }
        }

        return $this->response->setJSON($links);
    }

    public function storeDataGuru()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $guruNama           = $this->request->getPost('guru_nama');
            $guruNip            = $this->request->getPost('guru_nip');
            $guruNik            = $this->request->getPost('guru_nik');
            $alasan             = $this->request->getPost('alasan');
            $cabangDinasAsalId  = $this->request->getPost('cabang_dinas_asal_id');
            $sekolahAsal        = $this->request->getPost('sekolah_asal_nama');
            $sekolahTujuan      = $this->request->getPost('sekolah_tujuan_nama');
            $jenisUsulan        = $this->request->getPost('jenis_usulan');

            // 🔒 Validasi input minimal
            if (empty($guruNip) || empty($guruNik) || empty($sekolahAsal) || empty($sekolahTujuan) || empty($cabangDinasAsalId)) {
                throw new \Exception('Data tidak lengkap.');
            }
            // ===== VALIDASI BISNIS CREATE (sesuai logika terbaru) =====
            $nip = $guruNip; $nik = $guruNik;

            // A) Ada proses awal 01–03 jenis apapun → blok semua
            $activeAnyEarly = $this->usulanModel
                ->select('status')
                ->where('guru_nip', $nip)
                ->where('guru_nik', $nik)
                ->whereIn('status', ['01','02','03'])
                ->first();
            if ($activeAnyEarly) {
                throw new \Exception('Tidak dapat membuat usulan baru: masih ada usulan lain pada tahap awal (01–03).');
            }

            // B) Flag per-jenis
            $activeMutasiTetap = $this->usulanModel
                ->select('status')
                ->where('guru_nip', $nip)
                ->where('guru_nik', $nik)
                ->where('jenis_usulan', 'mutasi_tetap')
                ->whereIn('status', ['01','02','03','04','05','06'])
                ->first();

            $activeNdOrPnd = $this->usulanModel
                ->select('status, jenis_usulan')
                ->where('guru_nip', $nip)
                ->where('guru_nik', $nik)
                ->whereIn('jenis_usulan', ['nota_dinas','perpanjangan_nota_dinas'])
                ->whereIn('status', ['01','02','03','04','05','06'])
                ->first();

            // C) ND sudah ada dari SUMBER APA PUN:
            //    - ND reguler yang sudah selesai (usulan jenis 'nota_dinas' status 07), ATAU
            //    - file ND/ND Perpanjangan yang pernah diunggah ke tabel sk_mutasi
            $ndSelesaiByUsulan = $this->usulanModel
                ->where('guru_nip', $nip)
                ->where('guru_nik', $nik)
                ->where('jenis_usulan', 'nota_dinas')
                ->where('status', '07')
                ->countAllResults() > 0;

            $ndFromSk = $db->table('sk_mutasi sm')
                ->join('usulan u', 'u.nomor_usulan = sm.nomor_usulan', 'inner')
                ->where('u.guru_nip', $nip)
                ->where('u.guru_nik', $nik)
                ->whereIn('sm.jenis_mutasi', ['Nota Dinas', 'Nota Dinas Perpanjangan'])
                ->countAllResults() > 0;

            $hasNdAnywhere = ($ndSelesaiByUsulan || $ndFromSk);


            // D) Keputusan final berdasar jenis yang dipilih
            switch ($jenisUsulan) {
                case 'mutasi_tetap':
                    if ($activeMutasiTetap) {
                        throw new \Exception('Tidak dapat membuat Mutasi Tetap baru: masih ada Mutasi Tetap aktif (01–06).');
                    }
                    // NOTE: tidak melarang jika ada ND/PND aktif; itu jalur lain
                    break;

                case 'nota_dinas':
                    // tidak boleh ada ND/PND aktif, dan tidak boleh sudah punya ND doc (harusnya Perpanjangan)
                    if ($activeNdOrPnd) {
                        throw new \Exception('Tidak dapat membuat Nota Dinas: masih ada ND/Perpanjangan ND aktif (01–06).');
                    }
                    if ($hasNdAnywhere) {
                        throw new \Exception('Tidak dapat membuat Nota Dinas: riwayat ND sudah ada. Ajukan Perpanjangan Nota Dinas.');
                    }
                    break;

                case 'perpanjangan_nota_dinas':
                    // wajib punya ND doc sebelumnya (dari ND/PND/mutasi_tetap)
                    if (!$hasNdAnywhere) {
                        // Jangan throw, cukup warning
                        log_message('warning', "Perpanjangan ND dibuat tanpa riwayat ND. NIP={$guruNip}, NIK={$guruNik}");
                        session()->setFlashdata('warning', 
                            'Perhatian: Anda membuat Perpanjangan Nota Dinas meskipun belum ada Nota Dinas sebelumnya yang tercatat di sistem.');
                    }

                    // tidak boleh ada ND/PND aktif
                    if ($activeNdOrPnd) {
                        throw new \Exception('Tidak dapat membuat Perpanjangan ND: masih ada ND/Perpanjangan ND aktif (01–06).');
                    }
                    // NOTE: walau ada mutasi_tetap 05/06, Perpanjangan ND tetap diperbolehkan (sesuai bisnis)
                    break;

                default:
                    throw new \Exception('Jenis usulan tidak dikenali.');
            }

            // 🔢 Generate nomor usulan
            $kodeCabang = $this->getKodeCabangDinas($cabangDinasAsalId);
            $tanggal    = date('Ymd');
            $lastUsulan = $this->usulanModel
                ->select('nomor_usulan')
                ->like('nomor_usulan', "{$kodeCabang}{$tanggal}", 'after')
                ->orderBy('nomor_usulan', 'DESC')
                ->first();
            $nomorUrut     = ($lastUsulan) ? sprintf('%04d', ((int)substr($lastUsulan['nomor_usulan'], -4)) + 1) : '0001';
            $nomorUsulan   = "{$kodeCabang}{$tanggal}{$nomorUrut}";

            // 💾 Simpan ke tabel usulan
            $insert = $this->usulanModel->save([
                'guru_nama'        => $guruNama,
                'guru_nik'         => $guruNik,
                'guru_nip'         => $guruNip,
                'sekolah_asal'     => $sekolahAsal,
                'sekolah_tujuan'   => $sekolahTujuan,
                'alasan'           => $alasan,
                'cabang_dinas_id'  => $cabangDinasAsalId,
                'nomor_usulan'     => $nomorUsulan,
                'status'           => '01',
                'jenis_usulan'     => $jenisUsulan,
            ]);

            if (!$insert) {
                throw new \Exception('Gagal menyimpan usulan.');
            }

            // 📘 Simpan riwayat status sesuai jenis usulan
            $statusKeterangan = match ($jenisUsulan) {
                'mutasi_tetap'  => 'Input data usulan Mutasi oleh Cabang Dinas',
                'nota_dinas'    => 'Input data usulan Nota Dinas oleh Cabang Dinas', 
                'perpanjangan_nota_dinas' => 'Input data usulan perpanjangan Nota Dinas oleh Cabang Dinas',
                default                    => 'Input data GTK & Instansi' // fallback
            };

            $this->addStatusHistory($nomorUsulan, '01', $statusKeterangan);


            $db->transCommit();
            session()->set('nomor_usulan', $nomorUsulan);

            // ✅ Gabungkan flashdata success + warning
            $redirect = redirect()->to("/usulan/upload-berkas/{$nomorUsulan}")
                ->with('success', 'Data berhasil disimpan. Lanjutkan upload berkas.');
                
                $warning = session()->getFlashdata('warning');
            if ($warning) {
                $redirect->with('warning', $warning);
            }

            return $redirect;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'storeDataGuru() ERROR: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
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

        // Tentukan jumlah berkas berdasarkan jenis_usulan
        $jenisUsulan = $usulan['jenis_usulan'] ?? 'mutasi_tetap';
        $totalBerkas = ($jenisUsulan === 'mutasi_tetap') ? 21 : 20;

        $db->transBegin();

        try {
            $googleDriveLinks = $this->request->getPost('google_drive_link');

            // Validasi jumlah link
            if (count($googleDriveLinks) < $totalBerkas) {
                throw new \Exception("Semua tautan berkas ($totalBerkas) harus diisi.");
            }

            $dataBerkas = [];
            $timestamp = date('Y-m-d H:i:s');

            for ($i = 0; $i < $totalBerkas; $i++) {
                $dataBerkas[] = [
                    'nomor_usulan' => $nomor_usulan,
                    'drive_link'   => $googleDriveLinks[$i] ?? '',
                    'created_at'   => $timestamp,
                ];
            }

            $usulanDriveModel->where('nomor_usulan', $nomor_usulan)->delete();
            $usulanDriveModel->insertBatch($dataBerkas);

            $db->transCommit();

            session()->setFlashdata('success', 'Berkas berhasil diunggah dan disimpan!');
            session()->setFlashdata('nomor_usulan', $nomor_usulan);

            return redirect()->to('/usulan/upload-berkas/' . $nomor_usulan);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Gagal menyimpan berkas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan berkas. Silakan coba lagi.');
        }
    }

    
    public function getRiwayatUsulan()
    {
        $nik = $this->request->getGet('nik');
        $nip = $this->request->getGet('nip');

        if (!$nik || !$nip) {
            return $this->response->setStatusCode(400)->setJSON([]);
        }

        $riwayat = $this->usulanModel
            ->where('guru_nik', $nik)
            ->where('guru_nip', $nip)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->response->setJSON($riwayat);
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
        $usulan = $this->usulanModel->where('nomor_usulan', $nomor_usulan)->first();

        if (!$usulan) {
            return redirect()->to('/usulan')->with('error', 'Data usulan tidak ditemukan.');
        }

        $usulanDriveModel = new \App\Models\UsulanDriveModel();
        $driveLinks = $usulanDriveModel
            ->where('nomor_usulan', $nomor_usulan)
            ->orderBy('id', 'ASC')
            ->findAll();

        // Tentukan jumlah slot
        $jenisUsulan = $usulan['jenis_usulan'] ?? 'mutasi_tetap';
        $totalBerkas = ($jenisUsulan === 'mutasi_tetap') ? 21 : 20;

        $usulan_drive_links = array_fill(0, $totalBerkas, '');
        foreach ($driveLinks as $index => $link) {
            if ($index < $totalBerkas) {
                $usulan_drive_links[$index] = $link['drive_link'];
            }
        }

        return view('usulan/editberkas', [
            'usulan'             => $usulan,
            'nomor_usulan'       => $nomor_usulan,
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
                'guru_nama'      => $this->request->getPost('guru_nama'),
                'sekolah_tujuan' => $sekolahTujuan['nama_sekolah'],
                'alasan'         => $this->request->getPost('alasan'),
                'jenis_usulan'   => $this->request->getPost('jenis_usulan') // ← tambahkan ini
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

        $usulan = $this->usulanModel->where('nomor_usulan', $nomor_usulan)->first();
        if (!$usulan) {
            session()->setFlashdata('error', 'Nomor usulan tidak valid.');
            return redirect()->to('/usulan/edit-berkas/' . $nomor_usulan);
        }

        // Tentukan jumlah berkas
        $jenisUsulan = $usulan['jenis_usulan'] ?? 'mutasi_tetap';
        $totalBerkas = ($jenisUsulan === 'mutasi_tetap') ? 21 : 20;

        $db->transBegin();

        try {
            $googleDriveLinks = $this->request->getPost('google_drive_link');

            if (count($googleDriveLinks) < $totalBerkas) {
                throw new \Exception("Semua tautan berkas ($totalBerkas) harus diisi.");
            }

            $dataBerkas = [];
            $timestamp = date('Y-m-d H:i:s');

            for ($i = 0; $i < $totalBerkas; $i++) {
                $dataBerkas[] = [
                    'nomor_usulan' => $nomor_usulan,
                    'drive_link'   => $googleDriveLinks[$i] ?? '',
                    'updated_at'   => $timestamp,
                ];
            }

            $usulanDriveModel->where('nomor_usulan', $nomor_usulan)->delete();
            $usulanDriveModel->insertBatch($dataBerkas);

            $db->transCommit();

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
                'usulan.jenis_usulan',
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

        // ✅ Format jenis usulan sesuai ketentuan
        $jenisRaw = $usulan['jenis_usulan'];
        $usulan['jenis_usulan'] = match ($jenisRaw) {
            'mutasi_tetap' => 'MUTASI',
            'nota_dinas' => 'NOTA DINAS',
            'perpanjangan_nota_dinas' => 'PERPANJANGAN NOTA DINAS',
            default => strtoupper(str_replace('_', ' ', $jenisRaw)),
        };

        $data = [
            'usulan' => $usulan,
            'tanggal_cetak' => date('Y-m-d')
        ];

        $html = view('usulan/pdf_resi', $data);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->set_option('defaultFont', 'Arial');
        $dompdf->set_option('isHtml5ParserEnabled', true);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        ob_end_clean();

        // 🔽 Nama file dinamis
        $filename = 'resi_usulan_' . $nomorUsulan . '.pdf';
        header("Content-Type: application/pdf");
        header("Content-Disposition: inline; filename=\"$filename\"");
        echo $dompdf->output();
        exit;
    }
    public function checkNipNik()
    {
        $nip   = $this->request->getGet('nip');
        $nik   = $this->request->getGet('nik');
        $jenis = $this->request->getGet('jenis');

        if (empty($nip) || empty($nik) || empty($jenis)) {
            return $this->response->setJSON(['exists' => false]);
        }

        // Usulan aktif terakhir (jenis apa saja)
        $usulanAktif = $this->usulanModel
            ->select('status, jenis_usulan')
            ->where('guru_nip', $nip)
            ->where('guru_nik', $nik)
            ->whereIn('status', ['01','02','03','04','05','06'])
            ->orderBy('created_at', 'DESC')
            ->first();

        // Usulan aktif dengan jenis yg sama
        $sameJenis = $this->usulanModel
            ->select('status')
            ->where('guru_nip', $nip)
            ->where('guru_nik', $nik)
            ->where('jenis_usulan', $jenis)
            ->whereIn('status', ['01','02','03','04','05','06'])
            ->orderBy('created_at', 'DESC')
            ->first();

        // Usulan aktif dengan jenis berbeda
        $usulanLain = $this->usulanModel
            ->select('status')
            ->where('guru_nip', $nip)
            ->where('guru_nik', $nik)
            ->where('jenis_usulan !=', $jenis)
            ->whereIn('status', ['01','02','03','04','05','06'])
            ->orderBy('created_at', 'DESC')
            ->first();

        // Sudah ada ND selesai (status 07) dari jalur ND reguler
        $notaDinasSelesai = $this->usulanModel
            ->where('guru_nip', $nip)
            ->where('guru_nik', $nik)
            ->where('jenis_usulan', 'nota_dinas')
            ->where('status', '07')
            ->countAllResults();

        // 🔎 Ada ND/PND AKTIF (status 01–06) ?
        $ndPndActive = $this->usulanModel
            ->select('status')
            ->where('guru_nip', $nip)
            ->where('guru_nik', $nik)
            ->whereIn('jenis_usulan', ['nota_dinas','perpanjangan_nota_dinas'])
            ->whereIn('status', ['01','02','03','04','05','06'])
            ->orderBy('created_at', 'DESC')
            ->first();

        // 🔎 PERNAH ada dokumen ND (baik dari Mutasi Tetap/ND/PND) ?
        $hasNdDocAnywhere = (bool) $this->db->table('sk_mutasi sm')
            ->join('usulan u', 'u.nomor_usulan = sm.nomor_usulan', 'inner')
            ->where('u.guru_nip', $nip)
            ->where('u.guru_nik', $nik)
            ->whereIn('sm.jenis_mutasi', ['Nota Dinas', 'Nota Dinas Perpanjangan'])
            ->countAllResults();

        return $this->response->setJSON([
            'exists'               => $usulanAktif ? true : false,
            'status'               => $usulanAktif['status'] ?? null,
            'jenis'                => $usulanAktif['jenis_usulan'] ?? null,
            'sameJenisStatus'      => $sameJenis['status'] ?? null,
            'statusJenisLainAktif' => $usulanLain['status'] ?? null,
            'noteDone07'           => $notaDinasSelesai > 0,
            'ndPndActiveStatus'    => $ndPndActive['status'] ?? null,   // 👈 baru
            'hasNdDocAnywhere'     => $hasNdDocAnywhere,               // 👈 baru
        ]);
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
        $db->transStart();

        try {
            // 🔹 Hapus berkas di tabel usulan_drive_links
            $db->table('usulan_drive_links')->where('nomor_usulan', $nomorUsulan)->delete();

            // 🔹 Hapus data history
            $db->table('usulan_status_history')->where('nomor_usulan', $nomorUsulan)->delete();

            // 🔹 Hapus file & data di tabel pengiriman_usulan
            $pengirimanFiles = $db->table('pengiriman_usulan')
                ->where('nomor_usulan', $nomorUsulan)
                ->get()
                ->getResultArray();

            foreach ($pengirimanFiles as $file) {
                if (!empty($file['dokumen_rekomendasi'])) {
                    $filePath = WRITEPATH . 'uploads/rekomendasi/' . $file['dokumen_rekomendasi'];
                    if (is_file($filePath)) {
                        if (@unlink($filePath)) {
                            log_message('debug', "File Pengiriman berhasil dihapus: {$filePath}");
                        } else {
                            log_message('error', "Gagal menghapus file Pengiriman: {$filePath}");
                        }
                    } else {
                        log_message('error', "File Pengiriman tidak ditemukan di path: {$filePath}");
                    }
                }
            }
            $db->table('pengiriman_usulan')->where('nomor_usulan', $nomorUsulan)->delete();

            // 🔹 Hapus file & data di tabel sk_mutasi
            $skMutasiFiles = $db->table('sk_mutasi')
                ->where('nomor_usulan', $nomorUsulan)
                ->get()
                ->getResultArray();

            foreach ($skMutasiFiles as $file) {
                if (!empty($file['file_skmutasi'])) {
                    $filePath = WRITEPATH . 'uploads/sk_mutasi/' . $file['file_skmutasi'];
                    if (is_file($filePath)) {
                        @unlink($filePath);
                        log_message('debug', "File SK Mutasi berhasil dihapus: {$filePath}");
                    }
                }
            }
            $db->table('sk_mutasi')->where('nomor_usulan', $nomorUsulan)->delete();

            // 🔹 Hapus file & data di tabel rekom_kadis
            if (!empty($usulan['id_rekomkadis'])) {
                $rekom = $db->table('rekom_kadis')
                    ->where('id', $usulan['id_rekomkadis'])
                    ->get()
                    ->getRowArray();

                if ($rekom && !empty($rekom['file_rekomkadis'])) {
                    $filePath = WRITEPATH . 'uploads/rekom_kadis/' . $rekom['file_rekomkadis'];
                    if (is_file($filePath)) {
                        @unlink($filePath);
                        log_message('debug', "File Rekom Kadis berhasil dihapus: {$filePath}");
                    }
                }

                $db->table('rekom_kadis')->where('id', $usulan['id_rekomkadis'])->delete();
            }

            // 🔹 Terakhir hapus data usulan
            $this->usulanModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', "Gagal menghapus: Terjadi kesalahan dalam transaksi database.");
                throw new \Exception('Gagal menghapus data.');
            }

            return redirect()->to('/usulan')->with('success', 'Data usulan berhasil dihapus.');
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Gagal menghapus usulan: ' . $e->getMessage());
            return redirect()->to('/usulan')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }



    public function deletetolak($id)
    {
        $db = \Config\Database::connect();
        $db->transStart(); // Mulai transaksi untuk mencegah data tidak sinkron
    
        try {
            $pengirimanModel = new \App\Models\PengirimanUsulanModel();
            $usulanModel = new \App\Models\UsulanModel();
            $usulanDriveModel = new \App\Models\UsulanDriveModel();
            $historyModel = new \App\Models\UsulanStatusHistoryModel();
    
            // 🔍 1. Cek apakah usulan ada
            $usulan = $usulanModel->find($id);
            if (!$usulan) {
                log_message('error', "Gagal menghapus: Usulan dengan ID {$id} tidak ditemukan.");
                throw new \Exception('Data usulan tidak ditemukan.');
            }
            $nomorUsulan = $usulan['nomor_usulan'];
    
            // 🔍 2. Hapus data terkait di pengiriman_usulan
            $pengiriman = $pengirimanModel->where('nomor_usulan', $nomorUsulan)->first();
            if ($pengiriman) {
                log_message('debug', "Menghapus data pengiriman_usulan untuk nomor_usulan: {$nomorUsulan}");
    
                // 🔍 Hapus file PDF jika ada
                if (!empty($pengiriman['dokumen_rekomendasi'])) {
                    $filePath = WRITEPATH . 'uploads/rekomendasi/' . $pengiriman['dokumen_rekomendasi'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    } else {
                        log_message('error', "File rekomendasi tidak ditemukan: {$filePath}");
                    }
                }
                $pengirimanModel->where('nomor_usulan', $nomorUsulan)->delete();
            } else {
                log_message('error', "Tidak ditemukan data pengiriman_usulan untuk nomor_usulan: {$nomorUsulan}");
            }
    
            // 🔍 3. Hapus data terkait di usulan_status_history
            $historyCount = $historyModel->where('nomor_usulan', $nomorUsulan)->countAllResults();
            if ($historyCount > 0) {
                log_message('debug', "Menghapus data usulan_status_history untuk nomor_usulan: {$nomorUsulan}");
                $historyModel->where('nomor_usulan', $nomorUsulan)->delete();
            } else {
                log_message('error', "Tidak ditemukan data usulan_status_history untuk nomor_usulan: {$nomorUsulan}");
            }
    
            // 🔍 4. Hapus data terkait di usulan_drive_links
            $driveCount = $usulanDriveModel->where('nomor_usulan', $nomorUsulan)->countAllResults();
            if ($driveCount > 0) {
                log_message('debug', "Menghapus data usulan_drive_links untuk nomor_usulan: {$nomorUsulan}");
                $usulanDriveModel->where('nomor_usulan', $nomorUsulan)->delete();
            } else {
                log_message('error', "Tidak ditemukan data usulan_drive_links untuk nomor_usulan: {$nomorUsulan}");
            }
    
            // 🔍 5. Hapus data utama di tabel usulan
            log_message('debug', "Menghapus data usulan dengan ID: {$id}");
            $usulanModel->delete($id);
    
            $db->transComplete(); // Selesaikan transaksi
    
            if ($db->transStatus() === false) {
                log_message('error', "Gagal menghapus: Terjadi kesalahan dalam transaksi database.");
                throw new \Exception('Gagal menghapus data.');
            }
    
            session()->setFlashdata('success', 'Usulan dan dokumen terkait berhasil dihapus!');
            return redirect()->to('/usulan');
    
        } catch (\Exception $e) {
            $db->transRollback(); // Jika terjadi error, batalkan semua perubahan
            log_message('error', "Gagal menghapus usulan: " . $e->getMessage());
            return redirect()->to('/usulan')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
    
    public function revisi($nomorUsulan)
    {
        // Cari usulan berdasarkan nomor_usulan
        $usulan = $this->usulanModel->where('nomor_usulan', $nomorUsulan)->first();

        if (!$usulan) {
            return redirect()->to('/usulan')->with('error', 'Usulan tidak ditemukan.');
        }

        // Ambil semua drive link
        $usulanDriveModel = new \App\Models\UsulanDriveModel();
        $driveLinks = $usulanDriveModel
            ->where('nomor_usulan', $nomorUsulan)
            ->orderBy('id', 'ASC')
            ->findAll();

        $usulan_drive_links = array_column($driveLinks, 'drive_link');

        // Kirim ke view
        return view('usulan/revisi', [
            'usulan' => $usulan,
            'usulan_drive_links' => $usulan_drive_links
        ]);
    }

public function updateRevisi($id) 
{
    // Cari data usulan by ID
    $usulan = $this->usulanModel->find($id);
    if (!$usulan) {
        return redirect()->to('/usulan')->with('error', 'Data usulan tidak ditemukan.');
    }

    $nomorUsulan = $usulan['nomor_usulan'];
    $jenis       = $usulan['jenis_usulan']; 
    $driveLinks  = $this->request->getPost('google_drive_link');

    if (!$driveLinks || !is_array($driveLinks)) {
        return redirect()->back()->with('error', 'Data tautan berkas tidak valid.');
    }

    // === Konfigurasi jumlah berkas berdasarkan jenis_usulan ===
    $activeIndexes   = range(0, 19);           // default Mutasi Tetap
    $optionalIndexes = [6, 9, 16, 19];         // opsional untuk Mutasi Tetap

    if ($jenis === 'nota_dinas') {
        $activeIndexes   = [0,2,5,7,8,10,13,17,18,19];
        $optionalIndexes = []; // semua wajib
    }
    elseif ($jenis === 'perpanjangan_nota_dinas') {
        $activeIndexes   = [0,1,19];
        $optionalIndexes = []; // semua wajib
    }

    // === Filter hanya index aktif dari input ===
    $filteredLinks = [];
    foreach ($activeIndexes as $i) {
        $filteredLinks[$i] = isset($driveLinks[$i]) ? trim($driveLinks[$i]) : '';
    }

    // === Validasi wajib diisi (skip opsional) ===
    foreach ($activeIndexes as $i) {
        if (!in_array($i, $optionalIndexes) && empty($filteredLinks[$i])) {
            return redirect()->back()->with('error', "Berkas ke-".($i+1)." wajib diisi.");
        }
    }

    $db = \Config\Database::connect();
    $db->transBegin();

    try {
        $usulanDriveModel = new \App\Models\UsulanDriveModel();

        // Ambil data lama untuk nomor_usulan ini
        $existingLinks = $usulanDriveModel
            ->where('nomor_usulan', $nomorUsulan)
            ->orderBy('id', 'ASC')
            ->findAll();

        // Update/Insert sesuai filteredLinks
        foreach ($filteredLinks as $index => $link) {
            if (!empty($existingLinks[$index]['id'])) {
                $usulanDriveModel->update($existingLinks[$index]['id'], [
                    'drive_link' => $link,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                $usulanDriveModel->insert([
                    'nomor_usulan' => $nomorUsulan,
                    'drive_link'   => $link,
                    'created_at'   => date('Y-m-d H:i:s')
                ]);
            }
        }

        // Reset status ke 01 (siap kirim ulang)
        $this->usulanModel->update($id, ['status' => '01']);

        // Tambah riwayat status
        $this->historyModel->insert([
            'nomor_usulan'    => $nomorUsulan,
            'status'          => '01',
            'updated_at'      => date('Y-m-d H:i:s'),
            'catatan_history' => 'Data usulan dilakukan revisi oleh Cabang Dinas'
        ]);

        $db->transCommit();

        session()->setFlashdata('success', 'Revisi berhasil disimpan. Silakan lakukan pengiriman ulang.');
        return redirect()->to('/usulan');

    } catch (\Exception $e) {
        $db->transRollback();
        log_message('error', 'Gagal menyimpan revisi: ' . $e->getMessage());
        return redirect()->to('/usulan/revisi/' . $id)->with('error', 'Terjadi kesalahan saat menyimpan revisi.');
    }
}




    
    
    


}
