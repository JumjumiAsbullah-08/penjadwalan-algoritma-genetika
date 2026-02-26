<?= $this->extend('layout/template'); ?>

<?= $this->section('konten'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Styling Tabel Jadwal Professional */
    .schedule-table { table-layout: fixed; width: 100%; border-collapse: collapse; }
    .schedule-table th, .schedule-table td { border: 1px solid #dee2e6; vertical-align: middle; padding: 4px; height: 85px; }
    .schedule-table th { background-color: #f8f9fa; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; height: auto; padding: 10px; }
    
    /* Sel Isi Jadwal */
    .cell-content { height: 100%; width: 100%; padding: 8px; border-radius: 6px; transition: all 0.2s; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden; }
    .cell-content:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 10; }
    
    /* Subject Name */
    .subject-name { font-weight: bold; font-size: 0.85rem; color: #2c3e50; line-height: 1.3; margin-bottom: 4px; margin-top: 10px; white-space: normal; }
    .teacher-name { font-size: 0.75rem; color: #7f8c8d; font-style: italic; }
    
    /* Badge Ruangan */
    .room-badge { font-size: 0.65rem; background: rgba(52, 58, 64, 0.9); color: #fff; padding: 2px 6px; border-radius: 4px; position: absolute; top: 3px; right: 3px; z-index: 5; max-width: 90%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* Baris Istirahat */
    .break-row td { background-color: #fff3cd !important; color: #856404; font-weight: bold; text-align: center; letter-spacing: 2px; font-size: 0.8rem; padding: 5px !important; height: 30px !important; }

    /* Sembunyikan Footer Tanda Tangan di Layar Biasa (Hanya muncul saat Print) */
    .footer-print { display: none; }

    /* CSS KHUSUS PRINT */
    @media print {
        @page { size: landscape; margin: 10mm; } /* Margin sedikit dilonggarkan agar muat lebih banyak */
        
        body * { visibility: hidden; }
        .printable-area, .printable-area * { visibility: visible; }
        .printable-area { position: absolute; left: 0; top: 0; width: 100%; }
        
        .no-print { display: none !important; }
        
        /* Reset Card Style untuk Print */
        .card { border: none !important; box-shadow: none !important; margin-bottom: 0 !important; }
        
        /* Warna Background Tetap Muncul */
        .schedule-table th { background-color: #eee !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .break-row td { background-color: #eee !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .cell-content { border: 1px solid #ccc !important; box-shadow: none !important; }
        
        /* --- LOGIKA HALAMAN & TANDA TANGAN --- */
        
        /* Container per Kelas */
        .schedule-card { 
            page-break-after: always; /* Selalu ganti halaman setelah satu kelas selesai */
            display: block;
            width: 100%;
        }

        /* Mencegah Baris Tabel Terpotong Tengah */
        tr { 
            page-break-inside: avoid !important; 
            break-inside: avoid !important; 
        }
        
        /* PENGATURAN TANDA TANGAN (TTD) PROFESSIONAL */
        .footer-print { 
            display: block !important; 
            width: 100%;
            margin-top: 30px; /* Jarak dari tabel */
            
            /* Logic: "Keep Together" */
            /* Browser akan berusaha menaruh TTD di halaman yang sama dengan tabel. */
            /* JIKA TIDAK MUAT, seluruh blok TTD akan pindah ke halaman baru (tidak terpotong). */
            page-break-inside: avoid !important; 
            break-inside: avoid !important;
        }
        
        .room-badge { border: 1px solid #000; color: #000; background: #fff; }
    }
</style>

<?php
    // 1. AMBIL ROLE LOGIN
    $role = session()->get('role');

    // 2. KONEKSI DATABASE
    $db = \Config\Database::connect();

    // --- LOGIKA PENGAMBILAN DATA KEPALA SEKOLAH (Guru > Users) ---
    
    // Opsi A: Cari di tabel 'guru' yang tugas tambahannya 'Kepala Sekolah'
    // (Ini yang paling akurat karena ada NIP)
    $kepsekGuru = $db->table('guru')
                     ->where('tugas_tambahan', 'Kepala Sekolah')
                     ->get()->getRowArray();

    if ($kepsekGuru) {
        $nama_kepsek = $kepsekGuru['nama_guru'];
        $nip_kepsek  = $kepsekGuru['nip'];
    } else {
        // Opsi B: Jika tidak ada di tabel guru, cari di tabel 'users' yang role-nya 'kepsek'
        $kepsekUser = $db->table('users')
                         ->where('role', 'kepsek')
                         ->get()->getRowArray();
                         
        $nama_kepsek = $kepsekUser['nama_lengkap'] ?? '..........................';
        $nip_kepsek  = '-'; // Tabel users biasanya tidak punya kolom NIP, jadi strip
    }

    // 3. AMBIL CATATAN REVISI (Jika ada)
    $konfigRow = $db->table('konfigurasi')->where('nama_key', 'status_jadwal')->get()->getRowArray();
    $catatan_revisi = $konfigRow['catatan_revisi'] ?? null;
?>

<div class="container-fluid">

    <?php if($status_jadwal == 'draft'): ?>
        
        <?php if(!empty($catatan_revisi)): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 no-print">
                <div class="d-flex"> 
                    <div class="bg-white bg-opacity-50 p-2 rounded-circle me-3 mt-1 align-self-start">
                        <i class="fas fa-exclamation-triangle fa-lg text-danger"></i>
                    </div>
                    <div class="w-100">
                        <h6 class="fw-bold m-0 text-uppercase">Jadwal Perlu Revisi!</h6>
                        <p class="small mb-2">Kepala Sekolah menolak jadwal pengajuan sebelumnya.</p>
                        
                        <div class="bg-white bg-opacity-25 p-3 rounded border border-danger border-opacity-25 text-dark fst-italic">
                            <i class="fas fa-quote-left me-2 opacity-50"></i> 
                            <?= $catatan_revisi ?>
                        </div>

                        <?php if($role == 'admin'): ?>
                        <div class="mt-3">
                            <small class="text-danger fw-bold">* Silakan perbaiki jadwal, lalu ajukan kembali.</small>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="ms-3 align-self-center">
                        <?php if($role == 'admin'): ?>
                            <a href="<?= base_url('hasil/ajukan') ?>" 
                            class="btn btn-danger btn-sm fw-bold shadow-sm btn-confirm-action text-nowrap" 
                            data-type="ajukan">
                            <i class="fas fa-paper-plane me-2"></i> Ajukan Ulang
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-secondary border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center no-print">
                <div class="bg-white bg-opacity-50 p-2 rounded-circle me-3">
                    <i class="fas fa-edit fa-lg text-secondary"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0">Status Jadwal: DRAFT</h6>
                    <small>Jadwal masih dalam tahap penyusunan.</small>
                </div>
                <div class="ms-auto">
                    <?php if($role == 'admin'): ?>
                        <a href="<?= base_url('hasil/ajukan') ?>" 
                           class="btn btn-primary btn-sm fw-bold shadow-sm btn-confirm-action" 
                           data-type="ajukan">
                           <i class="fas fa-paper-plane me-2"></i> Ajukan ke Kepsek
                        </a>
                    <?php else: ?>
                        <span class="badge bg-secondary">Menunggu Pengajuan Admin</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    <?php elseif($status_jadwal == 'pending'): ?>
        <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center no-print">
            <div class="bg-white bg-opacity-50 p-2 rounded-circle me-3">
                <i class="fas fa-hourglass-half fa-lg text-warning"></i>
            </div>
            <div>
                <h6 class="fw-bold m-0">Status Jadwal: MENUNGGU PERSETUJUAN</h6>
                <small>Mohon Kepala Sekolah untuk memeriksa jadwal ini.</small>
            </div>
             <div class="ms-auto d-flex gap-2">
                <?php if($role == 'admin'): ?>
                    <a href="<?= base_url('hasil/batal_ajukan') ?>" 
                       class="btn btn-outline-dark btn-sm fw-bold btn-confirm-action" 
                       data-type="batal_ajukan"> 
                       <i class="fas fa-undo me-1"></i> Tarik Pengajuan
                    </a>
                <?php elseif($role == 'kepsek'): ?>
                    <a href="<?= base_url('hasil/reset_status') ?>" 
                       class="btn btn-danger btn-sm fw-bold shadow-sm btn-confirm-action" 
                       data-type="tolak">
                       <i class="fas fa-times me-2"></i> Tolak / Revisi
                    </a>
                    <a href="<?= base_url('hasil/approve') ?>" 
                       class="btn btn-success btn-sm fw-bold shadow-sm btn-confirm-action" 
                       data-type="approve">
                       <i class="fas fa-check me-2"></i> SETUJUI JADWAL
                    </a>
                <?php endif; ?>
            </div>
        </div>

    <?php elseif($status_jadwal == 'approved'): ?>
        <div class="alert alert-info border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center no-print">
            <div class="bg-white bg-opacity-50 p-2 rounded-circle me-3">
                <i class="fas fa-clipboard-check fa-lg text-info"></i>
            </div>
            <div class="me-3">
                <h6 class="fw-bold m-0">Status Jadwal: DISETUJUI (SIAP TERBIT)</h6>
                <small>Kepsek telah menyetujui. Siap untuk dipublikasikan.</small>
            </div>
            <div class="ms-auto">
                <?php if($role == 'admin'): ?>
                    <a href="<?= base_url('arsip/publish') ?>" 
                       class="btn btn-success btn-sm fw-bold shadow-sm animate__animated animate__pulse animate__infinite btn-confirm-action"
                       data-type="publish_arsip"> <i class="fas fa-rocket me-2"></i> PUBLISH & ARSIPKAN
                    </a>
                <?php else: ?>
                    <span class="badge bg-info text-dark">Menunggu Admin Publish</span>
                <?php endif; ?>
            </div>
        </div>

    <?php elseif($status_jadwal == 'published'): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center no-print">
            <div class="bg-white bg-opacity-50 p-2 rounded-circle me-3">
                <i class="fas fa-check-circle fa-lg text-success"></i>
            </div>
            <div>
                <h6 class="fw-bold m-0">Status Jadwal: TERBIT (LIVE)</h6>
                <small>Jadwal sudah tayang dan tersimpan aman di menu Riwayat.</small>
            </div>
            <div class="ms-auto">
                <?php if($role == 'admin'): ?>
                    <a href="<?= base_url('hasil/batal_publish') ?>" 
                       class="btn btn-danger btn-sm fw-bold shadow-sm btn-confirm-action" 
                       data-type="batal_publish">
                       <i class="fas fa-times-circle me-2"></i> Tarik Jadwal
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>


    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h5 class="fw-bold text-dark m-0"><i class="fas fa-calendar-alt me-2"></i> Hasil Jadwal Pelajaran</h5>
            <small class="text-muted">Tahun Ajaran <?= $tahun_ajaran ?? '2025/2026' ?> (Semester <?= $semester ?? 'Ganjil' ?>)</small>
        </div>
        <div class="d-flex gap-2">
            <select id="classFilter" class="form-select shadow-sm fw-bold border-primary" style="width: 250px;">
                <option value="all">Tampilkan Semua Kelas</option>
                <?php foreach($data_kelas as $k): ?>
                    <option value="kelas-<?= $k['id_kelas'] ?>"><?= $k['nama_kelas'] ?></option>
                <?php endforeach; ?>
            </select>
            
            <?php if($status_jadwal == 'approved' || $status_jadwal == 'published'): ?>
                <button onclick="window.print()" class="btn btn-dark shadow-sm fw-bold">
                    <i class="fas fa-print me-2"></i> Cetak / PDF
                </button>
            <?php else: ?>
                <button class="btn btn-secondary shadow-sm fw-bold" disabled style="cursor: not-allowed;" title="Menunggu Persetujuan Kepsek">
                    <i class="fas fa-lock me-2"></i> Cetak Terkunci
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="printable-area">
        
        <?php foreach($data_kelas as $k): 
            $namaKelas = $k['nama_kelas'];
            $idKelas   = $k['id_kelas'];
            $hasJadwal = isset($jadwal[$namaKelas]);
        ?>

        <div class="card shadow-sm mb-5 schedule-card border-0" id="kelas-<?= $idKelas ?>">
            
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="d-flex justify-content-between align-items-end border-bottom pb-2">
                    <div>
                        <h4 class="fw-bold text-primary m-0" style="font-family: 'Segoe UI', sans-serif;">JADWAL PELAJARAN</h4>
                        <span class="text-muted small text-uppercase">Semester <?= $semester ?? 'Ganjil' ?> - TA <?= $tahun_ajaran ?? '2025/2026' ?></span>
                    </div>
                    <div class="text-end">
                        <h1 class="fw-bold text-dark m-0 display-6"><?= $namaKelas ?></h1>
                        <?php if($status_jadwal == 'published'): ?>
                            <span class="badge bg-success"><i class="fas fa-globe me-1"></i> Live</span>
                        <?php elseif($status_jadwal == 'approved'): ?>
                            <span class="badge bg-info text-dark"><i class="fas fa-check me-1"></i> Verified</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Draft</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-0">
                <?php if(!$hasJadwal): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="far fa-calendar-times fa-3x mb-3 opacity-25"></i>
                        <p>Belum ada jadwal untuk kelas ini.</p>
                    </div>
                <?php else: ?>
                    
                    <div class="table-responsive">
                        <table class="table schedule-table m-0">
                            <thead>
                                <tr class="text-center">
                                    <th width="5%" class="bg-dark text-white">JAM</th>
                                    <th width="10%" class="bg-dark text-white">WAKTU</th>
                                    <?php foreach($hari_list as $hari): ?>
                                        <th width="14.1%"><?= strtoupper($hari) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    // LOGIKA ROWSPAN (COSPLAN)
                                    $skip_slots = []; 
                                ?>

                                <?php for($jam = 1; $jam <= $max_jam; $jam++): ?>
                                    
                                    <?php if(isset($istirahat[$jam])): ?>
                                        <tr class="break-row">
                                            <td class="bg-dark text-white border-dark text-center fw-bold"><?= $jam ?></td>
                                            <td class="bg-dark text-white border-dark text-center small"><?= $waktu_jam[$jam] ?? '-' ?></td>
                                            <td colspan="6">
                                                <i class="fas fa-mug-hot me-2"></i> ISTIRAHAT
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td class="text-center fw-bold bg-light text-secondary"><?= $jam ?></td>
                                            <td class="text-center small text-muted bg-light"><?= $waktu_jam[$jam] ?? '00:00 - 00:00' ?></td>
                                            
                                            <?php foreach($hari_list as $hari): 
                                                if(isset($skip_slots[$hari][$jam])) continue;

                                                $slot = $jadwal[$namaKelas][$hari][$jam] ?? null;
                                                
                                                // Hitung Rowspan
                                                $rowspan = 1;
                                                if($slot) {
                                                    for($next = $jam + 1; $next <= $max_jam; $next++) {
                                                        if(isset($istirahat[$next])) break;
                                                        $next_slot = $jadwal[$namaKelas][$hari][$next] ?? null;
                                                        
                                                        if($next_slot && 
                                                           $next_slot['mapel'] == $slot['mapel'] && 
                                                           $next_slot['guru'] == $slot['guru'] && 
                                                           $next_slot['ruang'] == $slot['ruang']) {
                                                            $rowspan++;
                                                            $skip_slots[$hari][$next] = true; 
                                                        } else {
                                                            break;
                                                        }
                                                    }
                                                }
                                            ?>
                                                
                                                <td rowspan="<?= $rowspan ?>" class="p-1">
                                                    <?php if($slot): ?>
                                                        <div class="cell-content h-100 w-100 rounded-3 border" 
                                                             style="background-color: <?= $slot['warna'] ?>; border-color: rgba(0,0,0,0.05) !important;">
                                                            
                                                            <span class="room-badge"><?= $slot['ruang'] ?></span>
                                                            
                                                            <div class="subject-name">
                                                                <?= $slot['mapel'] ?>
                                                            </div>
                                                            
                                                            <div class="teacher-name text-wrap">
                                                                <i class="fas fa-user-tie fa-xs me-1"></i> <?= $slot['guru'] ?>
                                                            </div>

                                                            <?php if($rowspan > 1): ?>
                                                                <div class="mt-1 badge bg-white text-dark border shadow-sm" style="width:fit-content; font-size: 9px; align-self: flex-start;">
                                                                    <?= $rowspan ?> Jam
                                                                </div>
                                                            <?php endif; ?>

                                                        </div>
                                                    <?php else: ?>
                                                        <div class="h-100 w-100 d-flex align-items-center justify-content-center opacity-25">
                                                            <i class="fas fa-minus text-muted"></i>
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

                <?php endif; ?>
            </div>
            
            <?php if($status_jadwal == 'approved' || $status_jadwal == 'published'): ?>
            <div class="card-footer bg-white border-0 pt-5 pb-5 footer-print">
                <div class="row">
                    <div class="col-8"></div> 
                    
                    <div class="col-4 text-center">
                        <p class="mb-2">
                            Sipagimbar, <?= date('d F Y') ?> <br>
                            Mengetahui,<br>
                            <strong>Kepala Sekolah</strong>
                        </p>
                        
                        <div class="my-3">
                            <?php 
                                // Text QR yang informatif
                                $isiQR = "DOKUMEN VALID.\n\nDisetujui oleh:\n" . $nama_kepsek . "\nJabatan: Kepala Madrasah\nTanggal: " . date('d F Y') . "\nStatus: SAH & FINAL";
                                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=" . urlencode($isiQR);
                            ?>
                            <img src="<?= $qrUrl ?>" alt="QR Validasi" style="width: 80px; height: 80px; opacity: 0.9;">
                            <br>
                            <small class="text-muted fst-italic" style="font-size: 8px;">Ditandatangani secara elektronik</small>
                        </div>

                        <p class="fw-bold text-decoration-underline mb-0 text-uppercase"><?= $nama_kepsek ?></p>
                        <small>NIP. <?= (!empty($nip_kepsek) && $nip_kepsek != '-') ? $nip_kepsek : '..........................' ?></small>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <?php endforeach; ?>
    </div>

</div>

<script>
    // 1. FILTER KELAS JS
    const filterSelect = document.getElementById('classFilter');
    const allCards = document.querySelectorAll('.schedule-card');

    if(filterSelect) {
        filterSelect.addEventListener('change', function() {
            const selected = this.value;
            if(selected === 'all') {
                allCards.forEach(card => card.style.display = 'block');
            } else {
                allCards.forEach(card => {
                    if(card.id === selected) card.style.display = 'block';
                    else card.style.display = 'none';
                });
            }
        });
    }

    // 2. SWEETALERT ACTION
    document.querySelectorAll('.btn-confirm-action').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); 
            const url = this.getAttribute('href');
            const type = this.getAttribute('data-type');
            
            let title = ''; let text = ''; let icon = ''; 
            let confirmBtnText = ''; let btnColor = '';

            if(type === 'ajukan') {
                title = 'Ajukan ke Kepala Sekolah?'; text = 'Jadwal akan dikunci.'; icon = 'question'; confirmBtnText = 'Ya, Ajukan!'; btnColor = '#0d6efd';
            } 
            else if(type === 'batal_ajukan') {
                title = 'Tarik Pengajuan?'; text = 'Jadwal kembali ke Draft.'; icon = 'warning'; confirmBtnText = 'Ya, Tarik!'; btnColor = '#ffc107'; 
            }
            else if(type === 'publish_arsip') {
                title = 'Publish & Arsipkan?'; text = 'Jadwal akan TAYANG.'; icon = 'success'; confirmBtnText = 'Ya, Terbitkan!'; btnColor = '#198754';
            } 
            else if(type === 'batal_publish') {
                title = 'Tarik Jadwal?'; text = 'Jadwal di-unpublish.'; icon = 'error'; confirmBtnText = 'Ya, Tarik!'; btnColor = '#dc3545';
            }
            else if(type === 'approve') {
                title = 'Setujui Jadwal?'; text = 'Siap diterbitkan.'; icon = 'success'; confirmBtnText = 'Ya, Setuju'; btnColor = '#198754';
            }
            else if(type === 'tolak') {
                title = 'Tolak / Revisi?';
                text = 'Tulis alasan penolakan:';
                icon = 'warning'; confirmBtnText = 'Kirim Revisi'; btnColor = '#dc3545';

                Swal.fire({
                    title: title, text: text, icon: icon, input: 'textarea',
                    inputPlaceholder: 'Alasan revisi...',
                    showCancelButton: true, confirmButtonColor: btnColor, confirmButtonText: confirmBtnText, cancelButtonText: 'Batal',
                    inputValidator: (value) => { if (!value) return 'Wajib diisi!' }
                }).then((result) => {
                    if (result.isConfirmed) { window.location.href = url + '?alasan=' + encodeURIComponent(result.value); }
                });
                return;
            }

            Swal.fire({
                title: title, text: text, icon: icon,
                showCancelButton: true, confirmButtonColor: btnColor, confirmButtonText: confirmBtnText, cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = url; }
            });
        });
    });

    <?php if(session()->getFlashdata('success')): ?>
        Swal.fire({icon: 'success', title: 'Berhasil!', text: '<?= session()->getFlashdata('success') ?>', timer: 2000, showConfirmButton: false});
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        Swal.fire({icon: 'error', title: 'Gagal!', text: '<?= session()->getFlashdata('error') ?>', confirmButtonColor: '#dc3545'});
    <?php endif; ?>
</script>

<?= $this->endSection(); ?>