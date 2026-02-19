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

<h1 class="h3 mb-4 text-gray-800"><i class="fas fa-undo-alt"></i> Revisi Usulan</h1>

<!-- Ringkasan Data Guru, Sekolah, dan History Usulan -->
<div class="card shadow-sm p-4 mb-4">
    <div class="row">
        <!-- Kolom 1: Data GTK -->
        <div class="col-md-4 mb-3 mb-md-0">
            <h5 class="text-primary"><i class="fas fa-user"></i> Data GTK</h5>
            <p><strong>Nama Guru:</strong> <br><?= $usulan['guru_nama']; ?></p>
            <p><strong>NIP:</strong> <br><?= $usulan['guru_nip']; ?></p>
            <p><strong>NIK:</strong> <br><?= $usulan['guru_nik']; ?></p>
        </div>

        <!-- Kolom 2: Data Sekolah -->
        <div class="col-md-4 mb-3 mb-md-0">
            <h5 class="text-primary"><i class="fas fa-school"></i> Data Sekolah</h5>
            <p><strong>Sekolah Asal:</strong> <br><?= $usulan['sekolah_asal']; ?></p>
            <p><strong>Sekolah / Instansi Tujuan:</strong> <br><?= $usulan['sekolah_tujuan']; ?></p>
            <p><strong>Alasan Mutasi:</strong> <br><?= $usulan['alasan']; ?></p>
        </div>

        <!-- Kolom 3: History Usulan -->
        <div class="col-md-4">
            <h5 class="text-primary"><i class="fas fa-history"></i> History Usulan</h5>
            <p>Menampilkan seluruh riwayat usulan berdasarkan NIP & NIK guru ini.</p>
            <button type="button" class="btn btn-sm-custom btn-info" data-toggle="modal" data-target="#riwayatModal">
                <i class="fas fa-search"></i> Lihat
            </button>
        </div>
    </div>
</div>

