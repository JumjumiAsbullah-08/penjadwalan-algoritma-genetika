<nav class="navbar-custom d-flex justify-content-between align-items-center">
    <div>
        <h5 class="mb-0 fw-bold text-success">Sistem Penjadwalan Mata Pelajaran</h5>
        <small class="text-muted">Tahun Ajaran 2025/2026</small>
    </div>
    <div class="d-flex align-items-center">
        <div class="me-3 text-end d-none d-md-block">
            <span class="d-block fw-bold text-dark"><?= session()->get('nama_lengkap') ?? 'User' ?></span>
            <span class="d-block small text-muted text-uppercase"><?= session()->get('role') ?? 'Admin' ?></span>
        </div>
        <img src="https://ui-avatars.com/api/?name=<?= session()->get('nama_lengkap') ?>&background=047857&color=fff" class="rounded-circle" width="45" height="45">
    </div>
</nav>