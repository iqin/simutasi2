<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-4 text-gray-800"><i class="fas fa-file-signature"></i> Rekomendasi Kadis</h1>

<div class="row">
    <!-- Bagian 3: Tabel Usulan Belum Memiliki Rekomendasi -->
    <div class="col-md-6">
        <div class="table-container">
            <!-- Baris Header -->
            <div class="filter-section">
                <label class="text-primary"><i class="fas fa-info-circle"></i> 05.3: Usulan (Belum Terbit Rekom)</label>
                <div class="d-flex align-items-center">
                    <!-- Pilihan jumlah data per halaman -->
                    <form method="get" id="perPageBelumTerkaitForm">
                        <select name="perPageBelumTerkait" class="form-control form-control-sm"
                                onchange="document.getElementById('perPageBelumTerkaitForm').submit();">
                            <option value="10" <?= $perPageBelumTerkait == 10 ? 'selected' : '' ?>>10</option>
                            <option value="25" <?= $perPageBelumTerkait == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?= $perPageBelumTerkait == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= $perPageBelumTerkait == 100 ? 'selected' : '' ?>>100</option>
                        </select>
                    </form>

                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama GTK</th>
                            <th>NIP</th>
                            <th>Sekolah Asal</th>
                            <th>Jenis Usulan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usulanBelumTerkait as $index => $usulan): ?>
                            <tr id="row-<?= $usulan['id'] ?>">
                                <td><?= $index + 1; ?></td>
                                <td><?= esc($usulan['guru_nama']) ?></td>
                                <td><?= esc($usulan['guru_nip']) ?></td>
                                <td><?= esc($usulan['sekolah_asal']) ?></td>
                                <td>
                                    <?php
                                    $mapJenis = [
                                        'mutasi_tetap' => 'MUTASI',
                                        'nota_dinas' => 'NOTA DINAS',
                                        'perpanjangan_nota_dinas' => 'PERPANJANGAN ND'
                                    ];
                                        echo $mapJenis[$usulan['jenis_usulan']] ?? '-';
                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-success btn-sm" 
                                            onclick="toggleForm('form-upload-<?= $usulan['id'] ?>', 'row-<?= $usulan['id'] ?>')">
                                        <i class="fas fa-upload"></i>
                                    </button>
                                </td>
                            </tr>
                            <!-- Form Upload Rekomendasi -->
                            <tr id="form-upload-<?= $usulan['id'] ?>" class="upload-form-container" style="display: none;">
                                <td colspan="5">
                                    <div class="card p-3 shadow-sm">
                                        <h6 class="text-primary"><i class="fas fa-info-circle"></i> Form Upload (Bundel Berkas dari Srikandi)</h6>
                                        <form method="post" action="<?= base_url('rekomkadis/upload'); ?>" enctype="multipart/form-data">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id_usulan" value="<?= $usulan['id']; ?>">
                                            <div class="mb-2">
                                                <label class="form-label">Upload File (PDF, Maks 10 MB)</label>
                                                <input type="file" name="file_rekomkadis" class="form-control form-control-sm" accept=".pdf" required>
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" class="btn btn-primary btn-sm-custom"><i class="fas fa-save"></i> Simpan</button>
                                                <button type="button" class="btn btn-secondary btn-sm-custom" onclick="closeForm('form-upload-<?= $usulan['id'] ?>')">
                                                    <i class="fas fa-window-close"></i> Batal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination untuk tabel 05.3 -->
            <div class="pagination-container">
                <?= $pagerBelumTerkait->links('usulan_belum_terkait_pagination', 'default_full') ?>
            </div>
        </div>
    </div>
    <!-- Tabel 05.4: Usulan Terkait dengan File Rekomendasi Kadis -->
    <div class="col-md-6">
        <div class="table-container">
            <!-- Baris Header -->
            <div class="filter-section">
                <label class="text-primary"><i class="fas fa-info-circle"></i> 05.4: Usulan (Telah Terbit Rekom)</label>
                <div class="d-flex align-items-center">
                    <input type="text" id="searchUsulanInput" class="form-control form-control-sm me-2"
                        placeholder="Nama GTK / Nomor Usulan" onkeyup="filterUsulanTable()"
                        value="<?= esc($keywordUsulan) ?>" style="max-width: 250px;">

                    <form method="get" id="perPageUsulanForm">
                        <select name="perPageUsulan" class="form-control form-control-sm"
                                onchange="document.getElementById('perPageUsulanForm').submit();">
                            <option value="25" <?= $perPageUsulan == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?= $perPageUsulan == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= $perPageUsulan == 100 ? 'selected' : '' ?>>100</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Nama GTK</th>
                            <th>NIP</th>
                            <th>Sekolah Asal</th>
                            <th>Jenis Usulan</th>                            
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usulanTerkait as $usulan): ?>
                            <tr>
                                <td><?= esc($usulan['guru_nama']) ?></td>
                                <td><?= esc($usulan['guru_nip']) ?></td>
                                <td><?= esc($usulan['sekolah_asal']) ?></td>
                                <td>
                                    <?php
                                        echo $mapJenis[$usulan['jenis_usulan']] ?? '-';
                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" 
                                            onclick="showDetailUsulan(<?= htmlspecialchars(json_encode($usulan)) ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination untuk tabel 05.4 -->
            <div class="pagination-container">
                <?= $pagerUsulan->links('usulan_terkait_pagination', 'default_full') ?>
            </div>
        </div>

        <!-- Detail Data Usulan (Desain Mirip Detail Telaah) -->
        <div id="detailDataUsulan" class="detail-container" style="display: none;">
            <label class="text-primary"><i class="fas fa-info-circle"></i> 05.4: Detail Rekomendasi Kadis</label>
            <table class="table detail-table">
                <tbody>
                    <tr>
                        <th>Nomor Usulan</th>
                        <td id="detailNomorUsulan"></td>
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
                        <th>Sekolah Asal</th>
                        <td id="detailSekolahAsal"></td>
                    </tr>
                    <tr>
                        <th>Sekolah Tujuan</th>
                        <td id="detailSekolahTujuan"></td>
                    </tr>                
                    <tr>
                        <th>Tanggal Input Rekomendasi</th>
                        <td id="detailTanggalRekom"></td>
                    </tr>                    
                    <tr>
                    <th>Berkas Rekom Kadis</th>
                    <td>
                        <a id="berkasRekomLink" href="#" target="_blank" class="btn btn-info btn-sm">
                            <i class="fas fa-file-pdf"></i> Lihat
                        </a>
                        <button class="btn btn-danger btn-sm" 
                                onclick="confirmHapusRekom()">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>

            <button class="btn btn-secondary mt-2 btn-sm-custom" onclick="hideDetailUsulan()">
                <i class="fas fa-window-close"></i>
            </button>
        </div>
    </div>
