<?= $this->extend('layouts/main_layout') ?>
<?= $this->section('content') ?>

<div class="container">
    <h1 class="mb-4"><i class="fas fa-building"></i> Tambah Cabang Dinas</h1>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <form action="/cabang-dinas/store" method="post" onsubmit="return validateForm()">
        <div class="row">
            <!-- Bagian Kiri -->
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="kode_cabang">Kode Cabang</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-code"></i></span>
                        <input type="text" name="kode_cabang" id="kode_cabang" class="form-control"
                               value="<?= $newKodeCabang; ?>" readonly>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="nama_cabang">Nama Cabang</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                        <input type="text" name="nama_cabang" id="nama_cabang" class="form-control"
                               placeholder="Masukkan Nama Cabang Dinas" required>
                    </div>
                </div>
            </div>

            <!-- Bagian Kanan -->
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="id_kab">Wilayah Kabupaten <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="form-check-container" style="width: 100%; padding-left: 10px;">
                            <?php foreach ($kabupaten as $kab): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="id_kab[]" id="kab_<?= $kab['id_kab'] ?>" value="<?= $kab['id_kab'] ?>">
                                    <label class="form-check-label" for="kab_<?= $kab['id_kab'] ?>">
                                        <?= $kab['nama_kab'] ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <small class="text-muted">Pilih satu atau lebih kabupaten yang menjadi wilayah kerja.</small>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="/cabang-dinas" class="btn btn-secondary btn-sm-custom"><i class="fas fa-arrow-left"></i> Kembali</a>
            <button type="submit" class="btn btn-primary btn-sm-custom"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </form>
</div>

<script>
    function validateForm() {
        let checkboxes = document.querySelectorAll('input[name="id_kab[]"]:checked');

        if (checkboxes.length === 0) {
            Swal.fire({
                title: "Input Gagal!",
                text: "Minimal satu kabupaten harus dipilih.",
                icon: "warning",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK"
            });
            return false; // Mencegah pengiriman formulir
        }
        return true; // Melanjutkan pengiriman formulir
    }
</script>

<?= $this->endSection() ?>
