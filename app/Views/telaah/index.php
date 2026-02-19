<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-4 text-gray-800"><i class="fas fa-file-alt"></i> Telaah Usulan</h1>
<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('message'); ?>
        </button>
    </div>
<?php endif; ?>
<!-- Notifikasi untuk pesan flash -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success'); ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error'); ?>
    </div>
<?php endif; ?>
<div class="row">
    <!-- Tabel Menunggu Telaah -->
    <div class="col-md-6">
        <div class="filter-section">
            <label class="text-primary"><i class="fas fa-info-circle"></i> 04.1. Menunggu Telaah</label>            
            <div class="d-flex">
                <input type="text" id="filterMenunggu" class="form-control filter-input" placeholder="Filter Nama Guru" onkeyup="filterTable('tableMenunggu', this.value)">
                <form method="get" id="perPageFormMenunggu">
                    <select name="perPage" class="form-control" onchange="document.getElementById('perPageFormMenunggu').submit();">
                        <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table id="tableMenunggu" class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Nama GTK</th>                  
                        <th>Sekolah Asal</th>
                        <th>Jenis Usulan</th>                        
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usulanMenunggu)): ?>
                        <?php foreach ($usulanMenunggu as $usulan): ?>
                            <tr>
                                <td><?= $usulan['guru_nama']; ?></td>                      
                                <td><?= $usulan['sekolah_asal']; ?></td>
                                <td>
                                    <?php
                                        $jenisMap = [
                                            'mutasi_tetap' => 'MUTASI',
                                            'nota_dinas' => 'NOTA DINAS',
                                            'perpanjangan_nota_dinas' => 'PERPANJANGAN ND'
                                        ];
                                        echo $jenisMap[$usulan['jenis_usulan']] ?? strtoupper($usulan['jenis_usulan']);
                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="showDetail(<?= htmlspecialchars(json_encode($usulan)) ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Data belum tersedia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="pagination-container">
            <?= $pagerMenunggu->links('usulanMenunggu', 'default_full'); ?>
        </div>
        <!-- Detail Data -->
        <div id="detailData" class="detail-container" style="display: none;">
            <label class="text-primary"><i class="fas fa-info-circle"></i> 04.1 Detail Usulan</label>            
            <table class="table table-responsive table-bordered detail-table">
                <tbody>
                    <tr>
                        <th>Nomor Usulan</th>
                        <td id="detailNomorUsulan"></td>
                    </tr>
                    <tr>
                        <th>Jenis Usulan</th>
                        <td id="detailJenisUsulan"></td>
                    </tr>                    
                    <input type="hidden" id="hiddenJenisUsulanTelaah">
                    <tr>
                        <th>Nama GTK</th>
                        <td id="detailNamaGuru"></td>
                    </tr>
                    <tr>
                        <th>NIP</th>
                        <td id="detailNIP"></td>
                    </tr>
                    <tr>
                        <th>NIK</th>
                        <td id="detailNIK"></td>
                    </tr>
                    <tr>
                        <th>Sekolah Asal</th>
                        <td id="detailSekolahAsal"></td>
                    </tr>
                    <tr>
                        <th>Sekolah / Instansi Tujuan</th>
                        <td id="detailSekolahTujuan"></td>
                    </tr>
                    <tr>
                        <th>Alasan</th>
                        <td id="detailAlasan"></td>
                    </tr>
                    <tr>
                        <th>Berkas Usulan</th>
                        <td>
                            <button id="lihatBerkasBtn" class="btn btn-info btn-sm">
                                <i class="fas fa-file-pdf"></i> Lihat
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Input Usulan</th>
                        <td id="detailTanggalInput"></td>
                    </tr> 
                    <tr>
                        <th colspan="2" class="text-left text-primary custom-bg"><i class="fas fa-info-circle"></i> 
                            Rekomendasi Cabang Dinas
                        </th>
                    </tr>                                     
                    <tr>
                        <th>Nama Cabang Dinas</th>
                        <td id="detailNamaCabang"></td>
                    </tr>
                    <tr>
                        <th>Dokumen Rekomendasi</th>
                        <td>
                            <a id="dokumenRekomendasiLink" href="#" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-file-pdf"></i> Lihat
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Operator</th>
                        <td id="detailOperator"></td>
                    </tr>
                    <tr>
                        <th>No. HP</th>
                        <td id="detailNoHP"></td>
                    </tr>
                    <tr>
                        <th>Tanggal Dikirimkan</th>
                        <td id="detailTanggalDikirim"></td>
                    </tr> 
                    <tr>
                        <th colspan="2" class="text-left text-primary custom-bg"><i class="fas fa-info-circle"></i> 
                            Status Verifikasi Dinas Pendidikan
                        </th>
                    </tr>                     
                    <tr>
                        <th>Status Verifikasi</th>
                        <td id="detailStatusVerifikasi"></td>
                    </tr>    
                    <tr>
                        <th>Tanggal Verifikasi Berkas</th>
                        <td id="detailTanggalUpdate"></td>
                    </tr>                                                         
                    <tr>
                        <th>Catatan</th>
                        <td id="detailCatatan"></td>
                    </tr>
                    <?php if (!$readonly): ?>
                    <!-- Tombol Aksi Verifikasi -->
                    <tr>
                        <td colspan="2" class="text-center">
                            <p class="mb-2 bg-warning-custom text-white p-2 text-center"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Setelah dilakukan telaah secara mendalam terkait usulan mutasi guru yang dimaksud,<br \>maka usulan ini dinyatakan...</p>
                            <button class="btn btn-danger btn-sm-custom me-2" onclick="telaahDokumen('Ditolak')">
                                <i class="fas fa-times"></i> Ditolak
                            </button>
                            <button class="btn btn-sm-custom btn-success" onclick="telaahDokumen('Disetujui')">
                                <i class="fas fa-check"></i> Disetujui
                            </button>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>

            </table>
            <button class="btn btn-secondary btn-sm-custom mt-2" onclick="hideDetail()">
                <i class="fas fa-window-close"></i>
            </button>
        </div>
                
        <!-- Modal Daftar Berkas -->
        <div class="modal fade" id="berkasModal" tabindex="-1" aria-labelledby="berkasModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="berkasModalLabel">Daftar Berkas Scan</h5>
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
    </div>

    <!-- Tabel Sudah Ditelaah -->
    <div class="col-md-6">
        <div class="filter-section">
            <label class="text-primary"><i class="fas fa-info-circle"></i> 04.2. Sudah Ditelaah</label>              
            <div class="d-flex">
                <input type="text" id="filterDitelaah" class="form-control filter-input" placeholder="Filter Nama Guru" onkeyup="filterTable('tableDitelaah', this.value)">
                <!-- Dropdown untuk Filter Status -->
                <select id="filterStatusTelaah" class="form-control" onchange="filterStatus()">
                    <option value="">Status</option>
                    <option value="Disetujui">Disetujui</option>
                    <option value="Ditolak">Ditolak</option>
                </select>
                <form method="get" id="perPageFormDitelaah">
                    <select name="perPage" class="form-control" onchange="document.getElementById('perPageFormDitelaah').submit();">
                        <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table id="tableDitelaah" class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Nama GTK</th>
                        <th>Sekolah Asal</th>
                        <th>Jenis Usulan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usulanDitelaah)): ?>
                        <?php foreach ($usulanDitelaah as $usulan): ?>
                            <tr>
                                <td><?= $usulan['guru_nama']; ?></td>
                                <td><?= $usulan['sekolah_asal']; ?></td>
                                <td align="center">
                                    <?php
                                        $jenisMap = [
                                            'mutasi_tetap' => 'MUTASI',
                                            'nota_dinas' => 'NOTA DINAS',
                                            'perpanjangan_nota_dinas' => 'PERPANJANGAN ND'
                                        ];
                                        echo $jenisMap[$usulan['jenis_usulan']] ?? strtoupper($usulan['jenis_usulan']);?> <br />                                    
                                    <span class="badge <?= $usulan['status_telaah'] === 'Disetujui' ? 'badge-success' : 'badge-danger'; ?>">
                                        <?= $usulan['status_telaah']; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="showDetailKanan(<?= htmlspecialchars(json_encode($usulan)) ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Data belum tersedia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="pagination-container">
            <?= $pagerDitelaah->links('usulanDitelaah', 'default_full'); ?>
        </div>
        <!-- Detail Data Tabel Kanan -->
        <div id="detailDataKanan" class="detail-container" style="overflow-x: auto;">
            <label class="text-primary"><i class="fas fa-info-circle"></i> 04.2 Detail Telaah Usulan</label>            
            <table class="table table-responsive table-bordered detail-table">
                <tbody>
                    <tr>
                        <th>Nomor Usulan</th>
                        <td id="detailNomorUsulanKanan"></td>
                    </tr>
                    <tr>
                        <th>Jenis Usulan</th>
                        <td id="detailJenisUsulanKanan"></td>
                    </tr>                    
                    <tr>
                        <th>Nama GTK</th>
                        <td id="detailNamaGuruKanan"></td>
                    </tr>
                    <tr>
                        <th>NIP</th>
                        <td id="detailNIPKanan"></td>
                    </tr>
                    <tr>
                        <th>NIK</th>
                        <td id="detailNIKKanan"></td>
                    </tr>
                    <tr>
                        <th>Sekolah Asal</th>
                        <td id="detailSekolahAsalKanan"></td>
                    </tr>
                    <tr>
                        <th>Sekolah Tujuan</th>
                        <td id="detailSekolahTujuanKanan"></td>
                    </tr>
                    <tr>
                        <th>Alasan</th>
                        <td id="detailAlasanKanan"></td>
                    </tr>
                    <tr>
                        <th>Tanggal Input Usulan</th>
                        <td id="detailTanggalInputKanan"></td>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-left text-primary custom-bg"><i class="fas fa-info-circle"></i> 
                            Rekomendasi Cabang Dinas</th>
                    </tr>                    
                    <tr>
                        <th>Nama Cabang Dinas</th>
                        <td id="detailNamaCabangKanan"></td>
                    </tr>
                    <tr>
                        <th>Dokumen Rekomendasi</th>
                        <td>
                            <a id="dokumenRekomendasiLinkKanan" href="#" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-file-pdf"></i> Lihat
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Operator</th>
                        <td id="detailOperatorKanan"></td>
                    </tr>
                    <tr>
                        <th>No. HP</th>
                        <td id="detailNoHPKanan"></td>
                    </tr>
                    <tr>
                        <th>Tanggal Dikirimkan</th>
                        <td id="detailTanggalDikirimKanan"></td>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-left text-primary custom-bg"><i class="fas fa-info-circle"></i> 
                            Status Telaah Kabid GTK</th>
                    </tr>                       
                    <tr>
                        <th>Tanggal Telaah</th>
                        <td id="detailTanggalTelaahKanan"></td>
                    </tr>
                    <tr>
                        <th>Catatan Telaah</th>
                        <td id="detailCatatanTelaahKanan"></td>
                    </tr>
                </tbody>
            </table>

            <!-- Status Telaah Container -->
            <div class="mt-3 p-3 border rounded status-container" id="statusContainerKanan">
                <h6 class="status-title">Status Telaah:</h6>
                <p id="statusTelaahNoteKanan" class="fw-bold text-center status-note"></p>
				<?php if (!$readonly): ?>
                  <button class="btn btn-danger btn-sm-custom mt-2" 
                          id="btnBatalkanTelaah" 
                          style="display:none;" 
                          onclick="batalkanTelaah(this.getAttribute('data-nomor'))">
                      <i class="fas fa-undo"></i> Batalkan Telaah
                  </button>
                <?php endif; ?>
            </div>

            <button class="btn btn-secondary btn-sm-custom mt-2" onclick="hideDetailKanan()">
                <i class="fas fa-window-close"></i>
            </button>

        </div>


    </div>
