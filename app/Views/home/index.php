<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMUTASI - Sistem Informasi Mutasi Guru</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- AOS (Animation On Scroll) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="/assets/css/simutasi-landingpage.css">
</head>
<body>

    <!-- Navbar -->
<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top shadow">
    <div class="container d-flex align-items-center">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="/assets/img/dinas-pendidikan-aceh.png" alt="Logo Instansi" width="120" height="40" class="me-2">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="#features">Fitur</a></li>
                <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                <li class="nav-item"><a class="nav-link" href="/login"><i class="fas fa-sign-in-alt"></i> Masuk</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<div class="hero-section">
    <h1 class="hero-title" data-aos="fade-up">Sistem Informasi Mutasi Guru</h1>
    <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="200">
        Mengelola proses mutasi guru dengan cepat, transparan, dan efisien.
    </p>
    <div class="mt-4">
        <a href="/lacak-mutasi" class="btn btn-main" data-aos="fade-up" data-aos-delay="400">
            <i class="fas fa-search"></i> Lacak Usulan
        </a>
    </div>
</div>

    <!-- Fitur -->
<!-- Fitur -->
<div class="container features" id="features">
    <div class="row">
        <!-- Integrasi Data -->
        <div class="col-md-4" data-aos="zoom-in">
            <div class="feature-box">
                <i class="fas fa-database"></i>
                <h4>Integrasi Data</h4>
                <p>Pengelolaan data mutasi guru terintegrasi, memastikan validasi cepat dan akurat.</p>
            </div>
        </div>

        <!-- Transparansi -->
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
            <div class="feature-box">
                <i class="fas fa-balance-scale"></i>
                <h4>Transparansi</h4>
                <p>Proses mutasi terdokumentasi dengan jelas, meminimalisir kesalahan administrasi.</p>
            </div>
        </div>

        <!-- Pelaporan -->
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="400">
            <div class="feature-box">
                <i class="fas fa-chart-line"></i>
                <h4>Pelaporan</h4>
                <p>Setiap tahapan mutasi dapat dipantau secara real-time oleh guru.</p>
            </div>
        </div>
    </div>
</div>


