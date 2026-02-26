<?= $this->extend('layout/template'); ?>

<?= $this->section('konten'); ?>

<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-microchip me-2"></i> Detail Perhitungan Algoritma Genetika</h1>

    <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
        <li class="nav-item"><button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#tab1">1. Process (Alur)</button></li>
        <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab2">2. Encoding</button></li>
        <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab3">3. Inisialisasi</button></li>
        <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab4">4. Fitness</button></li>
        <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab5">5. Seleksi</button></li>
        <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab6">6. Crossover</button></li>
        <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab7">7. Mutasi</button></li>
        <li class="nav-item"><button class="nav-link fw-bold text-success" data-bs-toggle="tab" data-bs-target="#tab8">8. Grafik Hasil</button></li>
    </ul>

    <div class="tab-content">
        
        <div class="tab-pane fade show active" id="tab1">
            <div class="card shadow border-left-primary">
                <div class="card-body">
                    <h5 class="fw-bold text-primary">Flowchart Algoritma</h5>
                    <p>Proses utama yang dijalankan oleh sistem:</p>
                    <div class="text-center my-4">
                        <span class="badge bg-secondary p-2">Mulai</span> <i class="fas fa-arrow-right"></i>
                        <span class="badge bg-info p-2">Ambil Data DB</span> <i class="fas fa-arrow-right"></i>
                        <span class="badge bg-warning text-dark p-2">Encoding & Inisialisasi</span> <i class="fas fa-arrow-right"></i>
                        <span class="badge bg-primary p-2">Loop Generasi</span>
                    </div>
                    <div class="card bg-light p-3">
                        <h6><strong>Looping Generasi (Evolusi):</strong></h6>
                        <ol>
                            <li><strong>Hitung Fitness:</strong> Evaluasi kualitas setiap jadwal.</li>
                            <li><strong>Seleksi:</strong> Pilih induk terbaik menggunakan Roulette Wheel.</li>
                            <li><strong>Crossover:</strong> Kawin silang gen antar induk.</li>
                            <li><strong>Mutasi:</strong> Ubah acak sebagian kecil gen.</li>
                            <li><strong>Evaluasi Ulang:</strong> Cek apakah solusi optimal ditemukan (Fitness = 1).</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab2">
            <div class="card shadow border-left-success">
                <div class="card-body">
                    <h5 class="fw-bold text-success">Encoding Cerdas (Representasi Kromosom)</h5>
                    <p>Sistem tidak menyimpan jadwal per 1 jam, melainkan <strong>per BLOK</strong> agar jadwal guru tidak terpecah-pecah.</p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>Data Pengampu (Input)</th>
                                    <th><i class="fas fa-arrow-right"></i></th>
                                    <th>Hasil Encoding (Gen)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($tab_encoding as $row): ?>
                                <tr>
                                    <td class="text-start">
                                        <strong><?= $row['nama_guru'] ?></strong><br>
                                        Mapel: <?= $row['nama_mapel'] ?><br>
                                        Total: <span class="badge bg-danger"><?= $row['jumlah_jam'] ?> JP</span>
                                    </td>
                                    <td class="align-middle"><i class="fas fa-random fa-2x text-gray-300"></i></td>
                                    <td class="text-start">
                                        <?php 
                                            $sisa = $row['jumlah_jam'];
                                            while($sisa > 0) {
                                                $blok = ($sisa >= 4) ? 2 : $sisa;
                                                echo '<span class="badge bg-success mb-1">Gen: Blok '.$blok.' JP</span> ';
                                                $sisa -= $blok;
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab3">
            <div class="card shadow border-left-info">
                <div class="card-body">
                    <h5 class="fw-bold text-info">Inisialisasi Populasi</h5>
                    <p>Setiap Gen yang terbentuk diberi <strong>Waktu (Hari/Jam)</strong> dan <strong>Ruangan</strong> secara ACAK sesuai batasan jenis ruangan.</p>
                    
                    <table class="table table-striped small">
                        <thead>
                            <tr>
                                <th>Gen (Guru - Mapel)</th>
                                <th>Durasi</th>
                                <th>Hasil Acak Hari</th>
                                <th>Hasil Acak Jam</th>
                                <th>Hasil Acak Ruang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($tab_inisialisasi as $row): ?>
                            <tr>
                                <td><?= $row['guru'] ?> <br> <small><?= $row['mapel'] ?></small></td>
                                <td><?= $row['durasi'] ?> JP</td>
                                <td class="fw-bold text-primary"><?= $row['hari'] ?></td>
                                <td class="fw-bold text-primary">Jam ke-<?= $row['jam'] ?></td>
                                <td class="fw-bold text-primary"><?= $row['ruang'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab4">
            <div class="card shadow border-left-warning">
                <div class="card-body">
                    <h5 class="fw-bold text-warning">Perhitungan Fitness (Evaluasi)</h5>
                    <p>Rumus yang digunakan untuk menilai kualitas jadwal:</p>
                    <div class="text-center h4 my-3">
                        $$Fitness = \frac{1}{1 + \sum (P_{guru} + P_{kelas} + P_{ruang})}$$
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-danger">
                                <strong>Penalti (Pelanggaran):</strong>
                                <ul>
                                    <li>Guru Bentrok: +10 Poin</li>
                                    <li>Kelas Bentrok: +10 Poin</li>
                                    <li>Ruang Bentrok: +10 Poin</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <strong>Hasil Akhir Data Real:</strong>
                                <?php 
                                    $finalF = $final_fitness;
                                    $penalti = ($finalF > 0) ? round((1/$finalF) - 1) : 999;
                                ?>
                                <ul>
                                    <li>Fitness Akhir: <strong><?= number_format($finalF, 5) ?></strong></li>
                                    <li>Total Penalti: <strong><?= $penalti ?></strong></li>
                                    <li>Status: <?= ($penalti == 0) ? 'SEMPURNA' : 'BELUM OPTIMAL' ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab5">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="fw-bold">Seleksi Roulette Wheel</h5>
                    <p>Individu dengan Fitness lebih besar memiliki "potongan kue" (peluang) lebih besar untuk terpilih.</p>
                    
                    <table class="table table-bordered text-center">
                        <thead class="bg-secondary text-white">
                            <tr>
                                <th>Kandidat</th>
                                <th>Fitness</th>
                                <th>Probabilitas (%)</th>
                                <th>Visualisasi Peluang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($tab_seleksi as $s): ?>
                            <tr>
                                <td><?= $s['id'] ?></td>
                                <td><?= $s['fitness'] ?></td>
                                <td><?= number_format($s['prob'], 1) ?>%</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?= $s['prob'] ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab6">
            <div class="card shadow border-left-danger">
                <div class="card-body">
                    <h5 class="fw-bold text-danger">Crossover (Kawin Silang)</h5>
                    <p>Mempertukarkan gen antara Induk A dan Induk B pada titik potong tertentu.</p>
                    
                    <div class="row text-center">
                        <div class="col-md-5 border p-3 bg-light">
                            <h6><strong>Induk A</strong></h6>
                            <span class="badge bg-primary">Gen 1</span>
                            <span class="badge bg-primary">Gen 2</span>
                            <span class="badge bg-primary">Gen 3</span>
                        </div>
                        <div class="col-md-2 align-self-center">
                            <i class="fas fa-times fa-2x"></i>
                        </div>
                        <div class="col-md-5 border p-3 bg-light">
                            <h6><strong>Induk B</strong></h6>
                            <span class="badge bg-danger">Gen 4</span>
                            <span class="badge bg-danger">Gen 5</span>
                            <span class="badge bg-danger">Gen 6</span>
                        </div>
                    </div>
                    <div class="text-center my-3"><i class="fas fa-arrow-down"></i> Hasil Anak</div>
                     <div class="row text-center">
                        <div class="col-md-5 offset-md-3 border p-3 bg-white shadow-sm">
                            <h6><strong>Anak Baru</strong></h6>
                            <span class="badge bg-primary">Gen 1</span>
                            <span class="badge bg-primary">Gen 2</span>
                            <span class="badge bg-danger">Gen 6</span> </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab7">
            <div class="card shadow border-left-dark">
                <div class="card-body">
                    <h5 class="fw-bold text-dark">Mutasi (Perubahan Acak)</h5>
                    <p>Setiap gen memiliki peluang 10% untuk berubah posisi (Hari/Jam/Ruang) guna mencari kemungkinan baru.</p>

                    <table class="table table-bordered">
                        <tr>
                            <th>Data Gen (Sebelum)</th>
                            <th class="text-center"><i class="fas fa-random"></i></th>
                            <th>Data Gen (Sesudah Mutasi)</th>
                        </tr>
                        <?php if(!empty($tab_evolusi)): $g = $tab_evolusi[0]; ?>
                        <tr>
                            <td>
                                <strong><?= $g['nama'] ?></strong><br>
                                Hari: <?= $g['hari'] ?><br>
                                Jam: <?= $g['jam'] ?>
                            </td>
                            <td class="align-middle text-center text-danger">MUTASI</td>
                            <td class="bg-warning bg-opacity-10">
                                <strong><?= $g['nama'] ?></strong><br>
                                Hari: <strong class="text-danger">Sabtu</strong> (Berubah)<br>
                                Jam: <strong class="text-danger">1</strong> (Berubah)
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Grafik Konvergensi (Fitness vs Konflik)</h6>
                    <small class="text-white-50">Data Real Hasil Generate</small>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 450px;">
                        <canvas id="realFitnessChart"></canvas>
                    </div>
                    <hr>
                    <div class="row text-center small text-muted">
                        <div class="col-md-6">
                            <span class="text-success fw-bold"><i class="fas fa-chart-line"></i> Garis Hijau (Fitness)</span>:
                            Menunjukkan kualitas jadwal. Semakin naik mendekati 1.0, semakin bagus.
                        </div>
                        <div class="col-md-6">
                            <span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle"></i> Garis Merah (Konflik)</span>:
                            Menunjukkan jumlah bentrok. Semakin turun mendekati 0, semakin sempurna.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Ambil Data History dari Controller
    const historyData = <?= json_encode($history) ?>;
    
    // Pastikan ada data sebelum render
    if(historyData && historyData.length > 0) {
        
        const ctx = document.getElementById('realFitnessChart').getContext('2d');
        const labels = historyData.map(h => 'Gen ' + h.generasi);
        const dataFitness = historyData.map(h => h.fitness);
        const dataKonflik = historyData.map(h => h.konflik);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Nilai Fitness (0 - 1)',
                        data: dataFitness,
                        borderColor: '#1cc88a', // Hijau
                        backgroundColor: 'rgba(28, 200, 138, 0.1)',
                        yAxisID: 'y',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 2
                    },
                    {
                        label: 'Jumlah Konflik (Penalti)',
                        data: dataKonflik,
                        borderColor: '#e74a3b', // Merah
                        backgroundColor: 'rgba(231, 74, 59, 0.05)',
                        borderDash: [5, 5],
                        yAxisID: 'y1',
                        tension: 0.4,
                        fill: false,
                        pointRadius: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Iterasi Generasi' },
                        grid: { display: false }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Fitness (Makin Tinggi Bagus)' },
                        min: 0,
                        max: 1.1
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Jml Konflik (Makin Rendah Bagus)' },
                        grid: { drawOnChartArea: false } // Agar grid tidak tumpang tindih
                    }
                }
            }
        });
    } else {
        document.getElementById('realFitnessChart').parentNode.innerHTML = 
            '<div class="alert alert-warning text-center">Data History kosong. Silakan Generate Jadwal ulang.</div>';
    }
</script>

<?= $this->endSection(); ?>