<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-4 text-gray-800"><i class="fas fa-users"></i> Manajemen Admin</h1>

<!-- Notifikasi -->
<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('message'); ?>
    </div>
<?php endif; ?>

<!-- Header: Tombol Tambah & Pagination -->
<div class="header-container">
    <!-- Tombol Tambah Admin -->
    <div class="action-buttons">
        <a href="/admin/create" class="btn btn-primary btn-sm-custom">
            <i class="fas fa-plus-circle"></i> Tambah
        </a>
    </div>

    <!-- Pagination -->
    <div class="filter-section">
        <select id="per_page" name="per_page" class="form-control pagination-select" onchange="updatePerPage()">
            <option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
            <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
            <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
        </select>
    </div>
</div>

<!-- Tabel Data Admin -->
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>No. HP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $currentUserId = session()->get('id'); // ID admin yang sedang login
            $no = 1 + ($perPage * ($pager->getCurrentPage('users') - 1));
            foreach ($users as $user): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= esc($user['nama']); ?></td>
                    <td><?= esc($user['username']); ?></td>
                    <td><?= esc($user['email']); ?></td>
                    <td><?= esc($user['no_hp']); ?></td>
                    <td class="action-column">
                        <?php if ($user['id'] != $currentUserId) : ?>
                            <a href="/admin/edit/<?= $user['id']; ?>" class="btn btn-warning btn-sm-custom">
                                <i class="fas fa-edit"></i>Edit
                            </a>
                            <button onclick="confirmDelete(<?= $user['id']; ?>)" class="btn btn-danger btn-sm-custom">
                                <i class="fas fa-trash-alt"></i>Hapus
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center">
    <p class="pagination-info">Menampilkan <?= count($users); ?> dari <?= $pager->getTotal('users'); ?> data</p>
    <?= $pager->links('users', 'default_full'); ?>
</div>

<!-- Script -->
<script>
    function updatePerPage() {
        const perPage = document.getElementById('per_page').value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        window.location.href = url.toString();
    }

    function confirmDisable(userId) {
        Swal.fire({
            title: "Konfirmasi",
            text: "Anda akan menonaktifkan akun ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Nonaktifkan!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/admin/disable/${userId}`;
            }
        });
    }

    function confirmEnable(userId) {
        Swal.fire({
            title: "Konfirmasi",
            text: "Anda akan mengaktifkan akun ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Aktifkan!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/admin/enable/${userId}`;
            }
        });
    }

    function confirmDelete(userId) {
        Swal.fire({
            title: "Konfirmasi Hapus",
            text: "Apakah Anda yakin ingin menghapus data ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/admin/delete/${userId}`;
            }
        });
    }
</script>

<?= $this->endSection(); ?>
