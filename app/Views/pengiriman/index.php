<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>




<h1 class="h3 mb-4 text-gray-800"><i class="fas fa-paper-plane"></i> Pengiriman Usulan</h1>
<!-- Formulir Pengiriman -->
<div class="card mb-4">
    <?php if (!$readonly): ?>
    <div class="card-body">
        <form action="/pengiriman/updateStatus" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <div class="row">
                <!-- Sisi Kiri -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nomorUsulan">Pilih Usulan yang akan dikirim</label>
                        <select id="nomorUsulan" name="nomor_usulan" class="form-control" required>
                            <option value="">Pilih Usulan</option>
                            <?php foreach ($status01Usulan as $usulan): ?>  
                                <?php
                                    $jenisMap = [
                                        'mutasi_tetap' => 'Mutasi',
                                        'nota_dinas' => 'Nota Dinas',
                                        'perpanjangan_nota_dinas' => 'Perpanjangan ND'
                                    ];
                                    $jenisLabel = $jenisMap[$usulan['jenis_usulan']] ?? $usulan['jenis_usulan'];
                                ?>
                                <option value="<?= $usulan['nomor_usulan'] ?>">
                                    <?= $usulan['guru_nama'] ?> - <?= $usulan['sekolah_asal'] ?> - <?= $jenisLabel ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>  
                    <div class="form-group">
                        <label for="dokumenRekomendasi">Unggah Dokumen Rekomendasi (PDF, Maksimal 1 MB)</label>
                        <input type="file" id="dokumenRekomendasi" name="dokumen_rekomendasi" class="form-control" accept=".pdf" required>
                    </div>
                </div>

                <!-- Sisi Kanan -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="operator">Nama Operator Cabang Dinas</label>
                        <input type="text" id="operator" name="operator" class="form-control" value="<?= session()->get('nama') ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="noHp">No. HP Operator</label>
                        <input type="text" id="noHp" name="no_hp" class="form-control" placeholder="Masukkan nomor HP" required>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm-custom"><i class="fas fa-paper-plane"></i> Kirim</button>
        </form>
    </div>
    <?php endif; ?>
</div>


