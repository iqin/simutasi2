<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-file-import"></i> Import Data Sekolah
</h1>

<div class="card">
    <div class="card-body">
        <p class="text-muted"><i class="fas fa-info-circle"></i> Silakan pilih kabupaten dan unggah file Excel.</p>

        <form id="importForm" enctype="multipart/form-data" method="post">
            <?= csrf_field(); ?>

            <div class="mb-3">
                <label for="kabupaten_id" class="form-label">Pilih Kabupaten</label>
                <select class="form-control" id="kabupaten_id" name="kabupaten_id" required>
                    <option value="">Pilih Kabupaten</option>
                    <?php foreach ($kabupaten as $row): ?>
                        <option value="<?= $row['id_kab']; ?>"><?= $row['nama_kab']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="file_excel" class="form-label">Unggah File Excel</label>
                <input type="file" class="form-control" id="file_excel" name="file_excel" accept=".xls,.xlsx" required>
            </div>

            <p><a href="/sekolah/download-template" class="btn btn-link"><i class="fas fa-download"></i> Unduh Template Excel</a></p>

                <button type="button" id="previewBtn" class="btn btn-info btn-sm-custom me-2 btn-sm-custom"><i class="fas fa-eye"></i> Pratinjau</button>
                <a href="/sekolah" class="btn btn-secondary btn-sm-custom">Batal</a>
        </form>
    </div>
</div>

<div id="preview-container" class="card mt-3 d-none">
    <div class="card-body">
        <h5 class="card-title">Pratinjau Data</h5>
        <p id="total-data"></p>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>NPSN</th>
                        <th>Nama Sekolah</th>
                        <th>Alamat</th>
                        <th>Jenjang</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="preview-body"></tbody>
            </table>
        </div>
        <button id="saveBtn" class="btn btn-success btn-sm-custom mt-3 d-none"><i class="fas fa-save"></i> Simpan ke Database</button>
    </div>
</div>

<!-- Tambahkan jQuery sebelum script lainnya -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $("#previewBtn").click(function() {
            let formData = new FormData($("#importForm")[0]);
            $.ajax({
                url: "/sekolah/previewExcel",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status === "success") {
                        $("#preview-container").removeClass("d-none");
                        $("#preview-body").html("");
                        $("#total-data").html(`<b>Total Data: ${response.total}</b>`);

                        response.data.forEach(row => {
                            $("#preview-body").append(`
                                <tr>
                                    <td>${row.npsn}</td>
                                    <td>${row.nama_sekolah}</td>
                                    <td>${row.alamat_sekolah}</td>
                                    <td>${row.jenjang}</td>
                                    <td>${row.status}</td>
                                </tr>
                            `);
                        });

                        $("#saveBtn").removeClass("d-none").data("json", response.data);
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal!",
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: "error",
                        title: "Terjadi Kesalahan!",
                        text: "Gagal memproses file!"
                    });
                }
            });
        });

        $("#saveBtn").click(function() {
            let kabupatenId = $("#kabupaten_id").val();

            if (!kabupatenId) {
                Swal.fire({
                    icon: "warning",
                    title: "Kabupaten tidak dipilih!",
                    text: "Silakan pilih kabupaten terlebih dahulu."
                });
                return;
            }

            let dataToSave = {
                kabupaten_id: kabupatenId,
                data: $(this).data("json")
            };

            $.ajax({
                url: "<?= site_url('sekolah/saveImportedData') ?>",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify(dataToSave),
                success: function(response) {
                    Swal.fire({
                        icon: response.error > 0 ? "warning" : "success",
                        title: response.error > 0 ? "Sebagian Gagal!" : "Berhasil!",
                        text: `Import selesai! Berhasil: ${response.success}, Gagal: ${response.error}`,
                        confirmButtonText: "OK"
                    }).then(() => {
                        if (response.success > 0) {
                            location.reload();
                        }
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: "error",
                        title: "Terjadi Kesalahan!",
                        text: "Gagal menyimpan data!"
                    });
                }
            });
        });
    });
</script>

<?= $this->endSection(); ?>
