<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-4 text-gray-800"><i class="fas fa-file-export"></i> Kirim Berkas ke BKA (Khusus Mutasi)</h1>

<div class="row">
    <!-- Tabel Kiri: Data Usulan Siap Dikirim -->
    <div class="col-md-6">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="text-primary"><i class="fas fa-info-circle"></i> 06.1. Berkas Siap Dikirim</label>
        <input type="text" id="filterNamaGuru" class="form-control w-50" placeholder="Filter Nama GTK..." onkeyup="filterTable('tableSiapKirim', this.value)">
    </div>
        <div class="table-responsive">
            <table id="tableSiapKirim" class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pilih</th>
                        <th>Nama GTK</th>
                        <th>NIP</th>                        
                        <th>Sekolah Asal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usulanSiapKirim)): ?>
                        <?php foreach ($usulanSiapKirim as $index => $usulan): ?>
                            <tr>
                                <td><?= $index + 1 + ($pagerSiapKirim->getCurrentPage('usulanSiapKirim') - 1) * $pagerSiapKirim->getPerPage('usulanSiapKirim') ?></td>
                                <td>
                                    <label class="custom-checkbox">
                                        <input type="checkbox" class="check-usulan"
                                            data-nomor="<?= $usulan['nomor_usulan']; ?>"
                                            data-nama="<?= $usulan['guru_nama']; ?>"
                                            data-sekolah-asal="<?= $usulan['sekolah_asal']; ?>"
                                            data-sekolah-tujuan="<?= $usulan['sekolah_tujuan']; ?>">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>                              
                                <td><?= $usulan['guru_nama']; ?></td>
                                <td><?= $usulan['guru_nip']; ?></td>                                  
                                <td><?= $usulan['sekolah_asal']; ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="showDetail(<?= htmlspecialchars(json_encode($usulan)) ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada berkas siap dikirim.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
            <!-- Pagination -->
        <div class="pagination-container">
            <?= $pagerSiapKirim->links('usulanSiapKirim', 'default_full'); ?>
        </div>
                <!-- Tabel Baru: 06.1.1 Data Akan Dikirim -->
        <div id="dataAkanDikirimContainer" class="mt-3 p-3 border rounded shadow-sm" style="display: none;">
            <label class="text-primary"><i class="fas fa-info-circle"></i> 06.1.1 Data Berkas Mutasi Akan Dikirim</label>
            <div class="table-responsive">
                <table id="tableAkanDikirim" class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama GTK</th>
                            <th>Sekolah Asal</th>
                            <th>Sekolah Tujuan</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- Tombol Kirim & Batal -->
            <button class="btn btn-success btn-sm-custom" onclick="kirimDataKeBKPSDM()">Kirim</button>
            <button class="btn btn-secondary btn-sm-custom" onclick="batalKirim()">Batal</button>
        </div>

            <!-- Detail Usulan (Ditampilkan saat tombol "View" diklik) -->
        <div id="detailData" class="detail-container">
            <label class="text-primary"><i class="fas fa-info-circle"></i> 06.1.1 Detail Usulan (Mutasi)</label>
            <table  class="table-responsive table-bordered detail-table">
                <tbody>
                    <tr><th>Nomor Usulan</th><td id="detailNomorUsulan"></td></tr>
                    <tr><th>Nama GTK</th><td id="detailNamaGuru"></td></tr>
                    <tr><th>NIP</th><td id="detailNIP"></td></tr>
                    <tr><th>NIK</th><td id="detailNIK"></td></tr>
                    <tr><th>Sekolah Asal</th><td id="detailSekolahAsal"></td></tr>
                    <tr><th>Sekolah Tujuan</th><td id="detailSekolahTujuan"></td></tr>
                    <tr><th>Alasan</th><td id="detailAlasan"></td></tr>
                    <tr>
                        <th>Berkas Usulan</th>
                        <td>
                            <button id="lihatBerkasBtn" class="btn btn-info btn-sm">
                                <i class="fas fa-file-pdf"></i> Lihat
                            </button>
                        </td>
                    </tr>
                    <tr><th>Tanggal Input Usulan</th><td id="detailTanggalInput"></td></tr>
                    <tr>
                        <th colspan="2" class="text-left text-primary custom-bg"><i class="fas fa-info-circle"></i> 
                            Rekomendasi Cabang Dinas</th>
                    </tr>  
                    <tr><th>Nama Cabang Dinas</th><td id="detailNamaCabang"></td></tr>
                    <tr><th>Dokumen Rekomendasi</th>
                        <td>
                            <a id="dokumenRekomendasiLink" href="#" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-file-pdf"></i> Lihat
                            </a>
                        </td>
                    </tr>
                    <tr><th>Operator</th><td id="detailOperator"></td></tr>
                    <tr><th>No. HP</th><td id="detailNoHP"></td></tr>
                    <tr><th>Tanggal Dikirimkan</th><td id="detailTanggalDikirim"></td></tr>
                    <tr>
                        <th colspan="2" class="text-left text-primary custom-bg"><i class="fas fa-info-circle"></i> 
                            Status Telaah Kabid GTK</th>
                    </tr>
                    <tr><th>Status Telaah</th><td id="detailStatusTelaah"></td></tr>
                    <tr><th>Tanggal Telaah</th><td id="detailTanggalTelaah"></td></tr>
                    <tr><th>Catatan Telaah</th><td id="detailCatatanTelaah"></td></tr>
                    <tr>
                        <th colspan="2" class="text-left text-primary custom-bg"><i class="fas fa-info-circle"></i> 
                            Rekomendasi Kadis</th>
                    </tr>
                    <tr><th>Tanggal Input Rekomendasi</th><td id="detailTanggalRekom"></td></tr>
                    <tr><th>Berkas Rekom Kadis</th>
                        <td>
                            <a id="fileRekomLink" href="#" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-file-pdf"></i> Lihat
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p></p>
            <button class="btn btn-secondary btn-sm-custom" onclick="hideDetail()">
                <i class="fas fa-window-close"></i>
            </button>
        </div>
        
        <!-- Modal Daftar Berkas -->
        <div class="modal fade" id="berkasModal" tabindex="-1" aria-labelledby="berkasModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="berkasModalLabel">Daftar Berkas Scan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Berkas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="berkasList">
                                    <tr><td colspan="3" class="text-center">Memuat data...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm-custom" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Tabel Kanan: Data Usulan yang Sudah Dikirim -->
    <div class="col-md-6">
        <div class="filter-section">
            <label class="text-primary"><i class="fas fa-info-circle"></i> 06.2. Berkas Sudah Dikirim</label>
            <div class="d-flex">
                <input type="text" id="filterBerkasDikirim" class="form-control filter-input" placeholder="Nama GTK..." onkeyup="filterTableBerkasDikirim()">
                <form method="get" id="perPageBerkasDikirimForm">
                    <select name="perPageBerkasDikirim" class="form-control" onchange="document.getElementById('perPageBerkasDikirimForm').submit();">
                        <option value="25" <?= $perPageBerkasDikirim == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $perPageBerkasDikirim == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $perPageBerkasDikirim == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table id="tableBerkasDikirim" class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Nama GTK</th>
                        <th>NIP</th>
                        <th>Sekolah Asal</th>            
                        <th>Tgl Kirim</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usulanSudahDikirim)): ?>
                        <?php foreach ($usulanSudahDikirim as $usulan): ?>
                            <tr>
                                <td><?= $usulan['guru_nama']; ?></td>
                                <td><?= $usulan['guru_nip']; ?></td>   
                                <td><?= $usulan['sekolah_asal']; ?></td>                             
                                <td><?= date('d-m-Y', strtotime($usulan['tglkirimbkpsdm'])); ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="showDetailKanan(<?= htmlspecialchars(json_encode($usulan)) ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada berkas yang telah dikirim.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="pagination-container">
            <?= $pagerSudahDikirim->links('usulanSudahDikirim', 'default_full'); ?>
        </div>
        <!-- Detail Usulan (Ditampilkan saat tombol "View" diklik) -->
        <div id="detailDataKanan" class="detail-container">
            <label class="text-primary"><i class="fas fa-info-circle"></i> 06.2.1 Detail Berkas Mutasi Sudah Dikirim</label>
            <table class="table table-responsive table-bordered detail-table">
                <tbody>
                    <tr><th>Nomor Usulan</th><td id="detailNomorUsulanKanan"></td></tr>
                    <tr><th>Nama GTK</th><td id="detailNamaGuruKanan"></td></tr>
                    <tr><th>NIP</th><td id="detailNIPKanan"></td></tr>
                    <tr><th>NIK</th><td id="detailNIKKanan"></td></tr>
                    <tr><th>Sekolah Asal</th><td id="detailSekolahAsalKanan"></td></tr>
                    <tr><th>Sekolah Tujuan</th><td id="detailSekolahTujuanKanan"></td></tr>
                    <tr><th>Alasan</th><td id="detailAlasanKanan"></td></tr>

                    <tr><th>Tanggal Input Usulan</th><td id="detailTanggalInputKanan"></td></tr>
                    <tr>
                        <th colspan="2" class="text-left text-primary custom-bg"><i class="fas fa-info-circle"></i> 
                            Rekomendasi Cabang Dinas</th>
                    </tr>
                    <tr><th>Nama Cabang Dinas</th><td id="detailNamaCabangKanan"></td></tr>
                    <tr><th>Dokumen Rekomendasi</th>
                        <td>
                            <a id="dokumenRekomendasiLinkKanan" href="#" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-file-pdf"></i> Lihat
                            </a>
                        </td>
                    </tr>
                    <tr><th>Operator</th><td id="detailOperatorKanan"></td></tr>
                    <tr><th>No. HP</th><td id="detailNoHPKanan"></td></tr>
                    <tr><th>Tanggal Dikirimkan</th><td id="detailTanggalDikirimKanan"></td></tr>
                    <tr>
                        <th colspan="2" class="text-left text-primary custom-bg"><i class="fas fa-info-circle"></i> 
                            Status Telaah Kabid GTK</th>
                    </tr>
                    <tr><th>Status Telaah</th><td id="detailStatusTelaahKanan"></td></tr>
                    <tr><th>Tanggal Telaah</th><td id="detailTanggalTelaahKanan"></td></tr>
                    <tr><th>Catatan Telaah</th><td id="detailCatatanTelaahKanan"></td></tr>
                    <tr>
                        <th colspan="2" class="text-left text-primary custom-bg"><i class="fas fa-info-circle"></i> 
                            Rekomendasi Kadis</th>
                    </tr>
                    <tr><th>Tanggal Input Rekomendasi</th><td id="detailTanggalRekomKanan"></td></tr>
                    <tr><th>Berkas Rekom Kadis</th>
                        <td>
                            <a id="fileRekomLinkKanan" href="#" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-file-pdf"></i> Lihat
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button class="btn btn-secondary btn-sm-custom" onclick="hideDetailKanan()">
                <i class="fas fa-window-close"></i>
            </button>
        </div>

    </div>
