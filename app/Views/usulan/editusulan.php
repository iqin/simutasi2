<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<style>
    .nav-custom {
        background-color: #e9ecef;
    }
    .nav-tabs .nav-link.active {
        background-color: white !important;
        color: #6c757d !important;
        font-weight: bold;
    }
    .nav-tabs .nav-link {
        color: #6c757d;
        transition: 0.3s;
    }
    .nav-tabs .nav-link:hover {
        color: #495057;
    }
</style>

<h1 class="h3 mb-4 text-gray-800"><i class="fas fa-folder"></i> Edit Usulan Mutasi</h1>
<!-- Form dalam Card -->
<div class="card mt-3">
    <!-- Tab Bar -->
    <ul class="nav nav-tabs nav-custom">
        <li class="nav-item">
            <a class="nav-link active" href="#">1️⃣ Data GTK & Instansi</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/usulan/edit-berkas/<?= $usulan['nomor_usulan']; ?>">2️⃣ Upload Berkas</a>
        </li>
    </ul>

    <div class="card-body">
        <form id="editUsulanForm" action="/usulan/update-usulan/<?= $usulan['id'] ?>" method="post">
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="jenis_usulan">Jenis Usulan</label>
                        <input type="text" class="form-control" id="jenis_usulan_label" value="<?php
                            $label = [
                                'mutasi_tetap' => 'Mutasi',
                                'nota_dinas' => 'Nota Dinas',
                                'perpanjangan_nota_dinas' => 'Perpanjangan Nota Dinas'
                            ];
                            echo $label[$usulan['jenis_usulan']] ?? strtoupper($usulan['jenis_usulan']);
                        ?>" readonly>

                        <!-- Hidden input agar value tetap dikirim ke server -->
                        <input type="hidden" name="jenis_usulan" value="<?= $usulan['jenis_usulan'] ?>">
                    </div>                    
                    <div class="form-group mb-3">
                        <label for="guruNama">Nama GTK</label>
                        <input type="text" name="guru_nama" id="guruNama" class="form-control" value="<?= $usulan['guru_nama'] ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="guruNip">NIP</label>
                        <input type="text" id="guruNip" class="form-control" value="<?= $usulan['guru_nip'] ?>" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label for="guruNik">NIK</label>
                        <input type="text" id="guruNik" class="form-control" value="<?= $usulan['guru_nik'] ?>" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">Email Aktif GTK</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= $usulan['email'] ?? '' ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="no_hp">No. HP (WhatsApp) GTK</label>
                        <input type="text" name="no_hp" id="no_hp" class="form-control" value="<?= $usulan['no_hp'] ?? '' ?>" placeholder="08xxxxxxxxxx" required>
                    </div>
                </div>

                <!-- Kolom Tengah -->
                <div class="col-md-8">
                    <div class="row">
                        <!-- Kabupaten Asal & Cabang Dinas Asal -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="kabupatenAsal">Kabupaten Asal</label>
                                <input type="text" id="kabupatenAsal" class="form-control" value="<?= $usulan['kabupaten_asal_nama'] ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="cabangDinasAsal">Cabang Dinas Asal</label>
                                <input type="text" id="cabangDinasAsal" class="form-control" value="<?= $usulan['cabang_dinas_asal_nama'] ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Sekolah Asal -->
                    <div class="form-group mb-3">
                        <label for="sekolahAsal">Sekolah Asal</label>
                        <input type="text" id="sekolahAsal" class="form-control" value="<?= $usulan['sekolah_asal_nama'] ?>" readonly>
                    </div>

                    <div class="row">
                        <!-- Kabupaten Tujuan & Cabang Dinas Tujuan -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="kabupatenTujuan">Kabupaten Tujuan</label>
                                <select id="kabupatenTujuan" name="kabupaten_tujuan_id" class="form-control" required>
                                    <option value="">-- Pilih Kabupaten --</option>
                                    <?php foreach ($kabupatenList as $kabupaten): ?>
                                        <option value="<?= $kabupaten['id_kab']; ?>" <?= ($usulan['kabupaten_tujuan_nama'] == $kabupaten['nama_kab']) ? 'selected' : '' ?>>
                                            <?= $kabupaten['nama_kab']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="cabangDinasTujuan">Cabang Dinas Tujuan</label>
                                <input type="text" id="cabangDinasTujuan" class="form-control" value="<?= $usulan['cabang_dinas_tujuan_nama'] ?>" readonly>
                                <input type="hidden" id="cabangDinasTujuanId" name="cabang_dinas_tujuan_id" value="<?= $usulan['cabang_dinas_tujuan_id']; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Sekolah Tujuan -->
                    <div class="form-group mb-3">
                        <label for="sekolahTujuan">Sekolah / Instansi Tujuan</label>
                        <select id="sekolahTujuan" name="sekolah_tujuan" class="form-control w-100" required>
                            <option value="<?= $usulan['sekolah_tujuan']; ?>" selected><?= $usulan['sekolah_tujuan_nama']; ?></option>
                        </select>
                        <input type="hidden" name="sekolah_tujuan_nama" id="sekolahTujuanNama" value="<?= $usulan['sekolah_tujuan_nama']; ?>">
                    </div>
                    <!-- Alasan Mutasi -->
                    <div class="form-group mb-3">
                        <label for="alasan">Alasan Mutasi</label>
                        <textarea name="alasan" id="alasan" class="form-control" rows="4" required><?= $usulan['alasan'] ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Tombol -->
            <div class="d-flex justify-content-between mt-4">
                <a href="/usulan" class="btn btn-sm-custom btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                <div>
                    <button type="submit" class="btn btn-sm-custom btn-success" id="btnSimpan"><i class="fas fa-save"></i> Simpan</button>
                    <a href="/usulan/edit-berkas/<?= $usulan['nomor_usulan']; ?>" class="btn btn-sm-custom btn-primary"><i class="fas fa-edit"></i> Edit Berkas</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT AJAX -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    let form = document.getElementById("editUsulanForm");

    form.addEventListener("submit", function(event) {
        event.preventDefault(); // Mencegah pengiriman langsung

        Swal.fire({
            title: 'Berhasil!',
            text: 'Data usulan berhasil diperbarui.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            form.submit(); // Kirim form setelah SweetAlert dikonfirmasi
        });
    });
});


