<?php

// Load PHPMailer classes
require_once APPPATH . 'ThirdParty/PHPMailer/src/PHPMailer.php';
require_once APPPATH . 'ThirdParty/PHPMailer/src/SMTP.php';
require_once APPPATH . 'ThirdParty/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function send_email_phpmailer($to, $subject, $message)
{
    $mail = new PHPMailer(true);

    try {
        // Aktifkan debug untuk melihat komunikasi SMTP
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Matikan semua debug
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'simutasi.pusakagtkaceh@gmail.com';
        $mail->Password   = 'kvfmnwjabfjqpxag'; // GANTI DENGAN APP PASSWORD ANDA
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // 🔥 TAMBAHKAN OPSI INI UNTUK MENGATASI ERROR SSL (HANYA UNTUK DEVELOPMENT)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Pengirim dan penerima
        $mail->setFrom('simutasi.pusakagtkaceh@gmail.com', 'SIMUTASI');
        $mail->addAddress($to);

        // Konten
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Tampilkan error detail
        echo "<h3 style='color:red;'>PHPMailer Error:</h3>";
        echo "<pre>" . $mail->ErrorInfo . "</pre>";
        return false;
    }
}

/**
 * Mendapatkan template email standar SIMUTASI
 *
 * @param string $nama Nama penerima
 * @param string $jenisLabel Jenis usulan (Mutasi, Nota Dinas, dll)
 * @param string $nomor Nomor usulan
 * @param string $pesanUtama Pesan utama yang menjelaskan kejadian
 * @param string $status Teks status (misal: "01 - Usulan Direvisi")
 * @param string $link URL halaman lacak
 * @return string HTML email
 */
function getEmailTemplate(string $nama, string $jenisLabel, string $nomor, string $pesanUtama, string $status, string $link): string
{
    return "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <h3 style='color: #2c3e50;'>Assalamu'alaikum Wr. Wb.</h3>
        <p> Yth Bapak / Ibu " . htmlspecialchars($nama) . ",</p>
        
        <p>{$pesanUtama}</p>
        
        <p style='background: #f8f9fa; padding: 10px; border-left: 3px solid #28a745;'>
            <strong>Status:</strong> {$status}
        </p>
        
        <p>Silakan pantau melalui halaman lacak: <a href='{$link}' style='color: #007bff;'>Lacak Usulan</a></p>
        
        <hr style='border: 1px solid #eee;'>
        <p style='color: #6c757d; font-size: 12px;'>© Dinas Pendidikan Aceh</p>
        <p style='color: #6c757d; font-size: 10px;'>ini adalah email otomatis dari aplikasi SIMUTASI PusakaGTKAceh. Harap tidak membalas email ini.</p>
    </div>";
}