</div>


<script>

    document.addEventListener("DOMContentLoaded", function () {
        let isProcessing = false; // untuk mencegah double klik
        let lihatBerkasBtn = document.getElementById("lihatBerkasBtn");

        if (lihatBerkasBtn) {
            lihatBerkasBtn.addEventListener("click", function () {
                let nomorUsulan = document.getElementById("detailNomorUsulan").textContent;

                console.log("[DEBUG] Nomor usulan dari detail:", nomorUsulan);

                if (!nomorUsulan) {
                //  console.error("[ERROR] Nomor usulan tidak ditemukan!");
                    alert("Nomor usulan tidak tersedia.");
                    return;
                }

                showBerkasModal(nomorUsulan);
            });
        }
    });


    function filterTable(tableId, searchValue) {
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tr');
        const value = searchValue.toLowerCase();

        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let match = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j].textContent.toLowerCase().includes(value)) {
                    match = true;
                    break;
                }
            }

            rows[i].style.display = match ? '' : 'none';
        }
    }

function showDetail(data) {
    // BAGIAN 1: Informasi Usulan Guru
    document.getElementById('detailNomorUsulan').textContent = data.nomor_usulan || '-';
    document.getElementById('detailJenisUsulan').textContent =
        data.jenis_usulan === 'mutasi_tetap' ? 'MUTASI' :
        data.jenis_usulan === 'nota_dinas' ? 'NOTA DINAS' :
        data.jenis_usulan === 'perpanjangan_nota_dinas' ? 'PERPANJANGAN ND' :
        (data.jenis_usulan || '-');

    // simpan jenis_usulan asli untuk modal
    document.getElementById('hiddenJenisUsulanTelaah').value = data.jenis_usulan || 'mutasi_tetap';
 
    document.getElementById('detailNamaGuru').textContent = data.guru_nama || '-';
    document.getElementById('detailNIP').textContent = data.guru_nip || '-';
    document.getElementById('detailNIK').textContent = data.guru_nik || '-';
    document.getElementById('detailSekolahAsal').textContent = data.sekolah_asal || '-';
    document.getElementById('detailSekolahTujuan').textContent = data.sekolah_tujuan || '-';
    document.getElementById('detailAlasan').textContent = data.alasan || '-';
    document.getElementById('detailTanggalInput').textContent = data.created_at ? new Date(data.created_at).toLocaleDateString('id-ID') : '-';
/*
        const berkasLink = document.getElementById('berkasUsulanLink');
        if (data.google_drive_link) {
            berkasLink.href = data.google_drive_link;
            berkasLink.style.display = 'inline-block';
        } else {
            berkasLink.style.display = 'none';
        }
*/
    // BAGIAN 2: Informasi Rekomendasi Cabang Dinas
    document.getElementById('detailNamaCabang').textContent = data.nama_cabang || '-';
    document.getElementById('detailOperator').textContent = data.operator || '-';
    document.getElementById('detailNoHP').textContent = data.no_hp || '-';
    document.getElementById('detailTanggalDikirim').textContent = data.tanggal_dikirim ? new Date(data.tanggal_dikirim).toLocaleDateString('id-ID') : '-';

        const dokumenLink = document.getElementById('dokumenRekomendasiLink');
        if (data.dokumen_rekomendasi) {
            dokumenLink.href = `/uploads/rekomendasi/${data.dokumen_rekomendasi}`;
            dokumenLink.style.display = 'inline-block';
        } else {
            dokumenLink.style.display = 'none';
        }
    // BAGIAN 3: Informasi Verifikasi
    document.getElementById('detailStatusVerifikasi').textContent = data.status_usulan_cabdin || '-';
    document.getElementById('detailTanggalUpdate').textContent = data.tanggal_update ? new Date(data.tanggal_update).toLocaleDateString('id-ID') : '-';
    document.getElementById('detailCatatan').textContent = data.catatan || '-';
    
    document.getElementById('detailData').style.display = 'block';
    window.scrollTo(0, document.getElementById('detailData').offsetTop);
}

    function hideDetail() {
        document.getElementById('detailData').style.display = 'none';
    }

