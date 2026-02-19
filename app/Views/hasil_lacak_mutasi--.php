<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Lacak Usulan Mutasi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/simutasi-lacak.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body class="track-body">

    <div class="container">
        <div class="card track-card shadow-lg">
            <div class="card-body text-center p-4">
                
                <!-- Logo -->
                <img src="/assets/img/dinaspendidikanaceh.png" class="track-logo" alt="SIMUTASI Logo">
                
                <!-- Judul -->
                <h5 class="track-title mt-3"><i class="fas fa-history"></i> Riwayat Usulan</h5>

                <!-- Tabel Informasi -->
                <table class="info-table">
                    <tr>
                        <td><strong>Nomor Usulan</strong></td>
                        <td><span class="highlight"><?= $nomorUsulan ?></span></td>
                        <td><strong>Sekolah Asal</strong></td>
                        <td><?= $sekolahAsal ?></td>
                    </tr>
                    <tr>
                        <td><strong>Nama Guru</strong></td>
                        <td><?= $namaGuru ?></td>
                        <td><strong>Sekolah Tujuan</strong></td>
                        <td><?= $sekolahTujuan ?></td>
                    </tr>
                    <tr>
                        <td><strong>NIP</strong></td>
                        <td><?= $nipGuru ?></td>
                        <td><strong>Tanggal Input</strong></td>
                        <td><?= date('d M Y', strtotime($tanggalUsulan)) ?></td>
                    </tr>
                </table>

                <!-- Cek apakah ada data -->
                <?php if (empty($results)) : ?>
                    <p class="empty-data"><i class="fas fa-exclamation-triangle"></i> Data tidak ditemukan. Periksa kembali nomor usulan & NIP Anda.</p>
                <?php else : ?>
                    <ul class="timeline">
                    <?php foreach ($results as $data) : ?>
                        <li class="timeline-item">
                            <p class="status"><i class="fas fa-check-circle"></i> <?= strtoupper($data['status']) ?> - <?= $data['catatan_history'] ?></p>
                            <p class="time">
                                <i class="far fa-calendar-alt"></i> 
                                <?php 
                                    $bulan = [
                                        'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
                                        'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
                                        'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                                        'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
                                    ];

                                    // Ambil tanggal dari database tanpa waktu
                                    $tanggal = date('d F Y', strtotime($data['updated_at']));
                                    $tanggal = str_replace(array_keys($bulan), array_values($bulan), $tanggal);

                                    echo $tanggal;
                                ?>
                            </p>

                            <?php if ($data['status'] == '07' && !empty($fileSK) && !empty($tokenSK)): ?>
                                <a href="/lacak-mutasi/download/sk/<?= $nomorUsulan; ?>/<?= $tokenSK; ?>" class="download-btn">
                                    <i class="fas fa-download"></i> Unduh <?= ($jenisMutasi === 'SK Mutasi') ? 'SK Mutasi' : 'Nota Dinas'; ?>
                                </a>
                            <?php endif; ?>

                            <?php if ($data['status'] == '05' && !empty($fileRekomKadis) && !empty($tokenRekom)): ?>
                                <a href="/lacak-mutasi/download/rekom/<?= $nomorUsulan; ?>/<?= $tokenRekom; ?>" class="download-btn">
                                    <i class="fas fa-download"></i> Unduh Rekomendasi Kadis
                                </a>
                            <?php endif; ?>
                            <?php
                                $statusTelaah = $pengirimanUsulan['status_telaah'] ?? null;
                            ?>

                            <?php if ($data['status'] == '02' && !empty($fileDokumenRekom) && !empty($tokenDokumenRekom)): ?>
                                <?php if ($statusTelaah === null): ?>
                                    <a href="/lacak-mutasi/download/dokumen/<?= $nomorUsulan; ?>/<?= $tokenDokumenRekom; ?>" class="download-btn">
                                        <i class="fas fa-download"></i> Unduh Dokumen Rekomendasi
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                           
                            <?php if ($data['status'] == '01' && !empty($googleDriveLink)): ?>
                                <a href="<?= $googleDriveLink; ?>" target="_blank" class="download-btn">
                                    <i class="fas fa-download"></i> Unduh Dokumen
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                
                <!-- Tombol Kembali -->
                <a href="/lacak-mutasi" class="btn btn-danger w-100 mt-3">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Chatbox Saran -->
    <div id="chatbox-container">
        <div id="chatbox-header">
            <span><i class="fas fa-comment-dots"></i> Kotak Saran</span>
            <button id="close-chatbox">&times;</button>
        </div>
        <div id="chatbox-body">
        <p class="saran-ajakan">
            <i class="fas fa-info-circle"></i> 
            Kami menghargai masukan Anda! Berikan saran untuk perbaikan proses mutasi.
        </p>


            <label  align="left">Email <span class="text-danger">*</span></label>
            <input type="email" id="email-guru" placeholder="Masukkan email Anda" required>

            <label  align="left">Saran <span class="text-danger">*</span></label>
            <textarea id="saran-text" placeholder="Tulis saran Anda di sini..." rows="3" required></textarea>
        </div>
        <div id="chatbox-footer">
            <button id="send-saran" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim</button>
        </div>
    </div>

    <!-- Tombol Buka Chatbox -->
    <div id="chatbox-toggle">
        <i class="fas fa-comment-alt"></i>
    </div>

    <footer class="footer">
        <p>&copy; 2025 SIMUTASI | Dinas Pendidikan Aceh</p>
    </footer>

    <script>
        document.getElementById('chatbox-toggle').addEventListener('click', function() {
            document.getElementById('chatbox-container').style.display = 'block';
        });

        document.getElementById('close-chatbox').addEventListener('click', function() {
            document.getElementById('chatbox-container').style.display = 'none';
        });

        document.getElementById('send-saran').addEventListener('click', function() {
            var email = document.getElementById('email-guru').value;
            var saranText = document.getElementById('saran-text').value;

            if (email.trim() === '' || saranText.trim() === '') {
                alert('Email dan saran wajib diisi!');
                return;
            }

            var formData = new FormData();
            formData.append('nomor_usulan', '<?= $nomorUsulan ?>');
            formData.append('email', email);
            formData.append('saran', saranText);
            
            console.log("Mengirim data:", Object.fromEntries(formData.entries()));

            fetch('/lacak-mutasi/submit-saran', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => response.json())
            .then(data => {
                Swal.fire({
                    title: "Terima Kasih!",
                    text: data.message,
                    icon: "success",
                    confirmButtonText: "OK",
                    width: "400px",
                    padding: "15px",
                    timer: 3000,
                    customClass: {
                        popup: "small-swal-popup"
                    }
                }).then(() => {
                    document.getElementById('chatbox-container').style.display = 'none';
                    document.getElementById('email-guru').value = "";
                    document.getElementById('saran-text').value = "";
                });
            }).catch(error => {
                Swal.fire({
                    title: "Error!",
                    text: "Terjadi kesalahan, coba lagi.",
                    icon: "error",
                    confirmButtonText: "OK",
                    width: "350px",
                    padding: "15px"
                });
            });

        });

        // Langsung tampilkan chatbox saat halaman selesai dimuat
        window.addEventListener('DOMContentLoaded', function () {
            document.getElementById('chatbox-container').style.display = 'block';
        });
    </script>
</body>
</html>

</body>
</html>
