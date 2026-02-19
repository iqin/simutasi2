<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Lacak Usulan Mutasi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/simutasi-lacak.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    
    /* Mengurangi shadow agar lebih soft dan border seragam */
    .status-card {
        background: #f9f9f9;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 8px; /* Jarak antar card lebih rapat */
        box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.05); /* Shadow lebih ringan */
        border-left: 4px solid #007bff; /* Warna border seragam (Biru) */
    }

    /* Styling untuk tabel informasi */
    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    /* Header tabel */
    .info-table tr:first-child {
        background: #007bff;
    }
    
    /* Baris tabel */
    .info-table td {
        padding: 10px;
        border: 1px solid #ddd;
        font-size: 14px;
    }
    
    /* Membuat teks dalam tabel lebih readable */
    .info-table td strong {
        color: white;
        
    }
    
    /* Agar tabel lebih fleksibel di mobile */
    @media screen and (max-width: 768px) {
        .info-table td {
            display: block;
            width: 100%;
            text-align: left;
        }
    }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container">
            <div class="header">
                <img src="/assets/img/logo.png" alt="Logo Pemerintah Aceh">
                <h2>PEMERINTAH ACEH</h2>
                <h2>DINAS PENDIDIKAN</h2>
                <p>Jl. Tgk. Mohd. Daud Beureueh No.22, Telp. 22620 Banda Aceh Kodepos 23121</p>
            </div>

            <h3><i class="fas fa-history"></i> Riwayat Usulan</h3>

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

            <?php if (empty($results)) : ?>
                <p class="empty-data"><i class="fas fa-exclamation-triangle"></i> Data tidak ditemukan. Periksa kembali nomor usulan & NIP Anda.</p>
            <?php else : ?>
                <ul class="timeline">
                <?php foreach ($results as $data) : ?>
                    <li class="timeline-item">
                        <div class="status-card">
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
                                    <i class="fas fa-download"></i> Unduh
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
                           
                        </div>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            
            <a href="/lacak-mutasi" class="download-btn" style="background: #dc3545;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
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

            fetch('/lacak-mutasi/submit-saran', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => response.json())
            .then(data => {
                Swal.fire({
                    title: "Terkirim!",
                    text: data.message,
                    icon: "success",
                    confirmButtonText: "OK",
                    width: "350px",
                    padding: "15px",
                    timer: 3000,
                    /*customClass: {
                        popup: "small-swal-popup"
                    }*/
                }).then(() => {
                    document.getElementById('chatbox-container').style.display = 'none';
                    document.getElementById('email-guru').value = "";
                    document.getElementById('saran-text').value = "";
                });
            }).catch(error => {
                console.error("Error Fetch:", error);
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
