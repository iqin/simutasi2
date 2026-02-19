<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-building"></i> Daftar Cabang Dinas
</h1>

<!-- Header dengan Tombol Aksi dan Filter -->
<div class="header-container">
    <!-- Tombol Tambah di Kiri -->
    <div class="action-buttons">
        <a href="/cabang-dinas/create" class="btn btn-primary btn-sm-custom">
            <i class="fas fa-plus-circle"></i> Tambah
        </a>
    </div>

    <!-- Filter & Pagination di Kanan -->
    <div class="filter-section">
        <label for="per_page" class="mb-0"></label>
        <select id="per_page" class="form-control pagination-select" onchange="updatePerPage()">
            <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
            <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
        </select>
    </div>
</div>

<!-- Tabel Data Cabang Dinas -->
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode Cabang</th>
                <th>Nama Cabang</th>
                <th>Wilayah Kabupaten</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1 + ($perPage * ($pager->getCurrentPage('cabang_dinas') - 1));
            foreach ($cabang_dinas as $row): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['kode_cabang']; ?></td>
                    <td><?= $row['nama_cabang']; ?></td>
                    <td><?= $row['kabupaten_wilayah']; ?></td>
                    <td class="action-column">
                        <a href="/cabang-dinas/edit/<?= $row['id']; ?>" class="btn btn-warning btn-sm-custom">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button onclick="confirmDelete(<?= $row['id']; ?>)" class="btn btn-danger btn-sm-custom">
                            <i class="fas fa-trash-alt"></i> Hapus
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center">
    <p class="pagination-info">Menampilkan <?= count($cabang_dinas); ?> dari <?= $pager->getTotal('cabang_dinas'); ?> data</p>
    <?= $pager->links('cabang_dinas', 'default_full'); ?>
</div>

<script>
    function updatePerPage() {
        const perPage = document.getElementById('per_page').value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        window.location.href = url.toString();
    }

    function confirmDelete(id) {
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/cabang-dinas/delete/" + id;
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                title: "Berhasil!",
                text: "<?= session()->getFlashdata('success'); ?>",
                icon: "success",
                confirmButtonText: "OK"
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                title: "Gagal!",
                text: "<?= session()->getFlashdata('error'); ?>",
                icon: "error",
                confirmButtonText: "OK"
            });
        <?php endif; ?>
    });
</script>

<?= $this->endSection(); ?>
