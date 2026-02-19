<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<style>
    /* Styling untuk tampilan lebih clean */
    .info-title {
        font-size: 16px !important;
        font-weight: bold !important;
        margin-bottom: 2px !important;
    }

    .info-date {
        font-size: 11px !important;
        color: #6c757d !important;
        display: block !important;
        margin-bottom: 5px !important;
    }

    .info-content p {
        font-size: 14px !important;
        line-height: 1.6 !important;
        color: #5a5a5a !important;
        font-family: Arial, sans-serif !important;
        margin-bottom: 10px !important; /* Beri ruang antar paragraf */
    }

    .info-content ul, .info-content ol {
        padding-left: 18px !important;
    }

    .info-content img {
        max-width: 100% !important; /* Pastikan gambar tidak melebihi batas */
        height: auto !important; /* Pastikan proporsi tetap */
        display: block !important; /* Hindari inline styling */
        margin: 10px 0 !important; /* Beri ruang agar tidak terlalu menempel */
    }

    .info-card {
        border: none !important;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1) !important;
        padding: 15px !important;
    }

    .info-list {
        padding: 0 !important;
    }

    .info-item {
        border: none !important;
        padding-bottom: 10px !important;
    }
</style>

<h1 class="h5 mb-4 text-gray-800"><i class="fas fa-info-circle"></i> Info Pengembangan</h1>

<div class="card mb-4 info-card">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error'); ?></div>
    <?php endif; ?>
    
    <div class="card-body">
        <ul class="list-group list-group-flush info-list">
            <?php if (empty($infos)): ?>
                <li class="list-group-item info-item text-center">
                    <p class="text-muted">Tidak ada informasi yang tersedia.</p>
                </li>
            <?php else: ?>
                <?php foreach ($infos as $info): ?>
                    <li class="list-group-item info-item">
                        <h6 class="text-primary info-title"><?= esc($info['judul']); ?></h6>
                        <small class="info-date">
                        <i class="fas fa-calendar-alt text-secondary"></i> 
                            <?= date('d M Y', strtotime($info['tanggal'])); ?>
                        </small>
                        <div class="info-content">
                            <?= html_entity_decode($info['deskripsi']); ?> <!-- Hanya tampilkan status "public" -->
                        </div>
                        <hr class="my-2">
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.info-content').forEach(el => {
            console.log("Font Size:", window.getComputedStyle(el).fontSize);
            console.log("Line Height:", window.getComputedStyle(el).lineHeight);
            console.log("Font Family:", window.getComputedStyle(el).fontFamily);
        });
    });
</script>

<?= $this->endSection(); ?>
