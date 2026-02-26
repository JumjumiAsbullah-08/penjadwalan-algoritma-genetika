<?= $this->extend('layout/template'); ?>

<?= $this->section('konten'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark m-0"><i class="fas fa-cogs me-2"></i> Generator Jadwal</h5>
            <small class="text-muted">Pusat kendali pembuatan jadwal otomatis.</small>
        </div>
        
        <?php if($status['jadwal'] > 0): ?>
            <div class="d-flex gap-2">
                <a href="<?= base_url('hasil') ?>" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                    <i class="fas fa-table me-2"></i> Lihat Hasil Jadwal
                </a>

                <?php if(isset($status_jadwal) && $status_jadwal == 'published'): ?>
                    
                    <span d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="Jadwal sedang Tayang (Live). Tarik Jadwal di menu Hasil untuk membuka gembok ini.">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 shadow-sm fw-bold" disabled style="cursor: not-allowed; opacity: 0.7;">
                            <i class="fas fa-lock me-2"></i> Reset Terkunci
                        </button>
                    </span>

                <?php else: ?>
                    
                    <button type="button" class="btn btn-danger rounded-pill px-4 shadow-sm fw-bold" onclick="konfirmasiReset()">
                        <i class="fas fa-trash-restore me-2"></i> Reset Jadwal
                    </button>

                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold m-0"><i class="fas fa-clipboard-check text-primary me-2"></i> Pemeriksaan Data (System Health)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        
                        <?php 
                        $items = [
                            ['Data Guru', $status['guru'], 'guru', 'users'],
                            ['Mata Pelajaran', $status['mapel'], 'mapel', 'book'],
                            ['Data Kelas', $status['kelas'], 'kelas', 'chalkboard'],
                            ['Data Ruangan', $status['ruangan'], 'ruangan', 'building'],
                            ['Slot Waktu (Jam)', $status['jam'], 'jampelajaran', 'clock'],
                            ['Beban Mengajar', $status['pengampu'], 'pengampu', 'briefcase']
                        ];
                        
                        $siapGenerate = true; // Flag utama

                        foreach($items as $item): 
                            $label = $item[0];
                            $count = $item[1];
                            $link  = $item[2];
                            $icon  = $item[3];
                            $ok    = $count > 0;
                            if(!$ok) $siapGenerate = false;
                        ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-<?= $ok ? 'success' : 'danger' ?> bg-opacity-10 p-3 rounded-circle me-3 text-<?= $ok ? 'success' : 'danger' ?>">
                                    <i class="fas fa-<?= $icon ?> fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold <?= $ok ? 'text-dark' : 'text-danger' ?>"><?= $label ?></h6>
                                    <small class="text-muted">
                                        Status: <?= $ok ? '<span class="text-success fw-bold">Siap</span>' : '<span class="text-danger fw-bold">Kosong / Belum Diisi</span>' ?>
                                    </small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h4 class="mb-0 fw-bold <?= $ok ? 'text-dark' : 'text-danger' ?>"><?= $count ?></h4>
                                <a href="<?= base_url($link) ?>" class="btn btn-sm btn-link text-decoration-none">Kelola <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                    <i class="fas fa-chart-pie fa-5x"></i>
                </div>
                <div class="card-body p-4 position-relative" style="z-index: 2;">
                    <h6 class="fw-bold mb-3 border-bottom border-white border-opacity-25 pb-2">Analisa Kapasitas</h6>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Kebutuhan Jam:</span>
                        <span class="fw-bold"><?= $analisa['butuh'] ?> JP</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Ketersediaan Slot:</span>
                        <span class="fw-bold"><?= $analisa['punya'] ?> Slot</span>
                    </div>

                    <?php if($analisa['aman']): ?>
                        <div class="alert alert-success bg-white text-success fw-bold text-center mb-0 shadow-sm">
                            <i class="fas fa-check-circle me-1"></i> Kapasitas Aman
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger bg-white text-danger fw-bold text-center mb-0 shadow-sm">
                            <i class="fas fa-exclamation-triangle me-1"></i> Slot Kurang!
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 text-center">
                    <?php if($status['jadwal'] > 0): ?>
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h5 class="fw-bold">Jadwal Selesai!</h5>
                        <p class="text-muted small">Jadwal pelajaran telah berhasil digenerate. Silakan lihat hasilnya atau reset jika ingin membuat ulang.</p>
                        <a href="<?= base_url('hasil') ?>" class="btn btn-outline-primary rounded-pill w-100 fw-bold">Lihat Jadwal</a>
                    <?php else: ?>
                        <i class="fas fa-robot fa-4x text-secondary mb-3"></i>
                        <h5 class="fw-bold">Algoritma Penjadwalan</h5>
                        <p class="text-muted small">Sistem akan menyusun jadwal otomatis dengan metode Heuristik + Genetika. Proses mungkin memakan waktu.</p>
                        
                        <?php if($siapGenerate && $analisa['aman']): ?>
                            <form action="<?= base_url('generate/process') ?>" method="post" id="formGenerate">
                                <button type="button" class="btn btn-primary btn-lg w-100 rounded-pill shadow fw-bold pulse-button" onclick="startGenerate()">
                                    <i class="fas fa-magic me-2"></i> Generate Sekarang
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-lg w-100 rounded-pill fw-bold" disabled>
                                <i class="fas fa-ban me-2"></i> Data Belum Siap
                            </button>
                            <small class="text-danger d-block mt-2 fw-bold">Lengkapi data di menu sebelah kiri!</small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Animasi Denyut Tombol */
    .pulse-button {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7); }
        70% { transform: scale(1.02); box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
    }
</style>

<script>
    function startGenerate() {
        Swal.fire({
            title: 'Mulai Proses Generate?',
            text: "Sistem akan mengacak dan menyusun jadwal. Proses ini mungkin memakan waktu 1-3 menit.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Mulai!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan Loading
                let timerInterval;
                Swal.fire({
                    title: 'Sedang Menyusun Jadwal...',
                    html: 'Mohon tunggu, jangan tutup halaman ini.<br><b>Progress:</b> <span id="progressText">0%</span>',
                    timerProgressBar: true,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        // Simulasi Progress Bar Visual (Karena PHP proses di backend)
                        let progress = 0;
                        const content = Swal.getHtmlContainer().querySelector('#progressText');
                        timerInterval = setInterval(() => {
                            progress += Math.floor(Math.random() * 5);
                            if(progress > 90) progress = 90; // Mentok di 90 sampe selesai
                            content.textContent = progress + '%';
                        }, 500);
                    },
                    willClose: () => {
                        clearInterval(timerInterval);
                    }
                });
                
                // Submit Form
                document.getElementById('formGenerate').submit();
            }
        });
    }

    function konfirmasiReset() {
        Swal.fire({
            title: 'Reset Jadwal?',
            text: "Seluruh data jadwal yang sudah ada akan dihapus! Data Guru, Mapel, dll akan terbuka kembali.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset Semuanya!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('generate/reset') ?>";
            }
        });
    }

    // Flash Message
    <?php if(session()->getFlashdata('success')): ?>
        Swal.fire({icon: 'success', title: 'Berhasil', text: '<?= session()->getFlashdata('success') ?>', timer: 3000});
    <?php endif; ?>
</script>

<?= $this->endSection(); ?>