<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-edit"></i> Edit Sekolah / Instansi
</h1>

<form action="/sekolah/update/<?= $sekolah['id']; ?>" method="post">
    <?= csrf_field(); ?>

    <input type="hidden" name="id" value="<?= $sekolah['id']; ?>">

    <div class="mb-3">
        <label for="npsn" class="form-label">NPSN</label>
        <input type="text" class="form-control" id="npsn" name="npsn" value="<?= $sekolah['npsn']; ?>" readonly>
    </div>

    <div class="mb-3">
        <label for="kabupaten_id" class="form-label">Kabupaten</label>
        <input type="text" class="form-control" value="<?= $sekolah['nama_kab']; ?>" disabled>
        
        <!-- Hidden input untuk tetap mengirim nilai kabupaten_id -->
        <input type="hidden" name="kabupaten_id" value="<?= $sekolah['kabupaten_id']; ?>">
    </div>

    <div class="mb-3">
        <label for="nama_sekolah" class="form-label">Nama Sekolah</label>
        <input type="text" class="form-control" id="nama_sekolah" name="nama_sekolah" value="<?= $sekolah['nama_sekolah']; ?>" required>
    </div>

    <div class="mb-3">
        <label for="alamat_sekolah" class="form-label">Alamat</label>
        <textarea class="form-control" id="alamat_sekolah" name="alamat_sekolah" required><?= $sekolah['alamat_sekolah']; ?></textarea>
    </div>

    <div class="mb-3">
        <label for="jenjang" class="form-label">Jenjang</label>
        <select class="form-control" id="jenjang" name="jenjang" required>
            <option value="SLB" <?= $sekolah['jenjang'] == 'SLB' ? 'selected' : ''; ?>>SLB</option>
            <option value="SMA" <?= $sekolah['jenjang'] == 'SD' ? 'selected' : ''; ?>>SD</option>
            <option value="SMK" <?= $sekolah['jenjang'] == 'SMP' ? 'selected' : ''; ?>>SMP</option>
            <option value="SLB" <?= $sekolah['jenjang'] == 'SMA' ? 'selected' : ''; ?>>SMA</option>
            <option value="SMA" <?= $sekolah['jenjang'] == 'SMK' ? 'selected' : ''; ?>>SMK</option>
            <option value="SMK" <?= $sekolah['jenjang'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-control" id="status" name="status" required>
            <option value="Negeri" <?= $sekolah['status'] == 'Negeri' ? 'selected' : ''; ?>>Negeri</option>
            <option value="Swasta" <?= $sekolah['status'] == 'Swasta' ? 'selected' : ''; ?>>Swasta</option>
            <option value="Swasta" <?= $sekolah['status'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>            
        </select>
    </div>

    <button type="submit" class="btn btn-primary btn-sm-custom"><i class="fas fa-save"></i> Simpan</button>
    <a href="/sekolah" class="btn btn-secondary btn-sm-custom">Batal</a>
</form>

<?= $this->endSection(); ?>
