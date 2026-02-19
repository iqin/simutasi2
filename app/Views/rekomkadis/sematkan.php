<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-4 text-gray-800"><i class="fas fa-file-signature"></i>05.3: Sematkan Rekomendasi</h1>

<div class="row">
    <!-- Tabel Kiri: Data Usulan Terpilih -->
    <div class="col-md-6 table-container">
        <label class="text-primary" style="padding-top: 10px;">
            <i class="fas fa-info-circle"></i> 05.3.1: Data Usulan Terpilih
        </label>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Nomor Usulan</th>
                        <th>Nama Guru</th>
                        <th>Sekolah Asal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= esc($usulanTerpilih['nomor_usulan']) ?></td>
                        <td><?= esc($usulanTerpilih['guru_nama']) ?></td>
                        <td><?= esc($usulanTerpilih['sekolah_asal']) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Kanan: Data Rekomendasi -->
    <div class="col-md-6 table-container">
        <!-- Header dengan Pencarian dan Pagination -->
        <div class="filter-section">
        <label class="text-primary"><i class="fas fa-info-circle"></i> 05.3.2: Daftar Rekom</label>  
            <div class="d-flex">          
                <input type="text" id="searchInput" class="form-control form-control-sm me-2"
                       placeholder="Cari Nomor / Perihal Rekom" onkeyup="filterTable()"
                       value="<?= esc($keyword) ?>" style="max-width: 250px;">

                <form method="get" id="perPageForm">
                    <select name="perPage" class="form-control form-control-sm"
                            onchange="document.getElementById('perPageForm').submit();">
                        <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table id="rekomTable" class="table-sm table-striped">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Perihal</th>
                        <th>Tanggal Rekom</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daftarRekom as $rekom): ?>
                        <tr>
                            <td><?= esc($rekom['nomor_rekomkadis']) ?></td>
                            <td><?= esc($rekom['perihal_rekomkadis']) ?></td>
                            <td><?= date('d-m-Y', strtotime($rekom['tanggal_rekomkadis'])) ?></td>
                            <td class="action-buttons">
                                    <a href="<?= base_url('file/rekomkadis/' . esc($rekom['file_rekomkadis'])) ?>" 
                                       class="btn btn-info btn-sm" target="_blank" title="Lihat Surat Rekom Kadis">
                                        <i class="fas fa-file-pdf"></i> 
                                    </a>

                                    <button class="btn btn-danger btn-sm" onclick="sematkanRekom('<?= $rekom['id'] ?>')">
                                        <i class="fas fa-check"></i> Pilih
                                    </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-container mt-3">
            <?= $pager->links('rekom_pagination', 'default_full') ?>
        </div>
    </div>
    <div class="mt-3" style="padding-bottom: 10px;">
        <a href="/rekomkadis" class="btn btn-secondary me-2 btn-sm-custom"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</div>



<script>
    function filterTable() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let table = document.getElementById('rekomTable'); 
        let rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) { // Mulai dari indeks 1 (skip header)
            let nomor = rows[i].getElementsByTagName('td')[0]?.textContent.toLowerCase() || '';
            let perihal = rows[i].getElementsByTagName('td')[1]?.textContent.toLowerCase() || '';

            if (nomor.includes(input) || perihal.includes(input)) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }

    function sematkanRekom(rekomId) {
        Swal.fire({
            title: 'Konfirmasi Penyematan',
            text: "Usulan terkait mutasi akan disematkan dengan surat rekomendasi yang Anda pilih. Apakah Anda yakin?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Iya, Sematkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna menekan "Iya", lanjutkan penyematan
                fetch('/sematkan/proses', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        idUsulan: "<?= esc($usulanTerpilih['id']) ?>",
                        idRekomkadis: rekomId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Rekomendasi berhasil disematkan!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "/rekomkadis";
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message || 'Terjadi kesalahan!',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan jaringan atau server.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    }

</script>

<?= $this->endSection(); ?>
