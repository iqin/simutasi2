<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Data Kontak - SIMUTASI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1030bd 0%, #0051ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }

        .kontak-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            max-width: 420px;
            width: 100%;
            padding: 18px 16px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .logo {
            max-width: 50px;
            margin-bottom: 5px;
        }

        .instansi-text {
            font-size: 0.8rem;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .instansi-subtext {
            font-size: 0.7rem;
            color: #666;
            margin-top: 2px;
        }

        .title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
            margin: 8px 0 3px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .title i {
            color: #4e73df;
            font-size: 1.1rem;
        }

        .subtitle {
            font-size: 0.75rem;
            color: #666;
            text-align: center;
            margin-bottom: 15px;
            line-height: 1.3;
            padding: 0 5px;
        }

        /* INFO BOX - TETAP RAPI NAMUN TEKS LEBIH JELAS */
        .info-box {
            background: #f8f9fc;
            border-left: 3px solid #4e73df;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 18px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }

        .info-row {
            display: flex;
            align-items: baseline;
            margin-bottom: 8px;
            font-size: 0.85rem;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-row i {
            color: #4e73df;
            width: 20px;
            font-size: 0.85rem;
            margin-right: 6px;
        }

        .info-label {
            font-weight: 600;
            color: #2c3e50;
            min-width: 90px;
            font-size: 0.8rem;
        }

        .info-value {
            color: #1e293b;
            font-weight: 500;
            background: white;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid #e9ecef;
            font-size: 0.85rem;
        }

        /* Highlight untuk nomor usulan */
        .info-value.highlight {
            background: #e8f0fe;
            border-color: #4e73df;
            color: #1e3a8a;
            font-weight: 600;
        }

        .alert-error {
            background-color: #fef2f2;
            border-left: 3px solid #dc2626;
            color: #991b1b;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error i {
            font-size: 0.9rem;
            color: #dc2626;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
            font-size: 0.8rem;
        }

        .form-group label span {
            color: #dc2626;
            margin-left: 2px;
        }

        .form-group input {
            width: 100%;
            padding: 8px 10px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.8rem;
            transition: all 0.2s;
            background: #ffffff;
            height: 36px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4e73df;
            box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.15);
        }

        .form-group input::placeholder {
            color: #9ca3af;
            font-size: 0.75rem;
        }

        .form-group small {
            display: block;
            color: #6b7280;
            font-size: 0.65rem;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-group small i {
            color: #4e73df;
            font-size: 0.6rem;
        }

        .btn-submit {
            width: 100%;
            padding: 8px 12px;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(78, 115, 223, 0.2);
            height: 38px;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(78, 115, 223, 0.25);
        }

        .btn-submit i {
            font-size: 0.85rem;
        }

        .footer-note {
            text-align: center;
            margin-top: 15px;
            padding-top: 12px;
            border-top: 1px solid #edf2f7;
            color: #718096;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .footer-note i {
            color: #4e73df;
            font-size: 0.7rem;
        }

        .back-link {
            text-align: center;
            margin-top: 12px;
        }

        .back-link a {
            color: #64748b;
            text-decoration: none;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .back-link a:hover {
            color: #4e73df;
            background: #f8fafc;
        }

        /* Responsive */
        @media (max-width: 400px) {
            .kontak-card {
                padding: 15px 12px;
            }
            
            .title {
                font-size: 1.1rem;
            }
            
            .info-row {
                flex-wrap: wrap;
            }
            
            .info-label {
                min-width: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="kontak-card">
        <div class="header">
            <img src="/assets/img/logo.png" alt="Logo Pemerintah Aceh" class="logo">
            <div class="instansi-text">PEMERINTAH ACEH</div>
            <div class="instansi-subtext">DINAS PENDIDIKAN</div>
        </div>

        <div class="title">
            <i class="fas fa-address-card"></i>
            Lengkapi Data Kontak
        </div>
        <div class="subtitle">
            Usulan dalam proses memerlukan data kontak untuk notifikasi
        </div>

        <!-- INFO BOX - TETAP RAPI, TEKS LEBIH JELAS -->
        <div class="info-box">
            <div class="info-row">
                <i class="fas fa-hashtag"></i>
                <span class="info-label">Nomor Usulan</span>
                <span class="info-value highlight"><?= $nomorUsulan ?></span>
            </div>
            <div class="info-row">
                <i class="fas fa-id-card"></i>
                <span class="info-label">NIP</span>
                <span class="info-value"><?= $nip ?></span>
            </div>
            <div class="info-row">
                <i class="fas fa-user"></i>
                <span class="info-label">Nama Guru</span>
                <span class="info-value"><?= $nama ?></span>
            </div>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="/lacak-mutasi/update-kontak" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="nomor_usulan" value="<?= $nomorUsulan ?>">
            <input type="hidden" name="nip" value="<?= $nip ?>">

            <div class="form-group">
                <label>Email <span>*</span></label>
                <input 
                    type="email" 
                    name="email" 
                    value="<?= htmlspecialchars($email) ?>" 
                    placeholder="contoh@email.com" 
                    required
                >
                <small>
                    <i class="fas fa-info-circle"></i>
                    Untuk notifikasi perkembangan usulan
                </small>
            </div>

            <div class="form-group">
                <label>No. HP (WhatsApp) <span>*</span></label>
                <input 
                    type="text" 
                    name="no_hp" 
                    value="<?= htmlspecialchars($no_hp) ?>" 
                    placeholder="08xxxxxxxxxx" 
                    required
                >
                <small>
                    <i class="fas fa-info-circle"></i>
                    Untuk keperluan konfirmasi
                </small>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane"></i> 
                Lanjutkan
            </button>
        </form>

        <div class="back-link">
            <a href="/lacak-mutasi">
                <i class="fas fa-arrow-left"></i> 
                Kembali
            </a>
        </div>

        <div class="footer-note">
            <i class="fas fa-lock"></i> 
            Data Anda aman
        </div>
    </div>
</body>
</html>