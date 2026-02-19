<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<!-- 🔹 BARIS PERTAMA: 4 KOTAK STATISTIK -->
<div class="row">
    <?php 
    $cards = [
        ["01 : Usulan Mutasi", "Semua Tahun", $total_usulan, "fa-file-alt", "dashboard-card-blue", []],
        ["02 : Usulan Cabdin", "Tahun $tahun_saat_ini", $total_usulan_cabdin, "fa-paper-plane", "dashboard-card-yellow", [
            ["bg-primary text-white", "Terkirim: $total_terkirim"],
            ["bg-danger text-white", "Blm. Kirim: $usulan_belum_dikirim"]
        ]],
        ["03 : Verifikasi Dinas", "Tahun $tahun_saat_ini", $total_verif_dinas, "fa-check-circle", "dashboard-card-red", [
            ["bg-success text-white", "Lengkap: $total_lengkap"],
            ["bg-danger text-white", "Tdk. Lengkap: $total_tdk_lengkap"]
        ]],
        ["04 : Telaah Usulan", "Tahun $tahun_saat_ini", $total_telaah_kabid, "fa-user-tie", "dashboard-card-yellow", [
            ["bg-success text-white", "Disetujui: $telaah_disetujui"],
            ["bg-danger text-white", "Ditolak: $telaah_ditolak"]
        ]],
    ];
    foreach ($cards as $card) : ?>
        <div class="col-xl-3 col-md-6 col-sm-12 mb-4">
            <div class="dashboard-card-custom <?= $card[4]; ?>">
                <div class="dashboard-card-body">
                    <div class="dashboard-card-header">
                        <span class="dashboard-title"><?= $card[0]; ?></span>
                        <span class="dashboard-subtitle"><?= $card[1]; ?></span>
                    </div>
                    <!-- 🔹 Badge Status -->
                    <div class="dashboard-card-content">
                        <?php foreach ($card[5] as $badge) : ?>
                            <span class="badge <?= $badge[0]; ?> px-2 py-1 rounded-pill"><?= $badge[1]; ?></span>
                        <?php endforeach; ?>
                    </div>

                    <!-- 🔹 Footer: Angka & Ikon di Sebelah Kanan -->
                    <div class="dashboard-card-footer d-flex justify-content-end align-items-center">
                        <span class="dashboard-count me-2"><?= $card[2]; ?></span>
                        <i class="fas <?= $card[3]; ?> dashboard-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- 🔹 BARIS KEDUA: 3 KOTAK STATISTIK -->
<div class="row">
    <?php 
    $cards2 = [
        ["05 : Rekomendasi Kadis", "Tahun $tahun_saat_ini", $rekom_kadis, "fa-clipboard-check", "dashboard-card-green", [
            ["bg-success text-white", "Ada Rekom: $rekom_kadis_ada"],
            ["bg-danger text-white", "Belum Ada: $rekom_kadis_belum"]
        ]],
        ["06 : Dikirim ke BKA", "Tahun $tahun_saat_ini", $dikirim_bkpsdm, "fa-share-square", "dashboard-card-blue", [
            ["bg-success text-white", "Sudah: $kirim_bka_sudah"],
            ["bg-danger text-white", "Belum: $kirim_bka_belum"]
        ]],
        ["07 : SK Mutasi / ND Terbit", "Tahun $tahun_saat_ini", $terbit_sk, "fa-file-signature", "dashboard-card-purple", [
            ["bg-success text-white", "Nota Dinas: $nota_dinas"],
            ["bg-primary text-white", "SK Mutasi: $sk_mutasi"],
            //["bg-danger text-white", "Belum Terbit: $belum_terbit"]
        ]]
    ];
    foreach ($cards2 as $card) : ?>
        <div class="col-xl-4 col-md-6 col-sm-12 mb-4">
            <div class="dashboard-card-custom <?= $card[4]; ?>">
                <div class="dashboard-card-body">
                    <div class="dashboard-card-header">
                        <span class="dashboard-title"><?= $card[0]; ?></span>
                        <span class="dashboard-subtitle"><?= $card[1]; ?></span>
                    </div>
                    <!-- 🔹 Badge Status -->
                    <div class="dashboard-card-content">
                        <?php foreach ($card[5] as $badge) : ?>
                            <span class="badge <?= $badge[0]; ?> px-2 py-1 rounded-pill"><?= $badge[1]; ?></span>
                        <?php endforeach; ?>
                    </div>

                    <!-- 🔹 Footer: Angka & Ikon di Sebelah Kanan -->
                    <div class="dashboard-card-footer d-flex justify-content-end align-items-center">
                        <span class="dashboard-count me-2"><?= $card[2]; ?></span>
                        <i class="fas <?= $card[3]; ?> dashboard-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