document.addEventListener("DOMContentLoaded", function () {
    let sekolahTujuanDropdown = document.getElementById("sekolahTujuan");
    let sekolahTujuanNamaInput = document.getElementById("sekolahTujuanNama");
    let kabupatenTujuanDropdown = document.getElementById("kabupatenTujuan");

    // Ambil sekolah tujuan yang tersimpan di database
    let sekolahTerpilih = "<?= $usulan['sekolah_tujuan']; ?>";

    // Fungsi untuk memperbarui Cabang Dinas dan Sekolah berdasarkan Kabupaten Tujuan
    function updateCabangDinasDanSekolah(kabupatenId) {
        if (kabupatenId) {
            fetch(`/api/get-cabang-dinas/${kabupatenId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("cabangDinasTujuan").value = data.nama_cabang || "";
                    document.getElementById("cabangDinasTujuanId").value = data.id || "";
                });

            fetch(`/api/get-sekolah/${kabupatenId}`)
                .then(response => response.json())
                .then(data => {
                    let sekolahSelect = document.getElementById("sekolahTujuan");
                    sekolahSelect.innerHTML = '<option value="">-- Pilih Sekolah --</option>';
                    let isSelected = false;

                    data.forEach(sekolah => {
                        let selected = "";
                        if (sekolah.id == sekolahTerpilih) {
                            selected = "selected";
                            isSelected = true;
                        }
                        sekolahSelect.innerHTML += `<option value="${sekolah.id}" ${selected}>${sekolah.nama_sekolah}</option>`;
                    });

                    // Jika sekolah tujuan sebelumnya tidak ditemukan di daftar sekolah, tetap tampilkan dari database
                    if (!isSelected) {
                        sekolahSelect.innerHTML += `<option value="${sekolahTerpilih}" selected><?= $usulan['sekolah_tujuan_nama']; ?></option>`;
                    }

                    // Perbarui input hidden dengan nama sekolah yang terpilih
                    sekolahTujuanNamaInput.value = sekolahSelect.options[sekolahSelect.selectedIndex]?.text || "";
                });
        }
    }

    // Panggil fungsi saat halaman pertama kali dimuat
    updateCabangDinasDanSekolah(kabupatenTujuanDropdown.value);

    // Event listener saat kabupaten tujuan berubah
    kabupatenTujuanDropdown.addEventListener("change", function () {
        updateCabangDinasDanSekolah(this.value);
    });

    // Perbarui nama sekolah tujuan saat pengguna memilih sekolah baru
    sekolahTujuanDropdown.addEventListener("change", function () {
        sekolahTujuanNamaInput.value = this.options[this.selectedIndex]?.text || "";
    });
});


</script>

<?= $this->endSection(); ?>
