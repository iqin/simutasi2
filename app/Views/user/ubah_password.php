<?= $this->extend('layouts/main_layout') ?>
<?= $this->section('content') ?>

<h1 class="h3 mb-4 text-gray-800"><i class="fas fa-fw fa-key"></i> Ubah Password</h1>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error'); ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success'); ?>
    </div>
<?php endif; ?>

<form action="/user/updatePassword" method="post">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="current_password">Password Lama</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Masukkan password lama" required>
                </div>
            </div>
            <div class="form-group mb-3">
                <label for="new_password">Password Baru</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Masukkan password baru" required>
                </div>
            </div>
            <div class="form-group mb-3">
                <label for="confirm_password">Konfirmasi Password Baru</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Ulangi password baru" required>
                </div>
            </div>
        </div>
    </div>
    <a href="/dashboard" class="btn btn-sm-custom btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    <button type="submit" class="btn btn-sm-custom btn-primary"><i class="fas fa-save"></i> Simpan</button>
</form>

<?= $this->endSection() ?>