<!-- FAQ -->
<div class="container faq" id="faq">
    <h2 class="text-center mb-4">Pertanyaan Umum</h2>
    <div class="accordion" id="faqAccordion">

        <!-- Pertanyaan 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                    <i class="fas fa-question-circle me-2"></i> Bagaimana cara mengajukan mutasi?
                </button>
            </h2>
            <div id="faq1" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    Berikut langkah-langkahnya:
                    <ol>
                        <li><strong>Siapkan Berkas Usulan:</strong> Cetak dan unggah scan berkas PDF ke Google Drive.</li>
                        <li><strong>Serahkan ke Operator Cabang Dinas:</strong> Berikan berkas fisik dan tautan Google Drive.</li>
                        <li><strong>Terima Tanda Terima Usulan:</strong> Guru akan mendapatkan <i>Nomor Usulan</i> sebagai bukti pengajuan.</li>
                        <li><strong>Lacak Status Usulan:</strong> Gunakan <i>Nomor Usulan</i> untuk memantau proses mutasi hingga SK Mutasi diterbitkan.</li>
                    </ol>
                    <p class="mb-0"><strong>Catatan:</strong> Pastikan dokumen lengkap sebelum diserahkan agar proses berjalan lancar.</p>
                </div>
            </div>
        </div>

        
        <!-- Pertanyaan 2 -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                    <i class="fas fa-question-circle me-2"></i> Apa saja syarat yang harus dipenuhi untuk mengajukan mutasi?
                </button>
            </h2>
            <div id="faq2" class="accordion-collapse collapse">
                <div class="accordion-body">
                    Guru harus menyiapkan dokumen berikut dalam bentuk <strong>hardcopy</strong> dan <strong>softcopy</strong> (diunggah ke Google Drive):
                    <ul>
                        <li>Surat Pengantar dari Cabang Dinas Asal.</li>
                        <li>Surat Pengantar dari Kepala Sekolah Asal.</li>
                        <li>Surat Permohonan Pindah Tugas Bermaterai (Ditujukan Untuk Kepala Dinas).</li>
                        <li>Surat Permohonan Pindah Tugas Bermaterai (Ditujukan Untuk Kepala BKA).</li>
                        <li>Surat Permohonan Pindah Tugas Bermaterai (Ditujukan Untuk Gubernur cq Sekda Aceh).</li>
                        <li>Rekomendasi Kepala Sekolah Melepas Lengkap dengan Analisis (Jumlah jam, Siswa, Rombel, Guru Mapel Kurang atau Lebih).</li>                        
                        <li>Rekomendasi Melepas dari Pengawas Sekolah (Optional).</li>
                        <li>Rekomendasi Melepas & Menerima dari Pengawas Sekolah dan Kepala Cabang Dinas.</li>
                        <li>Rekomendasi Melepas dari Kepala Cabang Dinas Kab/Kota.</li>
                        <li>Rekomendasi Kepala Sekolah Menerima Lengkap dengan Analisis (Jumlah jam, Siswa, Rombel, Guru Mapel Kurang atau Lebih).</li>
                        <li>Rekomendasi Menerima dari Pengawas Sekolah (Optional).</li>
                        <li>Rekomendasi Menerima dari Kepala Cabang Dinas Kab/Kota.</li>
                        <li>Analisis Jabatan (Anjab) ditandatangani oleh Kepala Sekolah Melepas dan Mengetahui Kepala Dinas.</li>
                        <li>Surat Formasi GTK dari Sekolah Asal (Data Guru dan Tendik yang ditandatangani oleh Kepala Sekolah).</li>
                        <li>Foto Copy SK 80% dan SK Terakhir di Legalisir.</li>
                        <li>Foto Copy Karpeg dilegalisir.</li>
                        <li>Surat Keterangan tidak Pernah di Jatuhi Hukuman Disiplin ditandatangani oleh Kepala Sekolah Melepas.</li>
                        <li>Surat Keterangan Bebas Temuan Inspektorat ditandatangani oleh Kepala Sekolah Melepas (Optional).</li>
                        <li>Surat Keterangan Bebas Tugas Belajar/Izin Belajar ditandatangani oleh Kepala Sekolah Melepas.</li>
                        <li>Daftar Riwayat Hidup/ Riwayat Pekerjaan.</li>
                        <li>Surat Tugas Suami dan Foto Copy Buku Nikah (Optional).</li>                        
                    </ul>
                    <p class="mb-0"><strong>Catatan:</strong> Semua dokumen hardcopy diserahkan ke Operator Cabang Dinas, dan softcopy harus diunggah ke Google Drive.</p>
                </div>
            </div>
        </div>

        <!-- Pertanyaan 3 -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                    <i class="fas fa-question-circle me-2"></i> Bagaimana cara melacak status mutasi saya?
                </button>
            </h2>
            <div id="faq3" class="accordion-collapse collapse">
                <div class="accordion-body">
                    Guru dapat melacak status mutasi menggunakan <strong>Kartu Tanda Terima Usulan</strong> yang diberikan oleh Operator Cabang Dinas. Berikut langkah-langkahnya:
                    <ol>
                        <li><strong>Buka Halaman Lacak Usulan:</strong> Klik tombol <strong>Lacak Usulan</strong> di halaman utama atau akses langsung ke <a href="https://simutasi.pusakagtkaceh.id/lacak-mutasi" target="_blank">https://simutasi.pusakagtkaceh.id/lacak-mutasi</a>.</li>
                        <li><strong>Masukkan Nomor Usulan & NIP:</strong> Input data sesuai yang tertera pada tanda terima.</li>
                        <li><strong>Klik Tombol "Cari":</strong> Sistem akan menampilkan status terbaru proses mutasi Anda.</li>
                    </ol>
                    <p class="mb-0"><strong>Catatan:</strong> Jika ada kendala, silakan hubungi Operator Cabang Dinas untuk informasi lebih lanjut.</p>
                </div>
            </div>
        </div>


        <!-- Pertanyaan 4 -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                    <i class="fas fa-phone me-2"></i> Siapa yang dapat saya hubungi jika ada masalah dengan pengajuan mutasi?
                </button>
            </h2>
            <div id="faq5" class="accordion-collapse collapse">
                <div class="accordion-body">
                    Anda dapat menghubungi Layanan Informasi Pengaduan Guru dan Tenaga Kependidikan Dinas Pendidikan Aceh melalui Nomor Whatsapp : 
                  <strong>                
                  		<a href="https://wa.me/6285260000691" target="_blank" style="text-decoration: none;">
                          <i class="fab fa-whatsapp me-1 text-success"></i> 0852-6000-0691</a>
                  </strong>.
                </div>
            </div>
        </div>

    </div>
</div>

<div class="footer">
    <p>© 2025 SIMUTASI | Dinas Pendidikan Aceh</p>
    <div class="footer-icons">
        <a href="https://disdik.acehprov.go.id/"><i class="fab fa-chrome"></i></a>
        <a href="https://www.facebook.com/dinaspendidikanaceh"><i class="fab fa-facebook"></i></a>
        <a href="https://x.com/disdikacehprov"><i class="fab fa-x-twitter"></i></a>
        <a href="https://www.instagram.com/dinaspendidikanaceh/"><i class="fab fa-instagram"></i></a>
    </div>
</div>

    <!-- Bootstrap & AOS Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init();
    </script>

</body>
</html>
