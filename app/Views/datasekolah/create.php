<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-plus-circle"></i> Tambah Sekolah / Instansi
</h1>

<form action="/sekolah/store" method="post">
    <?= csrf_field(); ?>

    <div class="mb-3">
        <label for="npsn" class="form-label">NPSN (Optional)</label>
        <input type="text" class="form-control" id="npsn" name="npsn" >
    </div>

    <div class="mb-3">
        <label for="nama_sekolah" class="form-label">Nama Sekolah / Instansi</label>
        <input type="text" class="form-control" id="nama_sekolah" name="nama_sekolah" required>
    </div>

    <div class="mb-3">
        <label for="alamat_sekolah" class="form-label">Alamat</label>
        <textarea class="form-control" id="alamat_sekolah" name="alamat_sekolah" required></textarea>
    </div>

    <div class="mb-3">
        <label for="kabupaten_id" class="form-label">Kabupaten</label>
        <select class="form-control" id="kabupaten_id" name="kabupaten_id" required>
            <option value="">Pilih Kabupaten</option>
            <?php foreach ($kabupaten as $row): ?>
                <option value="<?= $row['id_kab']; ?>"><?= $row['nama_kab']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="jenjang" class="form-label">Jenjang</label>
        <select class="form-control" id="jenjang" name="jenjang" required>
            <option value="SLB">SLB</option>
            <option value="SD">SD</option>
            <option value="SMP">SMP</option>
            <option value="SLB">SLB</option>
            <option value="SMA">SMA</option>
            <option value="SMK">SMK</option>
            <option value="Lainnya">Lainnya</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-control" id="status" name="status" required>
            <option value="Negeri">Negeri</option>
            <option value="Swasta">Swasta</option>
            <option value="Lainnya">Lainnya</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary btn-sm-custom"><i class="fas fa-save"></i> Simpan</button>
    <a href="/sekolah" class="btn btn-secondary btn-sm-custom">Batal</a>
</form>

<?= $this->endSection(); ?>