<!-- Tabel Kiri dan Kanan -->
<div class="row">
    <!-- Tabel Kiri -->
    <div class="col-md-6">
        <div class="filter-section">
            <label class="text-primary"><i class="fas fa-info-circle"></i> 01: Belum Dikirim</label>
            <form method="get" id="formStatus01" class="d-flex">
                <input type="text" name="search_01" class="form-control filter-input" 
                    placeholder="Filter Nama GTK" value="<?= esc($search01 ?? '') ?>" autocomplete="off">
                <select name="perPage" class="form-control" onchange="this.form.submit()">
                    <option value="10" <?= ($perPage ?? 10) == 10 ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= ($perPage ?? 10) == 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= ($perPage ?? 10) == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= ($perPage ?? 10) == 100 ? 'selected' : '' ?>>100</option>
                </select>
                <noscript><button type="submit" class="btn btn-primary btn-sm">Cari</button></noscript>
            </form>
        </div>
        <div class="table-responsive">
            <table id="tableStatus01" class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Nama GTK</th>
                        <th>Sekolah Asal</th>
                        <th>Jenis Usulan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <?php
                    $jenisMap = [
                        'mutasi_tetap' => 'MUTASI',
                        'nota_dinas' => 'NOTA DINAS',
                        'perpanjangan_nota_dinas' => 'PERPANJANGAN ND'
                    ];
                ?>
                <tbody>
                <?php if (count($status01Usulan) === 0): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">Tidak ada usulan</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($status01Usulan as $usulan): ?>
                        <tr class="table-row">
                            <td><?= $usulan['guru_nama'] ?></td>
                            <td><?= $usulan['sekolah_asal'] ?></td>
                            <td><?= $jenisMap[$usulan['jenis_usulan']] ?? $usulan['jenis_usulan'] ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="showDetailKiri(<?= htmlspecialchars(json_encode([
                                    'nomor_usulan'   => $usulan['nomor_usulan'],
                                    'jenis_usulan_raw' => $usulan['jenis_usulan'],

                                    'guru_nama'      => $usulan['guru_nama'],
                                    'guru_nip'       => $usulan['guru_nip'],
                                    'guru_nik'       => $usulan['guru_nik'],
                                    'jenis_usulan'   => $jenisMap[$usulan['jenis_usulan']] ?? $usulan['jenis_usulan'],
                                    'sekolah_asal'   => $usulan['sekolah_asal'],
                                    'sekolah_tujuan' => $usulan['sekolah_tujuan'],
                                    'berkas_scan'    => $usulan['berkas_scan'] ?? null,
                                    'created_at'     => $usulan['created_at'],
                                ])) ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>

            </table>
        </div>
        <?php if (!empty($pager)) : ?>
            <div class="pagination-container">
                <?= $pager->links('page_status01', 'default_full', ['search_01' => $search01 ?? '', 'perPage' => $perPage ?? 10]) ?>
            </div>
        <?php endif; ?>

        <!-- Detail Tabel Kiri -->
        <div id="detailDataKiri" class="mt-4 detail-container" style="display: none;">
            <label class="text-primary"><i class="fas fa-info-circle"></i> Detail Usulan</label>
            <table class="table table-bordered detail-table">
                <tbody>
                    <tr><th>Nomor Usulan</th><td id="detailKiriNomorUsulan"></td></tr>
                    <tr><th>Jenis Usulan</th><td id="detailKiriJenisUsulan"></td></tr>
                    <input type="hidden" id="hiddenJenisUsulanKiri">

                    <tr><th>Nama GTK</th><td id="detailKiriNamaGuru"></td></tr>
                    <tr><th>NIP</th><td id="detailKiriNIP"></td></tr>
                    <tr><th>NIK</th><td id="detailKiriNIK"></td></tr>
                    <tr><th>Sekolah Asal</th><td id="detailKiriSekolahAsal"></td></tr>
                    <tr><th>Sekolah / Instansi Tujuan</th><td id="detailKiriSekolahTujuan"></td></tr>
                    <tr>
                        <th>Tanggal Input</th>
                        <td id="detailKiriTanggal"></td>
                    </tr>
                    <tr>
                        <th>Berkas Scan</th>
                        <td>
                            <button type="button" class="btn btn-info btn-sm" id="detailKiriBerkasScan" style="display: none;" onclick="showBerkasModal(document.getElementById('detailKiriNomorUsulan').textContent.trim())">
                                <i class="fas fa-file"></i> Lihat
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-secondary btn-sm-custom" onclick="hideDetailKiri()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    
    <!-- Tabel Kanan -->
    <div class="col-md-6">
        <div class="filter-section">
            <label class="text-primary"><i class="fas fa-info-circle"></i> 02: Usulan Terkirim</label>
            <form method="get" id="formStatus02" class="d-flex flex-wrap gap-2">
                <input type="text" name="search_02" class="form-control filter-input" 
                    placeholder="Filter Nama GTK" value="<?= esc($search02 ?? '') ?>" autocomplete="off" style="flex:2;">
                <select name="status_filter" class="form-control" style="flex:1;" onchange="this.form.submit()">
                    <option value="">Status</option>
                    <option value="Terkirim" <?= ($statusFilter ?? '') == 'Terkirim' ? 'selected' : '' ?>>Terkirim</option>
                    <option value="Lengkap" <?= ($statusFilter ?? '') == 'Lengkap' ? 'selected' : '' ?>>Lengkap</option>
                    <option value="TdkLengkap" <?= ($statusFilter ?? '') == 'TdkLengkap' ? 'selected' : '' ?>>TdkLengkap</option>
                </select>
                <select name="perPage" class="form-control w-auto" style="flex:1;" onchange="this.form.submit()">
                    <option value="10" <?= ($perPage ?? 10) == 10 ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= ($perPage ?? 10) == 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= ($perPage ?? 10) == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= ($perPage ?? 10) == 100 ? 'selected' : '' ?>>100</option>
                </select>
                <noscript><button type="submit" class="btn btn-primary btn-sm">Cari</button></noscript>
            </form>
        </div>

        <div class="table-responsive">
            <table id="tableStatus02" class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Nama GTK</th>                        
                        <th>Sekolah Asal</th>
                        <th>Jenis Usulan</th>                   
                        <th>Aksi</th>
                    </tr>
                </thead>
                <?php
                    $jenisMap = [
                        'mutasi_tetap' => 'MUTASI',
                        'nota_dinas' => 'NOTA DINAS',
                        'perpanjangan_nota_dinas' => 'PERPANJANGAN ND'
                    ];
                ?>
                <tbody>
                <?php if (count($status02Usulan) === 0): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">Tidak ada usulan</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($status02Usulan as $usulan): ?>
                        <tr class="table-row">
                            <td><?= $usulan['guru_nama'] ?></td>
                            <td><?= $usulan['sekolah_asal'] ?></td>
                            <td align="center"><?= $jenisMap[$usulan['jenis_usulan']] ?? $usulan['jenis_usulan'] ?>
                                <?php if (isset($usulan['status_usulan_cabdin']) && !empty($usulan['status_usulan_cabdin'])): ?><br />
                                    <span class="badge 
                                        <?= $usulan['status_usulan_cabdin'] === 'Terkirim' ? 'badge-primary' : ($usulan['status_usulan_cabdin'] === 'Lengkap' ? 'badge-success' : 'badge-danger') ?>">
                                        <?= $usulan['status_usulan_cabdin'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Belum Dikirim</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="showDetail(<?= htmlspecialchars(json_encode([
                                    'nomor_usulan' => $usulan['nomor_usulan'],
                                    'guru_nama' => $usulan['guru_nama'],
                                    'guru_nip' => $usulan['guru_nip'],
                                    'sekolah_asal' => $usulan['sekolah_asal'],
                                    'status_usulan_cabdin' => $usulan['status_usulan_cabdin'] ?? 'Belum Dikirim',
                                    'jenis_usulan'   => $jenisMap[$usulan['jenis_usulan']] ?? $usulan['jenis_usulan'],
                                    'catatan' => $usulan['catatan'] ?? '-',
                                    'updated_at' => $usulan['updated_at'],
                                    'dokumen_rekomendasi' => $usulan['dokumen_rekomendasi'] ?? null,
                                ])) ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>

            </table>
        </div>
        <?php if (!empty($pager)) : ?>
            <div class="pagination-container">
                <?= $pager->links('page_status02', 'default_full', ['search_02' => $search02 ?? '', 'perPage' => $perPage ?? 10, 'status_filter' => $statusFilter ?? '']) ?>
            </div>
        <?php endif; ?>

        <!-- Detail Data -->
        <div id="detailData" class="mt-4 detail-container" style="display: none;">
            <label class="text-primary"><i class="fas fa-info-circle"></i> Detail Usulan</label>
            <table class="table table-bordered detail-table">
                <tbody>
                    <tr>
                        <th>Nomor Usulan</th>
                        <td id="detailNomorUsulan"></td>
                    </tr>
                    <tr><th>Jenis Usulan</th><td id="detailJenisUsulan"></td></tr>
                    <tr>
                        <th>Nama GTK</th>
                        <td id="detailNamaGuru"></td>
                    </tr>
                    <tr>
                        <th>NIP</th>
                        <td id="detailNIP"></td>
                    </tr>
                    <tr>
                        <th>Sekolah Asal</th>
                        <td id="detailSekolahAsal"></td>
                    </tr>
                    <tr>
                        <th>Rekomendasi (PDF)</th>
                        <td>
                            <a id="viewPdfLink" href="#" target="_blank" class="btn btn-info  btn-sm">
                                <i class="fas fa-file-pdf"></i> Lihat
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Terkirim</th>
                        <td id="detailTanggalKirim"></td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td id="catatan"></td>
                    </tr>
                </tbody>
            </table>
            <!-- Status Usulan -->
            <div class="status-container mt-4 p-3 border rounded" id="statusContainer">
                <p id="status_usulan_cabdin" class="status-note text-center fw-bold"></p>
            </div>

            <button class="btn btn-secondary mt-3 btn-sm-custom" onclick="hideDetail()">
                <i class="fas fa-times"></i>
            </button>
        </div>

    </div>
