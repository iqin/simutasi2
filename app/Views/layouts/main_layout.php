<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard'; ?></title>

    <!-- Custom fonts for this template -->
    <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="<?= base_url('assets/css/sb-admin-2.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/simutasi-style.css'); ?>"> <!-- Pastikan CSS overlay tetap ada -->

    <link rel="stylesheet" href="<?= base_url('assets/css/loading-fullscreen.css') ?>">

</head>

<body id="page-top" class="d-flex flex-column min-vh-100">

    <!-- Page Wrapper -->
    <div id="wrapper" class="d-flex flex-grow-1">
        <!-- Sidebar -->
        <?= $this->include('layouts/sidebar'); ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column flex-grow-1">
            <!-- Main Content -->
            <div id="content" class="flex-grow-1 d-flex flex-column position-relative"> <!-- 🔹 Tambahkan `position-relative` -->
                <!-- Topbar -->
                <?= $this->include('layouts/topbar'); ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid flex-grow-1 position-relative"> <!-- 🔹 Tambahkan `position-relative` -->
                    <!-- 🔹 Loading Overlay Hanya di Area Konten -->
                    <div id="loadingOverlay" class="loading-overlay" style="position: absolute; width: 100%; height: 100%; z-index: 999;">
                        <div class="loading-content">
                            <div class="loader">
                                <span></span> <!-- Animasi Loader dari CSS -->
                            </div>
                            <p class="mt-2">Menyimpan data, harap tunggu...</p>
                        </div>
                    </div>

                    <?= $this->renderSection('content'); ?>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white mt-auto">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Simutasi 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Bootstrap core JavaScript -->
    <script src="<?= base_url('assets/vendor/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>

    <!-- Core plugin JavaScript -->
    <script src="<?= base_url('assets/vendor/jquery-easing/jquery.easing.min.js'); ?>"></script>
    <!-- Custom scripts for all pages -->
    <script src="<?= base_url('assets/js/sb-admin-2.min.js'); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('assets/js/simutasi-overlay.js'); ?>"></script>

    <script src="<?= base_url('assets/js/loading-fullscreen.js') ?>"></script>
    <!-- Fullscreen Loading Overlay HTML -->
    <div id="fullscreenLoading">
        <div class="loading-container">
            <div class="loading-spinner">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="loading-title" id="loadingTitle">SIMUTASI</div>
            <div class="loading-message" id="loadingMessage">Sedang Memproses Data</div>
            <div class="loading-submessage" id="loadingSubMessage">Harap tunggu, sistem sedang bekerja...</div>
            <div class="loading-progress">
                <div class="loading-progress-bar"></div>
            </div>
            <div class="loading-warning">
                <i class="fas fa-exclamation-circle"></i>
                Jangan refresh atau tutup halaman
            </div>
        </div>
    </div>

        <!-- Debugging: Cek versi jQuery -->
    <script>
        console.log("jQuery version:", $.fn.jquery);
    </script>
    
    <!-- Load Summernote hanya jika dibutuhkan -->
    <?= $this->renderSection('scripts'); ?>
</body>

</html>
