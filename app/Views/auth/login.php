<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMUTASI</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="/assets/css/simutasi-landingpage.css">
</head>
<body class="login-body">

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card login-card shadow-lg">
                    <div class="card-body text-center p-4">
                        <img src="/assets/img/dinaspendidikanaceh.png" class="login-logo" alt="Logo Instansi">

                        <h5 class="login-title mt-3"><i class="fas fa-sign-in-alt"></i> Login</h5>

                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form id="loginForm" action="/auth/authenticate" method="post" class="mt-3">
                            <div class="form-group mb-3">
                                <input type="text" class="form-control form-control-user" name="username" placeholder="Username" required>
                            </div>
                            <div class="form-group mb-3">
                                <div class="position-relative">
                                    <input type="password" class="form-control form-control-user" name="password" id="password" placeholder="Password" required>
                                    <i class="fa fa-eye position-absolute toggle-password" id="togglePassword" style="cursor: pointer; right: 10px; top: 50%; transform: translateY(-50%);"></i>
                                </div>
                            </div>

                            <button type="submit" id="loginBtn" class="btn btn-login w-100">
                                <span id="btnText">Masuk</span>
                                <span id="spinner" class="spinner-border spinner-border-sm d-none"></span>
                            </button>
                        </form>

                        <div class="d-flex justify-content-between mt-3 info-links">
                            <a href="/" class="info-link">
                                <i class="fas fa-arrow-left"></i> Beranda
                            </a>
                            <a href="/lacak-mutasi" class="info-link">
                                <i class="fas fa-search"></i> Lacak Usulan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const passwordField = document.getElementById("password");
            const togglePassword = document.getElementById("togglePassword");

            togglePassword.addEventListener("click", function () {
                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    togglePassword.classList.remove("fa-eye");
                    togglePassword.classList.add("fa-eye-slash");
                } else {
                    passwordField.type = "password";
                    togglePassword.classList.remove("fa-eye-slash");
                    togglePassword.classList.add("fa-eye");
                }
            });

            document.getElementById("loginForm").addEventListener("submit", function () {
                let btn = document.getElementById("loginBtn");
                let btnText = document.getElementById("btnText");
                let spinner = document.getElementById("spinner");

                btnText.textContent = "Memproses...";
                spinner.classList.remove("d-none");
                
                btn.disabled = true;
            });
        });
    </script>

</body>
</html>