<!-- Navigasi Tab -->
<div class="card mt-3">
    <ul class="nav nav-tabs nav-custom">
        <li class="nav-item">
            <a class="nav-link disabled">1️⃣ Data GTK & Instansi</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="#">2️⃣ Revisi Berkas</a>
        </li>
    </ul>
    <div class="card-body">
        <form action="/usulan/updateRevisi/<?= $usulan['id'] ?>" method="post">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $jenis = $usulan['jenis_usulan'] ?? 'mutasi_tetap';

                    // Default Mutasi Tetap
                    $berkasLabels = [
                        0=>"Surat Pengantar dari Cabang Dinas Asal",
                        1=>"Surat Pengantar dari Kepala Sekolah",
                        2=>"Surat Permohonan Pindah Tugas Bermaterai (Ditujukan Untuk Kepala Dinas)",
                        3=>"Surat Permohonan Pindah Tugas Bermaterai (Ditujukan Untuk Kepala BKA)",
                        4=>"Surat Permohonan Pindah Tugas Bermaterai (Ditujukan Untuk Gubernur cq Sekda Aceh)",
                        5=>"Rekomendasi Kepala Sekolah Melepas Lengkap dengan Analisis",
                        6=>"Rekomendasi Melepas dari Pengawas Sekolah (Optional)",
                        7=>"Rekomendasi Melepas dari Kepala Cabang Dinas Kab/Kota",
                        8=>"Rekomendasi Kepala Sekolah Menerima Lengkap dengan Analisis",
                        9=>"Rekomendasi Menerima dari Pengawas Sekolah (Optional)",
                        10=>"Rekomendasi Menerima dari Kepala Cabang Dinas Kab/Kota",
                        11=>"Analisis Jabatan (Anjab) dari sekolah melepas dan sekolah menerima",
                        12=>"Surat Formasi GTK dari Sekolah Asal",
                        13=>"Foto Copy SK 80% dan SK Terakhir di Legalisir",
                        14=>"Foto Copy Karpeg dilegalisir",
                        15=>"Surat Keterangan tidak Pernah di Jatuhi Hukuman Disiplin",
                        16=>"Surat Keterangan Bebas Temuan Inspektorat (Optional)",
                        17=>"Surat Keterangan Bebas Tugas Belajar/Izin Belajar",
                        18=>"Daftar Riwayat Hidup/ Riwayat Pekerjaan",
                        19=>"Surat Tugas Suami dan Foto Copy Buku Nikah (Optional)",
                        20=>"SKP 2 Tahun Terakhir"
                    ];
                    $activeIndexes = range(0,20);
                    $optionalIndexes = [6,9,16,19];


                    // Nota Dinas
                    if ($jenis === 'nota_dinas') {
                        $berkasLabels[0]  = "Surat Pengantar dari Cabdin Asal";
                        $berkasLabels[2]  = "Permohonan Nota Dinas Bermaterai Ditujukan Kepada Kepala Dinas Pendidikan Aceh";
                        $berkasLabels[5]  = "Rekomendasi Kepsek Melepas + Analisis Kebutuhan Guru";
                        $berkasLabels[7]  = "Rekomendasi Cabdin Melepas";
                        $berkasLabels[8]  = "Rekomendasi Kepsek Menerima + Analisis Kebutuhan Guru";
                        $berkasLabels[10] = "Rekomendasi Cabdin Menerima";
                        $berkasLabels[13] = "Fotokopi SK 80% dan SK Terakhir";
                        $berkasLabels[17] = "Surat Keterangan Bebas Tugas Belajar dari Kepsek";
                        $berkasLabels[18] = "SKP 1 Tahun Terakhir";
                        $berkasLabels[19] = "Surat Izin Suami/Istri & Buku Nikah";

                        $activeIndexes = [0,2,5,7,8,10,13,17,18,19];
                        $optionalIndexes = [];
                    }
                    // Perpanjangan Nota Dinas
                    elseif ($jenis === 'perpanjangan_nota_dinas') {
                        $berkasLabels[0]  = "Surat Pengantar dari Cabdin (sesuai lokasi ND)";
                        $berkasLabels[1]  = "Surat Keterangan Aktif dari Kepsek (sesuai ND)";
                        $berkasLabels[19] = "Lampiran Nota Dinas Sebelumnya (upload)";
                        $activeIndexes = [0,1,19];
                        $optionalIndexes = [];
                    }
                    ?>

                    <?php 
                        $totalBerkas = ($jenis === 'mutasi_tetap') ? 21 : 20;
                        for ($index=0; $index<$totalBerkas; $index++): 
                        ?>
                        <?php if (in_array($index, $activeIndexes)): ?>
                            <div class="form-group mb-2">
                                <label for="googleDriveLink<?= $index ?>">
                                    Berkas <?= $index+1 ?> - <?= htmlspecialchars($berkasLabels[$index]) ?>
                                </label>
                                <div class="input-group">
                                    <input type="text" 
                                        name="google_drive_link[]" 
                                        id="googleDriveLink<?= $index ?>" 
                                        class="form-control drive-input"
                                        value="<?= isset($usulan_drive_links[$index]) ? $usulan_drive_links[$index] : '' ?>"
                                        <?= in_array($index, $optionalIndexes) ? '' : 'required' ?>>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-sm-custom btn-info" onclick="previewLink(<?= $index ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <small id="errorMsg<?= $index ?>" class="form-text"></small>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="google_drive_link[]" id="googleDriveLink<?= $index ?>" 
                                   value="<?= isset($usulan_drive_links[$index]) ? $usulan_drive_links[$index] : '' ?>">
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="/usulan" class="btn btn-sm-custom btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-sm-custom btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> Simpan Revisi
                </button>
            </div>
        </form>
    </div>
</div>
<!-- Modal Riwayat Usulan -->
<div class="modal fade" id="riwayatModal" tabindex="-1" role="dialog" aria-labelledby="riwayatModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="riwayatModalLabel">Riwayat Usulan</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span>&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<table class="table table-bordered table-sm" id="riwayatTable">
			<thead class="thead-light">
				<tr>
				<th>No</th>
				<th>Jenis Usulan</th>
				<th>Nomor Usulan</th>
				<th>Status</th>
				<th>Tanggal</th>
				<th>Aksi</th>
				</tr>
			</thead>
			<tbody></tbody>
			</table>
		</div>
		</div>
	</div>
</div>

<!-- Load Modal History usulan -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    $('#riwayatModal').on('shown.bs.modal', function () {
        loadRiwayatUsulan();
    });
});

