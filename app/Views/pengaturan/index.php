<?= $this->extend('layout/template'); ?>

<?= $this->section('konten'); ?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* Override Font Utama ke Poppins */
    .poppins-font {
        font-family: 'Poppins', sans-serif !important;
    }
    .bg-gradient-primary {
        background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
    }
    .ls-1 { letter-spacing: 0.5px; } /* Letter spacing sedikit dikecilkan */
    .input-group-text { min-width: 45px; justify-content: center; background-color: #fff; }
    
    /* Custom Focus State supaya lebih elegan */
    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
    }
</style>

<div class="container-fluid poppins-font">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 mb-0 text-gray-800 fw-bold">Pengaturan Sistem</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-5"> <div class="card shadow border-0 rounded-3">
                <div class="card-header py-3 bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold"><i class="fas fa-cogs me-2"></i> Konfigurasi Periode</h6>
                    
                    <?php if($is_locked): ?>
                        <span class="badge bg-danger border border-white px-3 py-1 shadow-sm" style="font-size: 0.75rem;">
                            <i class="fas fa-lock me-1"></i> TERKUNCI
                        </span>
                    <?php else: ?>
                        <span class="badge bg-success border border-white px-3 py-1 shadow-sm" style="font-size: 0.75rem;">
                            <i class="fas fa-edit me-1"></i> AKTIF
                        </span>
                    <?php endif; ?>
                </div>

                <div class="card-body p-4">
                    
                    <?php if(session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm py-2" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2 fa-lg"></i>
                                <div class="small text-dark">
                                    <strong>Berhasil!</strong> <?= session()->getFlashdata('success') ?>
                                </div>
                            </div>
                            <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm py-2" role="alert">
                             <div class="d-flex align-items-center">
                                <i class="fas fa-times-circle text-danger me-2 fa-lg"></i>
                                <div class="small text-dark">
                                    <strong>Gagal!</strong> <?= session()->getFlashdata('error') ?>
                                </div>
                            </div>
                            <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session()->getFlashdata('warning')): ?>
                        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm py-2" role="alert">
                             <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle text-warning me-2 fa-lg"></i>
                                <div class="small text-dark">
                                    <strong>Perhatian:</strong> <?= session()->getFlashdata('warning') ?>
                                </div>
                            </div>
                            <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($is_locked): ?>
                        <div class="alert alert-light border-danger d-flex align-items-start shadow-sm mb-3 py-2 px-3">
                            <div class="me-3 mt-1 text-danger"><i class="fas fa-ban fa-lg"></i></div>
                            <div>
                                <h6 class="alert-heading fw-bold text-danger mb-1" style="font-size: 0.9rem;">Mode Edit Nonaktif</h6>
                                <p class="mb-0 text-muted" style="font-size: 0.8rem; line-height: 1.4;">
                                    Jadwal sudah digenerate. Silakan <strong>Reset Jadwal</strong> di menu Generate jika ingin mengganti periode aktif.
                                </p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info border-0 shadow-sm mb-3 py-2 px-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle text-info me-3"></i>
                                <span class="text-muted" style="font-size: 0.8rem;">
                                    Perubahan ini akan mempengaruhi seluruh data <strong>Beban Mengajar</strong>.
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('pengaturan/update') ?>" method="post">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase ls-1 mb-1">Tahun Ajaran Aktif</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text text-primary border-end-0"><i class="fas fa-calendar-alt"></i></span>
                                <input type="text" name="tahun_ajaran" list="listTahun" 
                                       class="form-control border-start-0 bg-light fw-bold text-dark" 
                                       placeholder="Contoh: 2025/2026" 
                                       style="font-size: 0.95rem;"
                                       value="<?= $active['tahun'] ?>"
                                       <?= $is_locked ? 'readonly' : '' ?> required>
                                
                                <datalist id="listTahun">
                                    <?php foreach($opsi_tahun as $thn): ?>
                                        <option value="<?= $thn ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="form-text text-muted fst-italic ms-1 mt-1" style="font-size: 0.75rem;">Format: YYYY/YYYY (Misal: 2024/2025)</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary small text-uppercase ls-1 mb-1">Semester Aktif</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text text-primary border-end-0"><i class="fas fa-adjust"></i></span>
                                <select name="semester" class="form-select border-start-0 bg-light fw-bold text-dark" 
                                        style="font-size: 0.95rem;"
                                        <?= $is_locked ? 'disabled' : '' ?>>
                                    <option value="Ganjil" <?= ($active['semester'] == 'Ganjil') ? 'selected' : '' ?>>Ganjil (Semester 1)</option>
                                    <option value="Genap" <?= ($active['semester'] == 'Genap') ? 'selected' : '' ?>>Genap (Semester 2)</option>
                                </select>
                            </div>
                            <?php if($is_locked): ?>
                                <input type="hidden" name="semester" value="<?= $active['semester'] ?>">
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary shadow-sm fw-bold py-2" 
                                    style="font-size: 0.9rem; letter-spacing: 0.5px;"
                                    <?= $is_locked ? 'disabled' : '' ?>>
                                <i class="fas fa-save me-2"></i> SIMPAN PERUBAHAN
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>