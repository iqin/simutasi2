<?= $this->extend('layouts/main_layout') ?>
<?= $this->section('content') ?>

<div class="container">
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-fw fa-user"></i>  Profil Anda
    </h1>
    <!-- Tampilkan pesan sukses -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>    
    <form action="/user/updateProfile" method="post">
        <?= csrf_field() ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="nama">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="nama" id="nama" class="form-control" value="<?= $user['nama'] ?>" placeholder="Masukkan Nama Lengkap" required>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control" value="<?= $user['email'] ?>" placeholder="Masukkan Email" required>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="no_hp">Nomor Handphone</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="text" name="no_hp" id="no_hp" class="form-control" value="<?= $user['no_hp'] ?>" placeholder="Masukkan Nomor Handphone" required>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="username">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                        <input type="text" name="username" id="username" class="form-control" value="<?= $user['username'] ?>" readonly>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="role">Role</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                        <input type="text" name="role" id="role" class="form-control" value="<?= $roleName ?>" readonly>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="instansi">Instansi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                        <input type="text" name="instansi" id="instansi" class="form-control" value="<?= ($user['role'] === 'operator') ? implode(', ', $hakAkses) : $instansi ?>" readonly>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($user['role'] === 'dinas'): ?>
            <div class="form-group mb-3">
                <label for="hak_akses">Hak Akses</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <textarea class="form-control" readonly><?= implode(', ', $hakAkses) ?></textarea>
                </div>
            </div>
        <?php endif; ?>
        <div class="d-flex justify-content-between mt-4">
            <a href="/dashboard" class="btn btn-sm-custom btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            <button type="submit" class="btn btn-sm-custom btn-primary"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
