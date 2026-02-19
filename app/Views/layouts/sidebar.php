<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('/dashboard'); ?>">
        <div class="sidebar-brand-icon">
            <i class="fas fa-user-shield"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Simutasi</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('/dashboard'); ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Menu Utama
    </div>

    <?php if (session()->get('role') === 'admin'): ?>
        <!-- Master Data -->        
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#MasterData" aria-expanded="true" aria-controls="MasterData">
                <i class="fas fa-fw fa-database"></i>
                <span>Master Data</span>
            </a>
            <div id="MasterData" class="collapse" aria-labelledby="headingMaster" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Kelola Data Master:</h6>
                    <a class="collapse-item" href="/kabupaten"><i class="fas fa-map-marked-alt"></i> Data Kabupaten</a>
                    <a class="collapse-item" href="/cabang-dinas"><i class="fas fa-city"></i> Data Cabang Dinas</a>
                    <a class="collapse-item" href="/sekolah"><i class="fas fa-school"></i> Data Instansi</a>
                </div>
            </div>
        </li>

        <!-- Manajemen User -->        
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#userManagement" aria-expanded="true" aria-controls="userManagement">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen User</span>
            </a>
            <div id="userManagement" class="collapse" aria-labelledby="headingUsers" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Kelola Pengguna:</h6>
                    <a class="collapse-item" href="/admin"><i class="fas fa-user-cog"></i> Admin</a>
                    <a class="collapse-item" href="/kabidgtk"><i class="fas fa-user-tie"></i> Kabid GTK</a>
                    <a class="collapse-item" href="/operatordinas"><i class="fas fa-user"></i> Operator Dinas</a>
                    <a class="collapse-item" href="/operatorcabdin"><i class="fas fa-user-friends"></i> Operator Cabdin</a>
                    <h6 class="collapse-header">Log User:</h6>
                    <a class="collapse-item" href="/log_aktivitas">
                        <i class="fas fa-history"></i> Log Aktivitas
                    </a>
                </div>
            </div>
        </li>
        <!-- Usulan Menu -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#usulanMenu" aria-expanded="true" aria-controls="usulanMenu">
                <i class="fas fa-fw fa-folder"></i>
                <span>Usulan Mutasi Guru</span>
            </a>
            <div id="usulanMenu" class="collapse" aria-labelledby="headingUsulan" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">CABANG DINAS:</h6>
                    <a class="collapse-item" href="<?= base_url('/usulan'); ?>"><i class="fas fa-inbox"></i> Penerimaan Usulan</a>
                    <a class="collapse-item" href="<?= base_url('/pengiriman'); ?>"><i class="fas fa-paper-plane"></i> Pengiriman Usulan</a>
                    <h6 class="collapse-header">DINAS PROVINSI:</h6>
                    <a class="collapse-item" href="<?= base_url('/verifikasi'); ?>"><i class="fas fa-file-alt"></i> Verifikasi Dokumen</a>
                    <a class="collapse-item" href="<?= base_url('/telaah'); ?>"><i class="fas fa-search"></i> Telaah Usulan</a>
                    <a class="collapse-item" href="<?= base_url('/rekomkadis'); ?>"><i class="fas fa-stamp"></i> Rekomendasi Kadis</a>
                    <a class="collapse-item" href="<?= base_url('/berkasbkpsdm'); ?>"><i class="fas fa-file-upload"></i> Kirim Usulan ke BKA</a>
                    <a class="collapse-item" href="<?= base_url('/skmutasi'); ?>"><i class="fas fa-file-signature"></i> SK Mutasi/ND</a>
                </div>
            </div>
        </li>        
    <?php endif; ?>
    <?php if (session()->get('role') === 'kabid'): ?>
        <!-- Manajemen User -->        
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#userManagement" aria-expanded="true" aria-controls="userManagement">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen User</span>
            </a>
            <div id="userManagement" class="collapse" aria-labelledby="headingUsers" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Kelola Pengguna:</h6>
                    <a class="collapse-item" href="/operatordinas"><i class="fas fa-user"></i> Operator Dinas</a>
                </div>
            </div>
        </li>
        <!-- Usulan Menu -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#usulanMenu" aria-expanded="true" aria-controls="usulanMenu">
                <i class="fas fa-fw fa-folder"></i>
                <span>Usulan Mutasi Guru</span>
            </a>
            <div id="usulanMenu" class="collapse" aria-labelledby="headingUsulan" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">CABANG DINAS:</h6>
                    <a class="collapse-item" href="<?= base_url('/usulan'); ?>"><i class="fas fa-inbox"></i> Penerimaan Usulan</a>
                    <a class="collapse-item" href="<?= base_url('/pengiriman'); ?>"><i class="fas fa-paper-plane"></i> Pengiriman Usulan</a>
                    <h6 class="collapse-header">DINAS PROVINSI:</h6>
                    <a class="collapse-item" href="<?= base_url('/verifikasi'); ?>"><i class="fas fa-file-alt"></i> Verifikasi Dokumen</a>
                    <a class="collapse-item" href="<?= base_url('/telaah'); ?>"><i class="fas fa-search"></i> Telaah Usulan</a>
                    <a class="collapse-item" href="<?= base_url('/rekomkadis'); ?>"><i class="fas fa-stamp"></i> Rekomendasi Kadis</a>
                    <a class="collapse-item" href="<?= base_url('/berkasbkpsdm'); ?>"><i class="fas fa-file-upload"></i> Kirim Usulan ke BKA</a>
                    <a class="collapse-item" href="<?= base_url('/skmutasi'); ?>"><i class="fas fa-file-signature"></i> SK Mutasi/ND</a>
                </div>
            </div>
        </li>        
    <?php endif; ?>

    <?php if (session()->get('role') === 'dinas'): ?>
        <!-- Manajemen User -->        
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#userManagement" aria-expanded="true" aria-controls="userManagement">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen User</span>
            </a>
            <div id="userManagement" class="collapse" aria-labelledby="headingUsers" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Kelola Pengguna:</h6>
                    <a class="collapse-item" href="/operatorcabdin"><i class="fas fa-user-friends"></i> Operator Cabdin</a>
                </div>
            </div>
        </li>
        <!-- Usulan Menu -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#usulanMenu" aria-expanded="true" aria-controls="usulanMenu">
                <i class="fas fa-fw fa-folder"></i>
                <span>Usulan Mutasi Guru</span>
            </a>
            <div id="usulanMenu" class="collapse" aria-labelledby="headingUsulan" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">DINAS PROVINSI:</h6>
                    <a class="collapse-item" href="<?= base_url('/verifikasi'); ?>"><i class="fas fa-file-alt"></i> Verifikasi Dokumen</a>
                    <a class="collapse-item" href="<?= base_url('/telaah'); ?>"><i class="fas fa-search"></i> Telaah Usulan</a>
                    <a class="collapse-item" href="<?= base_url('/rekomkadis'); ?>"><i class="fas fa-stamp"></i> Rekomendasi Kadis</a>
                    <a class="collapse-item" href="<?= base_url('/berkasbkpsdm'); ?>"><i class="fas fa-file-upload"></i> Kirim Usulan ke BKA</a>
                    <a class="collapse-item" href="<?= base_url('/skmutasi'); ?>"><i class="fas fa-file-signature"></i> SK Mutasi/ND</a>
                </div>
            </div>
        </li>          
    <?php endif; ?>    

    <?php if (session()->get('role') === 'operator'): ?>
        <!-- Usulan Menu -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#usulanMenu" aria-expanded="true" aria-controls="usulanMenu">
                <i class="fas fa-fw fa-folder"></i>
                <span>Usulan Mutasi Guru</span>
            </a>
            <div id="usulanMenu" class="collapse" aria-labelledby="headingUsulan" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">CABANG DINAS:</h6>
                    <a class="collapse-item" href="<?= base_url('/usulan'); ?>"><i class="fas fa-inbox"></i> Penerimaan Usulan</a>
                    <a class="collapse-item" href="<?= base_url('/pengiriman'); ?>"><i class="fas fa-paper-plane"></i> Pengiriman Usulan</a>
                </div>
            </div>
        </li>        
    <?php endif; ?>    

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Informasi
    </div>
    <!-- Info Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#infoMenu" aria-expanded="true" aria-controls="infoMenu">
            <i class="fas fa-info-circle"></i>
            <span>Informasi</span>
        </a>
        <div id="infoMenu" class="collapse" aria-labelledby="headingInfo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Pengembangan:</h6>
                <a class="collapse-item" href="<?= base_url('/info_pengembangan'); ?>">
                    <i class="fas fa-info-circle"></i> Info Pengembangan
                </a>
                <a class="collapse-item" href="<?= base_url('/tutorial'); ?>">
                    <i class="fab fa-youtube"></i> Video Tutorial
                </a>

                <?php if (session()->get('role') == 'admin'): ?>
                    <a class="collapse-item" href="<?= base_url('/kelola_info'); ?>">
                        <i class="fas fa-cogs"></i> Kelola Info
                    </a>
                    <a class="collapse-item" href="<?= base_url('/kelola_tutorial'); ?>">
                        <i class="fas fa-cogs"></i> Kelola Video Tutorial
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
    <!-- Nav Item - Helpdesk -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('/helpdesk'); ?>">
            <i class="fas fa-phone-alt"></i>
            <span>Helpdesk</span>
        </a>
    </li>

    <!-- Nav Item - Kotak Saran -->
    <?php if (session()->get('role') == 'admin' || session()->get('role') == 'kabid'): ?>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('/kotak-saran'); ?>">
                <i class="fas fa-envelope"></i>
                <span>Kotak Saran</span>
                <?php if (!empty($jumlahBelumDibalas) && $jumlahBelumDibalas > 0): ?>
                    <span class="badge badge-danger"><?= $jumlahBelumDibalas ?></span>
                <?php endif; ?>
            </a>
        </li>
    <?php endif; ?>





    <hr class="sidebar-divider d-none d-md-block">
    <!-- Logout -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('/logout'); ?>">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>
</ul>
