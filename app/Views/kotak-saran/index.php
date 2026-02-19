<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>
<style>
    input[readonly], textarea[readonly] {
        background-color: #fff !important;
        color: #495057;
        border: 1px solid #ced4da;
    }
</style>
<h1 class="h5 mb-4 text-gray-800"><i class="fas fa-comments"></i> Kotak Saran</h1>

<!-- Header dengan Tombol Aksi dan Filter -->
<div class="header-container d-flex justify-content-end align-items-center gap-3">
    <!-- Filter Status & Jumlah Data -->
    <div class="filter-section px-2">
        <select id="status" name="status" class="form-control form-control-sm pagination-select" style="min-width: 110px;" onchange="updateFilter()">
            <option value="">Semua</option>
            <option value="Belum Dibalas" <?= $selectedStatus == 'Belum Dibalas' ? 'selected' : '' ?>>Belum Dibalas</option>
            <option value="Sudah Dibalas" <?= $selectedStatus == 'Sudah Dibalas' ? 'selected' : '' ?>>Sudah Dibalas</option>
        </select>
    </div>

    <div class="filter-section">
        <select id="per_page" class="form-control form-control pagination-select" onchange="updatePerPage()">
            <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
            <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
        </select>
    </div>

<!-- Tabel Data Kotak Saran -->
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th style="width: 3%;">No.</th>
                <th style="width: 15%;">Nama Guru</th>
                <th style="width: 20%;">Email</th>
                <th style="width: 42%;">Saran</th>
                <th style="width: 12%;" class="text-center">Status</th>
                <th style="width: 8%;" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1 + ($perPage * ($pager->getCurrentPage('saran') - 1));
            if (!empty($saran) && is_array($saran)): 
                foreach ($saran as $item): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $item['guru_nama'] ?? 'Tidak Ada Data' ?></td>
                        <td><?= $item['email'] ?></td>
                        <td class="text-wrap" style="max-width: 300px; white-space: normal;">
                            <div style="max-height: 100px; overflow-y: auto; padding: 5px; border-radius: 5px;">
                                <?= nl2br(htmlspecialchars($item['saran'])) ?>
                            </div>
                        </td>
                        <td align="center">
                            <small class="text-muted d-block">
                                <?= ($item['status'] === 'Sudah Dibalas') 
                                    ? date('d-m-Y', strtotime($item['updated_at'])) 
                                    : date('d-m-Y', strtotime($item['created_at'])) ?>
                            </small>
                            <span class="badge <?= $item['status'] === 'Sudah Dibalas' ? 'badge-success' : 'badge-secondary' ?>">
                                <?= htmlspecialchars($item['status']) ?>
                            </span>
                        </td>
                        <td class="action-column" align="center">
                            <?php if ($item['status'] === 'Sudah Dibalas'): ?>
                                <button class="btn btn-success btn-sm-custom" 
                                    data-guru="<?= htmlspecialchars(json_encode($item['guru_nama'] ?? 'Tidak Ada Data'), ENT_QUOTES, 'UTF-8') ?>"
                                    data-email="<?= htmlspecialchars(json_encode($item['email'] ?? 'Tidak Ada Data'), ENT_QUOTES, 'UTF-8') ?>"
                                    data-saran="<?= htmlspecialchars(json_encode($item['saran']), ENT_QUOTES, 'UTF-8') ?>"
                                    data-balasan="<?= htmlspecialchars(json_encode($item['balasan'] ?? 'Belum ada balasan.'), ENT_QUOTES, 'UTF-8') ?>"
                                    data-created="<?= htmlspecialchars(json_encode($item['created_at'] ? date('d-m-Y', strtotime($item['created_at'])) : '-'), ENT_QUOTES, 'UTF-8') ?>"
                                    data-updated="<?= htmlspecialchars(json_encode($item['updated_at'] ? date('d-m-Y', strtotime($item['updated_at'])) : '-'), ENT_QUOTES, 'UTF-8') ?>"
                                    onclick="showDetailSaran(this)">
                                    <i class="fas fa-eye"></i>
                                </button>

                            <?php else: ?>
                                <a href="<?= base_url('/kotak-saran/balas/' . $item['id']) ?>" 
                                class="btn btn-primary btn-sm-custom">
                                    <i class="fas fa-reply"></i>
                                </a>
                            <?php endif; ?>
                            
                            <button class="btn btn-danger btn-sm-custom" onclick="confirmDelete(<?= $item['id'] ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>

                    </tr>
                <?php endforeach; 
            else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">
                        <i class="fas fa-info-circle"></i> Tidak ada data saran yang tersedia
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center">
    <p class="pagination-info">Menampilkan <?= count($saran) ?> dari <?= $pager->getTotal('saran') ?> data</p>
    <?= $pager->links('saran', 'default_full') ?>
</div>

<!-- Modal Detail Saran -->
<div id="modalDetailSaran" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center" style="padding: 12px 20px;">
            <h5 class="modal-title" id="berkasModalLabel"><i class="fas fa-comments"></i> Detail Saran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Nama Guru</strong></label>
                            <input type="text" id="detailGuruNama" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Email</strong></label>
                            <input type="email" id="detailEmail" class="form-control" readonly>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label><strong>Saran</strong></label>
                    <textarea id="detailSaran" class="form-control" rows="5" readonly></textarea>
                    <label class="text-muted" style="font-size: 0.7rem;">
                        <i class="fas fa-calendar-alt"></i> <span id="detailCreatedAt">-</span>
                    </label>
                </div>

                <div class="form-group">
                    <label><strong>Balasan</strong></label>
                    <textarea id="detailBalasan" class="form-control bg-light" rows="5" readonly></textarea>
                    <label class="text-muted" style="font-size: 0.7rem;">
                        <i class="fas fa-calendar-alt"></i> <span id="detailUpdatedAt">-</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm-custom" data-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetailSaran(button) {
        // Ambil data dari tombol yang diklik
        let nama = JSON.parse(button.getAttribute('data-guru'));
        let email = JSON.parse(button.getAttribute('data-email'));
        let saran = JSON.parse(button.getAttribute('data-saran'));
        let balasan = JSON.parse(button.getAttribute('data-balasan'));
        let createdAt = JSON.parse(button.getAttribute('data-created')) || "-";
        let updatedAt = JSON.parse(button.getAttribute('data-updated')) || "-";

        // Isi modal dengan data yang diambil
        document.getElementById('detailGuruNama').value = nama;
        document.getElementById('detailEmail').value = email;
        document.getElementById('detailSaran').value = saran;
        document.getElementById('detailBalasan').value = balasan;
        document.getElementById('detailCreatedAt').textContent = createdAt;
        document.getElementById('detailUpdatedAt').textContent = updatedAt;

        // Tampilkan modal
        $('#modalDetailSaran').modal('show');
    }

    function updateFilter() {
        const status = document.getElementById('status').value;
        const url = new URL(window.location.href);
        url.searchParams.set('status', status);
        window.location.href = url.toString();
    }

    function updatePerPage() {
        const perPage = document.getElementById('per_page').value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        window.location.href = url.toString();
    }

    function showSaran(id) {
        $.ajax({
            url: '/kotak-saran/get/' + id,
            method: 'GET',
            success: function(response) {
                $('#saranDetail').text(response.saran);
                $('#balasanDetail').text(response.balasan || 'Belum dibalas');
                $('#modalSaran').modal('show');
            }
        });
    }

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
                window.location.href = "/kotak-saran/delete/" + id;
            }
        });
    }
</script>

<?= $this->endSection() ?>