</div>
<script>

    document.addEventListener("DOMContentLoaded", function () {
        let lihatBerkasBtn = document.getElementById("lihatBerkasBtn");

        if (lihatBerkasBtn) {
            lihatBerkasBtn.addEventListener("click", function () {
                let nomorUsulan = document.getElementById("detailNomorUsulan").textContent;

                //console.log("[DEBUG] Nomor usulan dari detail:", nomorUsulan);

                if (!nomorUsulan) {
                //  console.error("[ERROR] Nomor usulan tidak ditemukan!");
                    alert("Nomor usulan tidak tersedia.");
                    return;
                }

                showBerkasModal(nomorUsulan);
            });
        }
    });

    document.querySelectorAll('.check-usulan').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const nomorUsulan = this.getAttribute('data-nomor');
            const namaGuru = this.getAttribute('data-nama');
            const sekolahAsal = this.getAttribute('data-sekolah-asal');
            const sekolahTujuan = this.getAttribute('data-sekolah-tujuan');

            if (this.checked) {
                // Tambahkan data ke tabel "06.1.2 Data Akan Dikirim"
                const row = `<tr data-nomor="${nomorUsulan}">
                                <td>${document.querySelectorAll('#tableAkanDikirim tbody tr').length + 1}</td>
                                <td>${namaGuru}</td>
                                <td>${sekolahAsal}</td>
                                <td>${sekolahTujuan}</td>
                             </tr>`;
                document.querySelector('#tableAkanDikirim tbody').insertAdjacentHTML('beforeend', row);
            } else {
                // Hapus data jika checkbox dicentang ulang
                document.querySelector(`#tableAkanDikirim tbody tr[data-nomor="${nomorUsulan}"]`).remove();
            }

            // Tampilkan tabel jika ada data yang dipilih
            document.getElementById('dataAkanDikirimContainer').style.display = document.querySelectorAll('#tableAkanDikirim tbody tr').length > 0 ? 'block' : 'none';
        });
    });

    // Tombol Batal
    function batalKirim() {
        document.querySelectorAll('.check-usulan:checked').forEach(checkbox => checkbox.checked = false);
        document.querySelector('#tableAkanDikirim tbody').innerHTML = '';
        document.getElementById('dataAkanDikirimContainer').style.display = 'none';
    }

    // Tombol Kirim
    function kirimDataKeBKPSDM() {
        const nomorUsulanList = Array.from(document.querySelectorAll('#tableAkanDikirim tbody tr'))
                                    .map(row => row.getAttribute('data-nomor'));

        if (nomorUsulanList.length === 0) {
            Swal.fire('Peringatan!', 'Pilih minimal satu data untuk dikirim.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Pengiriman Berkas Mutasi',
            html: "Apakah Anda yakin ingin mengirim berkas<b> Mutasi </b>ini ke BKA?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Kirim!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url('berkasbkpsdm/kirim') ?>', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest' // **Pastikan AJAX request**
                    },
                    body: JSON.stringify({ nomor_usulan: nomorUsulanList })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        let rincianHTML = `
                            <p style="text-align: center;">${data.message}</p>
                            <br><strong>Daftar Rincian:</strong>
                            <ul style="text-align: left; padding-left: 20px; margin-top: 5px;">
                        `;
                        data.daftar_rincian.forEach(item => {
                            rincianHTML += `<li>${item}</li>`;
                        });
                        rincianHTML += "</ul>";
                        Swal.fire({
                            title: 'Berhasil!',
                            html: rincianHTML,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal!', data.error, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error fetch:', error);
                    Swal.fire('Error!', 'Terjadi kesalahan. Coba lagi.', 'error');
                });
            }
        });
    }



    function showDetail(data) {
        document.getElementById('detailNomorUsulan').textContent = data.nomor_usulan || '-';
        document.getElementById('detailNamaGuru').textContent = data.guru_nama || '-';
        document.getElementById('detailNIP').textContent = data.guru_nip || '-';
        document.getElementById('detailNIK').textContent = data.guru_nik || '-';
        document.getElementById('detailSekolahAsal').textContent = data.sekolah_asal || '-';
        document.getElementById('detailSekolahTujuan').textContent = data.sekolah_tujuan || '-';
        document.getElementById('detailAlasan').textContent = data.alasan || '-';
        document.getElementById('detailTanggalInput').textContent = data.created_at ? new Date(data.created_at).toLocaleDateString('id-ID') : '-';
/*
        const berkasLink = document.getElementById('berkasUsulanLinkKiri');
        if (data.google_drive_link) {
            berkasLink.href = data.google_drive_link;
            berkasLink.style.display = 'inline-block';
        } else {
            berkasLink.style.display = 'none';
        }
*/
        // Data Rekomendasi Cabang Dinas
        document.getElementById('detailNamaCabang').textContent = data.nama_cabang || '-';
        document.getElementById('detailOperator').textContent = data.operator || '-';
        document.getElementById('detailNoHP').textContent = data.no_hp || '-';
        document.getElementById('detailTanggalDikirim').textContent = data.tanggal_dikirim ? new Date(data.tanggal_dikirim).toLocaleDateString('id-ID') : '-';

        const dokumenRekomendasiLink = document.getElementById('dokumenRekomendasiLink');
        if (data.dokumen_rekomendasi) {
            dokumenRekomendasiLink.href = `/uploads/rekomendasi/${data.dokumen_rekomendasi}`;
            dokumenRekomendasiLink.style.display = 'inline-block';
        } else {
            dokumenRekomendasiLink.style.display = 'none';
        }

        // Data Status Telaah Kabid GTK
        document.getElementById('detailStatusTelaah').textContent = data.status_telaah || '-';
        document.getElementById('detailTanggalTelaah').textContent = data.updated_at_telaah ? new Date(data.updated_at_telaah).toLocaleDateString('id-ID') : '-';
        document.getElementById('detailCatatanTelaah').textContent = data.catatan_telaah || '-';

        // Data Rekomendasi Kadis
        document.getElementById('detailTanggalRekom').textContent = data.tanggal_rekomkadis ? new Date(data.tanggal_rekomkadis).toLocaleDateString('id-ID') : '-';

        const fileRekomLink = document.getElementById('fileRekomLink');
        if (data.file_rekomkadis) {
            fileRekomLink.href = `/file/rekomkadis/${data.file_rekomkadis}`;
            fileRekomLink.style.display = 'inline-block';
        } else {
            fileRekomLink.style.display = 'none';
        }

        // Tampilkan detail
        document.getElementById('detailData').style.display = 'block';
        window.scrollTo(0, document.getElementById('detailData').offsetTop);
}


    function hideDetail() {
        document.getElementById('detailData').style.display = 'none';
    }