function loadRiwayatUsulan() {
    const nik = "<?= $usulan['guru_nik'] ?>";
    const nip = "<?= $usulan['guru_nip'] ?>";
    const nomorUsulanAktif = "<?= $usulan['nomor_usulan'] ?? $nomor_usulan ?>";

    fetch(`/api/riwayat-usulan?nik=${nik}&nip=${nip}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector("#riwayatTable tbody");
            tbody.innerHTML = "";

            const statusLabels = {
                '01': '01 : Usulan Telah diinput Cabang Dinas',
                '02': '02 : Usulan telah dikirim',
                '03': '03 : Sudah diverifikasi',
                '04': '04 : Ditelaah Kabid',
                '05': '05 : Rekomendasi Kadis terbit',
                '06': '06 : Dikirim ke BKPSDM',
                '07': '07 : SK/Nota Dinas terbit'
            };

            const filtered = data.filter(item => item.nomor_usulan !== nomorUsulanAktif);

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center">Tidak ada riwayat usulan lainnya.</td></tr>`;
                return;
            }

            filtered.forEach((item, i) => {
                const statusKey = (item.status ?? '').padStart(2, '0');
                const statusText = statusLabels[statusKey] || 'Tidak diketahui';

                const tanggal = item.created_at ? item.created_at.split(' ')[0].split('-').reverse().join('-') : '-';

                tbody.innerHTML += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${item.jenis_usulan.toUpperCase().replace(/_/g, ' ')}</td>
                        <td>${item.nomor_usulan}</td>
                        <td>${statusText}</td>
                        <td>${tanggal}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                <i class="fas fa-copy"></i> Salin (Coming Soon)
                            </button>
                        </td>

                    </tr>
                `;
            });
        })
        .catch(() => {
            Swal.fire("Gagal", "Gagal mengambil data riwayat usulan.", "error");
        });
}
</script>

<!-- SCRIPT VALIDASI -->
<script>
const googleDrivePattern = /^(https?:\/\/)?(www\.)?(drive\.google\.com\/(file\/d\/|open\?id=|drive\/folders\/)).+/;
const optionalIndexes = <?= json_encode($optionalIndexes) ?>;

function validateLinks() {
    let allValid = true;
    let submitBtn = document.getElementById("submitBtn");
    document.querySelectorAll(".drive-input").forEach((input) => {
        const index = parseInt(input.id.replace("googleDriveLink",""));
        let linkValue = input.value.trim();
        let feedbackElement = document.getElementById(`errorMsg${index}`);
        if (optionalIndexes.includes(index) && !linkValue) {
            feedbackElement.innerHTML = "✅ Opsional (Boleh dikosongkan)";
            feedbackElement.style.color = "gray";
            return;
        }
        if (!linkValue) {
            feedbackElement.innerHTML = "❌ Data masih kosong";
            feedbackElement.style.color = "red";
            allValid = false;
        } else if (!googleDrivePattern.test(linkValue)) {
            feedbackElement.innerHTML = "❌ Tautan tidak valid!";
            feedbackElement.style.color = "red";
            allValid = false;
        } else {
            feedbackElement.innerHTML = "✔ Tautan valid";
            feedbackElement.style.color = "green";
        }
    });
    submitBtn.disabled = !allValid;
}
document.querySelectorAll(".drive-input").forEach((input) => {
    input.addEventListener("input", validateLinks);
});
document.addEventListener("DOMContentLoaded", validateLinks);

function previewLink(index) {
    let inputId = `googleDriveLink${index}`;
    let inputElement = document.getElementById(inputId);
    let link = inputElement.value.trim();
    if (!link) {
        Swal.fire('Peringatan!','Tautan belum diisi.','warning');
        return;
    }
    if (!googleDrivePattern.test(link)) {
        Swal.fire('Peringatan!','Masukkan tautan Google Drive yang valid!','warning');
        return;
    }
    window.open(link, '_blank');
}
</script>

<?= $this->endSection(); ?>
