<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('content') ?>

<h1 class="h5 mb-4 text-gray-800"><i class="fas fa-reply"></i> Balas Saran</h1>

<div class="card shadow-sm mb-5"">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-comments"></i> Detail Saran
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="fw-bold">Nama Guru</label>
                    <input type="text" class="form-control" value="<?= $saran['guru_nama'] ?? 'Tidak Ada Data' ?>" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="fw-bold">Email</label>
                    <input type="email" class="form-control" value="<?= $saran['email'] ?>" readonly>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="fw-bold">Saran</label>
            <textarea class="form-control" rows="4" readonly><?= $saran['saran'] ?></textarea>
        </div>

        <form action="<?= base_url('/kotak-saran/submitBalasan/' . $saran['id']) ?>" method="post">
            <div class="mb-3">
                <label class="fw-bold">Balasan</label>
                <textarea name="balasan" class="form-control" rows="4" required></textarea>
            </div>

            <div class="d-flex justify-content-between">
                <a href="/kotak-saran" class="btn btn-secondary btn-sm-custom">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>

                <?php if (in_array(session()->get('role'), ['admin', 'kabid'])): ?>
                    <button type="submit" class="btn btn-primary btn-sm-custom">
                        <i class="fas fa-paper-plane"></i> Kirim Balasan
                    </button>
                <?php else: ?>
                    <div class="alert alert-danger">Anda tidak memiliki izin untuk membalas saran.</div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
