<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet"> <!-- Sesuaikan dengan CSS utama -->
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            text-align: center;
            background-color: #eef2f7; /* Warna lebih soft */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .error-container {
            max-width: 500px;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-top: 6px solid #4e73df; /* Warna utama SIMUTASI */
        }
        .error-container img {
            max-width: 80%;
            height: auto;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 100px;
            color: #dc3545; /* Warna merah mencolok */
            font-weight: 700;
            margin: 0;
            text-shadow: 2px 4px 6px rgba(0, 0, 0, 0.2);
        }
        p {
            font-size: 18px;
            color: #6c757d;
            margin: 10px 0 20px;
        }
        a {
            text-decoration: none;
            background-color: #4e73df; /* Warna utama SIMUTASI */
            color: white;
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
            transition: 0.3s ease-in-out;
        }
        a:hover {
            background-color: #3753c8; /* Warna lebih gelap saat hover */
            transform: scale(1.05);
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #6c757d;
        }
        @media (max-width: 600px) {
            h1 {
                font-size: 70px;
            }
            p {
                font-size: 16px;
            }
            .error-container {
                padding: 30px;
            }
        }
    </style>
</head>
<body>

<div class="error-container">
    <h1>404</h1>
    <p>Oops! Halaman yang Anda cari tidak ditemukan.</p>
    <a href="<?= base_url(); ?>">Kembali ke Beranda</a>
</div>

<div class="footer">
    © 2025 SIMUTASI | Dinas Pendidikan Aceh
</div>

</body>
</html>
