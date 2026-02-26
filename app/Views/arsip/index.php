<?= $this->extend('layout/template'); ?>

<?= $this->section('konten'); ?>

<?php
    $role = session()->get('role');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark m-0"><i class="fas fa-history me-2"></i> Riwayat Jadwal Pelajaran</h5>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Tanggal Terbit</th>
                            <th>Total Slot</th>
                            <th>Status</th>
                            <?php if($role == 'admin'): ?>
                                <th class="text-end pe-4">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($list_arsip)): ?>
                            <tr>
                                <td colspan="<?= ($role == 'admin') ? 6 : 5 ?>" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                    <p>Belum ada riwayat jadwal tersimpan.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($list_arsip as $row): 
                                // LOGIKA STATUS
                                $isPeriodeSama = ($row['tahun_ajaran'] == $info_aktif['tahun'] && $row['semester'] == $info_aktif['semester']);
                                
                                // Disebut LIVE jika periode sama DAN status config 'published'
                                $isLive = ($isPeriodeSama && $status_saat_ini == 'published');
                            ?>
                            <tr class="<?= $isLive ? 'bg-success bg-opacity-10' : '' ?>">
                                <td class="ps-4 fw-bold"><?= $row['tahun_ajaran'] ?></td>
                                <td>
                                    <?php if($row['semester'] == 'Ganjil'): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary">Ganjil</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning">Genap</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted"><i class="far fa-clock me-1"></i> <?= date('d M Y H:i', strtotime($row['tgl_diarsipkan'])) ?></small>
                                </td>
                                <td><?= $row['jumlah_slot'] ?> sesi</td>
                                <td>
                                    <?php if($isLive): ?>
                                        <span class="badge bg-success animate__animated animate__pulse animate__infinite shadow-sm">
                                            <i class="fas fa-satellite-dish me-1"></i> Sedang Tayang
                                        </span>
                                    <?php elseif($isPeriodeSama): ?>
                                        <span class="badge bg-warning text-dark border border-warning">
                                            <i class="fas fa-pause-circle me-1"></i> Non-Aktif (Ditarik)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-50 text-dark">Arsip Lama</span>
                                    <?php endif; ?>
                                </td>
                                
                                <?php if($role == 'admin'): ?>
                                <td class="text-end pe-4">
                                    <button onclick="hapusArsip('<?= $row['tahun_ajaran'] ?>', '<?= $row['semester'] ?>')" 
                                            class="btn btn-sm btn-outline-danger" title="Hapus Arsip">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </td>
                                <?php endif; ?>

                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function hapusArsip(tahun, semester) {
        Swal.fire({
            title: 'Hapus Arsip?',
            text: `Anda yakin ingin menghapus data jadwal TA ${tahun} (${semester})? Data di halaman siswa akan hilang.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus Permanen!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('arsip/delete/') ?>" + encodeURIComponent(tahun) + "/" + semester;
            }
        });
    }

    <?php if(session()->getFlashdata('success')): ?>
        Swal.fire({icon: 'success', title: 'Berhasil', text: '<?= session()->getFlashdata('success') ?>', timer: 2000, showConfirmButton: false});
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        Swal.fire({icon: 'error', title: 'Gagal', text: '<?= session()->getFlashdata('error') ?>'});
    <?php endif; ?>
</script>

<?= $this->endSection(); ?>