</div>

<!-- Modal Daftar Berkas -->
<div class="modal fade" id="modalBerkas" tabindex="-1" aria-labelledby="modalBerkasLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalBerkasLabel"> Daftar Berkas Scan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>#</th>
              <th>Nama Berkas</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="berkasList">
            <tr><td colspan="3" class="text-center">Memuat data...</td></tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm-custom" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>


<script>
function showBerkasModal(nomorUsulan) {
    document.getElementById('berkasList').innerHTML = `<tr><td colspan="3" class="text-center">Memuat data...</td></tr>`;

    fetch(`/usulan/getDriveLinks/${nomorUsulan}`)
        .then(response => response.json())
        .then(responseData => {
            // Ambil jenis_usulan dari hidden input detail kiri
            let jenisUsulan = document.getElementById('hiddenJenisUsulanKiri').value || 'mutasi_tetap';
            if (!responseData || !responseData.data || responseData.data.length === 0) {
                document.getElementById('berkasList').innerHTML = `<tr><td colspan="3" class="text-center text-danger">Tidak ada data berkas</td></tr>`;
                return;
            }

            // Default (Mutasi Tetap)
            let berkasLabels = [
                "Surat Pengantar dari Cabang Dinas Asal",
                "Surat Pengantar dari Kepala Sekolah",
                "Surat Permohonan Pindah Tugas Bermaterai (Ditujukan Untuk Kepala Dinas)",
                "Surat Permohonan Pindah Tugas Bermaterai (Ditujukan Untuk Kepala BKA)",
                "Surat Permohonan Pindah Tugas Bermaterai (Ditujukan Untuk Gubernur cq Sekda Aceh)",
                "Rekomendasi Kepala Sekolah Melepas Lengkap dengan Analisis",
                "Rekomendasi Melepas dari Pengawas Sekolah (Optional)",
                "Rekomendasi Melepas dari Kepala Cabang Dinas Kab/Kota",
                "Rekomendasi Kepala Sekolah Menerima Lengkap dengan Analisis",
                "Rekomendasi Menerima dari Pengawas Sekolah (Optional)",
                "Rekomendasi Menerima dari Kepala Cabang Dinas Kab/Kota",
                "Analisis Jabatan (Anjab) dari sekolah melepas dan sekolah menerima",
                "Surat Formasi GTK dari Sekolah Asal",
                "Foto Copy SK 80% dan SK Terakhir di Legalisir",
                "Foto Copy Karpeg dilegalisir",
                "Surat Keterangan tidak Pernah di Jatuhi Hukuman Disiplin",
                "Surat Keterangan Bebas Temuan Inspektorat (Optional)",
                "Surat Keterangan Bebas Tugas Belajar/Izin Belajar",
                "Daftar Riwayat Hidup/ Riwayat Pekerjaan",
                "Surat Tugas Suami dan Foto Copy Buku Nikah (Optional)",
                "SKP 2 Tahun Terakhir"
            ];
            let activeIndexes = [...Array(21).keys()];
            let optionalIndexes = [6, 9, 16, 19];


            // Nota Dinas → 10 berkas wajib semua
            if (jenisUsulan === 'nota_dinas') {
                berkasLabels[0]  = "Surat Pengantar dari Cabdin Asal";
                berkasLabels[2]  = "Permohonan Nota Dinas Bermaterai Ditujukan Kepada Kepala Dinas Pendidikan Aceh";
                berkasLabels[5]  = "Rekomendasi Kepsek Melepas + Analisis Kebutuhan Guru";
                berkasLabels[7]  = "Rekomendasi Cabdin Melepas";
                berkasLabels[8]  = "Rekomendasi Kepsek Menerima + Analisis Kebutuhan Guru";
                berkasLabels[10] = "Rekomendasi Cabdin Menerima";
                berkasLabels[13] = "Fotokopi SK 80% dan SK Terakhir";
                berkasLabels[17] = "Surat Keterangan Bebas Tugas Belajar dari Kepsek";
                berkasLabels[18] = "SKP 1 Tahun Terakhir";
                berkasLabels[19] = "Surat Izin Suami/Istri & Buku Nikah";

                activeIndexes = [0,2,5,7,8,10,13,17,18,19];
                optionalIndexes = []; // ✅ semua wajib
            }


            // Perpanjangan Nota Dinas
            else if (jenisUsulan === 'perpanjangan_nota_dinas') {
                berkasLabels[0]  = "Surat Pengantar dari Cabdin (sesuai lokasi ND)";
                berkasLabels[1]  = "Surat Keterangan Aktif dari Kepsek (sesuai ND)";
                berkasLabels[19] = "Lampiran Nota Dinas Sebelumnya (upload)";
                activeIndexes = [0,1,19];
                optionalIndexes = [];
            }


            let berkasList = document.getElementById('berkasList');
            berkasList.innerHTML = "";

            responseData.data.forEach((berkas, index) => {
                // Skip index non-aktif
                if (!activeIndexes.includes(index)) return;

                let driveLink = berkas.drive_link ? berkas.drive_link : "#";
                let berkasNama = berkasLabels[index] || `Berkas ${index + 1}`;
                let button = '';

                if (!berkas.drive_link) {
                    if (!optionalIndexes.includes(index)) {
                        button = `<span class="badge bg-danger">Kosong</span>`;
                    } else {
                        button = ""; // Tidak tampilkan jika opsional & kosong
                    }
                } else {
                    button = `<a href="${driveLink}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Lihat</a>`;
                }

                berkasList.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${berkasNama}</td>
                        <td>${button}</td>
                    </tr>
                `;
            });

        })
        .catch(error => {
            document.getElementById('berkasList').innerHTML = `<tr><td colspan="3" class="text-center text-danger">Gagal memuat data</td></tr>`;
        });

    let modal = new bootstrap.Modal(document.getElementById("modalBerkas"));
    modal.show();
}


</script>



<script>
    /*
    function filterTable(tableId, searchValue) {
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tr');
        const value = searchValue.toLowerCase();

        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let match = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j].textContent.toLowerCase().indexOf(value) > -1) {
                    match = true;
                    break;
                }
            }

            rows[i].style.display = match ? '' : 'none';
        }
    }*/
</script>

<script>
    function showDetailKiri(data) {
        document.getElementById('detailKiriNomorUsulan').textContent = data.nomor_usulan || '-';
        document.getElementById('detailKiriJenisUsulan').textContent = data.jenis_usulan || '-';
        // Simpan jenis_usulan asli (mutasi_tetap / nota_dinas / perpanjangan_nota_dinas)
        document.getElementById('hiddenJenisUsulanKiri').value = data.jenis_usulan_raw || data.jenis_usulan;

        document.getElementById('detailKiriNamaGuru').textContent = data.guru_nama || '-';
        document.getElementById('detailKiriNIP').textContent = data.guru_nip || '-';
        document.getElementById('detailKiriNIK').textContent = data.guru_nik || '-';
        document.getElementById('detailKiriSekolahAsal').textContent = data.sekolah_asal || '-';
        document.getElementById('detailKiriSekolahTujuan').textContent = data.sekolah_tujuan || '-';
        
        // Format tanggal input
        document.getElementById('detailKiriTanggal').textContent = data.created_at
            ? new Date(data.created_at).toLocaleDateString('id-ID')
            : '-';

        // Link Berkas Scan
        const btn = document.getElementById('detailKiriBerkasScan');
        if (data.nomor_usulan) {
            btn.style.display = 'inline-block';
        } else {
            btn.style.display = 'none';
        }

        // Tampilkan detail box
        document.getElementById('detailDataKiri').style.display = 'block';
        window.scrollTo(0, document.getElementById('detailDataKiri').offsetTop);
    }

    function hideDetailKiri() {
        document.getElementById('detailDataKiri').style.display = 'none';
    }
</script>

<script>
    function showDetail(data) {
        document.getElementById('detailNomorUsulan').textContent = data.nomor_usulan || '-';
        document.getElementById('detailJenisUsulan').textContent = data.jenis_usulan || '-';        
        document.getElementById('detailNamaGuru').textContent = data.guru_nama || '-';
        document.getElementById('detailNIP').textContent = data.guru_nip || '-';
        document.getElementById('detailSekolahAsal').textContent = data.sekolah_asal || '-';
        document.getElementById('detailTanggalKirim').textContent = data.updated_at
            ? new Date(data.updated_at).toLocaleDateString('id-ID')
            : '-';
        document.getElementById('catatan').textContent = data.catatan || '-';

        const statusElement = document.getElementById('status_usulan_cabdin');
        const statusContainer = document.getElementById('statusContainer');
        statusElement.textContent = '';
        statusContainer.className = 'status-container';

        if (data.status_usulan_cabdin === 'Lengkap') {
            statusContainer.classList.add('success');
            statusElement.innerHTML = `
                <span class="danger"><i class="fas fa-check-circle text-success"></i> Lengkap</span>
                <br>
                <span class="status-subnote">Dilanjutkan ke proses telaah usulan oleh Kabid. GTK</span>
            `;
        } else if (data.status_usulan_cabdin === 'TdkLengkap') {
            statusContainer.classList.add('danger');
            statusElement.innerHTML = `
                <span class="danger"><i class="fas fa-times-circle text-danger"></i> TdkLengkap</span>
                <br>
                <span class="status-subnote">Revisi Perbaikan di Menu Penerimaan Usulan</span>
            `;
        } else if (data.status_usulan_cabdin === 'Terkirim') {
            statusContainer.classList.add('pending');
            statusElement.innerHTML = `
                <span class="danger"><i class="fas fa-paper-plane"></i> Terkirim</span>
                <br>
                <span class="status-subnote">Menunggu Verifikasi Dinas Provinsi</span>
            `;
        } else {
            statusElement.textContent = 'Status Tidak Diketahui';
        }

        const pdfLink = document.getElementById('viewPdfLink');
        if (data.dokumen_rekomendasi) {
            pdfLink.href = '/uploads/rekomendasi/' + data.dokumen_rekomendasi;
            pdfLink.style.display = 'inline-block';
        } else {
            pdfLink.style.display = 'none';
        }

        document.getElementById('detailData').style.display = 'block';
        window.scrollTo(0, document.getElementById('detailData').offsetTop);
    }


    function hideDetail() {
        document.getElementById('detailData').style.display = 'none';
    }
    //SweetAlert
    document.addEventListener("DOMContentLoaded", function () {
        // SweetAlert untuk notifikasi sukses
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                title: 'Berhasil!',
                text: '<?= session()->getFlashdata('success'); ?>',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                title: 'Gagal!',
                text: '<?= session()->getFlashdata('error'); ?>',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    });

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Debounce untuk input search tabel kiri
        const inputSearch01 = document.querySelector('input[name="search_01"]');
        if (inputSearch01) {
            inputSearch01.addEventListener('keyup', debounce(function() {
                this.form.submit();
            }, 500));
        }

        // Debounce untuk input search tabel kanan
        const inputSearch02 = document.querySelector('input[name="search_02"]');
        if (inputSearch02) {
            inputSearch02.addEventListener('keyup', debounce(function() {
                this.form.submit();
            }, 500));
        }
    });
</script>


<?= $this->endSection(); ?>