//filter cari guru siap kirim
    function filterTable(tableId, searchValue) {
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tr');
        const value = searchValue.toLowerCase();

        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let match = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j].textContent.toLowerCase().includes(value)) {
                    match = true;
                    break;
                }
            }

            rows[i].style.display = match ? '' : 'none';
        }
    }

    function showDetailKanan(data) {
        document.getElementById('detailNomorUsulanKanan').textContent = data.nomor_usulan || '-';
        document.getElementById('detailNamaGuruKanan').textContent = data.guru_nama || '-';
        document.getElementById('detailNIPKanan').textContent = data.guru_nip || '-';
        document.getElementById('detailNIKKanan').textContent = data.guru_nik || '-';
        document.getElementById('detailSekolahAsalKanan').textContent = data.sekolah_asal || '-';
        document.getElementById('detailSekolahTujuanKanan').textContent = data.sekolah_tujuan || '-';
        document.getElementById('detailAlasanKanan').textContent = data.alasan || '-';
        document.getElementById('detailTanggalInputKanan').textContent = data.created_at ? new Date(data.created_at).toLocaleDateString('id-ID') : '-';

        // Data Rekomendasi Cabang Dinas
        document.getElementById('detailNamaCabangKanan').textContent = data.nama_cabang || '-';
        document.getElementById('detailOperatorKanan').textContent = data.operator || '-';
        document.getElementById('detailNoHPKanan').textContent = data.no_hp || '-';
        document.getElementById('detailTanggalDikirimKanan').textContent = data.tanggal_dikirim 
            ? new Date(data.tanggal_dikirim).toLocaleDateString('id-ID') 
            : '-';

        const dokumenRekomendasiLinkKanan = document.getElementById('dokumenRekomendasiLinkKanan');
        if (data.dokumen_rekomendasi) {
            dokumenRekomendasiLinkKanan.href = `/uploads/rekomendasi/${data.dokumen_rekomendasi}`;
            dokumenRekomendasiLinkKanan.style.display = 'inline-block';
        } else {
            dokumenRekomendasiLinkKanan.style.display = 'none';
        }

        // Data Status Telaah Kabid GTK
        document.getElementById('detailStatusTelaahKanan').textContent = data.status_telaah || '-';
        document.getElementById('detailTanggalTelaahKanan').textContent = data.updated_at_telaah 
            ? new Date(data.updated_at_telaah).toLocaleDateString('id-ID') 
            : '-';
        document.getElementById('detailCatatanTelaahKanan').textContent = data.catatan_telaah || '-';

        // Data Rekomendasi Kadis
        document.getElementById('detailTanggalRekomKanan').textContent = data.tanggal_rekomkadis 
            ? new Date(data.tanggal_rekomkadis).toLocaleDateString('id-ID') 
            : '-';

        const fileRekomLinkKanan = document.getElementById('fileRekomLinkKanan');
        if (data.file_rekomkadis) {
            fileRekomLinkKanan.href = `/file/rekomkadis/${data.file_rekomkadis}`;
            fileRekomLinkKanan.style.display = 'inline-block';
        } else {
            fileRekomLinkKanan.style.display = 'none';
        }

        // Tampilkan detail
        document.getElementById('detailDataKanan').style.display = 'block';
        window.scrollTo(0, document.getElementById('detailDataKanan').offsetTop);
    }

    function hideDetailKanan() {
        document.getElementById('detailDataKanan').style.display = 'none';
    }


    function filterTableBerkasDikirim() {
        const input = document.getElementById('filterBerkasDikirim').value.toLowerCase();
        const table = document.getElementById('tableBerkasDikirim');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) { // Mulai dari baris ke-1 (skip header)
            let nomorUsulan = rows[i].getElementsByTagName('td')[0]?.textContent.toLowerCase() || '';
            let namaGuru = rows[i].getElementsByTagName('td')[1]?.textContent.toLowerCase() || '';

            if (nomorUsulan.includes(input) || namaGuru.includes(input)) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }

    function showBerkasModal(nomorUsulan) {
    document.getElementById('berkasList').innerHTML = `<tr><td colspan="3" class="text-center">Memuat data...</td></tr>`;

    fetch(`/usulan/getDriveLinks/${nomorUsulan}`)
        .then(response => response.json())
        .then(responseData => {
            if (!responseData || !responseData.data || responseData.data.length === 0) {
                document.getElementById('berkasList').innerHTML = `<tr><td colspan="3" class="text-center text-danger">Tidak ada data berkas</td></tr>`;
                return;
            }

            let berkasLabels = [
                "Surat Pengantar dari Cabang Dinas Asal",
                "Surat Pengantar dari Kepala Sekolah",
                "Surat Permohonan Pindah Tugas Bermaterai (Ditujukan Untuk Kepala Dinas)",
                "Surat Permohonan Pindah Tugas Bermaterai (Ditujukan Untuk Kepala BKA)",
                "Surat Permohonan Pindah Tugas Bermaterai (Ditujukan Untuk Gubernur cq Sekda Aceh)",
                "Rekomendasi Kepala Sekolah Melepas Lengkap dengan Analisis",
                "Rekomendasi Melepas dari Pengawas Sekolah (Optional)",
                "Rekomendasi Melepas dari Kepala Cabang Dinas Kab/Kota",
                "Rekomendasi Kepala Sekolah Menerima Lengkap dengan Analisis",
                "Rekomendasi Menerima dari Pengawas Sekolah (Optional)",
                "Rekomendasi Menerima dari Kepala Cabang Dinas Kab/Kota",
                "Analisis Jabatan (Anjab) dari sekolah melepas dan sekolah menerima",
                "Surat Formasi GTK dari Sekolah Asal",
                "Foto Copy SK 80% dan SK Terakhir di Legalisir",
                "Foto Copy Karpeg dilegalisir",
                "Surat Keterangan tidak Pernah di Jatuhi Hukuman Disiplin",
                "Surat Keterangan Bebas Temuan Inspektorat (Optional)",
                "Surat Keterangan Bebas Tugas Belajar/Izin Belajar",
                "Daftar Riwayat Hidup/ Riwayat Pekerjaan",
                "Surat Tugas Suami dan Foto Copy Buku Nikah (Optional)",
                "SKP 2 Tahun Terakhir"
            ];

            const optionalIndexes = [6, 9, 16, 19]; 


            let berkasList = document.getElementById('berkasList');
            berkasList.innerHTML = ""; 

            responseData.data.forEach((berkas, index) => {
                let row = document.createElement('tr');
                let driveLink = berkas.drive_link ? berkas.drive_link : "#";
                let berkasNama = berkasLabels[index] || `Berkas ${index + 1}`;

                let button;
                if (!berkas.drive_link) {
                    if (!optionalIndexes.includes(index)) {
                        button = `<button class="btn btn-danger btn-sm" onclick="showMissingFileAlert('${berkasNama}')">
                                    <i class="fas fa-exclamation-circle"></i> Kosong
                                  </button>`;
                    } else {
                        button = ""; // Jika opsional dan kosong, tombol tidak muncul
                    }
                } else {
                    button = `<a href="${driveLink}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat
                              </a>`;
                }

                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${berkasNama}</td>
                    <td>${button}</td>
                `;
                berkasList.appendChild(row);
            });
        })
        .catch(error => {
            document.getElementById('berkasList').innerHTML = `<tr><td colspan="3" class="text-center text-danger">Gagal memuat data</td></tr>`;
        });

    let modal = new bootstrap.Modal(document.getElementById("berkasModal"));
    modal.show();
}


</script>

<?= $this->endSection(); ?>
