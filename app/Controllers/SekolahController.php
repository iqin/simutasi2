<?php

namespace App\Controllers;

require_once APPPATH . 'Libraries/PhpSpreadsheetLoader.php';

use App\Controllers\BaseController;
use App\Models\SekolahModel;
use App\Models\KabupatenModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SekolahController extends BaseController
{
    protected $sekolahModel;
    protected $kabupatenModel;

    public function __construct()
    {
        // Cek apakah user adalah admin, jika bukan redirect ke dashboard
        if (session()->get('role') !== 'admin') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak.')->send();
            exit();
        }
    
        // Inisialisasi model
        $this->sekolahModel = new SekolahModel();
        $this->kabupatenModel = new KabupatenModel();
    }

    public function index()
    {
        $perPage = $this->request->getVar('per_page') ?: 10;
        $kabupatenId = $this->request->getVar('kabupaten'); // Ambil filter dari URL

        // Query dasar
        $query = $this->sekolahModel
            ->select('data_sekolah.*, kabupaten.nama_kab')
            ->join('kabupaten', 'data_sekolah.kabupaten_id = kabupaten.id_kab', 'left')
            ->orderBy('data_sekolah.nama_sekolah', 'ASC');

        // Jika ada filter kabupaten, tambahkan kondisi WHERE
        if (!empty($kabupatenId)) {
            $query->where('data_sekolah.kabupaten_id', $kabupatenId);
        }

        $data = [
            'sekolah' => $query->paginate($perPage, 'sekolah'),
            'pager' => $this->sekolahModel->pager,
            'perPage' => $perPage,
            'kabupaten' => $this->kabupatenModel->findAll(), // Untuk dropdown filter
            'selectedKabupaten' => $kabupatenId, // Agar filter tetap terpilih
        ];

        return view('datasekolah/index', $data);
    }


    public function create()
    {
        $data = [
            'kabupaten' => $this->kabupatenModel->findAll(),
        ];

        return view('datasekolah/create', $data);
    }

    public function store()
    {
/*
        $npsn = $this->request->getPost('npsn');

        // Cek apakah NPSN hanya angka dan 8 digit
        if (!preg_match('/^\d{8}$/', $npsn)) {
            return redirect()->back()->with('error', 'NPSN harus terdiri dari 8 digit angka!')->withInput();
        }

        // Cek apakah NPSN sudah ada di database
        if ($this->sekolahModel->where('npsn', $npsn)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'NPSN sudah terdaftar!')->withInput();
        }
*/
        // Simpan data baru
        $this->sekolahModel->insert([
            'npsn' => $this->request->getPost('npsn'),
            'nama_sekolah' => $this->request->getPost('nama_sekolah'),
            'alamat_sekolah' => $this->request->getPost('alamat_sekolah'),
            'kabupaten_id' => $this->request->getPost('kabupaten_id'),
            'jenjang' => $this->request->getPost('jenjang'),
            'status' => $this->request->getPost('status')
        ]);

        return redirect()->to('/sekolah')->with('success', 'Sekolah berhasil ditambahkan!');
    }


    public function edit($id)
    {
        $sekolah = $this->sekolahModel
            ->select('data_sekolah.*, kabupaten.nama_kab')
            ->join('kabupaten', 'data_sekolah.kabupaten_id = kabupaten.id_kab', 'left')
            ->where('data_sekolah.id', $id)
            ->first();

        if (!$sekolah) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Sekolah dengan ID $id tidak ditemukan.");
        }

        $data = [
            'sekolah' => $sekolah
        ];

        return view('datasekolah/edit', $data);
    }

    public function update($id)
    {
        $sekolah = $this->sekolahModel->find($id);
        if (!$sekolah) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Sekolah dengan ID $id tidak ditemukan.");
        }

        $kabupaten_id = $this->request->getPost('kabupaten_id');
        $kabupaten = $this->kabupatenModel->find($kabupaten_id);

        if (!$kabupaten) {
            return redirect()->back()->with('error', 'Kabupaten yang dipilih tidak valid!')->withInput();
        }

        // Update data sekolah
        $this->sekolahModel->update($id, [
            'nama_sekolah' => $this->request->getPost('nama_sekolah'),
            'alamat_sekolah' => $this->request->getPost('alamat_sekolah'),
            'kabupaten_id' => $kabupaten_id,
            'jenjang' => $this->request->getPost('jenjang'),
            'status' => $this->request->getPost('status')
        ]);

        return redirect()->to('/sekolah')->with('success', 'Sekolah berhasil diperbarui!');
    }

    public function delete($id)
    {
        $sekolah = $this->sekolahModel->find($id);

        if (!$sekolah) {
            return redirect()->to('/sekolah')->with('error', 'Data tidak ditemukan!');
        }

        $this->sekolahModel->delete($id);

        return redirect()->to('/sekolah')->with('success', 'Sekolah berhasil dihapus!');
    }

    public function importView()
    {
        return view('datasekolah/import', ['kabupaten' => $this->kabupatenModel->findAll()]);
    }

    public function previewExcel()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Metode tidak valid']);
        }

        $file = $this->request->getFile('file_excel');
        if (!$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'File tidak valid atau kosong']);
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $previewData = [];
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Lewati header
                $previewData[] = [
                    'npsn'           => $row[0] ?? '',
                    'nama_sekolah'   => $row[1] ?? '',
                    'alamat_sekolah' => $row[2] ?? '',
                    'jenjang'        => $row[3] ?? '',
                    'status'         => $row[4] ?? ''
                ];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'total'  => count($previewData),
                'data'   => $previewData
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memproses file: ' . $e->getMessage()]);
        }
    }


    public function saveImportedData()
    {
        try {
            $jsonData = $this->request->getJSON();
            if (!$jsonData || empty($jsonData->data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak ada data untuk disimpan'
                ]);
            }

            // Ambil kabupaten_id dari JSON yang dikirim
            $kabupaten_id = $jsonData->kabupaten_id ?? null;
            if (!$kabupaten_id) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Kabupaten tidak dipilih!',
                ]);
            }

            $successCount = 0;
            $errorCount = 0;
            $errorDetails = [];

            foreach ($jsonData->data as $row) {
                // Pastikan data tidak kosong
                if (empty($row->npsn) || empty($row->nama_sekolah)) {
                    $errorCount++;
                    $errorDetails[] = "Data kosong untuk NPSN: " . ($row->npsn ?? 'NULL');
                    continue;
                }

                // Periksa apakah kabupaten_id valid
                $kabupaten = $this->kabupatenModel->find($kabupaten_id);
                if (!$kabupaten) {
                    $errorCount++;
                    $errorDetails[] = "Kabupaten ID tidak valid untuk NPSN: " . $row->npsn;
                    continue;
                }

                // Pastikan NPSN unik
                $existing = $this->sekolahModel->where('npsn', $row->npsn)->first();
                if ($existing) {
                    $errorCount++;
                    $errorDetails[] = "NPSN " . $row->npsn . " sudah ada di database.";
                    continue;
                }

                // Data untuk disimpan
                $data = [
                    'npsn'           => $row->npsn,
                    'nama_sekolah'   => $row->nama_sekolah,
                    'alamat_sekolah' => $row->alamat_sekolah,
                    'jenjang'        => $row->jenjang,
                    'status'         => $row->status,
                    'kabupaten_id'   => $kabupaten_id
                ];

                // Simpan ke database
                if ($this->sekolahModel->insert($data)) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errorDetails[] = "Gagal menyimpan data untuk NPSN: " . $row->npsn;
                }
            }

            return $this->response->setJSON([
                'status'       => 'success',
                'message'      => "Import selesai! Berhasil: $successCount, Gagal: $errorCount",
                'success'      => $successCount,
                'error'        => $errorCount,
                'error_details'=> $errorDetails
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kesalahan saat menyimpan data: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function downloadTemplate()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header Kolom
            $sheet->setCellValue('A1', 'NPSN')
                  ->setCellValue('B1', 'Nama Sekolah')
                  ->setCellValue('C1', 'Alamat')
                  ->setCellValue('D1', 'Jenjang (SLB, SMA, SMK)')
                  ->setCellValue('E1', 'Status (Negeri/Swasta)');

            $filename = 'Template_Import_Sekolah.xlsx';

            // Header untuk download file
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    }


}
