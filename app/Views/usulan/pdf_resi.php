<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Resi Usulan Mutasi</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            width: 100%;
            padding: 20px;
            border: 2px solid #000;
            border-radius: 10px;
        }
        .header {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .content {
            margin-top: 20px;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            table-layout: fixed; /* Perubahan: Mencegah kolom berubah ukuran secara tidak proporsional */
        }
        .table-data td, .table-data th {
            border: 1px solid #000;
            padding: 6px;
            font-size: 14px;
            word-wrap: break-word; /* Perubahan: Memastikan teks panjang tidak melebar */
            white-space: pre-wrap; /* Perubahan: Memungkinkan teks panjang tetap dalam batas kolom */
        }
        .table-data th {
            background-color: #f1f1f1;
            text-align: left;
            width: 30%; /* Perubahan: Menjaga keseimbangan lebar kolom */
        }
        .table-data td {
            width: 70%; /* Perubahan: Memastikan teks berada dalam batas kolom */
        }
        .table-data td:nth-child(2) {
            max-width: 300px; /* Perubahan: Membatasi lebar maksimum kolom untuk teks panjang */
        }
        .footer {
            font-size: 10px;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Tanda Terima <br>Usulan Mutasi Guru dan Tenaga Kependidikan</div>

        <div class="content">
            <table class="table-data">
                <tr>
                    <th>Jenis Usulan</th>
                    <td><?= isset($usulan['jenis_usulan']) ? $usulan['jenis_usulan'] : 'Data tidak tersedia'; ?></td>
                </tr>
                <tr>
                    <th>No. Resi</th>
                    <td><?= isset($usulan['nomor_usulan']) ? $usulan['nomor_usulan'] : 'Data tidak tersedia'; ?></td>
                </tr>
                <tr>
                    <th>Nama Guru</th>
                    <td><?= isset($usulan['nama_guru']) ? $usulan['nama_guru'] : 'Data tidak tersedia'; ?></td>
                </tr>
                <tr>
                    <th>NIP</th>
                    <td><?= isset($usulan['nip']) ? $usulan['nip'] : 'Data tidak tersedia'; ?></td>
                </tr>
                <tr>
                    <th>NIK</th>
                    <td><?= isset($usulan['nik']) ? $usulan['nik'] : 'Data tidak tersedia'; ?></td>
                </tr>                
                <tr>
                    <th>Asal Sekolah</th>
                    <td><?= isset($usulan['sekolah_asal']) ? $usulan['sekolah_asal'] : 'Data tidak tersedia'; ?></td>
                </tr>
                <tr>
                    <th>Cabang Dinas</th>
                    <td><?= isset($usulan['nama_cabang']) ? $usulan['nama_cabang'] : 'Data tidak tersedia'; ?></td>
                </tr>
                <tr>
                    <th>Sekolah / Instansi Tujuan</th>
                    <td><?= isset($usulan['sekolah_tujuan']) ? $usulan['sekolah_tujuan'] : 'Data tidak tersedia'; ?></td>
                </tr>
                <tr>
                    <th>Alasan Mutasi</th>
                    <td><?= isset($usulan['alasan_mutasi']) ? $usulan['alasan_mutasi'] : 'Data tidak tersedia'; ?></td>
                </tr>
                <tr>
                    <th>Tanggal Usulan</th>
                    <td><?= isset($usulan['tanggal_usulan']) ? date('d/m/Y', strtotime($usulan['tanggal_usulan'])) : 'Data tidak tersedia'; ?></td>
                </tr>
                <tr>
                    <th>Tanggal Cetak</th>
                    <td><?= isset($tanggal_cetak) ? date('d/m/Y', strtotime($tanggal_cetak)) : 'Data tidak tersedia'; ?></td>

                </tr>
            </table>
        </div>

        <div class="footer">
            * Kartu ini merupakan tanda bukti pengajuan usulan mutasi Guru dan Tenaga Kependidikan. <br>
            <?= date('Y'); ?> © Dinas Pendidikan Aceh.
        </div>
    </div>
</body>
</html>
