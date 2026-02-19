<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-map-marked-alt"></i> Tambah Kabupaten
</h1>

<!-- Tampilkan Pesan Error Jika Ada -->
<?php if (session()->get('error')): ?>
    <div class="alert alert-danger" role="alert">
        <?= session()->get('error'); ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-6">
        <form action="/kabupaten/store" method="post">
            <div class="mb-3">
                <label for="nama_kab" class="form-label">Nama Kabupaten/Kota</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-map"></i> <!-- Ikon untuk Nama Kabupaten -->
                    </span>
                    <input type="text" name="nama_kab" id="nama_kab" class="form-control" 
                           value="<?= old('nama_kab'); ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="nama_ibukotakab" class="form-label">Ibukota Kabupaten</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-city"></i> <!-- Ikon untuk Ibukota -->
                    </span>
                    <input type="text" name="nama_ibukotakab" id="nama_ibukotakab" class="form-control" 
                           value="<?= old('nama_ibukotakab'); ?>" required>
                </div>
            </div>
            <div class="text-end">
                <a href="/kabupaten" class="btn btn-secondary me-2 btn-sm-custom"><i class="fas fa-arrow-left"></i> Kembali</a>
                <button type="submit" class="btn btn-primary btn-sm-custom"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>
