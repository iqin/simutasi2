<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-4 text-gray-800"><i class="fas fa-file-alt"></i> Telaah Dokumen</h1>

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
                        <th>Nama Guru</th>
                        <th>NIP</th>                      
                        <th>Sekolah Asal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usulanMenunggu)): ?>
                        <?php foreach ($usulanMenunggu as $usulan): ?>
                            <tr>
                                <td><?= $usulan['guru_nama']; ?></td>
                                <td><?= $usulan['guru_nip']; ?></td>                          
                                <td><?= $usulan['sekolah_asal']; ?></td>
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
            <table class="table-responsive table-bordered detail-table">
                <tbody>
                    <tr>
                        <th>Nomor Usulan</th>
                        <td id="detailNomorUsulan"></td>
                    </tr>
                    <tr>
                        <th>Nama Guru</th>
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
                        <th>Sekolah Tujuan</th>
                        <td id="detailSekolahTujuan"></td>
                    </tr>
                    <tr>
                        <th>Alasan</th>
                        <td id="detailAlasan"></td>
                    </tr>
                    <tr>
                        <th>Berkas Usulan</th>
                        <td>
                            <a id="berkasUsulanLink" href="#" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-file-pdf"></i> Lihat
                            </a>
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
                        <th>Tanggal Update</th>
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
                            <button class="btn btn-danger me-2 btn-sm-custom" onclick="telaahDokumen('Ditolak')">
                                <i class="fas fa-times"></i> Ditolak
                            </button>
                            <button class="btn btn-success btn-sm-custom" onclick="telaahDokumen('Disetujui')">
                                <i class="fas fa-check"></i> Disetujui
                            </button>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>

            </table>
            <button class="btn btn-secondary mt-2 btn-sm-custom" onclick="hideDetail()">
                <i class="fas fa-window-close"></i>
            </button>
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
            <table id="tableDitelaah" class="table table-sm  table-striped">
                <thead>
                    <tr>
                        <th>Nama Guru</th>
                        <th>Sekolah Asal</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usulanDitelaah)): ?>
                        <?php foreach ($usulanDitelaah as $usulan): ?>
                            <tr>
                                <td><?= $usulan['guru_nama']; ?></td>
                                <td><?= $usulan['sekolah_asal']; ?></td>
                                <td align="center"><?= date('d-m-Y', strtotime($usulan['updated_at_telaah'])); ?><br />
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
            <label class="text-primary"><i class="fas fa-info-circle"></i> 04.2 Detail Usulan</label>             
            <table class="table-responsive table-bordered detail-table">
                <tbody>
                    <tr>
                        <th>Nomor Usulan</th>
                        <td id="detailNomorUsulanKanan"></td>
                    </tr>
                    <tr>
                        <th>Nama Guru</th>
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
                        <th>Berkas Usulan</th>
                        <td>
                            <a id="berkasUsulanLinkKanan" href="#" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-file-pdf"></i> Lihat
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Input Usulan</th>
                        <td id="detailTanggalInputKanan"></td>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-left text-primary custom-bg"><i class="fas fa-info-circle"></i> 
                            Rekomendasi Cabang Dinas
                        </th>
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
                            Status Telaah Kabid GTK
                        </th>
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
                <p id="statusTelaahNoteKanan" class="fw-bold text-center status-note"></p>
            </div>

            <button class="btn btn-secondary mt-2 btn-sm-custom" onclick="hideDetailKanan()">
                <i class="fas fa-window-close"></i>
            </button>
        </div>


    </div>
</div>


