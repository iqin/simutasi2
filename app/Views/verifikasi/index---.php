<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>


<h1 class="h3 mb-4 text-gray-800"><i class="fas fa-file-alt"></i> Verifikasi Dokumen</h1>

<div class="row">
    <!-- Tabel 03: Usulan Menunggu Verifikasi -->
    <div class="col-md-6">
    <div class="filter-section">
        <label class="text-primary"><i class="fas fa-info-circle"></i> 03.1: Menunggu Verifikasi</label>
        <input type="text" id="filterMenunggu" class="form-control filter-input" placeholder="Filter Nama Guru" onkeyup="filterTable('tableMenunggu', this.value)">
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
                            <td colspan="5" class="text-center">Data pada Cabang Dinas tidak ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($pagerMenunggu)) : ?>
            <div class="pagination-container">
                <?= $pagerMenunggu->links('page_status03', 'default_full'); ?>
            </div>
        <?php endif; ?>
        <!-- Detail Usulan -->
        <div id="detailData" class="mt-4 detail-container"  style="display: none;">
            <label class="text-primary"><i class="fas fa-info-circle"></i> 03.1 Detail Usulan</label>
            <table class="table-responsive table-bordered detail-table">
                <tbody>
                    <tr>
                        <th>Nomor Usulan</th>
                        <td id="detailNomorUsulan"></td>
                    </tr>
                    <tr>
                        <th>Jenis Usulan</th>
                        <td id="detailJenisUsulan"></td>
                    </tr>
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
                        <th>Dokumen Rekom</th>
                        <td>
                            <a id="dokumenRekomendasiLink" href="#" target="_blank" class="btn btn-info  btn-sm">
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
                    
                    <?php if (!$readonly): ?>
                    <!-- Tombol Aksi Verifikasi -->
                    <tr>
                        <td colspan="2" class="text-center">
                            <p class="mb-2 bg-warning-custom text-white p-2 text-center"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Pastikan anda untuk melakukan verifikasi kelengkapan<br \>dokumen usulan beserta rekomendasi Cabang Dinas pengusul<br \>sebelum klik tombol dibawah ini.</p>
                            <button class="btn btn-sm-custom btn-danger me-2" onclick="verifikasi('TdkLengkap')">
                                <i class="fas fa-times"></i> TdkLengkap
                            </button>
                            <button class="btn btn-sm-custom btn-success" onclick="verifikasi('Lengkap')">
                                <i class="fas fa-check"></i> Lengkap
                            </button>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <button class="btn btn-secondary btn-sm-custom" onclick="hideDetail()">
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

    <!-- Tabel 04: Usulan Diverifikasi -->
    <div class="col-md-6">
        <div class="filter-section">
            <label class="text-primary"><i class="fas fa-info-circle"></i> 03.2: Usulan Diverifikasi</label>
            <input type="text" id="filterDiverifikasi" class="form-control filter-input" placeholder="Filter Nama Guru" onkeyup="filterTable('tableDiverifikasi', this.value)">
            <!-- Dropdown untuk Filter Status -->
            <select id="filterStatusVerifikasi" class="form-control" onchange="filterStatus()">
                <option value="">Status</option>
                <option value="Lengkap">Lengkap</option>
                <option value="TdkLengkap">TdkLengkap</option>
            </select>
        </div>
        <div class="table-responsive">
            <table id="tableDiverifikasi" class="table table-sm table-striped">
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
                    <?php if (!empty($usulanDiverifikasi)): ?>
                        <?php foreach ($usulanDiverifikasi as $usulan): ?>
                            <tr>
                                <td><?= $usulan['guru_nama']; ?></td>
                                <td><?= $usulan['sekolah_asal']; ?></td>
                                <td align="center"><?= $jenisMap[$usulan['jenis_usulan']] ?? $usulan['jenis_usulan'] ?><br />
                                    <span class="badge <?= $usulan['status_usulan_cabdin'] === 'Lengkap' ? 'badge-success' : 'badge-danger'; ?>">
                                        <?= $usulan['status_usulan_cabdin']; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="showDetailDiverifikasi(<?= htmlspecialchars(json_encode($usulan)) ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data untuk proses diverifikasi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($pagerDiverifikasi)) : ?>
            <div class="pagination-container">
                <?= $pagerDiverifikasi->links('page_status04', 'default_full'); ?>
            </div>
        <?php endif; ?>
        <div id="detailDataDiverifikasi" class="detail-container" style="display: none;" >
        <label class="text-primary"><i class="fas fa-info-circle"></i> 03.2 Detail Usulan</label>
        <table class="table-responsive table-bordered detail-table">
            <tbody>
                <tr>
                    <th>Nomor Usulan</th>
                    <td id="detailNomorUsulanDiverifikasi"></td>
                </tr>
                <tr>
                    <th>Jenis Usulan</th>
                    <td id="detailJenisUsulanDiverifikasi"></td>
                </tr>                
                <tr>
                    <th>Nama GTK</th>
                    <td id="detailNamaGuruDiverifikasi"></td>
                </tr>
                <tr>
                    <th>NIP</th>
                    <td id="detailNIPDiverifikasi"></td>
                </tr>
                <tr>
                    <th>NIK</th>
                    <td id="detailNIKDiverifikasi"></td>
                </tr>
                <tr>
                    <th>Sekolah Asal</th>
                    <td id="detailSekolahAsalDiverifikasi"></td>
                </tr>
                <tr>
                    <th>Sekolah / Instansi Tujuan</th>
                    <td id="detailSekolahTujuanDiverifikasi"></td>
                </tr>
                <tr>
                    <th>Alasan</th>
                    <td id="detailAlasanDiverifikasi"></td>
                </tr>
                <tr>
                    <th>Tanggal Input Usulan</th>
                    <td id="detailTanggalInputDiverifikasi"></td>
                </tr>
                <tr>
                    <th colspan="2" class="text-left text-primary custom-bg"><i class="fas fa-info-circle"></i> Rekomendasi Cabang Dinas</th>
                </tr>
                <tr>
                    <th>Nama Cabang Dinas</th>
                    <td id="detailNamaCabangDiverifikasi"></td>
                </tr>
                <tr>
                    <th>Dokumen Rekomendasi</th>
                    <td>
                        <a id="dokumenRekomendasiLinkDiverifikasi" href="#" target="_blank" class="btn btn-info btn-sm">
                            <i class="fas fa-file-pdf"></i> Lihat
                        </a>
                    </td>
                </tr>
                <tr>
                    <th>Operator</th>
                    <td id="detailOperatorDiverifikasi"></td>
                </tr>
                <tr>
                    <th>No. HP</th>
                    <td id="detailNoHPDiverifikasi"></td>
                </tr>
                <tr>
                    <th>Tanggal Dikirimkan</th>
                    <td id="detailTanggalDikirimDiverifikasi"></td>
                </tr>
                <tr>
                    <th>Catatan Verifikasi</th>
                    <td id="detailCatatanDiverifikasi"></td>
                </tr>
                <tr>
                    <th>Tanggal Diverifikasi</th>
                    <td id="detailTanggalVerifikasiDiverifikasi"></td>
                </tr>

            </tbody>
        </table>

        <div class="status-container mt-4 p-3 border rounded" id="statusContainerDiverifikasi">
            <label id="statusVerifikasiDiverifikasi" class="fw-bold text-center status-note"></label>
        </div>

        <button class="btn btn-sm-custom btn-secondary mt-2" onclick="hideDetailDiverifikasi()">
            <i class="fas fa-window-close"></i>
        </button>
    </div>
    </div>