function telaahDokumen(status) {
    const nomorUsulan = document.getElementById('detailNomorUsulan').textContent;

    if (!nomorUsulan) {
        Swal.fire({
            icon: 'error',
            title: 'Nomor Usulan Tidak Ditemukan',
            text: 'Pastikan detail usulan sudah ditampilkan sebelum melanjutkan.',
            confirmButtonText: 'OK',
        });
        return;
    }

    let dialogTitle = '';
    let dialogText = '';
    let inputPlaceholder = '';
    let isInputRequired = false;

    if (status === 'Ditolak') {
        dialogTitle = 'Konfirmasi Penolakan';
        dialogText = `
            <div style="color:black; font-weight:bold; border:2px solid red; padding:5px; margin-top:12px; margin-bottom:12px;">
                PROSES INI TIDAK BISA DIBATALKAN
            </div>
            <div>Anda akan menolak usulan ini. Masukkan alasan penolakan pada kolom berikut!</div>
        `;
        inputPlaceholder = 'Tulis alasan di sini...';
        inputDefaultValue = 'Usulan mutasi tidak memenuhi syarat';
        isInputRequired = true;
    } else if (status === 'Disetujui') {
        dialogTitle = 'Konfirmasi Penerimaan';
        dialogText = 'Anda akan menerima usulan ini. Tambahkan catatan penerimaan (opsional).';
        inputPlaceholder = 'Tulis catatan di sini...';
        inputDefaultValue = 'Usulan mutasi memenuhi syarat'; // Teks awal untuk persetujuan
    }

    Swal.fire({
        title: dialogTitle,
        html: dialogText,
        input: 'textarea',
        inputPlaceholder: inputPlaceholder,
        inputValue: inputDefaultValue, // Menyisipkan teks awal
        inputAttributes: {
            'aria-label': inputPlaceholder,
        },
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (isInputRequired && !value) {
                return 'Catatan wajib diisi!';
            }
        },
    }).then((result) => {
        if (result.isConfirmed) {
            const catatan = result.value || '';
            kirimHasilTelaah(nomorUsulan, status, catatan);
        }
    });

}

