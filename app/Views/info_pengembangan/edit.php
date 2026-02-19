<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<h1 class="h5 mb-4 text-gray-800"><i class="fas fa-info-circle"></i> Edit Info Pengembangan</h1>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?= base_url('kelola_info/update/' . $info['id']); ?>" method="post">
            <?= csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label"><strong>Judul</strong></label>
                <input type="text" name="judul" class="form-control" value="<?= esc($info['judul']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Deskripsi</strong></label>
                <textarea name="deskripsi" id="editor" class="form-control" required><?= html_entity_decode($info['deskripsi']); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Status</strong></label>
                <select name="status" class="form-control">
                    <option value="draft" <?= ($info['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                    <option value="public" <?= ($info['status'] === 'public') ? 'selected' : ''; ?>>Public</option>
                </select>
            </div>            
            <div class="d-flex justify-content-between">
                <a href="<?= base_url('kelola_info'); ?>" class="btn btn-sm-custom btn-secondary">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn btn-sm-custom btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>

<!-- **CDN Summernote & jQuery** -->
<?= $this->section('scripts'); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        if (typeof $.fn.summernote !== 'undefined') {
            $('#editor').summernote({
                height: 300,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']], // Mengaktifkan kembali "picture" agar bisa menyisipkan gambar via URL
                    ['view', ['codeview']]
                ],
                callbacks: {
                    onImageUpload: function() {
                        alert('Upload gambar langsung tidak diperbolehkan! Silakan gunakan URL gambar online.');
                    },
                    onInit: function() {
                        // Nonaktifkan upload gambar lokal, tetapi tetap izinkan penyisipan URL gambar
                        $(".note-image-input").prop('disabled', true).css('cursor', 'not-allowed');
                    }
                }
            });
        } else {
            console.error("Summernote tidak terdeteksi.");
        }
    });
</script>


<?= $this->endSection(); ?>
