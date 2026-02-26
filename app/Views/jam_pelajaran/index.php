<?= $this->extend('layout/template'); ?>

<?= $this->section('konten'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .nav-pills .nav-link.active {
        background-color: #f8f9fa !important; /* Warna abu sangat muda agar tetap kontras */
        color: #198754 !important; /* Warna hijau success sesuai tema */
        border-color: #198754 !important;
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
    }
    .nav-pills .nav-link {
        color: #6c757d;
    }
</style>

<div class="container-fluid">

    <?php if($is_locked): ?>
        <div class="alert alert-warning d-flex align-items-center mb-4 shadow-sm border-0 rounded-3">
            <div class="bg-warning bg-opacity-25 p-3 rounded-circle me-3 text-warning"><i class="fas fa-lock fa-lg"></i></div>
            <div>
                <h6 class="fw-bold mb-1">Pengaturan Terkunci</h6>
                <span class="small text-muted">Data Jadwal tidak dapat diubah karena <b>Jadwal Pelajaran sudah digenerate</b>.</span>
            </div>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 overflow-hidden rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
            <div>
                <h5 class="mb-0 text-success fw-bold"><i class="fas fa-clock me-2"></i> Jam Pelajaran</h5>
                <p class="text-muted small mb-0">Atur slot waktu, durasi, dan istirahat.</p>
            </div>
            
            <?php if(!$is_locked): ?>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalWizard">
                        <i class="fas fa-magic me-2"></i> Generator
                    </button>
                    <button type="button" class="btn btn-outline-success rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="fas fa-plus"></i> Manual
                    </button>
                </div>
            <?php else: ?>
                <button class="btn btn-secondary rounded-pill px-4" disabled><i class="fas fa-lock me-2"></i> Terkunci</button>
            <?php endif; ?>
        </div>
        
        <div class="card-body bg-light bg-opacity-10">
            
            <ul class="nav nav-pills mb-4 gap-2 justify-content-center" id="pills-tab" role="tablist">
                <?php $first = true; foreach(array_keys($jam_per_hari) as $hari): ?>
                <li class="nav-item">
                    <button class="nav-link <?= $first ? 'active' : '' ?> rounded-pill px-4 fw-bold border bg-white" 
                            id="pills-<?= $hari ?>-tab" data-bs-toggle="pill" data-bs-target="#pills-<?= $hari ?>" type="button">
                        <?= $hari ?>
                    </button>
                </li>
                <?php $first = false; endforeach; ?>
            </ul>

            <form action="<?= base_url('jampelajaran/deleteMultiple') ?>" method="post" id="formDeleteMultiple">
                
                <div id="floatingAction" class="position-fixed bottom-0 end-0 m-4" style="z-index: 1050; display: none;">
                    <button type="button" class="btn btn-danger btn-lg shadow-lg rounded-pill px-4 fw-bold animate__animated animate__bounceIn" id="btnDeleteSelected">
                        <i class="fas fa-trash-alt me-2"></i> Hapus (<span id="countSelected">0</span>)
                    </button>
                </div>

                <div class="tab-content" id="pills-tabContent">
                    <?php $first = true; foreach($jam_per_hari as $hari => $slots): ?>
                    <div class="tab-pane fade <?= $first ? 'show active' : '' ?>" id="pills-<?= $hari ?>">
                        
                        <?php if(empty($slots)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="far fa-calendar-times fa-3x mb-3 opacity-25"></i>
                                <h6>Belum ada jadwal untuk <?= $hari ?></h6>
                                <small>Gunakan tombol Generator atau Manual di atas.</small>
                            </div>
                        <?php else: ?>
                            
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <div class="timeline-container position-relative py-2">
                                        <div class="position-absolute top-0 bottom-0 start-0 ms-5 border-start border-2 border-secondary border-opacity-25" 
                                             style="border-style: dashed !important; z-index: 0; left: 15px;"></div>

                                        <?php if(!$is_locked): ?>
                                        <div class="mb-4 ms-5 ps-4">
                                            <div class="form-check">
                                                <input class="form-check-input check-all cursor-pointer" type="checkbox" data-target=".check-item-<?= $hari ?>">
                                                <label class="form-check-label small fw-bold text-muted ms-1">Pilih Semua</label>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <?php foreach($slots as $jam): 
                                            $isRest = $jam['is_istirahat'] == 1;
                                            
                                            // Style Config
                                            $cardBg   = $isRest ? 'bg-warning bg-opacity-10 border-warning' : 'bg-white border-light';
                                            $dotColor = $isRest ? '#ffc107' : '#0d6efd'; 
                                            $icon     = $isRest ? 'fa-mug-hot text-warning' : 'fa-book-open text-primary';
                                            $title    = $isRest ? 'ISTIRAHAT' : 'JAM KE-' . $jam['jam_ke'];
                                            
                                            $diff = round(abs(strtotime($jam['waktu_selesai']) - strtotime($jam['waktu_mulai'])) / 60);
                                        ?>
                                        
                                        <div class="d-flex align-items-center mb-3 position-relative" style="z-index: 1;">
                                            
                                            <div class="me-3" style="width: 20px;">
                                                <?php if(!$is_locked): ?>
                                                    <input class="form-check-input check-item check-item-<?= $hari ?>" type="checkbox" name="ids[]" value="<?= $jam['id_jam'] ?>">
                                                <?php endif; ?>
                                            </div>

                                            <div class="text-end me-3" style="width: 65px;">
                                                <span class="fw-bold text-dark d-block small font-monospace"><?= substr($jam['waktu_mulai'],0,5) ?></span>
                                                <span class="small text-muted d-block font-monospace" style="font-size: 11px;"><?= substr($jam['waktu_selesai'],0,5) ?></span>
                                            </div>
                                            
                                            <div class="me-3 rounded-circle d-flex align-items-center justify-content-center shadow-sm bg-white" 
                                                 style="width: 32px; height: 32px; border: 3px solid <?= $dotColor ?>;">
                                                <i class="fas <?= $icon ?> fa-xs"></i>
                                            </div>

                                            <div class="card flex-grow-1 shadow-sm border <?= $isRest ? 'border-warning' : '' ?> rounded-3" 
                                                 style="transition: transform 0.2s;">
                                                <div class="card-body py-3 px-3 d-flex justify-content-between align-items-center position-relative overflow-hidden <?= $cardBg ?>">
    
                                                    <div class="position-absolute start-0 top-0 bottom-0 bg-<?= $isRest ? 'warning' : 'primary' ?>" style="width: 4px;"></div>

                                                    <div class="ps-2 position-relative" style="z-index: 2;">
                                                        <h6 class="mb-1 fw-bold text-uppercase <?= $isRest ? 'text-warning' : 'text-dark' ?>" style="letter-spacing: 0.5px;">
                                                            <?= $title ?>
                                                        </h6>
                                                        
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge rounded-pill bg-white text-secondary border shadow-sm me-2">
                                                                <i class="far fa-clock me-1 text-primary"></i> <?= $diff ?> Menit
                                                            </span>
                                                            <?php if($isRest): ?>
                                                                <span class="badge rounded-pill bg-warning text-dark shadow-sm">
                                                                    <i class="fas fa-pause me-1"></i> Break
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <?php if(!$is_locked): ?>
                                                    <div class="position-relative" style="z-index: 2;">
                                                        <button type="button" class="btn btn-light text-primary btn-sm rounded-circle shadow-sm border btn-edit" 
                                                                style="width: 38px; height: 38px; transition: transform 0.2s;"
                                                                onmouseover="this.style.transform='scale(1.1)'" 
                                                                onmouseout="this.style.transform='scale(1)'"
                                                                data-bs-toggle="modal" data-bs-target="#modalEdit"
                                                                data-id="<?= $jam['id_jam'] ?>"
                                                                data-hari="<?= $jam['hari'] ?>"
                                                                data-ke="<?= $jam['jam_ke'] ?>"
                                                                data-mulai="<?= $jam['waktu_mulai'] ?>"
                                                                data-selesai="<?= $jam['waktu_selesai'] ?>"
                                                                data-istirahat="<?= $jam['is_istirahat'] ?>"
                                                                title="Edit Slot Waktu">
                                                            <i class="fas fa-pen fa-sm"></i>
                                                        </button>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                        <?php endif; ?>
                    </div>
                    <?php $first = false; endforeach; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalWizard" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title fw-bold"><i class="fas fa-magic me-2"></i> Generator Jadwal Otomatis</h5>
                    <p class="mb-0 small text-white-50">Buat slot waktu masal dengan cepat dan akurat.</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('jampelajaran/generate') ?>" method="post">
                <div class="modal-body p-4">
                    
                    <div class="alert alert-danger border-0 d-flex align-items-center mb-4 shadow-sm">
                        <div class="bg-white text-danger p-2 rounded-circle me-3">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Hati-hati!</h6>
                            <small>Tindakan ini akan <b>MENGHAPUS SEMUA JADWAL LAMA</b> pada hari yang Anda pilih.</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold text-secondary small text-uppercase mb-2">1. Pilih Hari Target</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; foreach($hariList as $h): ?>
                            <input type="checkbox" class="btn-check" name="days[]" id="wiz_<?= $h ?>" value="<?= $h ?>" checked>
                            <label class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm" for="wiz_<?= $h ?>"><?= $h ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6 border-end">
                            <h6 class="text-primary fw-bold mb-3"><i class="fas fa-hourglass-start me-2"></i> Waktu Belajar</h6>
                            <div class="mb-3">
                                <label class="small fw-bold text-muted">Jam Mulai</label>
                                <input type="time" name="start_time" class="form-control fw-bold fs-5 text-center" value="07:30" required>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <label class="small fw-bold text-muted">Durasi (Menit)</label>
                                    <input type="number" name="slot_duration" class="form-control text-center fw-bold" value="45" required>
                                </div>
                                <div class="col-6">
                                    <label class="small fw-bold text-muted">Total Slot</label>
                                    <input type="number" name="total_slots" class="form-control text-center fw-bold" value="8" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-warning fw-bold mb-3"><i class="fas fa-mug-hot me-2"></i> Jam Istirahat</h6>
                            
                            <div class="bg-warning bg-opacity-10 p-3 rounded-3 border border-warning border-opacity-25">
                                <div class="mb-3">
                                    <label class="small fw-bold text-dark d-block mb-2">Sisipkan Istirahat SETELAH Jam ke:</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php for($i=1; $i<=10; $i++): ?>
                                            <input type="checkbox" class="btn-check" name="breaks[]" id="break_<?= $i ?>" value="<?= $i ?>" <?= ($i==4 || $i==7) ? 'checked' : '' ?>>
                                            <label class="btn btn-sm btn-outline-warning text-dark fw-bold" for="break_<?= $i ?>" style="width: 35px;"><?= $i ?></label>
                                        <?php endfor; ?>
                                    </div>
                                    <small class="text-muted fst-italic mt-1 d-block" style="font-size: 11px;">*Klik angka untuk memilih (Bisa lebih dari satu).</small>
                                </div>
                                
                                <div>
                                    <label class="small fw-bold text-dark">Durasi Istirahat (Menit)</label>
                                    <input type="number" name="break_duration" class="form-control border-warning text-center fw-bold" value="20">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fas fa-check-circle me-2"></i> Generate Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach(['Tambah', 'Edit'] as $mode): ?>
<div class="modal fade" id="modal<?= $mode ?>" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header <?= $mode == 'Tambah' ? 'bg-success' : 'bg-primary' ?> text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-clock me-2"></i> <?= $mode ?> Slot Waktu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('jampelajaran/' . ($mode == 'Tambah' ? 'store' : 'update')) ?>" method="post">
                <div class="modal-body p-4">
                    <?php if($mode == 'Edit'): ?><input type="hidden" name="id_jam" id="edit_id"><?php endif; ?>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="small fw-bold text-uppercase text-secondary">Hari</label>
                            <select name="hari" id="<?= strtolower($mode) ?>_hari" class="form-select bg-light fw-bold" required>
                                <?php foreach(array_keys($jam_per_hari) as $h): ?><option value="<?= $h ?>"><?= $h ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold text-uppercase text-secondary">Jam Ke-</label>
                            <input type="number" name="jam_ke" id="<?= strtolower($mode) ?>_ke" class="form-control fw-bold" placeholder="Cth: 1" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="small fw-bold text-uppercase text-secondary">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" id="<?= strtolower($mode) ?>Mulai" class="form-control text-center fw-bold fs-5" required>
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold text-uppercase text-secondary">Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" id="<?= strtolower($mode) ?>Selesai" class="form-control text-center fw-bold fs-5" required>
                        </div>
                    </div>

                    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calculator text-muted me-2"></i>
                            <small class="fw-bold text-muted">Total Durasi:</small>
                        </div>
                        <span id="<?= strtolower($mode) ?>DurasiText" class="badge bg-secondary">0 Menit</span>
                    </div>

                    <div class="p-3 border rounded bg-white d-flex align-items-center justify-content-between cursor-pointer shadow-sm" onclick="document.getElementById('<?= strtolower($mode) ?>Istirahat').click()">
                        <div>
                            <span class="d-block fw-bold text-dark"><i class="fas fa-mug-hot text-warning me-2"></i> Ini Waktu Istirahat?</span>
                            <small class="text-muted">Aktifkan jika ini bukan jam pelajaran efektif.</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input fs-4" type="checkbox" name="is_istirahat" id="<?= strtolower($mode) ?>Istirahat" onclick="event.stopPropagation()">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn <?= $mode == 'Tambah' ? 'btn-success' : 'btn-primary' ?> rounded-pill px-4 fw-bold shadow-sm">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
    // --- 1. SMART DURATION CALCULATOR (Realtime) ---
    function calcDuration(mode) {
        const s = document.getElementById(mode+'Mulai');
        const e = document.getElementById(mode+'Selesai');
        const t = document.getElementById(mode+'DurasiText');
        
        const update = () => {
            if(s.value && e.value) {
                let start = new Date("1970-01-01 " + s.value);
                let end = new Date("1970-01-01 " + e.value);
                let diff = Math.round((end - start) / 60000);
                
                if(diff > 0) { 
                    t.innerText = diff + " Menit"; 
                    t.className = "badge bg-success shadow-sm fs-6";
                } else { 
                    t.innerText = "Waktu Salah!"; 
                    t.className = "badge bg-danger shadow-sm fs-6";
                }
            }
        };
        s.addEventListener('change', update); e.addEventListener('change', update);
    }
    calcDuration('tambah'); calcDuration('edit');

    // --- 2. CHECKBOX & FLOATING DELETE ---
    const checkItems = document.querySelectorAll('.check-item');
    const floatingAction = document.getElementById('floatingAction');
    const countSelected = document.getElementById('countSelected');
    const checkAlls = document.querySelectorAll('.check-all');

    function updateFloating() {
        const count = document.querySelectorAll('.check-item:checked').length;
        if(count > 0) {
            floatingAction.style.display = 'block';
            countSelected.innerText = count;
        } else {
            floatingAction.style.display = 'none';
        }
    }

    checkAlls.forEach(ca => {
        ca.addEventListener('change', function() {
            let target = this.getAttribute('data-target');
            document.querySelectorAll(target).forEach(c => c.checked = this.checked);
            updateFloating();
        });
    });

    checkItems.forEach(c => c.addEventListener('change', updateFloating));

    document.getElementById('btnDeleteSelected').addEventListener('click', function() {
        Swal.fire({
            title: 'Hapus item terpilih?', 
            text: "Data yang dihapus tidak bisa dikembalikan!", 
            icon: 'warning',
            showCancelButton: true, 
            confirmButtonColor: '#d33', 
            confirmButtonText: 'Ya, Hapus!'
        }).then((r) => { if(r.isConfirmed) document.getElementById('formDeleteMultiple').submit(); });
    });

    // --- 3. POPULATE EDIT MODAL ---
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.getAttribute('data-id');
            document.getElementById('edit_hari').value = this.getAttribute('data-hari');
            document.getElementById('edit_ke').value = this.getAttribute('data-ke');
            document.getElementById('editMulai').value = this.getAttribute('data-mulai').substring(0,5);
            document.getElementById('editSelesai').value = this.getAttribute('data-selesai').substring(0,5);
            document.getElementById('editIstirahat').checked = (this.getAttribute('data-istirahat') == 1);
            
            document.getElementById('editMulai').dispatchEvent(new Event('change'));
        });
    });

    // --- 4. FLASH MESSAGES ---
    <?php if(session()->getFlashdata('success')): ?>
        Swal.fire({icon: 'success', title: 'Berhasil', text: '<?= session()->getFlashdata('success') ?>', timer: 2000, showConfirmButton: false});
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        Swal.fire({icon: 'error', title: 'Gagal', text: '<?= session()->getFlashdata('error') ?>'});
    <?php endif; ?>
</script>

<?= $this->endSection(); ?>