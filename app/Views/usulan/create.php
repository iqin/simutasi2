<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>
<style>
    .nav-custom {
        background-color: #e9ecef;
    }

    .nav-tabs .nav-link.active {
        background-color: white !important;
        color: #6c757d; !important;
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
<?php if (session()->get('role') !== 'dinas'): ?>

    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-folder"></i> Tambah Usulan Mutasi</h1>

    <!-- Form dalam Card -->
    <div class="card mt-3">
        <!-- Tab Bar -->
        <ul class="nav nav-tabs nav-custom">
            <li class="nav-item">
                <a class="nav-link active" href="#">1️⃣ Data GTK & Instansi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">2️⃣ Upload Berkas</a>
            </li>
        </ul>
        <div class="card-body">
            <form action="/usulan/store-data-guru" method="post">
                <!-- Baris pertama: kolom kiri dan kanan -->
                <div class="row">
                    <!-- Kolom Kiri (1/3) -->
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="jenis_usulan">Jenis Usulan</label>
                            <select name="jenis_usulan" id="jenis_usulan" class="form-control" required>
                                <option value="mutasi_tetap" <?= old('jenis_usulan') === 'mutasi_tetap' ? 'selected' : '' ?>>Mutasi</option>
                                <option value="nota_dinas" <?= old('jenis_usulan') === 'nota_dinas' ? 'selected' : '' ?>>Nota Dinas</option>
                                <option value="perpanjangan_nota_dinas" <?= old('jenis_usulan') === 'perpanjangan_nota_dinas' ? 'selected' : '' ?>>Perpanjangan Nota Dinas</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="guruNama">Nama GTK</label>
                            <input type="text" name="guru_nama" id="guruNama" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="guruNip">NIP</label>
                            <input type="text" name="guru_nip" id="guruNip" class="form-control" 
                                maxlength="18" pattern="\d{18}" title="Masukkan tepat 18 digit angka tanpa spasi" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="guruNik">NIK</label>
                            <input type="text" name="guru_nik" id="guruNik" class="form-control" maxlength="16" pattern="\d{16}" title="Masukkan tepat 16 digit angka" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Email Aktif GTK <span class="badge bg-danger text-white">New</span></label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Email Aktif GTK <span class="badge bg-danger text-white">New</span></label>
                            <input type="text" name="no_hp" id="no_hp" class="form-control" placeholder="08xxxxxxxxxx" required>
                        </div>
                    </div>

                    <!-- Kolom Tengah (2/3) -->
                    <div class="col-md-8">
                        <div class="row">
                            <!-- Kabupaten Asal & Cabang Dinas Asal sejajar -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="kabupatenAsal">Kabupaten Asal</label>
                                    <select id="kabupatenAsal" name="kabupaten_asal_id" class="form-control" required>
                                        <option value="">-- Pilih Kabupaten --</option>
                                        <?php 
                                            if (session()->get('role') === 'operator') {
                                                foreach ($kabupatenListAsal as $kabupaten): ?>
                                                    <option value="<?= $kabupaten['id_kab']; ?>" <?= ($kabupaten['id_kab'] == $kabupaten_asal_id) ? 'selected' : '' ?>>
                                                        <?= $kabupaten['nama_kab']; ?>
                                                    </option>
                                                <?php endforeach;
                                            } else {
                                                foreach ($kabupatenListTujuan as $kabupaten): ?>
                                                    <option value="<?= $kabupaten['id_kab']; ?>" <?= ($kabupaten['id_kab'] == $kabupaten_asal_id) ? 'selected' : '' ?>>
                                                        <?= $kabupaten['nama_kab']; ?>
                                                    </option>
                                                <?php endforeach;
                                            }
                                        ?>                                                
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="cabangDinasAsal">Cabang Dinas Asal</label>
                                    <input type="text" id="cabangDinasAsal" class="form-control" readonly>
                                    <input type="hidden" id="cabangDinasAsalId" name="cabang_dinas_asal_id">
                                </div>
                            </div>
                        </div>

                        <?php 
                        $sekolahAsalList = isset($sekolahAsalList) ? $sekolahAsalList : []; 
                        ?>
                        <!-- Sekolah Asal -->
                        <div class="form-group mb-3">
                            <label for="sekolahAsal">Sekolah Asal</label>
                            <select id="sekolahAsal" name="sekolah_asal_id" class="form-control w-100" required>
                                <option value="">-- Pilih Sekolah --</option>
                                <?php foreach ($sekolahAsalList as $sekolah): ?>
                                    <option value="<?= $sekolah['id']; ?>"><?= $sekolah['nama_sekolah']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="sekolah_asal_nama" id="sekolahAsalNama">
                        </div>

                        <div class="row">
                            <!-- Kabupaten Tujuan & Cabang Dinas Tujuan sejajar -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="kabupatenTujuan">Kabupaten Tujuan</label>
                                    <select id="kabupatenTujuan" name="kabupaten_tujuan_id" class="form-control" required>
                                        <option value="">-- Pilih Kabupaten --</option>
                                        <?php foreach ($kabupatenListTujuan as $kabupaten): ?>
                                            <option value="<?= $kabupaten['id_kab']; ?>"><?= $kabupaten['nama_kab']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="cabangDinasTujuan">Cabang Dinas Tujuan</label>
                                    <input type="text" id="cabangDinasTujuan" class="form-control" readonly>
                                    <input type="hidden" id="cabangDinasTujuanId" name="cabang_dinas_tujuan_id">
                                </div>
                            </div>
                        </div>

                        <!-- Sekolah Tujuan -->
                        <div class="form-group mb-3">
                            <label for="sekolahTujuan">Sekolah / Instansi Tujuan</label>
                            <select id="sekolahTujuan" name="sekolah_tujuan_id" class="form-control w-100" required>
                                <option value="">-- Pilih Sekolah / Instansi Tujuan --</option>
                            </select>
                            <input type="hidden" name="sekolah_tujuan_nama" id="sekolahTujuanNama">
                        </div>
                        
                        <!-- Alasan Mutasi -->
                        <div class="form-group mb-3">
                            <label for="alasan">Alasan Mutasi</label>
                            <textarea name="alasan" id="alasan" class="form-control" rows="4" placeholder="Tulis alasan mutasi secara singkat dan jelas" required></textarea>
                        </div>
                    </div>
                </div> <!-- Penutup row pertama -->


                <!-- Tombol -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="/usulan" class="btn btn-sm-custom btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                    <button type="submit" class="btn btn-sm-custom btn-primary"><i class="fas fa-save"></i> Simpan & Lanjut</button>
                </div>
            </form>
        </div>
    </div>

<?php else: ?>
    <div class="alert alert-danger">Anda tidak memiliki izin untuk mengakses halaman ini.</div>
<?php endif; ?>

<!-- SCRIPT AJAX -->
<script>
// Ambil nama sekolah asal dan tujuan
document.getElementById("sekolahAsal").addEventListener("change", function () {
    let selectedOption = this.options[this.selectedIndex].text;
    document.getElementById("sekolahAsalNama").value = selectedOption;
});

document.getElementById("sekolahTujuan").addEventListener("change", function () {
    let selectedOption = this.options[this.selectedIndex].text;
    document.getElementById("sekolahTujuanNama").value = selectedOption;
});

// Fungsi untuk memperbarui Cabang Dinas berdasarkan Kabupaten dan menampilkan sekolah terkait
function updateCabangDinas(kabupatenId, targetCabangInput, targetCabangHidden, targetSekolahSelect) {
    if (kabupatenId) {
        fetch(`/api/get-cabang-dinas/${kabupatenId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById(targetCabangInput).value = data.nama_cabang || "";
                document.getElementById(targetCabangHidden).value = data.id || "";
            });

        // Ambil daftar sekolah sesuai kabupaten yang dipilih
        if (targetSekolahSelect) {
            fetch(`/api/get-sekolah/${kabupatenId}`)
                .then(response => response.json())
                .then(data => {
                    let sekolahSelect = document.getElementById(targetSekolahSelect);
                    sekolahSelect.innerHTML = '<option value="">-- Pilih --</option>';
                    data.forEach(sekolah => {
                        sekolahSelect.innerHTML += `<option value="${sekolah.id}">${sekolah.nama_sekolah}</option>`;
                    });
                });
        }
    }
}

// Event listener untuk Kabupaten Asal & Tujuan
document.getElementById("kabupatenAsal").addEventListener("change", function () {
    updateCabangDinas(this.value, "cabangDinasAsal", "cabangDinasAsalId", "sekolahAsal");
});

document.getElementById("kabupatenTujuan").addEventListener("change", function () {
    updateCabangDinas(this.value, "cabangDinasTujuan", "cabangDinasTujuanId", "sekolahTujuan");
});

// Fungsi untuk memastikan hanya angka yang bisa diketik di input NIP/NIK
document.getElementById("guruNip").addEventListener("input", function () {
    this.value = this.value.replace(/\D/g, '').substring(0, 18); // Hanya angka, max 18 digit
});

document.getElementById("guruNik").addEventListener("input", function () {
    this.value = this.value.replace(/\D/g, '').substring(0, 16); // Hanya angka, max 16 digit
});

// 🔖 Mapping status ke label teks
const statusLabels = {
    '01':'01 : Usulan Telah diinput Cabang Dinas, tetapi belum dikirim ke Dinas Provinsi',
    '02':'02 : Usulan telah terkirim, tetapi belum diverifikasi oleh Dinas Provinsi',
    '03':'03 : Berkas Usulan telah diverifikasi (Lengkap) dan menunggu proses telaah Kabid GTK',
    '04':'04 : Usulan sudah ditelaah oleh Kabid. GTK (Disetujui) dan menunggu Rekomendasi Kepala Dinas',
    '05':'05 : Rekomendasi Kepala Dinas telah terbit',
    '06':'06 : Berkas Usulan Telah dikirim ke BKA',
    '07':'07 : SK Mutasi / Nota Dinas sebelumnya telah terbit'
};

// 🔖 Mapping jenis usulan ke label
const jenisLabels = {
    'mutasi_tetap':'Mutasi',
    'nota_dinas':'Nota Dinas',
    'perpanjangan_nota_dinas':'Perpanjangan Nota Dinas'
};

// Helper: nonaktifkan opsi "Nota Dinas"
async function prefilterJenisOptions(nip, nik) {
  const select = document.getElementById("jenis_usulan");
  const optND  = select?.querySelector("option[value='nota_dinas']");
  if (!optND || nip.length < 18 || nik.length < 16) return;

  try {
    const res = await fetch(`/api/check-nip-nik?nip=${encodeURIComponent(nip)}&nik=${encodeURIComponent(nik)}&jenis=nota_dinas&_=${Date.now()}`);
    const d = await res.json();

    const disableNd =
      (d.ndPndActiveStatus !== null && d.ndPndActiveStatus !== undefined) ||
      d.hasNdDocAnywhere === true ||
      (d.sameJenisStatus !== null && d.sameJenisStatus !== undefined) ||
      d.noteDone07 === true;

    optND.disabled = disableNd;
    optND.textContent = disableNd ? 'Nota Dinas (tidak tersedia)' : 'Nota Dinas';
    if (disableNd && select.value === 'nota_dinas') {
      select.value = 'perpanjangan_nota_dinas';
    }
  } catch (e) {
    console.warn('prefilterJenisOptions error:', e);
  }
}


function checkNipNikAvailability() {
  const nip   = document.getElementById("guruNip").value.trim();
  const nik   = document.getElementById("guruNik").value.trim();
  const jenis = document.getElementById("jenis_usulan").value;
  const submitBtn = document.querySelector("button[type='submit']");

  if (nip.length < 18 || nik.length < 16) {
    submitBtn.disabled = true;
    return;
  }

  fetch(`/api/check-nip-nik?nip=${nip}&nik=${nik}&jenis=${jenis}&_=${Date.now()}`)
    .then(r => r.json())
    .then(data => {
      submitBtn.disabled = true; // default: kunci, buka bila lolos

      // 🔥 CEK APAKAH ADA USULAN DITOLAK
    if (data.adaUsulanDitolak) {
        Swal.fire({
            icon: 'error',
            title: 'Usulan Sebelumnya Ditolak',
            html: `Terdapat usulan dengan NIP/NIK ini yang dinyatakan <span style="color:red; font-weight:bold;">DI TOLAK</span> oleh Kabid. GTK selama proses telaah usulan<br><br>
                Silakan <b>hapus usulan sebelumnya</b> terlebih dahulu sebelum mengajukan usulan baru.`,
            confirmButtonText: 'OK'
        });
        return;
    }

      const sameJenisStatus   = data.sameJenisStatus;
      const statusJenisLain   = data.statusJenisLainAktif;
      const ndPndActiveStatus = data.ndPndActiveStatus;
      const hasNdDocAnywhere  = data.hasNdDocAnywhere;

      const jenisText         = jenisLabels[jenis] || 'Jenis tidak diketahui';
      const statusSameText    = statusLabels[(sameJenisStatus||'')?.toString().padStart(2,'0')] || '';
      const statusLainText    = statusLabels[(statusJenisLain||'')?.toString().padStart(2,'0')] || '';
      const statusNdPndText   = statusLabels[(ndPndActiveStatus||'')?.toString().padStart(2,'0')] || '';

      // A) Ada proses awal (01–03) jenis lain → blok semua
      if (statusJenisLain !== null && statusJenisLain !== undefined && parseInt(statusJenisLain) <= 3) {
        Swal.fire({
          icon:'info',
          title:'Usulan Sebelumnya Masih Aktif',
          html:`<b>Status Usulan Sebelumnya:</b><br>${statusLainText}<br><br>
                <span style="color:#d33"><i class="fas fa-times-circle me-1"></i>
                Anda belum dapat mengajukan usulan apapun saat ini.</span>`
        });
        return;
      }

      // B) Jika ada usulan AKTIF dengan JENIS yang sama → blok
      if (sameJenisStatus !== null && sameJenisStatus !== undefined) {
        const s = parseInt(sameJenisStatus);
        if (s <= 3) {
          Swal.fire({
            icon:'info',
            title:'Usulan Aktif Masih Berjalan',
            html:`<b>Jenis Usulan:</b> ${jenisText}<br><br>
                  <b>Status Saat Ini:</b><br>${statusSameText}<br><br>
                  <span style="color:#d33"><i class="fas fa-times-circle me-1"></i>
                  Anda belum dapat mengajukan usulan apapun saat ini.</span>`
          });
          return;
        }
        if (s >= 4 && s <= 6) {
          Swal.fire({
            icon:'info',
            title:'Jenis Usulan Sama Masih Aktif',
            html:`<b>Jenis Usulan:</b> ${jenisText}<br><br>
                  <b>Status Saat Ini:</b><br>${statusSameText}<br><br>
                  <span style="color:#d33"><i class="fas fa-ban me-1"></i>
                  Silakan ajukan usulan baru dengan jenis yang <b>berbeda</b>.</span>`
          });
          return;
        }
      }

      // C) Aturan KHUSUS berdasarkan pilihan jenis
      if (jenis === 'nota_dinas') {
        if (ndPndActiveStatus !== null && ndPndActiveStatus !== undefined) {
          Swal.fire({
            icon:'warning',
            title:'Tidak Dapat Mengajukan Nota Dinas',
            html:`Ditemukan usulan <b>Nota Dinas/Perpanjangan ND</b> yang masih aktif.<br>
                  <b>Status:</b> ${statusNdPndText}`
          });
          return;
        }
        if (hasNdDocAnywhere) {
          Swal.fire({
            icon:'warning',
            title:'Tidak Dapat Mengajukan Nota Dinas',
            html:`Riwayat <b>Nota Dinas</b> sudah ada di sistem (baik dari Mutasi Tetap, ND, ataupun Perpanjangan).<br>
                  Silakan ajukan <b>Perpanjangan Nota Dinas</b>.`
          });
          return;
        }
      }

      if (jenis === 'perpanjangan_nota_dinas') {
        if (ndPndActiveStatus !== null && ndPndActiveStatus !== undefined) {
          Swal.fire({
            icon:'info',
            title:'Tidak Dapat Mengajukan Perpanjangan',
            html:`Masih ada usulan <b>Nota Dinas/Perpanjangan ND</b> yang aktif.<br>
                  <b>Status:</b> ${statusNdPndText}`
          });
          return;
        }
      }

      // ✅ Lolos semua rule → aktifkan submit
      submitBtn.disabled = false;
    })
    .catch(() => { submitBtn.disabled = true; });
}

// ✅ Pastikan event listener berjalan
document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("jenis_usulan").addEventListener("change", checkNipNikAvailability);
  document.getElementById("guruNip").addEventListener("input", checkNipNikAvailability);
  document.getElementById("guruNik").addEventListener("input", checkNipNikAvailability);
});


// Cegah double submit
document.querySelector('form').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn.disabled) {
        e.preventDefault();
        return;
    }
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
});
</script>


<?= $this->endSection(); ?>