<!-- 🔹 BARIS KETIGA: GRAFIK -->
<div class="row">
    <!-- Grafik Bar/Line: Usulan Per Bulan -->
    <div class="col-xl-8 col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Jumlah Usulan Mutasi Per Bulan</h6>
                <select id="filterTahun" class="form-control form-control-sm" style="width: 120px;">
                    <!-- Pilihan tahun akan di-load dari database -->
                </select>
            </div>
            <div class="card-body">
                <canvas id="chartUsulan"></canvas>
            </div>
        </div>
    </div>

    <!-- Pie Chart: Distribusi Status Usulan -->
    <div class="col-xl-4 col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Distribusi Status Usulan</h6>
            </div>
            <div class="card-body">
                <canvas id="chartPie"></canvas>
            </div>
        </div>
    </div>
</div>
<!-- 🔹 Tabel Ringkasan Usulan Terbaru -->
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Usulan Mutasi Terbaru</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="tableUsulan" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Usulan</th>
                                <th>Nama Guru</th>
                                <th>Sekolah Asal</th>
                                <th>Sekolah Tujuan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="latestUsulanBody">
                            <tr><td colspan="6" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    let yearSelect = document.getElementById("filterTahun");

    // Ambil daftar tahun dari database
    fetch("<?= base_url('dashboard/getAvailableYears'); ?>")
        .then(response => response.json())
        .then(years => {
            yearSelect.innerHTML = ""; // Kosongkan dropdown

            if (years.length === 0) {
                yearSelect.innerHTML = "<option value=''>Tidak Ada Data</option>";
                return;
            }

            years.forEach(year => {
                let option = document.createElement("option");
                option.value = year;
                option.textContent = year;
                yearSelect.appendChild(option);
            });

            // Pilih tahun terbaru secara default & load chart + tabel
            yearSelect.value = years[0];
            loadCharts(years[0]);
            loadLatestUsulan();
        })
        .catch(error => console.error("Error fetching available years:", error));

    function loadCharts(year) {
        fetch(`<?= base_url('dashboard/getChartData'); ?>?year=${year}`)
            .then(response => response.json())
            .then(data => {
                console.log("Data Chart:", data); // Debugging console.log

                if (window.barChart) {
                    window.barChart.destroy();
                }
                if (window.pieChart) {
                    window.pieChart.destroy();
                }

                // Cek apakah data untuk grafik tidak kosong
                let isDataEmpty = data.usulan_per_bulan.every(val => val === 0);
                let isPieEmpty = data.data_pie.every(val => val === 0);

                // Bar Chart: Usulan per Bulan
                let ctx = document.getElementById("chartUsulan").getContext("2d");
                window.barChart = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: data.labels_bulan,
                        datasets: [{
                            label: "Jumlah Usulan",
                            data: data.usulan_per_bulan,
                            backgroundColor: "rgba(54, 162, 235, 0.6)",
                            borderColor: "rgba(54, 162, 235, 1)",
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: !isDataEmpty },
                            tooltip: { enabled: !isDataEmpty }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });

                // Pie Chart: Distribusi Status Usulan
                let ctxPie = document.getElementById("chartPie").getContext("2d");
                window.pieChart = new Chart(ctxPie, {
                    type: "pie",
                    data: {
                        labels: data.labels_pie,
                        datasets: [{
                            data: data.data_pie,
                            backgroundColor: ["#FFC107", "#28A745", "#DC3545"]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: !isPieEmpty },
                            tooltip: { enabled: !isPieEmpty }
                        }
                    }
                });
            })
            .catch(error => console.error("Error fetching chart data:", error));
    }

    function loadLatestUsulan() {
        fetch("<?= base_url('dashboard/getLatestUsulan'); ?>")
            .then(response => response.json())
            .then(data => {
                let tableBody = document.getElementById("latestUsulanBody");
                tableBody.innerHTML = ""; // Kosongkan tabel sebelum isi ulang

                if (data.length === 0) {
                    tableBody.innerHTML = "<tr><td colspan='6' class='text-center'>Tidak ada usulan terbaru</td></tr>";
                    return;
                }

                data.forEach((usulan, index) => {
                    let statusLabel = getStatusBadge(usulan.status);
                    tableBody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${usulan.nomor_usulan}</td>
                            <td>${usulan.guru_nama}</td>
                            <td>${usulan.sekolah_asal}</td>
                            <td>${usulan.sekolah_tujuan}</td>
                            <td>${statusLabel}</td>
                        </tr>
                    `;
                });
            })
            .catch(error => console.error("Error fetching latest usulan:", error));
    }

    function getStatusBadge(status) {
        let badgeColor;
        switch (status) {
            case "01": badgeColor = "badge badge-primary"; break;
            case "02": badgeColor = "badge badge-info"; break;
            case "03": badgeColor = "badge badge-warning"; break;
            case "04": badgeColor = "badge badge-secondary"; break;
            case "05": badgeColor = "badge badge-success"; break;
            case "06": badgeColor = "badge badge-dark"; break;
            case "07": badgeColor = "badge badge-danger"; break;
            default: badgeColor = "badge badge-light";
        }
        return `<span class="${badgeColor}">${status}</span>`;
    }

    // Update chart & tabel saat tahun berubah
    yearSelect.addEventListener("change", function() {
        loadCharts(this.value);
        loadLatestUsulan();
    });
});
</script>
<?= $this->endSection(); ?>
