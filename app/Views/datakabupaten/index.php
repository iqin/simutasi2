<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-map-marked-alt"></i> Daftar Kabupaten
</h1>

<!-- Header dengan Tombol Aksi dan Filter -->
<div class="header-container">
    <!-- Tombol Tambah di Kiri -->
    <div class="action-buttons">
        <a href="/kabupaten/create" class="btn btn-primary btn-sm-custom">
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

<!-- Tabel Data Kabupaten -->
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Kabupaten/Kota</th>
                <th>Ibukota</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1 + ($perPage * ($pager->getCurrentPage('kabupaten') - 1));
            foreach ($kabupaten as $row): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['nama_kab']; ?></td>
                    <td><?= $row['nama_ibukotakab']; ?></td>
                    <td class="action-column">
                        <a href="/kabupaten/edit/<?= $row['id_kab']; ?>" class="btn btn-warning btn-sm-custom">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button onclick="confirmDelete(<?= $row['id_kab']; ?>)" class="btn btn-danger btn-sm-custom">
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
    <p class="pagination-info">Menampilkan <?= count($kabupaten); ?> dari <?= $pager->getTotal('kabupaten'); ?> data</p>
    <?= $pager->links('kabupaten', 'default_full'); ?>
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
                window.location.href = "/kabupaten/delete/" + id;
            }
        });
    }

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

        // SweetAlert untuk notifikasi error
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
