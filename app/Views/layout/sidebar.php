<div class="sidebar d-flex flex-column flex-shrink-0" style="width: 260px;">
    <div class="sidebar-brand text-center">
        <i class="fas fa-school fa-lg me-2"></i> MAN SIPAGIMBAR
    </div>
    
    <?php $role = session()->get('role'); ?>

    <ul class="list-unstyled flex-column mb-auto mt-3">
        
        <li><a href="<?= base_url('dashboard') ?>" class="<?= uri_string() == 'dashboard' ? 'active' : '' ?>"><i class="fas fa-home"></i> Dashboard</a></li>
        
        <?php if($role == 'admin'): ?>
            <li class="px-3 mt-3 mb-1 text-uppercase small text-white-50 fw-bold">Data Master</li>
            <li><a href="<?= base_url('guru') ?>"><i class="fas fa-chalkboard-teacher"></i> Data Guru</a></li>
            <li><a href="<?= base_url('mapel') ?>"><i class="fas fa-book"></i> Data Mapel</a></li>
            <li><a href="<?= base_url('ruangan') ?>"><i class="fas fa-door-open"></i> Data Ruangan</a></li>
            <li><a href="<?= base_url('kelas') ?>"><i class="fas fa-users"></i> Data Kelas</a></li>
            <li><a href="<?= base_url('jam_pelajaran') ?>"><i class="fas fa-clock"></i> Jam Pelajaran</a></li>
            
            <li class="px-3 mt-3 mb-1 text-uppercase small text-white-50 fw-bold">Penjadwalan</li>
            <li><a href="<?= base_url('pengampu') ?>"><i class="fas fa-tasks"></i> Beban Mengajar</a></li>
            <li><a href="<?= base_url('generate') ?>"><i class="fas fa-cogs"></i> Generate Jadwal</a></li>
        <?php endif; ?>

        <?php if($role != 'admin'): ?>
            <li class="px-3 mt-3 mb-1 text-uppercase small text-white-50 fw-bold">Jadwal</li>
        <?php endif; ?>
        <li><a href="<?= base_url('hasil') ?>"><i class="fas fa-calendar-alt"></i> Lihat Hasil</a></li>
        <li><a href="<?= base_url('arsip') ?>"><i class="fas fa-history"></i> Riwayat Jadwal</a></li>
        <?php if($role == 'admin'): ?>
            <li class="px-3 mt-3 mb-1 text-uppercase small text-white-50 fw-bold">Pengaturan</li>
            <li><a href="<?= base_url('pengaturan') ?>"><i class="fas fa-cog"></i> Tahun Ajaran</a></li>
        <?php endif; ?>

        <li class="px-3 mt-3 mb-1 text-uppercase small text-white-50 fw-bold">Laporan</li>
        <li><a href="<?= base_url('laporan') ?>"><i class="fas fa-print"></i> Cetak Laporan</a></li>

        <?php if($role == 'admin'): ?>
            <li class="px-3 mt-3 mb-1 text-uppercase small text-white-50 fw-bold">Perhitungan</li>
            <li><a href="<?= base_url('perhitungan') ?>"><i class="fas fa-calculator"></i> Analisis Perhitungan</a></li>
        <?php endif; ?>

        <div class="p-3 border-top border-secondary mt-auto">
            <a href="<?= base_url('auth/logout') ?>" class="bg-danger text-white text-center" id="btnLogout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </ul>
</div>