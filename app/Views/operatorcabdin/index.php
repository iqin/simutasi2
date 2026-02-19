<?= $this->extend('layouts/main_layout') ?>
<?= $this->section('content') ?>

<h1 class="h3 mb-4 text-gray-800"><i class="fas fa-fw fa-users"></i> Manajemen Operator Cabdin</h1>

<!-- Header dengan Tombol Aksi & Pagination -->
<div class="header-container">
    <div class="action-buttons">
        <a href="/operatorcabdin/create" class="btn btn-primary btn-sm-custom">
            <i class="fas fa-plus-circle"></i> Tambah
        </a>
    </div>

    <div class="filter-section">
        <select id="per_page" name="per_page" class="form-control pagination-select" onchange="this.form.submit()">
            <option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
            <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
            <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
        </select>
    </div>
</div>

<!-- Tabel Data -->
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama</th>
                <th>Username</th>            
                <th>Email</th>
                <th>No. HP</th>
                <th>Cabang Dinas</th>            
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($users as $user): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $user['nama'] ?></td>       
                    <td><?= $user['username'] ?></td>         
                    <td><?= $user['email'] ?></td>
                    <td><?= $user['no_hp'] ?></td>     
                    <td><?= $user['nama_cabang'] ?></td>
                    <td class="action-column">
                        <a href="/operatorcabdin/edit/<?= $user['id'] ?>" class="btn btn-warning btn-sm-custom">
                            <i class="fas fa-edit"></i>Edit
                        </a>
                        <button onclick="confirmDelete(<?= $user['id'] ?>)" class="btn btn-danger btn-sm-custom">
                            <i class="fas fa-trash-alt"></i>Hapus
                        </button>
                        <?php if ($user['status'] === 'enable'): ?>
                            <button class="btn btn-success btn-sm-custom" onclick="confirmDisable(<?= $user['id'] ?>)">
                                <i class="fas fa-check-square"></i>Enable
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-sm-custom" onclick="confirmEnable(<?= $user['id'] ?>)">
                                <i class="fas fa-square"></i>Disable
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
    <p class="pagination-info">Menampilkan <?= count($users) ?> dari <?= $pager->getTotal('users') ?> data</p>
    <?= $pager->links('users', 'default_full') ?>
</div>

<script>
    function confirmDisable(userId) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Anda akan me-nonaktifkan akun ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Nonaktifkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/operatorcabdin/disable/${userId}`;
            }
        });
    }

    function confirmEnable(userId) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Anda akan mengaktifkan akun ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Aktifkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/operatorcabdin/enable/${userId}`;
            }
        });
    }

    function confirmDelete(userId) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/operatorcabdin/delete/${userId}`;
            }
        });
    }

    //SweetAlert
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

<?= $this->endSection() ?>