</div>

<script>
        function showDetailUsulan(data) {
        document.getElementById("detailNomorUsulan").innerText = data.nomor_usulan;
        document.getElementById("detailNamaGuru").innerText = data.guru_nama;
        document.getElementById("detailNIP").innerText = data.guru_nip || "-";
        document.getElementById("detailSekolahAsal").innerText = data.sekolah_asal;
        document.getElementById("detailSekolahTujuan").innerText = data.sekolah_tujuan || "-";   
        document.getElementById("detailTanggalRekom").textContent = data.tanggal_rekomkadis ? new Date(data.tanggal_rekomkadis).toLocaleDateString('id-ID') : '-';       

        let berkasRekomLink = document.getElementById("berkasRekomLink");
        if (data.file_rekomkadis) {
            berkasRekomLink.href = "/file/rekomkadis/" + data.file_rekomkadis;
            berkasRekomLink.style.display = "inline-block";
        } else {
            berkasRekomLink.style.display = "none";
        }

        // Tampilkan detail container
        document.getElementById("detailDataUsulan").style.display = "block";
    }

    function hideDetailUsulan() {
        document.getElementById("detailDataUsulan").style.display = "none";
    }


    function toggleForm(formId, rowId) {
        let form = document.getElementById(formId);
        let row = document.getElementById(rowId);

        // Tutup semua form lain sebelum membuka form baru
        document.querySelectorAll(".upload-form-container").forEach(el => {
            if (el.id !== formId) {
                el.style.display = "none";
            }
        });

        // Tampilkan atau sembunyikan form di bawah baris terkait
        if (form) {
            if (form.style.display === "none" || form.style.display === "") {
                form.style.display = "table-row"; 
                row.classList.add("table-active"); 
            } else {
                form.style.display = "none"; 
                row.classList.remove("table-active"); 
            }
        }
    }

    function closeForm(formId) {
        let form = document.getElementById(formId);
        if (form) {
            form.style.display = 'none';
        }
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

    function confirmHapusRekom() {
    let nomorUsulan = document.getElementById("detailNomorUsulan").textContent.trim(); // Ambil nomor usulan

    Swal.fire({
        title: 'Batalkan Rekom Kadis?',
        text: "Surat rekomendasi ini akan dihapus dan status usulan akan dikembalikan ke 04!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/rekomkadis/hapus/' + encodeURIComponent(nomorUsulan), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', 'Rekomendasi berhasil dihapus.', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Gagal!', data.message || 'Terjadi kesalahan!', 'error');
                }
            })
            .catch(error => {
                Swal.fire('Gagal!', 'Terjadi kesalahan jaringan atau server.', 'error');
            });
        }
    });
}


</script>

<?= $this->endSection(); ?>
