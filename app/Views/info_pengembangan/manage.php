<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<h1 class="h4 mb-4 text-gray-800">
    <i class="fas fa-info-circle"></i> Kelola Info Pengembangan
</h1>

<div class="header-container">
    <div class="action-buttons">
        <a href="<?= base_url('kelola_info/create'); ?>" class="btn btn-primary btn-sm-custom">
            <i class="fas fa-plus-circle"></i> Tambah
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="bg-primary text-white">
            <tr>
                <th width="5%">No.</th>
                <th width="15%">Judul</th>
                <th>Deskripsi</th>
                <th width="10%">Status</th>
                <th width="12%">Tanggal</th>
                <th width="12%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1 + ($perPage * ($pager->getCurrentPage('info_pengembangan') - 1));
            foreach ($infos as $info): 
                $deskripsi_pendek = strip_tags(html_entity_decode($info['deskripsi']));
                $deskripsi_pendek = strlen($deskripsi_pendek) > 100 ? substr($deskripsi_pendek, 0, 100) . '...' : $deskripsi_pendek;
            ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= esc($info['judul']); ?></td>
                    <td>
                        <div class="deskripsi-container">
                            <?= $deskripsi_pendek; ?>
                            <?php if (strlen(strip_tags(html_entity_decode($info['deskripsi']))) > 100): ?>
                                <a href="#" class="text-primary lihat-selengkapnya" 
                                   data-id="<?= $info['id']; ?>"
                                   data-judul="<?= esc($info['judul']); ?>"
                                   data-deskripsi="<?= htmlspecialchars(html_entity_decode($info['deskripsi'])); ?>">
                                    Lihat Selengkapnya
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge <?= $info['status'] === 'public' ? 'badge-success' : 'badge-secondary'; ?>" 
                            style="padding: 5px 10px; font-size: 12px; border-radius: 10px;">
                            <?= ucfirst($info['status']); ?>
                        </span>
                    </td>

                    <td><?= date('d M Y', strtotime($info['tanggal'])); ?></td>
                    <td class="text-center">
                        <a href="<?= base_url('kelola_info/edit/' . $info['id']); ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="confirmDelete(<?= $info['id']; ?>)" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal untuk Menampilkan Deskripsi Lengkap -->
<div class="modal fade" id="modalDeskripsi" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Detail Deskripsi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalDeskripsiBody">
                <!-- Isi deskripsi akan dimuat di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm-custom" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // SweetAlert untuk notifikasi sukses
        <?php if (session()->getFlashdata('message')): ?>
            Swal.fire({
                title: 'Berhasil!',
                text: '<?= session()->getFlashdata('message'); ?>',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        // SweetAlert untuk notifikasi error
        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                title: 'Gagal!',
                text: '<?= session()->getFlashdata('error'); ?>',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        // Event listener untuk "Lihat Selengkapnya"
        document.querySelectorAll(".lihat-selengkapnya").forEach(item => {
            item.addEventListener("click", function(event) {
                event.preventDefault();
                
                let judul = this.getAttribute("data-judul");
                let deskripsi = this.getAttribute("data-deskripsi");
                
                document.getElementById("modalLabel").textContent = judul;
                document.getElementById("modalDeskripsiBody").innerHTML = deskripsi;
                
                let modal = new bootstrap.Modal(document.getElementById("modalDeskripsi"));
                modal.show();
            });
        });
    });

    // SweetAlert untuk konfirmasi hapus
    function confirmDelete(id) {
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('kelola_info/delete/'); ?>" + id;
            }
        });
    }
</script>

<?= $this->endSection(); ?>
