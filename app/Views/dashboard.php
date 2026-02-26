<?= $this->extend('layout/template'); ?>

<?= $this->section('konten'); ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<?php
    $role = session()->get('role');
    $sapaan = ($role == 'admin') ? 'Administrator' : 'Kepala Sekolah';
?>

<div class="container-fluid">
    
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(120deg, #1cc88a 0%, #13855c 100%);">
        <div class="card-body p-4 text-white d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-25 p-3 rounded-circle me-4">
                     <i class="fas fa-school fa-2x"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">Selamat Datang, <?= $sapaan ?>!</h4>
                    <p class="mb-0 opacity-75">
                        Tahun Ajaran Aktif: 
                        <span class="badge bg-white text-success fw-bold ms-1">
                            <?= $tahun_aktif ?? '-' ?> (<?= $sem_aktif ?? '-' ?>)
                        </span>
                    </p>
                </div>
            </div>
            <div class="d-none d-md-block opacity-50">
                <i class="fas fa-chart-line fa-4x"></i>
            </div>
        </div>
    </div>

    <h6 class="fw-bold text-secondary text-uppercase mb-3 small ls-1"><i class="fas fa-database me-2"></i>Ringkasan Data Master</h6>
    <div class="row g-4 mb-4">
        
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 border-0 shadow-sm hover-lift border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase">Total Guru</span>
                            <h2 class="mb-0 fw-bold text-dark"><?= number_format($total_guru ?? 0) ?></h2>
                        </div>
                        <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                            <i class="fas fa-chalkboard-teacher fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card h-100 border-0 shadow-sm hover-lift border-start border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase">Total Kelas</span>
                            <h2 class="mb-0 fw-bold text-dark"><?= number_format($total_kelas ?? 0) ?></h2>
                        </div>
                        <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card h-100 border-0 shadow-sm hover-lift border-start border-4 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase">Total Ruangan</span>
                            <h2 class="mb-0 fw-bold text-dark"><?= number_format($total_ruangan ?? 0) ?></h2>
                        </div>
                        <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-3">
                            <i class="fas fa-building fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card h-100 border-0 shadow-sm hover-lift border-start border-4 border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase">Mata Pelajaran</span>
                            <h2 class="mb-0 fw-bold text-dark"><?= number_format($total_mapel ?? 0) ?></h2>
                        </div>
                        <div class="icon-shape bg-info bg-opacity-10 text-info rounded-3 p-3">
                            <i class="fas fa-book fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-chart-bar me-2"></i>Statistik Sekolah</h6>
                    <?php if($role == 'kepsek'): ?>
                        <span class="badge bg-primary bg-opacity-10 text-primary">Mode Eksekutif</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div id="mainChart" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center p-4">
                    <?php if(($total_jadwal ?? 0) > 0): ?>
                        <div class="avatar-lg bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:80px; height:80px;">
                            <i class="fas fa-check-circle fa-3x text-success animate__animated animate__pulse animate__infinite"></i>
                        </div>
                        <h5 class="fw-bold">Jadwal Tersedia</h5>
                        <p class="text-muted small">Sebanyak <strong><?= $total_jadwal ?></strong> slot jadwal telah digenerate.</p>
                        <a href="<?= base_url('hasil') ?>" class="btn btn-primary w-100 rounded-pill shadow-sm fw-bold">
                            <i class="fas fa-eye me-2"></i> Lihat Jadwal
                        </a>
                    <?php else: ?>
                        <div class="avatar-lg bg-secondary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:80px; height:80px;">
                            <i class="fas fa-clock fa-3x text-secondary"></i>
                        </div>
                        <h5 class="fw-bold text-secondary">Jadwal Belum Siap</h5>
                        <p class="text-muted small">Menunggu Admin melakukan generate jadwal.</p>
                        <button class="btn btn-light w-100 rounded-pill border fw-bold disabled">Data Belum Ada</button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-stream me-2"></i>Alur Sistem</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-4 py-3 d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:30px; height:30px; font-size:0.8rem;">1</div>
                            <div>
                                <h6 class="mb-0 fw-bold">Data Master</h6>
                                <small class="text-muted">Guru, Mapel, Ruang & Kelas</small>
                            </div>
                        </div>
                        <div class="list-group-item px-4 py-3 d-flex align-items-center">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:30px; height:30px; font-size:0.8rem;">2</div>
                            <div>
                                <h6 class="mb-0 fw-bold">Generate Jadwal</h6>
                                <small class="text-muted">Proses Algoritma Genetika</small>
                            </div>
                        </div>
                        <div class="list-group-item px-4 py-3 d-flex align-items-center bg-light">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:30px; height:30px; font-size:0.8rem;">3</div>
                            <div>
                                <h6 class="mb-0 fw-bold">Validasi & Publish</h6>
                                <small class="text-muted">Persetujuan Kepala Sekolah</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Styling Tambahan untuk Efek Professional */
    .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
    .ls-1 { letter-spacing: 1px; }
    .icon-shape { width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // --- KONFIGURASI CHART APEXCHARTS ---
        var options = {
            series: [{
                name: 'Total Data',
                // Data diambil dari PHP variable
                data: [
                    <?= $total_guru ?? 0 ?>, 
                    <?= $total_kelas ?? 0 ?>, 
                    <?= $total_ruangan ?? 0 ?>, // Pastikan Controller kirim variabel ini
                    <?= $total_mapel ?? 0 ?>
                ]
            }],
            chart: {
                type: 'bar', // Tipe Bar Chart Horizontal
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Nunito, sans-serif'
            },
            plotOptions: {
                bar: {
                    borderRadius: 5,
                    horizontal: true, // Ubah ke false jika ingin vertikal
                    distributed: true, // Warna warni tiap bar
                    barHeight: '60%'
                }
            },
            colors: ['#4e73df', '#f6c23e', '#1cc88a', '#36b9cc'], // Biru, Kuning, Hijau, Cyan
            dataLabels: {
                enabled: true,
                style: { colors: ['#fff'] },
                formatter: function (val, opt) { return val }
            },
            xaxis: {
                categories: ['Guru Pengajar', 'Rombongan Belajar', 'Ruangan Fisik', 'Mata Pelajaran'],
            },
            grid: {
                strokeDashArray: 4,
            },
            legend: { show: false }, // Hide legend karena sudah jelas di label
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (val) { return val + " Data" }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#mainChart"), options);
        chart.render();
    });

    // Flash Message (Jika ada)
    <?php if(session()->getFlashdata('success')): ?>
        Swal.fire({
            icon: 'success', 
            title: 'Berhasil', 
            text: '<?= session()->getFlashdata('success') ?>',
            timer: 2000,
            showConfirmButton: false
        });
    <?php endif; ?>
</script>

<?= $this->endSection(); ?>