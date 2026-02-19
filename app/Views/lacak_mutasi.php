<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Usulan Mutasi | SIMUTASI</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- CSS Kustom -->
    <link rel="stylesheet" href="/assets/css/simutasi-landingpage.css">
</head>
<body class="login-body">

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card login-card shadow-lg">
                    <div class="card-body text-center p-4">
                        
                        <!-- Logo -->
                        <img src="/assets/img/dinaspendidikanaceh.png" class="track-logo" alt="SIMUTASI Logo">
                        
                        <!-- Judul -->
                        <h5 class="login-title mt-3"><i class="fas fa-search"></i> Lacak Usulan</h5>

                        <!-- Pesan Error -->
                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Form Pencarian -->
                        <form action="/lacak-mutasi/search" method="POST" class="mt-3">
                            <div class="form-group mb-3">
                                <input type="text" class="form-control form-control-user" name="nomor_usulan" placeholder="Masukkan Nomor Usulan" required>
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" class="form-control form-control-user" name="nip" placeholder="Masukkan NIP" required>
                            </div>

                            <button type="submit" class="btn btn-login w-100"><i class="fas fa-search"></i> Cari</button>
                        </form>

                        <!-- Link Kembali ke Beranda -->
                        <div class="mt-3">
                            <a href="/" class="info-link">
                                <i class="fas fa-arrow-left"></i> Beranda
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
