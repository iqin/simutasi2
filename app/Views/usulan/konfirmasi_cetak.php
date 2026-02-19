<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<div class="container">
    <h1 class="h3 mb-4 text-center text-gray-800">Konfirmasi Cetak Kartu Resi</h1>

    <div class="alert alert-success text-center">
        <p>Usulan dengan nomor <strong><?= $nomor_usulan; ?></strong> berhasil ditambahkan.</p>
        <p>Apakah Anda ingin mencetak kartu resi usulan?</p>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="/usulan" class="btn btn-secondary btn-sm-custom">
            <i class="fas fa-arrow-left"></i> Tidak, Kembali ke Daftar Usulan
        </a>
        <button class="btn btn-primary btn-sm-custom" onclick="handleCetakResi('<?= $nomor_usulan; ?>')">
            <i class="fas fa-print"></i> Ya, Cetak Resi
        </button>
    </div>
</div>
<script>
    function handleCetakResi(nomorUsulan) {
        // Buka tab baru untuk file PDF
        const newTab = window.open(`/usulan/generate-resi/${nomorUsulan}`, '_blank');

        // Kembalikan tab ini ke halaman daftar usulan
        if (newTab) {
            newTab.focus();
            window.location.href = '/usulan';
        } else {
            alert('Popup blocker mencegah membuka tab baru. Silakan izinkan popup untuk melanjutkan.');
        }
    }
</script>
<?= $this->endSection(); ?>
