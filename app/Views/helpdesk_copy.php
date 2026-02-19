<?= $this->extend('layouts/main_layout'); ?>
<?= $this->section('content'); ?>
<style>

    /* Tabel utama */
    .helpdesk-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        color: #5a5c69;
        background-color: #ffffff; /* Background putih */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    /* Header tabel */
    .helpdesk-table th {
        background-color: #4e73df; /* Warna utama SIMUTASI */
        color: white;
        padding: 10px;
        text-align: center;
        border: 1px solid #ddd;
    }

    /* Isi tabel */
    .helpdesk-table td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: left;
    }

    /* Baris DAPIL */
    .helpdesk-table td[colspan] {
        background-color: #dbe4f3; /* Biru lebih soft */
        font-weight: bold;
        text-align: left;
        padding-left: 15px;
        color: #5a5c69;
    }

    /* Responsif */
    @media (max-width: 768px) {
        .helpdesk-table {
            font-size: 12px;
        }
    }
</style>
<div class="header-container">
    <h1 class="h5 mb-0 text-gray-800"><i class="fas fa-phone-alt"></i> Helpdesk</h1>
</div>
<div class="card mb-4">
    <!-- 🔹 Tabel Helpdesk -->
    <div class="card-body">
        <div class="table-responsive">
            <table class="helpdesk-table">
                <thead>
                    <tr>
                        <th width="40%">Kabupaten / Kota</th>
                        <th width="45%">Penghubung</th>
                        <th width="35%">No. HP</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="3">Dapil I</td></tr>
                    <tr><td>1. Banda Aceh</td><td rowspan="2">Evi Susantiana, SE</td><td rowspan="2"><i class="fab fa-whatsapp"></i> 085764002003</td></tr>
                    <tr><td>2. Aceh Besar</td></tr>
                    <tr><td>3. Sabang</td><td>Rahmatullah, A.Md</td><td><i class="fab fa-whatsapp"></i> 085316000086</a></td></tr>

                    <tr><td colspan="3">Dapil II</td></tr>
                    <tr><td>1. Pidie</td><td>Nurul Munajad, SP</td><td><i class="fab fa-whatsapp"></i> 082361886443</a></td></tr>
                    <tr><td>2. Pidie Jaya</td><td>Mushaddiq</td><td><i class="fab fa-whatsapp"></i> 081361545424</a></td></tr>

                    <tr><td colspan="3">Dapil III</td></tr>
                    <tr><td>1. Bireuen</td><td>Iskandar, SE</td><td><i class="fab fa-whatsapp"></i> 082364498089</a></td></tr>

                    <tr><td colspan="3">Dapil IV</td></tr>
                    <tr><td>1. Aceh Tengah</td><td>Rudi Fachriansyah, A.Md</td><td><i class="fab fa-whatsapp"></i> 085359078276</a></td></tr>
                    <tr><td>2. Bener Meriah</td><td>Abiem Siraj Adila</td><td><i class="fab fa-whatsapp"></i> 082166724700</a></td></tr>

                    <tr><td colspan="3">Dapil V</td></tr>
                    <tr><td>1. Aceh Utara</td><td>Nanda Pratiwi Caesaria, SE</td><td><i class="fab fa-whatsapp"></i> 085260991117</a></td></tr>
                    <tr><td>2. Lhokseumawe</td><td>Novi Eliza, A.Md</td><td><i class="fab fa-whatsapp"></i> 082363687479</a></td></tr>

                    <tr><td colspan="3">Dapil VI</td></tr>
                    <tr><td rowspan="3">1. Aceh Timur</td><td>Nursetiana, SE</td><td><i class="fab fa-whatsapp"></i> 082365424769</a></td></tr>
                    <tr><td>Zaid Muhammad Julana, S.Kom</td><td><i class="fab fa-whatsapp"></i> 085260021015</a></td></tr>
                    <tr><td>Misbahul Jannah, MM</td><td><i class="fab fa-whatsapp"></i> 082236876096</a></td></tr>

                    <tr><td colspan="3">Dapil VII</td></tr>
                    <tr><td>1. Langsa</td><td>Rosnelli, SHI</td><td><i class="fab fa-whatsapp"></i> 082310550575</a></td></tr>
                    <tr><td>2. Aceh Tamiang</td><td>Fahmi Syahnur, S.PI</td><td><i class="fab fa-whatsapp"></i> 082361509434</a></td></tr>

                    <tr><td colspan="3">Dapil VIII</td></tr>
                    <tr><td>1. Gayo Lues</td><td>Indra Sembiring</td><td><i class="fab fa-whatsapp"></i> 081362433990</a></td></tr>
                    <tr><td>2. Aceh Tenggara</td><td>Maulizal, ST</td><td><i class="fab fa-whatsapp"></i> 082360603040</a></td></tr>

                    <tr><td colspan="3">Dapil IX</td></tr>
                    <tr><td>1. Aceh Barat Daya</td><td>Irfan Suhendri</td><td><i class="fab fa-whatsapp"></i> 082162161714</a></td></tr>
                    <tr><td>2. Aceh Selatan</td><td>Vivianti, A.Md</td><td><i class="fab fa-whatsapp"></i> 082272848440</a></td></tr>
                    <tr><td>3. Aceh Singkil</td><td rowspan="2">Abdin Nashir, S.Pd</td><td rowspan="2"><i class="fab fa-whatsapp"></i> 08116888839</td></tr>
                    <tr><td>4. Subulussalam</td></tr>

                    <tr><td colspan="3">Dapil X</td></tr>
                    <tr><td>1. Aceh Jaya</td><td>Fitrianti, A.Md</td><td><i class="fab fa-whatsapp"></i> 085310775670</a></td></tr>
                    <tr><td>2. Aceh Barat</td><td rowspan="2">Putri Damayanti, S.Kom</td><td rowspan="2"><i class="fab fa-whatsapp"></i> 081226265604</td></tr>
                    <tr><td>3. Nagan Raya</td></tr>
                    <tr><td>4. Simeulue</td><td>Ibdu Chaldun</td><td><i class="fab fa-whatsapp"></i> 081362895023</a></td></tr>                    
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

