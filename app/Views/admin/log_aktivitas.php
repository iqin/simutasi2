<?= $this->extend('layouts/main_layout') ?>
<?= $this->section('content') ?>

<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-fw fa-history"></i> Log Aktivitas User
</h1>

<!-- Header & Filter -->
<div class="header-container">
    <div class="filter-section">
        <form method="get" class="d-flex align-items-center gap-2">
            <input type="text" name="search" id="search" class="form-control form-control-sm" 
                   value="<?= esc($search) ?>" placeholder="Cari username atau role...">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
            <?php if ($search): ?>
                <a href="/log_aktivitas" class="btn btn-light btn-sm"><i class="fas fa-eraser"></i> Reset</a>
            <?php endif; ?>
        </form>
        <form method="get" class="d-flex align-items-center gap-2">
            <label for="per_page" class="mb-0">Tampilkan:</label>
            <select id="per_page" name="per_page" class="form-control pagination-select" onchange="this.form.submit()">
                <option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
                <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
                <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
            </select>
        </form>
    </div>
</div>

<!-- Tabel Log Aktivitas -->
<div class="table-responsive">
    <table class="table table-sm table-striped">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Role</th>
                <th>Aksi</th>
                <th>IP Address</th>
                <th>Perangkat</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1 + ($pager->getCurrentPage() - 1) * $pager->getPerPage(); ?>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= esc($log['username']); ?></td>
                    <td>
                        <span class="badge bg-info text-white"><?= ucfirst($log['role']); ?></span>
                    </td>
                    <td>
                        <?php if ($log['action'] == 'login'): ?>
                            <span class="badge bg-success text-white"><i class="fas fa-sign-in-alt"></i> Login</span>
                        <?php else: ?>
                            <span class="badge bg-danger text-white"><i class="fas fa-sign-out-alt"></i> Logout</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($log['ip_address']); ?></td>
                    <td><?= esc($log['user_agent']); ?></td>
                    <td><?= date('d-m-Y H:i:s', strtotime($log['timestamp'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center">
    <p class="pagination-info">Menampilkan <?= count($logs) ?> dari <?= $pager->getTotal() ?> data</p>
    <?= $pager->links('default', 'default_full') ?>
</div>

<!-- DataTable Integration -->
<script>
    $(document).ready(function() {
        $('#logTable').DataTable({
            "ordering": false,
            "pageLength": <?= $perPage ?>,
            "searching": false, // Matikan search bawaan DataTable
            "language": {
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Tidak ada data yang ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "search": "Cari:",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Berikutnya",
                    "previous": "Sebelumnya"
                }
            }
        });
    });
</script>

<?= $this->endSection(); ?>