</div>

<script>

    document.addEventListener("DOMContentLoaded", function () {
        let lihatBerkasBtn = document.getElementById("lihatBerkasBtn");

        if (lihatBerkasBtn) {
            lihatBerkasBtn.addEventListener("click", function () {
                let nomorUsulan = document.getElementById("detailNomorUsulan").textContent;

                //console.log("[DEBUG] Nomor usulan dari detail:", nomorUsulan);

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
        //document.getElementById('detailCatatan').textContent = data.catatan || '-';

        const dokumenLink = document.getElementById('dokumenRekomendasiLink');
        if (data.dokumen_rekomendasi) {
            dokumenLink.href = `/uploads/rekomendasi/${data.dokumen_rekomendasi}`;
            dokumenLink.style.display = 'inline-block';
        } else {
            dokumenLink.style.display = 'none';
        }

        document.getElementById('detailData').style.display = 'block';
        window.scrollTo(0, document.getElementById('detailData').offsetTop);
    }

    function hideDetail() {
        document.getElementById('detailData').style.display = 'none';
    }

    function verifikasi(status) {
    const nomorUsulan = document.getElementById('detailNomorUsulan').textContent;

    if (!nomorUsulan) {
        Swal.fire({
            icon: 'error',
            title: '<i class="fas fa-times-circle"></i> Nomor Usulan Tidak Ditemukan',
            html: '<p>Pastikan detail usulan sudah ditampilkan sebelum melanjutkan.</p>',
        });
        return;
    }

    let catatan = '';
    if (status === 'TdkLengkap') {
        Swal.fire({
            title: '<i class="fas fa-info-circle text-danger"></i> <strong>Masukkan Alasan Penolakan</strong>',
            html: '<p class="text-muted">Berikan alasan penolakan untuk dokumen ini.</p>',
            input: 'textarea',
            inputPlaceholder: 'Tulis alasan di sini...',
            inputAttributes: {
                'aria-label': 'Tulis alasan penolakan',
            },
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-paper-plane"></i> Kirim',
            cancelButtonText: '<i class="fas fa-times"></i> Batal',
            inputValidator: (value) => {
                if (!value) {
                    return 'Alasan penolakan wajib diisi!';
                }
            },
            customClass: {
                popup: 'swal2-popup',
                title: 'swal2-title',
                htmlContainer: 'swal2-html-container',
                input: 'swal2-textarea',
                confirmButton: 'swal-button-confirm',
                cancelButton: 'swal-button-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                catatan = result.value;
                kirimDataVerifikasi(nomorUsulan, status, catatan);
            }
        });
    } else if (status === 'Lengkap') {
        Swal.fire({
            title: '<i class="fas fa-info-circle text-success"></i> <strong>Masukkan Catatan Penerimaan</strong>',
            html: '<p class="text-muted">Anda dapat menambahkan catatan penerimaan untuk dokumen ini (opsional).</p>',
            input: 'textarea',
            inputPlaceholder: 'Tulis catatan di sini...',
            inputAttributes: {
                'aria-label': 'Tulis catatan penerimaan',
            },
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-paper-plane"></i> Kirim',
            cancelButtonText: '<i class="fas fa-times"></i> Batal',
            customClass: {
                title: 'swal-title',
                confirmButton: 'swal-button-confirm',
                cancelButton: 'swal-button-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                catatan = result.value || '';
                kirimDataVerifikasi(nomorUsulan, status, catatan);
            }
        });
    }
}

function kirimDataVerifikasi(nomorUsulan, status, catatan) {
    const data = {
        nomor_usulan: nomorUsulan,
        status: status,
        catatan: catatan,
    };

    fetch('/verifikasi/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then((response) => {
            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: '<i class="fas fa-check-circle"></i> Verifikasi Berhasil',
                    html: '<p>Status verifikasi berhasil diperbarui.</p>',
                }).then(() => {
                    location.reload();
                });
            } else {
                return response.json().then((err) => {
                    throw new Error(err.error || 'Terjadi kesalahan.');
                });
            }
        })
        .catch((error) => {
            Swal.fire({
                icon: 'error',
                title: '<i class="fas fa-exclamation-triangle"></i> Gagal Memproses',
                html: `<p>${error.message}</p>`,
            });
        });
}

    function showDetailDiverifikasi(data) {
        // Isi data detail berdasarkan data yang dipilih
        document.getElementById('detailNomorUsulanDiverifikasi').textContent = data.nomor_usulan || '-';
        document.getElementById('detailJenisUsulanDiverifikasi').textContent =
            data.jenis_usulan === 'mutasi_tetap' ? 'MUTASI' :
            data.jenis_usulan === 'nota_dinas' ? 'NOTA DINAS' :
            data.jenis_usulan === 'perpanjangan_nota_dinas' ? 'PERPANJANGAN ND' :
            (data.jenis_usulan || '-');
        document.getElementById('detailNamaGuruDiverifikasi').textContent = data.guru_nama || '-';
        document.getElementById('detailNIPDiverifikasi').textContent = data.guru_nip || '-';
        document.getElementById('detailNIKDiverifikasi').textContent = data.guru_nik || '-';
        document.getElementById('detailSekolahAsalDiverifikasi').textContent = data.sekolah_asal || '-';
        document.getElementById('detailSekolahTujuanDiverifikasi').textContent = data.sekolah_tujuan || '-';
        document.getElementById('detailAlasanDiverifikasi').textContent = data.alasan || '-';
        document.getElementById('detailTanggalInputDiverifikasi').textContent = data.created_at
            ? new Date(data.created_at).toLocaleDateString('id-ID')
            : '-';
/*
        const berkasLink = document.getElementById('berkasUsulanLinkDiverifikasi');
        if (data.google_drive_link) {
            berkasLink.href = data.google_drive_link;
            berkasLink.style.display = 'inline-block';
        } else {
            berkasLink.style.display = 'none';
        }
*/
        document.getElementById('detailNamaCabangDiverifikasi').textContent = data.nama_cabang || '-';
        document.getElementById('detailOperatorDiverifikasi').textContent = data.operator || '-';
        document.getElementById('detailNoHPDiverifikasi').textContent = data.no_hp || '-';
        document.getElementById('detailTanggalDikirimDiverifikasi').textContent = data.tanggal_dikirim
            ? new Date(data.tanggal_dikirim).toLocaleDateString('id-ID')
            : '-';
        document.getElementById('detailCatatanDiverifikasi').textContent = data.catatan || '-';
        document.getElementById('detailTanggalVerifikasiDiverifikasi').textContent =
            data.updated_at ? new Date(data.updated_at).toLocaleDateString('id-ID') : '-';

        const dokumenLink = document.getElementById('dokumenRekomendasiLinkDiverifikasi');
        if (data.dokumen_rekomendasi) {
            dokumenLink.href = `/uploads/rekomendasi/${data.dokumen_rekomendasi}`;
            dokumenLink.style.display = 'inline-block';
        } else {
            dokumenLink.style.display = 'none';
        }

        // Tampilkan status verifikasi (Lengkap/TdkLengkap)
        const statusVerifikasi = document.getElementById('statusVerifikasiDiverifikasi');
        const statusContainer = document.getElementById('statusContainerDiverifikasi');
        if (data.status_usulan_cabdin === 'Lengkap') {
            statusVerifikasi.innerHTML = `<i class="fas fa-check-circle text-success"></i> Lengkap`;
            statusContainer.classList.add('success');
            statusContainer.classList.remove('danger');
        } else if (data.status_usulan_cabdin === 'TdkLengkap') {
            statusVerifikasi.innerHTML = `<i class="fas fa-times-circle text-danger"></i> TdkLengkap`;
            statusContainer.classList.add('danger');
            statusContainer.classList.remove('success');
        } else {
            statusVerifikasi.innerHTML = `<i class="fas fa-question-circle text-muted"></i> Tidak Diketahui`;
            statusContainer.classList.remove('success', 'danger');
        }


        // Tampilkan modal detail
        document.getElementById('detailDataDiverifikasi').style.display = 'block';
        window.scrollTo(0, document.getElementById('detailDataDiverifikasi').offsetTop);
    }