function kirimHasilTelaah(nomorUsulan, status, catatan) {
    const data = {
        nomor_usulan: nomorUsulan,
        status_telaah: status,
        catatan_telaah: catatan,
    };

    fetch('/telaah/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(data),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Gagal menghubungi server.');
            }
            return response.json();
        })
        .then((data) => {
            if (data.message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Proses Berhasil',
                    text: data.message,
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Proses Gagal',
                    text: data.error || 'Gagal memperbarui hasil telaah.',
                });
            }
        })
        .catch((error) => {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: error.message,
            });
        });
}
function showDetailKanan(data) {
    // BAGIAN 1: Informasi Usulan Guru
    document.getElementById('detailNomorUsulanKanan').textContent = data.nomor_usulan || '-';
    document.getElementById('detailJenisUsulanKanan').textContent =
            data.jenis_usulan === 'mutasi_tetap' ? 'MUTASI' :
            data.jenis_usulan === 'nota_dinas' ? 'NOTA DINAS' :
            data.jenis_usulan === 'perpanjangan_nota_dinas' ? 'PERPANJANGAN ND' :
            (data.jenis_usulan || '-');
    document.getElementById('detailNamaGuruKanan').textContent = data.guru_nama || '-';
    document.getElementById('detailNIPKanan').textContent = data.guru_nip || '-';
    document.getElementById('detailNIKKanan').textContent = data.guru_nik || '-';
    document.getElementById('detailSekolahAsalKanan').textContent = data.sekolah_asal || '-';
    document.getElementById('detailSekolahTujuanKanan').textContent = data.sekolah_tujuan || '-';
    document.getElementById('detailAlasanKanan').textContent = data.alasan || '-';
    document.getElementById('detailTanggalInputKanan').textContent = data.created_at
        ? new Date(data.created_at).toLocaleDateString('id-ID')
        : '-';
/*
    const berkasLink = document.getElementById('berkasUsulanLinkKanan');
    if (data.google_drive_link) {
        berkasLink.href = data.google_drive_link;
        berkasLink.style.display = 'inline-block';
    } else {
        berkasLink.style.display = 'none';
    }
*/
    // BAGIAN 2: Informasi Rekomendasi Cabang Dinas
    document.getElementById('detailNamaCabangKanan').textContent = data.nama_cabang || '-';
    document.getElementById('detailOperatorKanan').textContent = data.operator || '-';
    document.getElementById('detailNoHPKanan').textContent = data.no_hp || '-';
    document.getElementById('detailTanggalDikirimKanan').textContent = data.tanggal_dikirim
        ? new Date(data.tanggal_dikirim).toLocaleDateString('id-ID')
        : '-';

    const dokumenLink = document.getElementById('dokumenRekomendasiLinkKanan');
    if (data.dokumen_rekomendasi) {
        dokumenLink.href = `/uploads/rekomendasi/${data.dokumen_rekomendasi}`;
        dokumenLink.style.display = 'inline-block';
    } else {
        dokumenLink.style.display = 'none';
    }

    // BAGIAN 3: Status Telaah Kabid GTK
    document.getElementById('detailTanggalTelaahKanan').textContent = data.updated_at_telaah
        ? new Date(data.updated_at_telaah).toLocaleDateString('id-ID')
        : '-';
    document.getElementById('detailCatatanTelaahKanan').textContent = data.catatan_telaah || '-';

    // BAGIAN STATUS: Menampilkan informasi status telaah
    const statusNoteElement = document.getElementById('statusTelaahNoteKanan');
    const statusContainer = document.getElementById('statusContainerKanan');
     const btnBatalkan = document.getElementById('btnBatalkanTelaah');

    if (data.status_telaah === 'Disetujui' || data.status_telaah === 'Ditolak') {
        // Tampilkan status (berbeda ikon/badge untuk masing-masing)
        if (data.status_telaah === 'Disetujui') {
            statusNoteElement.innerHTML = `<i class="fas fa-check-circle text-success"></i> Disetujui`;
            statusContainer.classList.add('success');
            statusContainer.classList.remove('danger');
        } else {
            statusNoteElement.innerHTML = `<i class="fas fa-times-circle text-danger"></i> Ditolak`;
            statusContainer.classList.add('danger');
            statusContainer.classList.remove('success');
        }

        // 🔹 Tampilkan tombol Batalkan + simpan nomor usulan
        if (btnBatalkan) {
            btnBatalkan.style.display = 'inline-block';
            btnBatalkan.setAttribute("data-nomor", data.nomor_usulan);
        }

    } else {
        statusNoteElement.innerHTML = `<i class="fas fa-question-circle text-muted"></i> Tidak Diketahui`;
        statusContainer.classList.remove('success', 'danger');

        if (btnBatalkan) {
            btnBatalkan.style.display = 'none';
            btnBatalkan.removeAttribute('data-nomor');
        }
    }

    document.getElementById('detailDataKanan').style.display = 'block';
    window.scrollTo(0, document.getElementById('detailDataKanan').offsetTop);
}

