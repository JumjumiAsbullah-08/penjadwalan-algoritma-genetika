<?= $this->extend('layout/template'); ?>

<?= $this->section('konten'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-alt me-2"></i> Laporan Data Master</h1>
        <p class="mb-0 text-muted">Download data mentah dalam format PDF atau Excel</p>
    </div>

    <div class="row">
        <?php 
        // Array Konfigurasi Menu agar kodingan rapi
        $menuLaporan = [
            ['jenis' => 'guru', 'judul' => 'Data Guru', 'icon' => 'fa-chalkboard-teacher', 'color' => 'primary'],
            ['jenis' => 'mapel', 'judul' => 'Mata Pelajaran', 'icon' => 'fa-book', 'color' => 'success'],
            ['jenis' => 'kelas', 'judul' => 'Data Kelas', 'icon' => 'fa-users', 'color' => 'info'],
            ['jenis' => 'ruangan', 'judul' => 'Data Ruangan', 'icon' => 'fa-building', 'color' => 'warning'],
            ['jenis' => 'jam', 'judul' => 'Jam Pelajaran', 'icon' => 'fa-clock', 'color' => 'danger'],
        ];
        ?>

        <?php foreach($menuLaporan as $m): ?>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-start border-4 border-<?= $m['color'] ?> shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-<?= $m['color'] ?> text-uppercase mb-1">Laporan Master</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $m['judul'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas <?= $m['icon'] ?> fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('laporan/download?jenis='.$m['jenis'].'&format=pdf') ?>" target="_blank" class="btn btn-outline-danger btn-sm w-100 fw-bold">
                            <i class="fas fa-file-pdf me-1"></i> PDF / Print
                        </a>
                        <a href="<?= base_url('laporan/download?jenis='.$m['jenis'].'&format=excel') ?>" target="_blank" class="btn btn-outline-success btn-sm w-100 fw-bold">
                            <i class="fas fa-file-excel me-1"></i> Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?= $this->endSection(); ?>