function hideDetailDiverifikasi() {
    document.getElementById('detailDataDiverifikasi').style.display = 'none';
}
function filterStatus() {
    const filterValue = document.getElementById('filterStatusVerifikasi').value.toLowerCase();
    const table = document.getElementById('tableDiverifikasi');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const statusCell = rows[i].getElementsByTagName('td')[2]; // Kolom ke-4 (Status)
        if (statusCell) {
            const statusText = statusCell.textContent.toLowerCase();
            rows[i].style.display =
                filterValue === '' || statusText.includes(filterValue) ? '' : 'none';
        }
    }
}

function showBerkasModal(nomorUsulan) {
    document.getElementById('berkasList').innerHTML = `<tr><td colspan="3" class="text-center">Memuat data...</td></tr>`;

    fetch(`/usulan/getDriveLinks/${nomorUsulan}`)
        .then(response => response.json())
        .then(responseData => {
            if (!responseData || !responseData.data || responseData.data.length === 0) {
                document.getElementById('berkasList').innerHTML = `<tr><td colspan="3" class="text-center text-danger">Tidak ada data berkas</td></tr>`;
                return;
            }

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
                "Analisis Jabatan (Anjab) ditandatangani oleh Kepala Sekolah Melepas",
                "Surat Formasi GTK dari Sekolah Asal",
                "Foto Copy SK 80% dan SK Terakhir di Legalisir",
                "Foto Copy Karpeg dilegalisir",
                "Surat Keterangan tidak Pernah di Jatuhi Hukuman Disiplin",
                "Surat Keterangan Bebas Temuan Inspektorat (Optional)",
                "Surat Keterangan Bebas Tugas Belajar/Izin Belajar",
                "Daftar Riwayat Hidup/ Riwayat Pekerjaan",
                "Surat Tugas Suami dan Foto Copy Buku Nikah (Optional)"
            ];

            const optionalIndexes = [6, 9, 16, 19]; 

            let berkasList = document.getElementById('berkasList');
            berkasList.innerHTML = ""; 

            responseData.data.forEach((berkas, index) => {
                let row = document.createElement('tr');
                let driveLink = berkas.drive_link ? berkas.drive_link : "#";
                let berkasNama = berkasLabels[index] || `Berkas ${index + 1}`;

                let button = `<a href="${driveLink}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat
                              </a>`;

                if (!berkas.drive_link) {
                    if (!optionalIndexes.includes(index)) {
                        button = `<button class="btn btn-danger btn-sm" onclick="showMissingFileAlert('${berkasNama}')">
                                    <i class="fas fa-exclamation-circle"></i> Kosong
                                  </button>`;
                    } else {
                        button = ""; // Jika opsional dan kosong, tidak menampilkan tombol apapun
                    }
                }

                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${berkasNama}</td>
                    <td>${button}</td>
                `;
                berkasList.appendChild(row);
            });
        })
        .catch(error => {
            document.getElementById('berkasList').innerHTML = `<tr><td colspan="3" class="text-center text-danger">Gagal memuat data</td></tr>`;
        });

    let modal = new bootstrap.Modal(document.getElementById("berkasModal"));
    modal.show();
}


</script>

<?= $this->endSection(); ?>
