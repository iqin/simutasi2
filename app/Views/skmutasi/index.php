<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>


<h1 class="h3 mb-4 text-gray-800"><i class="fas fa-upload"></i> Unggah SK Mutasi / Nota Dinas</h1>

<div class="row">

    <!-- 🔹 Bagian Kiri: Data Usulan Siap Unggah SK Mutasi -->
    <div class="col-md-6">
        <div class="filter-section">
            <label class="text-primary"><i class="fas fa-info-circle"></i> 07.1. Belum ada SK Mutasi / ND</label>
            <!-- PERUBAHAN: form GET dengan input search dan select perPage -->
            <form method="get" id="formKiri" class="d-flex flex-wrap align-items-center gap-2">
                <input type="text" name="search_kiri" class="form-control form-control-sm" 
                    placeholder="Cari Nama atau Nomor Usulan" value="<?= esc($searchKiri ?? '') ?>" 
                    autocomplete="off" style="min-width: 150px; flex:2;">
                <select name="perPageKiri" class="form-control form-control-sm w-auto" onchange="this.form.submit()">
                    <option value="10" <?= ($perPageKiri ?? 10) == 10 ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= ($perPageKiri ?? 10) == 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= ($perPageKiri ?? 10) == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= ($perPageKiri ?? 10) == 100 ? 'selected' : '' ?>>100</option>
                </select>
                <noscript><button type="submit" class="btn btn-primary btn-sm">Cari</button></noscript>
            </form>
        </div>
        <div class="table-responsive">
            <table id="tableKiri" class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama GTK</th>
                        <th>Sekolah Asal</th>
                        <th>Jenis Usulan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usulanKiri)) : ?>
                        <?php foreach ($usulanKiri as $index => $usulan) : ?>
                            <tr id="row-<?= $usulan['nomor_usulan']; ?>">
                                <td><?= $index + 1; ?></td>
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
                                    <?php
                                    $nomor      = $usulan['nomor_usulan'];
                                    $alreadyNd  = !empty($hasNdByUsulan[$nomor]); 
                                    $canUpload  = true;

                                    // Mutasi tetap status 05: hanya boleh ND; kalau ND sudah ada, tidak ada opsi yang valid
                                    if ($usulan['jenis_usulan'] === 'mutasi_tetap' && $usulan['status'] === '05' && $alreadyNd) {
                                        $canUpload = false;
                                    }

                                    // 🔹 Tambahan filter role: hanya admin/kabid/dinas/operator yang boleh upload
                                    $role = session()->get('role');
                                    $allowedRoles = ['admin', 'kabid', 'dinas', 'operator'];
                                    if (!in_array($role, $allowedRoles)) {
                                        $canUpload = false;
                                    }
                                    ?>
                                    <?php if ($canUpload): ?>
                                        <button class="btn btn-success btn-sm" 
                                            onclick="toggleForm('form-upload-<?= $nomor; ?>', 'row-<?= $nomor; ?>')">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">
                                            <?= $alreadyNd ? 'ND sudah diunggah' : 'Tidak bisa upload'; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                            </tr>
                            <!-- Form Upload (Muncul di bawah baris data) -->
                            <tr id="form-upload-<?= $usulan['nomor_usulan']; ?>" class="upload-form-container" style="display: none;">
                                <td colspan="5">
                                    <div class="card p-3 shadow-sm">
                                        <h6 class="text-primary"><i class="fas fa-info-circle"></i> 07.1.1 Form Upload SK Mutasi / Nota Dinas</h6>
                                        <form method="post" action="<?= base_url('skmutasi/upload'); ?>" enctype="multipart/form-data">             
                                            <input type="hidden" name="nomor_usulan" value="<?= $usulan['nomor_usulan']; ?>">
                                            <input type="hidden" name="status_usulan" value="<?= $usulan['status']; ?>">
                                            
                                            <input type="hidden" name="jenis_usulan" value="<?= $usulan['jenis_usulan']; ?>"><!-- diperlukan di controller -->

                                            <div class="mb-2">
                                                <label class="form-label">Jenis Dokumen</label>
                                                <select name="jenis_mutasi" class="form-control form-control-sm" required>
                                                    <?php
                                                    $nomor      = $usulan['nomor_usulan'];
                                                    $alreadyNd  = !empty($hasNdByUsulan[$nomor]);
                                                    $jenisUsulan = $usulan['jenis_usulan'];
                                                    $status      = $usulan['status'];

                                                    if ($jenisUsulan === 'mutasi_tetap') {
                                                        if ($status === '05') {
                                                            // hanya ND; bila ND sudah ada, form sebetulnya tak akan dipanggil (tombol disembunyikan di C1)
                                                            if (!$alreadyNd) {
                                                                echo '<option value="Nota Dinas" selected>Nota Dinas</option>';
                                                            }
                                                        } elseif ($status === '06') {
                                                            // SK Mutasi selalu boleh
                                                            echo '<option value="SK Mutasi" selected>SK Mutasi</option>';
                                                            // ND masih boleh jika belum pernah upload
                                                            if (!$alreadyNd) {
                                                                echo '<option value="Nota Dinas">Nota Dinas</option>';
                                                            }
                                                        }
                                                    } else {
                                                        // nota_dinas / perpanjangan_nota_dinas => hanya ND
                                                        if ($jenisUsulan === 'nota_dinas') {
                                                            echo '<option value="Nota Dinas" selected>Nota Dinas</option>';
                                                        } else {
                                                            echo '<option value="Nota Dinas Perpanjangan" selected>Nota Dinas (Perpanjangan)</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>


                                            <div class="mb-2">
                                                <label class="form-label">Nomor SK</label>
                                                <input type="text" name="nomor_skmutasi" class="form-control form-control-sm" required>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Tanggal SK</label>
                                                <input type="date" name="tanggal_skmutasi" class="form-control form-control-sm" required>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Upload File (PDF, Maks 5 MB)</label>
                                                <input type="file" name="file_skmutasi" class="form-control form-control-sm" accept=".pdf" required>
                                            </div>

                                            <div class="text-right">
                                                <button type="submit" class="btn btn-primary btn-sm-custom"><i class="fas fa-save"></i> Simpan</button>
                                                <button type="button" class="btn btn-secondary btn-sm-custom" onclick="closeForm('form-upload-<?= $usulan['nomor_usulan']; ?>')">
                                                    <i class="fas fa-window-close"></i> Batal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>



                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data usulan yang siap diunggah.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
                <!-- Pagination -->
            <div class="pagination-container">
                    <?= $pagerKiri->links('usulanKiri', 'default_full', [
                    'search_kiri' => $searchKiri ?? '',
                    'perPageKiri' => $perPageKiri ?? 10
                ]) ?>

            </div>
    </div>
    <!-- 🔹 Bagian Kanan: Data SK Mutasi / Nota Dinas -->
    <div class="col-md-6">
        <div class="table-container">
            <!-- Baris Header -->
            <div class="filter-section">
                <label class="text-primary"><i class="fas fa-info-circle"></i> 07.2: Telah Terbit SK Mutasi/ND</label>
                <!-- PERUBAHAN: form GET dengan input search dan select perPage -->
                <form method="get" id="formKanan" class="d-flex flex-wrap align-items-center gap-2">
                    <input type="text" name="search_kanan" class="form-control form-control-sm" 
                        placeholder="Cari Nama atau Nomor Usulan" value="<?= esc($searchKanan ?? '') ?>" 
                        autocomplete="off" style="min-width: 150px; flex:2;">
                    <select name="perPageKanan" class="form-control form-control-sm w-auto" onchange="this.form.submit()">
                        <option value="25" <?= ($perPageKanan ?? 25) == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= ($perPageKanan ?? 25) == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= ($perPageKanan ?? 25) == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                    <noscript><button type="submit" class="btn btn-primary btn-sm">Cari</button></noscript>
                </form>
            </div>

            <!-- Tabel SK Mutasi / Nota Dinas -->
            <div class="table-responsive">
                <table class="table table-sm table-striped" id="tableSk">
                    <thead>
                        <tr>
                            <th>Nama GTK</th>
                            <th>Sekolah Asal</th>
                            <th>Jenis Usulan</th>
                            <th>Jenis Dokumen</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($usulanKanan)): ?>
                            <?php foreach ($usulanKanan as $sk): ?>
                            <tr data-search="<?= esc(strtolower(($sk['guru_nama'] ?? '') . ' ' . ($sk['nomor_usulan'] ?? ''))) ?>">
                                <td><?= esc($sk['guru_nama']) ?></td>
                                <td><?= esc($sk['sekolah_asal']) ?></td>
                                <td>
                                    <?php
                                    $jenisMap = [
                                        'mutasi_tetap' => 'MUTASI TETAP',
                                        'nota_dinas' => 'NOTA DINAS',
                                        'perpanjangan_nota_dinas' => 'PERPANJANGAN ND'
                                    ];
                                    echo $jenisMap[$sk['jenis_usulan']] ?? strtoupper((string)$sk['jenis_usulan']);
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    // fallback bila jenis_mutasi null (jarang, tapi aman)
                                    $jenisDok = $sk['jenis_mutasi'];
                                    if (!$jenisDok) {
                                        $jenisDok = ($sk['jenis_usulan'] === 'perpanjangan_nota_dinas')
                                            ? 'Nota Dinas Perpanjangan'
                                            : (($sk['jenis_usulan'] === 'nota_dinas') ? 'Nota Dinas' : '-');
                                    }
                                    ?>
                                    <?php if ($jenisDok === 'SK Mutasi'): ?>
                                        <span class="badge bg-primary text-white">SK Mutasi</span>
                                    <?php elseif ($jenisDok === 'Nota Dinas'): ?>
                                        <span class="badge bg-success text-white">Nota Dinas</span>
                                    <?php elseif ($jenisDok === 'Nota Dinas Perpanjangan'): ?>
                                        <span class="badge bg-warning text-dark">Perpanjangan ND</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary text-dark"><?= esc($jenisDok) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    // Data minimal yang dibutuhkan di detail
                                    $payload = [
                                        'id_skmutasi'       => $sk['id_skmutasi'] ?? null,
                                        'nomor_usulan'      => $sk['nomor_usulan'] ?? '',
                                        'guru_nama'         => $sk['guru_nama'] ?? '',
                                        'guru_nip'          => $sk['guru_nip'] ?? '',
                                        'sekolah_asal'      => $sk['sekolah_asal'] ?? '',
                                        'sekolah_tujuan'    => $sk['sekolah_tujuan'] ?? '',
                                        'nomor_skmutasi'    => $sk['nomor_skmutasi'] ?? '',
                                        'jenis_usulan'      => $sk['jenis_usulan'] ?? '',
                                        'jenis_mutasi'      => $jenisDok,
                                        'tanggal_skmutasi'  => $sk['tanggal_skmutasi'] ?? null,
                                        'file_skmutasi'     => $sk['file_skmutasi'] ?? null,
                                    ];
                                    ?>
                                    <button class="btn btn-info btn-sm"
                                            onclick="showDetailSk(<?= htmlspecialchars(json_encode($payload, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data SK Mutasi / Nota Dinas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-container">
                    <?= $pagerKanan->links('usulanKanan', 'default_full', [
                        'search_kanan' => $searchKanan ?? '',
                        'perPageKanan' => $perPageKanan ?? 25
                    ]) ?>
            </div>
        </div>

        <!-- Detail SK Mutasi / Nota Dinas -->
        <div id="detailDataSk" class="detail-container" style="display:none;">
            <label class="text-primary"><i class="fas fa-info-circle"></i> 07.2.1: Detail SK Mutasi / Nota Dinas</label>
            <input type="hidden" id="detailIdSkmutasi">
            <table class="table detail-table">
                <tbody>
                    <tr><th>Nomor Usulan</th><td id="detailNomorUsulan"></td></tr>
                    <tr><th>Nama Guru</th><td id="detailNamaGuru"></td></tr>
                    <tr><th>NIP</th><td id="detailNip"></td></tr>
                    <tr><th>Sekolah Asal</th><td id="detailSekolahAsal"></td></tr>
                    <tr><th>Sekolah Tujuan</th><td id="detailSekolahTujuan"></td></tr>
                    <tr><th>Nomor SK</th><td id="detailNomorSk"></td></tr>
                    <tr><th>Jenis Usulan</th><td id="detailJenisUsulan"></td></tr>
                    <tr><th>Jenis Dokumen</th><td id="detailJenis"></td></tr>
                    <tr><th>Tanggal SK</th><td id="detailTanggal"></td></tr>
                    <tr>
                        <th>Berkas</th>
                        <td>
                            <a id="berkasSkLink" href="#" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-file-pdf"></i> Lihat
                            </a>
                            <button class="btn btn-danger btn-sm" onclick="confirmHapusSk()">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-secondary mt-2 btn-sm-custom" onclick="hideDetailSk()">
                <i class="fas fa-window-close"></i>
            </button>
        </div>
    </div>

    <script>

    function showDetailSk(data) {
        document.getElementById("detailIdSkmutasi").value = data.id_skmutasi || '';

        document.getElementById("detailNomorUsulan").innerText   = data.nomor_usulan || '-';
        document.getElementById("detailNamaGuru").innerText      = data.guru_nama || '-';
        document.getElementById("detailNip").innerText           = data.guru_nip || '-';
        document.getElementById("detailSekolahAsal").innerText   = data.sekolah_asal || '-';
        document.getElementById("detailSekolahTujuan").innerText = data.sekolah_tujuan || '-';
        document.getElementById("detailNomorSk").innerText       = data.nomor_skmutasi || '-';

        const jenisUsulanMap = {
            'mutasi_tetap': 'Mutasi Tetap',
            'nota_dinas': 'Nota Dinas',
            'perpanjangan_nota_dinas': 'Perpanjangan ND'
        };
        document.getElementById("detailJenisUsulan").innerText =
            jenisUsulanMap[data.jenis_usulan] || (data.jenis_usulan || '-');

        document.getElementById("detailJenis").innerText = data.jenis_mutasi || '-';

        document.getElementById("detailTanggal").innerText =
            data.tanggal_skmutasi ? new Date(data.tanggal_skmutasi).toLocaleDateString('id-ID') : '-';

        const link = document.getElementById("berkasSkLink");
        if (data.file_skmutasi) {
            link.href = "/file/skmutasi/" + data.file_skmutasi;
            link.style.display = "inline-block";
        } else {
            link.style.display = "none";
        }

        document.getElementById("detailDataSk").style.display = "block";
    }


    function hideDetailSk() {
        document.getElementById("detailDataSk").style.display = "none";
    }

    function confirmHapusSk() {
        const idSk = document.getElementById("detailIdSkmutasi").value;
        if (!idSk) {
            Swal.fire('Gagal!', 'ID dokumen tidak ditemukan.', 'error');
            return;
        }

        Swal.fire({
            title: 'Hapus dokumen ini?',
            text: "File PDF akan dihapus dan status usulan disesuaikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // ===== TAMPILKAN LOADING OVERLAY =====
                const overlay = document.getElementById('fullscreenLoading');
                if (overlay) {
                    const titleEl = document.getElementById('loadingTitle');
                    const msgEl = document.getElementById('loadingMessage');
                    const subMsgEl = document.getElementById('loadingSubMessage');
                    
                    if (titleEl) titleEl.textContent = 'MENGHAPUS DOKUMEN';
                    if (msgEl) msgEl.textContent = 'Menghapus dokumen dari server';
                    if (subMsgEl) subMsgEl.textContent = 'Sedang memproses...';
                    
                    overlay.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }

                fetch('/skmutasi/delete/' + encodeURIComponent(idSk), {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(res => {
                    // Sembunyikan loading
                    if (overlay) {
                        overlay.style.display = 'none';
                        document.body.style.overflow = '';
                    }
                    
                    if (res.success) {
                        // NOTIFIKASI SUKSES TETAP SAMA
                        Swal.fire('Berhasil!', res.message || 'Dokumen dihapus.', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', res.message || 'Tidak dapat menghapus dokumen.', 'error');
                    }
                })
                .catch(() => {
                    // Sembunyikan loading jika error
                    if (overlay) {
                        overlay.style.display = 'none';
                        document.body.style.overflow = '';
                    }
                    Swal.fire('Gagal!', 'Terjadi kesalahan jaringan/server.', 'error');
                });
            }
        });
    }

    </script>



<script>
    document.addEventListener("DOMContentLoaded", function () {
        <?php if (session()->getFlashdata('success')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= session()->getFlashdata('success'); ?>',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?= session()->getFlashdata('error'); ?>',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    });
/*
function toggleForm(formId, rowId) {
    let form = document.getElementById(formId);
    let row = document.getElementById(rowId);

    // Pastikan baris utama tampil sebelum menampilkan form
    if (row && row.style.display === "none") {
        return; // Tidak melakukan apa-apa jika baris utama tersembunyi
    }

    // Tampilkan atau sembunyikan form
    if (form) {
        form.style.display = (form.style.display === "none" || form.style.display === "") ? "table-row" : "none";
    }
}*/

    function toggleForm(formId, rowId) {
        let form = document.getElementById(formId);
        let row = document.getElementById(rowId);

        // Pastikan baris utama tampil sebelum menampilkan form
        if (row && row.style.display === "none") {
            return; // Tidak melakukan apa-apa jika baris utama tersembunyi
        }

        // Tutup semua form yang terbuka sebelum membuka yang baru (agar tidak menumpuk)
        document.querySelectorAll(".upload-form-container").forEach(el => {
            if (el.id !== formId) {
                el.style.display = "none";
            }
        });

        // Sembunyikan efek highlight dari semua baris sebelum menyoroti yang baru
        document.querySelectorAll("tr").forEach(el => el.classList.remove("table-active"));

        // Tampilkan atau sembunyikan form dengan efek smooth
        if (form) {
            if (form.style.display === "none" || form.style.display === "") {
                form.style.display = "table-row"; // Tampilkan form
                row.classList.add("table-active"); // Sorot baris terkait
                form.classList.add("animate-fade"); // Efek animasi saat muncul
            } else {
                form.style.display = "none"; // Sembunyikan form
                row.classList.remove("table-active"); // Hapus sorotan baris
            }
        }
    }

    function closeForm(formId) {
        let form = document.getElementById(formId);
        if (form) {
            form.style.display = "none"; // Sembunyikan form
            form.classList.remove("animate-fade"); // Hapus animasi saat form ditutup
        }
    }


// Pastikan filter tidak menyembunyikan form yang sedang terbuka
function filterTable(tableId, searchValue) {
    let rows = document.querySelectorAll(`#${tableId} tbody tr`);
    
    rows.forEach(row => {
        if (row.id.startsWith("row-")) { // Baris utama data guru
            let formId = row.id.replace("row-", "form-upload-"); // ID form terkait
            let formRow = document.getElementById(formId);

            // Cek apakah row utama harus ditampilkan
            if (row.textContent.toLowerCase().includes(searchValue.toLowerCase())) {
                row.style.display = "";
                if (formRow && formRow.style.display !== "none") {
                    formRow.style.display = ""; // Pastikan form tetap terlihat
                }
            } else {
                row.style.display = "none";
                if (formRow) {
                    formRow.style.display = "none"; // Sembunyikan form jika baris utama tidak ada
                }
            }
        }
    });
}


function closeForm(formId) {
    let form = document.getElementById(formId);
    if (form) {
        form.style.display = 'none';
        form.dataset.visible = "false";
    }
}

function confirmDelete(url) {
    Swal.fire({
        title: "Yakin ingin menghapus?",
        text: "Data SK Mutasi dan file PDF akan dihapus!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, Hapus",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

// TAMBAHAN: debounce untuk submit otomatis saat mengetik
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
    // Debounce untuk input search kiri
    const inputKiri = document.querySelector('input[name="search_kiri"]');
    if (inputKiri) {
        inputKiri.addEventListener('keyup', debounce(function() {
            this.form.submit();
        }, 500));
    }

    // Debounce untuk input search kanan
    const inputKanan = document.querySelector('input[name="search_kanan"]');
    if (inputKanan) {
        inputKanan.addEventListener('keyup', debounce(function() {
            this.form.submit();
        }, 500));
    }
});

</script>

<script>
// ===== LOADING OVERLAY UNTUK FORM UPLOAD SK/ND =====
document.addEventListener('DOMContentLoaded', function() {
    // Tangani semua form upload SK/ND
    document.querySelectorAll('form[action*="skmutasi/upload"]').forEach(form => {
        console.log('Form upload SK/ND ditemukan:', form);
        
        form.addEventListener('submit', function(e) {
            // Cegah double submit
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                return;
            }
            
            form.dataset.submitting = 'true';
            console.log('Form upload SK/ND di-submit');
            
            // Nonaktifkan tombol submit
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            }
            
            // Tampilkan loading overlay
            const overlay = document.getElementById('fullscreenLoading');
            if (overlay) {
                const titleEl = document.getElementById('loadingTitle');
                const msgEl = document.getElementById('loadingMessage');
                const subMsgEl = document.getElementById('loadingSubMessage');
                
                if (titleEl) titleEl.textContent = 'MENGUNGGAH DOKUMEN';
                if (msgEl) msgEl.textContent = 'Menyimpan Dokumen ke server';
                if (subMsgEl) subMsgEl.textContent = 'Sedang memproses file...';
                
                overlay.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
            
            // Form akan tetap di-submit secara normal
            // Setelah redirect/reload, overlay akan hilang dengan sendirinya
        });
    });
});
</script>

<?= $this->endSection(); ?>
