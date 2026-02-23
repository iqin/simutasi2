<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>

<!-- PERUBAHAN: tambah dropdown tahun di atas header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    <div class="d-flex">
        <select id="filterTahunDashboard" class="form-control form-control-sm" style="width: 150px;" onchange="location.href='?tahun='+this.value;">
            <option value="semua" <?= $tahunTerpilih == 'semua' ? 'selected' : '' ?>>Semua Tahun</option>
            <?php foreach ($availableYears as $tahun): ?>
                <option value="<?= $tahun ?>" <?= $tahunTerpilih == $tahun ? 'selected' : '' ?>><?= $tahun ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<?php
// Helper untuk badge status
function getStatusBadge($status) {
    $badgeColor = match ($status) {
        '01' => 'badge badge-primary',
        '02' => 'badge badge-info',
        '03' => 'badge badge-warning',
        '04' => 'badge badge-secondary',
        '05' => 'badge badge-success',
        '06' => 'badge badge-dark',
        '07' => 'badge badge-danger',
        default => 'badge badge-light'
    };
    return "<span class='$badgeColor'>$status</span>";
}

$tahunLabel = ($tahunTerpilih == 'semua') ? 'Semua Tahun' : "Tahun $tahunTerpilih";
?>

<!-- 🔹 BARIS PERTAMA: 4 KOTAK STATISTIK -->
<div class="row">
    <?php 
    $cards = [
        ["01 : Usulan Mutasi", $tahunLabel, $total_usulan_filtered, "fa-file-alt", "dashboard-card-blue", []],
        ["02 : Usulan Cabdin", $tahunLabel, $total_usulan_cabdin, "fa-paper-plane", "dashboard-card-yellow", [
            ["bg-primary text-white", "Terkirim: $total_terkirim"],
            ["bg-danger text-white", "Blm. Kirim: $usulan_belum_dikirim"]
        ]],
        ["03 : Verifikasi Dinas", $tahunLabel, $total_verif_dinas, "fa-check-circle", "dashboard-card-red", [
            ["bg-success text-white", "Lengkap: $total_lengkap"],
            ["bg-danger text-white", "Tdk. Lengkap: $total_tdk_lengkap"]
        ]],
        ["04 : Telaah Usulan", $tahunLabel, $total_telaah_kabid, "fa-user-tie", "dashboard-card-yellow", [
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
                    <div class="dashboard-card-content">
                        <?php foreach ($card[5] as $badge) : ?>
                            <span class="badge <?= $badge[0]; ?> px-2 py-1 rounded-pill"><?= $badge[1]; ?></span>
                        <?php endforeach; ?>
                    </div>
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
        ["05 : Rekomendasi Kadis", $tahunLabel, $rekom_kadis, "fa-clipboard-check", "dashboard-card-green", [
            ["bg-success text-white", "Ada Rekom: $rekom_kadis_ada"],
            ["bg-danger text-white", "Belum Ada: $rekom_kadis_belum"]
        ]],
        ["06 : Dikirim ke BKA", $tahunLabel, $dikirim_bkpsdm, "fa-share-square", "dashboard-card-blue", [
            ["bg-success text-white", "Sudah: $kirim_bka_sudah"],
            ["bg-danger text-white", "Belum: $kirim_bka_belum"]
        ]],
        ["07 : SK Mutasi / ND Terbit", $tahunLabel, $terbit_sk, "fa-file-signature", "dashboard-card-purple", [
            ["bg-success text-white", "Nota Dinas: $nota_dinas"],
            ["bg-primary text-white", "SK Mutasi: $sk_mutasi"]
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
                    <div class="dashboard-card-content">
                        <?php foreach ($card[5] as $badge) : ?>
                            <span class="badge <?= $badge[0]; ?> px-2 py-1 rounded-pill"><?= $badge[1]; ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="dashboard-card-footer d-flex justify-content-end align-items-center">
                        <span class="dashboard-count me-2"><?= $card[2]; ?></span>
                        <i class="fas <?= $card[3]; ?> dashboard-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


<!-- 🔹 GRAFIK (tanpa dropdown tahun) -->
<div class="row">
    <div class="col-xl-8 col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Jumlah Usulan Mutasi Per Bulan</h6>
                <span class="text-muted"><?= $tahunLabel ?></span>
            </div>
            <div class="card-body">
                <canvas id="chartUsulan"></canvas>
            </div>
        </div>
    </div>
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

<!-- 🔹 TABEL USULAN TERBARU -->
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
                        <tbody>
                            <?php if (!empty($latest_usulan)): ?>
                                <?php foreach ($latest_usulan as $index => $usulan): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $usulan['nomor_usulan'] ?></td>
                                    <td><?= $usulan['guru_nama'] ?></td>
                                    <td><?= $usulan['sekolah_asal'] ?></td>
                                    <td><?= $usulan['sekolah_tujuan'] ?></td>
                                    <td><?= getStatusBadge($usulan['status']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
                            <?php endif; ?>
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
    // Data dari PHP
    const chartData = <?= json_encode($chartData) ?>;
    const labelsPie = <?= json_encode($chart_labels_pie) ?>;
    const dataPie = <?= json_encode($chart_data_pie) ?>;

    // Bar Chart: Usulan per Bulan (stacked)
    const ctxBar = document.getElementById("chartUsulan").getContext("2d");
    new Chart(ctxBar, {
        type: "bar",
        data: chartData,
        options: {
            responsive: true,
            scales: {
                y: { 
                    beginAtZero: true,
                    stacked: true, // Aktifkan stacking
                    title: {
                        display: true,
                        text: 'Jumlah Usulan'
                    }
                },
                x: {
                    stacked: true, // Aktifkan stacking
                    title: {
                        display: true,
                        text: 'Bulan'
                    }
                }
            },
            plugins: {
                legend: {
                    display: chartData.datasets.length > 1 // tampilkan legend jika lebih dari satu series
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            }
        }
    });

    // Pie Chart: Distribusi Status Usulan
    const ctxPie = document.getElementById("chartPie").getContext("2d");
    const isSemuaTahun = <?= json_encode($tahunTerpilih === 'semua') ?>;
    new Chart(ctxPie, {
        type: "pie",
        data: {
            labels: labelsPie,
            datasets: [{
                data: dataPie,
                backgroundColor: ["#FFC107", "#28A745", "#DC3545"]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: "bottom" },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (isSemuaTahun) {
                                // Tampilkan rincian per tahun
                                const label = context.label || '';
                                const statusPerTahun = <?= json_encode($status_per_tahun) ?>;
                                let detail = '';
                                for (const [tahun, counts] of Object.entries(statusPerTahun)) {
                                    let jumlah;
                                    if (label === 'Dalam Proses') jumlah = counts.dalam_proses;
                                    else if (label === 'Disetujui') jumlah = counts.disetujui;
                                    else if (label === 'Ditolak') jumlah = counts.ditolak;
                                    if (jumlah > 0) {
                                        detail += `${tahun}: ${jumlah}\n`;
                                    }
                                }
                                return detail.trim();
                            } else {
                                // Tampilkan format lama (label dan persentase)
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const dataset = context.dataset;
                                const total = dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        }
    });
});
</script>
<?= $this->endSection(); ?>