<script>
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
    document.getElementById('detailNamaGuru').textContent = data.guru_nama || '-';
    document.getElementById('detailNIP').textContent = data.guru_nip || '-';
    document.getElementById('detailNIK').textContent = data.guru_nik || '-';
    document.getElementById('detailSekolahAsal').textContent = data.sekolah_asal || '-';
    document.getElementById('detailSekolahTujuan').textContent = data.sekolah_tujuan || '-';
    document.getElementById('detailAlasan').textContent = data.alasan || '-';
    document.getElementById('detailTanggalInput').textContent = data.created_at ? new Date(data.created_at).toLocaleDateString('id-ID') : '-';

        const berkasLink = document.getElementById('berkasUsulanLink');
        if (data.google_drive_link) {
            berkasLink.href = data.google_drive_link;
            berkasLink.style.display = 'inline-block';
        } else {
            berkasLink.style.display = 'none';
        }

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
        dialogText = 'Anda akan menolak usulan ini. Masukkan alasan penolakan pada kolom berikut!';
        inputPlaceholder = 'Tulis alasan di sini...';
        inputDefaultValue = 'Usulan mutasi tidak memenuhi syarat'; // Teks awal untuk penolakan
        isInputRequired = true;
    } else if (status === 'Disetujui') {
        dialogTitle = 'Konfirmasi Penerimaan';
        dialogText = 'Anda akan menerima usulan ini. Tambahkan catatan penerimaan (opsional).';
        inputPlaceholder = 'Tulis catatan di sini...';
        inputDefaultValue = 'Usulan mutasi memenuhi syarat'; // Teks awal untuk persetujuan
    }

    Swal.fire({
        title: dialogTitle,
        text: dialogText,
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
                    text: data.success,
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
    document.getElementById('detailNamaGuruKanan').textContent = data.guru_nama || '-';
    document.getElementById('detailNIPKanan').textContent = data.guru_nip || '-';
    document.getElementById('detailNIKKanan').textContent = data.guru_nik || '-';
    document.getElementById('detailSekolahAsalKanan').textContent = data.sekolah_asal || '-';
    document.getElementById('detailSekolahTujuanKanan').textContent = data.sekolah_tujuan || '-';
    document.getElementById('detailAlasanKanan').textContent = data.alasan || '-';
    document.getElementById('detailTanggalInputKanan').textContent = data.created_at
        ? new Date(data.created_at).toLocaleDateString('id-ID')
        : '-';

    const berkasLink = document.getElementById('berkasUsulanLinkKanan');
    if (data.google_drive_link) {
        berkasLink.href = data.google_drive_link;
        berkasLink.style.display = 'inline-block';
    } else {
        berkasLink.style.display = 'none';
    }

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

    if (data.status_telaah === 'Disetujui') {
        statusNoteElement.innerHTML = `<i class="fas fa-check-circle text-success"></i> Disetujui`;
        statusContainer.classList.add('success');
        statusContainer.classList.remove('danger');
    } else if (data.status_telaah === 'Ditolak') {
        statusNoteElement.innerHTML = `<i class="fas fa-times-circle text-danger"></i> Ditolak`;
        statusContainer.classList.add('danger');
        statusContainer.classList.remove('success');
    } else {
        statusNoteElement.innerHTML = `<i class="fas fa-question-circle text-muted"></i> Tidak Diketahui`;
        statusContainer.classList.remove('success', 'danger');
    }

    document.getElementById('detailDataKanan').style.display = 'block';
    window.scrollTo(0, document.getElementById('detailDataKanan').offsetTop);
}

function hideDetailKanan() {
    document.getElementById('detailDataKanan').style.display = 'none';
}

function filterStatus() {
    const filterValue = document.getElementById('filterStatusTelaah').value.trim().toLowerCase(); // Trim untuk menghapus spasi ekstra
    const table = document.getElementById('tableDitelaah');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) { // Lewati header (index 0)
        const statusCell = rows[i].getElementsByTagName('td')[2]; // Gunakan indeks yang benar (Tanggal & Status)

        if (statusCell) {
            // Ambil hanya teks status (span badge) di dalam kolom Tanggal
            const statusText = statusCell.querySelector('.badge')?.textContent.trim().toLowerCase() || ''; 

            if (filterValue === '' || statusText === filterValue) { // Bandingkan langsung
                rows[i].style.display = ''; // Tampilkan baris
            } else {
                rows[i].style.display = 'none'; // Sembunyikan jika tidak sesuai filter
            }
        }
    }
}



    // SweetAlert untuk notifikasi sukses dan error
    document.addEventListener("DOMContentLoaded", function () {
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




</script>

<?= $this->endSection(); ?>
