<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelajaran - MAN Sipagimbar</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        /* 1. BACKGROUND ANIMASI GRADIENT */
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(-45deg, #0f3443, #1c885b, #0f5132, #022c22);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            color: #fff;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* 2. GLASSMORPHISM CARD */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            color: #333;
            overflow: hidden;
        }

        /* 3. NAVBAR KEREN */
        .navbar {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn-login {
            background: linear-gradient(45deg, #FF512F, #DD2476);
            border: none;
            color: white !important;
            font-weight: 600;
            padding: 8px 25px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(221, 36, 118, 0.4);
            transition: all 0.3s ease;
        }
        .btn-login:hover { transform: translateY(-2px); }

        /* 4. TOMBOL CARI & TABS */
        .nav-pills { background: #f1f3f5; padding: 5px; border-radius: 50px; display: inline-flex; }
        .nav-pills .nav-link { border-radius: 50px; color: #6c757d; font-weight: 600; padding: 10px 30px; }
        .nav-pills .nav-link.active { background: #0f5132; color: white; }
        .btn-search { background: #0f5132; color: white; border-radius: 12px; padding: 10px 20px; font-weight: bold; width: 100%; border: none; }
        .btn-search:hover { background: #0b3d26; transform: scale(1.02); }

        /* 5. TABEL JADWAL (MATRIX) */
        .schedule-title { color: #0f5132; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
        .table-custom { border-collapse: separate; border-spacing: 0; width: 100%; }
        .table-custom thead th {
            background: #f8f9fa;
            color: #444;
            font-size: 0.85rem;
            text-transform: uppercase;
            padding: 15px;
            border-bottom: 2px solid #ddd;
        }
        .table-custom tbody td {
            vertical-align: middle;
            padding: 6px; /* Padding sedikit dikecilkan agar muat banyak */
            border-bottom: 1px solid #eee;
            border-right: 1px solid #eee;
        }
        .table-custom tbody tr:last-child td { border-bottom: none; }
        
        /* ITEM MAPEL (CARD DALAM TABEL) */
        .mapel-card {
            background: #fff;
            border-left: 5px solid #34e89e;
            padding: 10px 8px; 
            border-radius: 6px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.2s;
            position: relative;
        }
        .mapel-card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .mapel-name { font-weight: 700; color: #333; font-size: 0.85rem; line-height: 1.2; margin-bottom: 4px; margin-top: 5px; }
        .mapel-info { font-size: 0.7rem; color: #666; font-style: italic; }
        
        /* BADGE RUANG */
        .badge-ruang { 
            background: #212529; 
            color: #fff; 
            font-size: 0.6rem; 
            padding: 2px 6px; 
            border-radius: 4px; 
            position: absolute; 
            top: -8px; 
            right: 2px; 
            z-index: 1;
        }
        .td-relative { position: relative; height: 100%; }

        /* TOMBOL X MERAH BULAT */
        .btn-close-custom {
            background: #dc3545;
            color: white !important;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.4);
            border: 2px solid white;
            z-index: 100;
        }
        .btn-close-custom:hover {
            background: #a71d2a;
            transform: scale(1.1);
            color: white !important;
        }

        /* SELECT2 CUSTOM */
        .select2-container--bootstrap-5 .select2-selection { border-radius: 12px; padding: 10px; height: auto; }

        /* ========================================= */
        /* CSS KHUSUS PRINT (PERBAIKAN TOTAL)        */
        /* ========================================= */
        @media print {
            @page { size: landscape; margin: 5mm; } 
            
            /* Reset Body */
            body { 
                background: white !important; 
                color: black !important; 
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }
            
            /* Sembunyikan elemen tidak penting */
            .navbar, .hero-text, .search-card, footer, .no-print, .btn-close-custom, button { 
                display: none !important; 
            }
            
            /* Paksa area print terlihat dan reset posisi */
            .printable-area { 
                visibility: visible !important;
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            /* Hapus batasan container bootstrap saat print */
            .container, .glass-card {
                width: 100% !important;
                max-width: none !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            /* Perbaiki Tabel */
            .table-custom { 
                width: 100% !important;
                border: 2px solid #000 !important; 
                border-collapse: collapse !important; 
            }
            .table-custom th, .table-custom td { 
                border: 1px solid #000 !important; 
                padding: 4px !important; /* Hemat tempat */
            }
            .table-custom thead th { 
                background-color: #f0f0f0 !important; 
                color: black !important; 
                -webkit-print-color-adjust: exact !important;
            }
            
            /* Paksa warna background mapel muncul */
            .mapel-card {
                border: 1px solid #999 !important;
                border-left-width: 6px !important;
                box-shadow: none !important;
                page-break-inside: avoid; /* Mencegah potongan di tengah card */
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important;
            }
            
            .badge-ruang { 
                border: 1px solid #000 !important; 
                -webkit-print-color-adjust: exact !important; 
            }
            
            /* Mencegah baris tabel terpotong antar halaman */
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top no-print">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url('/') ?>">
                <i class="fas fa-school me-2 text-warning animate__animated animate__swing animate__infinite" style="animation-duration: 3s;"></i> 
                MAN SIPAGIMBAR
            </a>
            <div class="ms-auto">
                <a href="<?= base_url('auth') ?>" class="btn btn-login">
                    <i class="fas fa-lock me-2"></i> Login
                </a>
            </div>
        </div>
    </nav>

    <div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 100vh; padding-top: 80px; padding-bottom: 50px;">
        
       <div class="text-center hero-text mb-5 animate__animated animate__fadeInDown no-print">
            <h1 class="display-4 fw-bold">Jadwal Pelajaran Digital</h1>
            <p class="fs-5 opacity-75">
                Tahun Ajaran <?= $tahun_aktif ?> - Semester <?= $sem_aktif ?>
            </p>
        </div>
        <div class="glass-card p-4 p-md-5 mb-5 w-100 animate__animated animate__fadeInUp no-print" style="max-width: 900px;">
            <div class="d-flex justify-content-center mb-4">
                <ul class="nav nav-pills" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link <?= ($filter_type != 'guru') ? 'active' : '' ?>" id="pills-siswa-tab" data-bs-toggle="pill" data-bs-target="#pills-siswa" type="button">
                            <i class="fas fa-user-graduate me-2"></i> Cari Kelas (Siswa)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link <?= ($filter_type == 'guru') ? 'active' : '' ?>" id="pills-guru-tab" data-bs-toggle="pill" data-bs-target="#pills-guru" type="button">
                            <i class="fas fa-chalkboard-teacher me-2"></i> Cari Guru
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade <?= ($filter_type != 'guru') ? 'show active' : '' ?>" id="pills-siswa">
                    <form action="<?= base_url('/') ?>" method="get" class="row g-3 align-items-end">
                        <div class="col-md-9">
                            <label class="form-label fw-bold text-muted small">PILIH KELAS ANDA</label>
                            <select name="kelas" class="form-select select2-search" required>
                                <option value="">-- Ketik Nama Kelas --</option>
                                <?php foreach($list_kelas as $k): ?>
                                    <option value="<?= $k['id_kelas'] ?>" <?= ($selected_id == $k['id_kelas'] && $filter_type == 'kelas') ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-search shadow" type="submit">LIHAT JADWAL <i class="fas fa-arrow-right ms-2"></i></button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade <?= ($filter_type == 'guru') ? 'show active' : '' ?>" id="pills-guru">
                    <form action="<?= base_url('/') ?>" method="get" class="row g-3 align-items-end">
                        <div class="col-md-9">
                            <label class="form-label fw-bold text-muted small">CARI NAMA GURU</label>
                            <select name="guru" class="form-select select2-search" required>
                                <option value="">-- Ketik Nama Guru --</option>
                                <?php foreach($list_guru as $g): ?>
                                    <option value="<?= $g['id_guru'] ?>" <?= ($selected_id == $g['id_guru'] && $filter_type == 'guru') ? 'selected' : '' ?>><?= $g['nama_guru'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-search shadow" type="submit">LIHAT JADWAL <i class="fas fa-arrow-right ms-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if($status_jadwal != 'published' && !session()->get('logged_in')): ?>
            <div class="glass-card p-5 text-center w-100 no-print" style="max-width: 600px;">
                <div class="mb-3"><i class="fas fa-tools fa-3x text-white bg-warning p-3 rounded-circle shadow"></i></div>
                <h4 class="fw-bold">Sedang Dalam Penyusunan</h4>
                <p class="text-muted">Jadwal belum dipublikasikan.</p>
            </div>
        <?php elseif(empty($jadwal_tampil) && $selected_id): ?>
            <div class="glass-card p-4 text-center animate__animated animate__headShake no-print">
                <i class="fas fa-search-minus fa-2x text-muted mb-2"></i>
                <h5 class="fw-bold text-dark">Data Tidak Ditemukan</h5>
                <p class="text-muted mb-0">Belum ada jadwal untuk pilihan <strong><?= $info_target ?></strong>.</p>
            </div>
        <?php elseif(!empty($jadwal_tampil)): ?>

            <div class="printable-area glass-card p-4 w-100 animate__animated animate__fadeInUp position-relative" style="max-width: 1200px;">
                
                <a href="<?= base_url('/') ?>" class="btn-close-custom position-absolute top-0 end-0 m-3 no-print">
                    <i class="fas fa-times"></i>
                </a>

                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <div class="d-flex align-items-center">
                        <img src="https://cdn-icons-png.flaticon.com/512/2921/2921222.png" width="50" class="me-3 d-none d-print-block">
                        <div>
                            <small class="text-muted text-uppercase fw-bold">Jadwal Pelajaran</small>
                            <h2 class="schedule-title m-0 text-dark"><?= $info_target ?></h2>
                        </div>
                    </div>
                    <button onclick="window.print()" class="btn btn-outline-success rounded-pill fw-bold btn-sm d-none d-md-block no-print me-5">
                        <i class="fas fa-print me-2"></i> Cetak / Simpan PDF
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom w-100">
                        <thead>
                            <tr class="text-center">
                                <th width="8%">Jam</th>
                                <?php foreach($hari_list as $hari): ?>
                                    <th width="15.3%"><?= strtoupper($hari) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $skip_slots = []; ?>
                            <?php for($jam=1; $jam<=$max_jam; $jam++): ?>
                                <?php if(isset($istirahat[$jam])): ?>
                                    <tr style="background-color: #fff3cd !important; page-break-inside: avoid;">
                                        <td class="fw-bold text-center text-warning" style="background: transparent; border: 1px solid #000 !important;"><?= $jam ?></td>
                                        <td colspan="6" class="text-center fw-bold text-warning py-3" style="letter-spacing: 2px; border: 1px solid #000 !important;">
                                            <i class="fas fa-mug-hot me-2"></i> ISTIRAHAT (<?= $istirahat[$jam] ?>)
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td class="text-center bg-light" style="border: 1px solid #000 !important;">
                                            <div class="fw-bold fs-5 text-dark"><?= $jam ?></div>
                                            <small class="text-muted"><?= $waktu_jam[$jam] ?? '-' ?></small>
                                        </td>
                                        <?php foreach($hari_list as $hari): 
                                            if(isset($skip_slots[$hari][$jam])) continue;
                                            $dataSlot = $jadwal_tampil[$hari][$jam] ?? null;
                                            $rowspan = 1;
                                            if($dataSlot) {
                                                for($next = $jam + 1; $next <= $max_jam; $next++) {
                                                    if(isset($istirahat[$next])) break;
                                                    $nextSlot = $jadwal_tampil[$hari][$next] ?? null;
                                                    if($nextSlot && $nextSlot['mapel'] == $dataSlot['mapel'] && $nextSlot['guru'] == $dataSlot['guru'] && $nextSlot['ruang'] == $dataSlot['ruang']) {
                                                        $rowspan++; $skip_slots[$hari][$next] = true;
                                                    } else { break; }
                                                }
                                            }
                                        ?>
                                            <td class="p-1 td-relative" rowspan="<?= $rowspan ?>" style="border: 1px solid #000 !important;">
                                                <?php if($dataSlot): ?>
                                                    <div class="mapel-card" style="border-left-color: <?= $dataSlot['warna'] ?>; background: linear-gradient(to right, <?= $dataSlot['warna'] ?>15, #ffffff); height: 100%;"> 
                                                        <span class="badge-ruang"><?= $dataSlot['ruang'] ?></span>
                                                        <div class="mapel-name"><?= $dataSlot['mapel'] ?></div>
                                                        <div class="mapel-info text-truncate">
                                                            <i class="fas <?= ($filter_type == 'guru') ? 'fa-users' : 'fa-user-tie' ?> me-1 opacity-50"></i>
                                                            <?= $dataSlot['guru'] ?>
                                                        </div>
                                                        <?php if($rowspan > 1): ?>
                                                            <div class="mt-1 badge bg-light text-dark border" style="width: fit-content; font-size: 9px;"><?= $rowspan ?> Jam</div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="text-center opacity-25 py-3 h-100 d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-minus text-secondary"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
                
                

                <div class="d-block d-md-none mt-3 no-print">
                    <button onclick="window.print()" class="btn btn-success w-100 rounded-pill fw-bold shadow">
                        <i class="fas fa-download me-2"></i> Simpan Jadwal
                    </button>
                </div>
            </div>

        <?php endif; ?>

    </div>

    <footer class="text-center py-4 text-white opacity-75 no-print">
        <small>&copy; <?= date('Y') ?> MAN SIPAGIMBAR. All Rights Reserved.</small>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('.select2-search').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Ketik untuk mencari...',
                allowClear: true
            });

            <?php if(!empty($jadwal_tampil)): ?>
                $('html, body').animate({
                    scrollTop: $(".schedule-title").offset().top - 100
                }, 1000);
            <?php endif; ?>
        });
    </script>
</body>
</html>