function hideDetailKanan() {
    document.getElementById('detailDataKanan').style.display = 'none';
}
function filterStatus() {
    const filterValue = document.getElementById('filterStatusTelaah').value.toLowerCase();
    const table = document.getElementById('tableDitelaah');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const statusCell = rows[i].getElementsByTagName('td')[3]; // Kolom ke-4 (Status)
        if (statusCell) {
            const statusText = statusCell.textContent.toLowerCase();
            rows[i].style.display =
                filterValue === '' || statusText.includes(filterValue) ? '' : 'none';
        }
    }
}
function showBerkasModal(nomorUsulan) {
    document.getElementById('berkasList').innerHTML =
        `<tr><td colspan="3" class="text-center">Memuat data...</td></tr>`;

    fetch(`/usulan/getDriveLinks/${nomorUsulan}`)
        .then(response => response.json())
        .then(responseData => {
            if (!responseData || !responseData.data || responseData.data.length === 0) {
                document.getElementById('berkasList').innerHTML =
                    `<tr><td colspan="3" class="text-center text-danger">Tidak ada data berkas</td></tr>`;
                return;
            }
            // default: Mutasi Tetap
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


            // ambil jenis_usulan dari hidden input
            let jenisUsulan = document.getElementById('hiddenJenisUsulanTelaah').value || 'mutasi_tetap';

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


            // mapping untuk perpanjangan nota dinas
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
                if (!activeIndexes.includes(index)) return;

                let driveLink = berkas.drive_link ? berkas.drive_link : "#";
                let berkasNama = berkasLabels[index] || `Berkas ${index + 1}`;
                let button = "";

                if (!berkas.drive_link) {
                    if (!optionalIndexes.includes(index)) {
                        button = `<span class="badge bg-danger">Kosong</span>`;
                    }
                } else {
                    button = `<a href="${driveLink}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat
                              </a>`;
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
            document.getElementById('berkasList').innerHTML =
                `<tr><td colspan="3" class="text-center text-danger">Gagal memuat data</td></tr>`;
        });

    let modal = new bootstrap.Modal(document.getElementById("berkasModal"));
    modal.show();
}
function batalkanTelaah(nomorUsulan) {
    console.log("[DEBUG] Tombol Batalkan ditekan");
    console.log("[DEBUG] Nomor usulan yang dikirim:", nomorUsulan);

    if (!nomorUsulan) {
        console.error("[ERROR] Nomor usulan tidak ada!");
        Swal.fire('Error!', 'Nomor usulan tidak ditemukan.', 'error');
        return;
    }

    Swal.fire({
        title: 'Konfirmasi',
        text: "Apakah Anda yakin ingin membatalkan telaah ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Batalkan'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log("[DEBUG] Kirim request ke server /telaah/batalkan");

            fetch('/telaah/batalkan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ nomor_usulan: nomorUsulan })
            })
            .then(res => {
                console.log("[DEBUG] Response status:", res.status);
                return res.json();
            })
            .then(data => {
                console.log("[DEBUG] Response JSON:", data);

                if (data.success) {
                    Swal.fire('Dibatalkan!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch((err) => {
                console.error("[ERROR] Fetch gagal:", err);
                Swal.fire('Error!', 'Terjadi kesalahan server.', 'error');
            });
        }
    });
}



</script>

<?= $this->endSection(); ?>
