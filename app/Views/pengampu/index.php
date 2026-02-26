<?= $this->extend('layout/template'); ?>

<?= $this->section('konten'); ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
    /* Fix Z-Index Select2 agar muncul di atas Modal */
    .select2-container { z-index: 999999 !important; }
    .select2-dropdown { z-index: 9999999 !important; }
    
    /* Styling Matrix Kelas (Grid Responsive & Elegant) */
    .class-matrix-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); 
        gap: 8px;
        max-height: 250px; 
        overflow-y: auto;
        padding: 12px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background-color: #f8f9fa;
    }
    
    /* Desain Tombol Checkbox Keren */
    .btn-check:checked + .btn-outline-primary {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
        box-shadow: 0 4px 6px rgba(13, 110, 253, 0.3);
        transform: translateY(-1px);
        font-weight: bold;
    }
    
    /* Label Kelas (Fix Text Wrapping) */
    .class-item-label {
        font-size: 0.75rem !important;
        font-weight: 500;
        padding: 8px 4px !important;
        width: 100%;
        height: 100%; 
        border-radius: 8px !important;
        transition: all 0.2s ease;
        text-align: center;
        white-space: normal !important; 
        word-wrap: break-word;
        line-height: 1.2;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .class-item-label:hover {
        background-color: #e9ecef;
    }
</style>

<div class="container-fluid">

    <?php 
        $guru_sudah_ada = [];
        if(!empty($daftar_pengampu)) {
            $guru_sudah_ada = array_unique(array_column($daftar_pengampu, 'id_guru'));
        }
        $guru_belum_ada = [];
        foreach($data_guru as $g) {
            if(!in_array($g['id_guru'], $guru_sudah_ada)) {
                $guru_belum_ada[] = $g;
            }
        }
    ?>

    <?php if(!empty($guru_belum_ada)): ?>
    <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-4 animate__animated animate__fadeInDown">
        <div class="d-flex align-items-start">
            <div class="bg-warning bg-opacity-25 p-2 rounded-circle me-3 text-warning">
                <i class="fas fa-user-clock fa-lg"></i>
            </div>
            <div class="w-100">
                <h6 class="fw-bold text-dark mb-1">Perhatian: <?= count($guru_belum_ada) ?> Guru Belum Memiliki Beban Mengajar</h6>
                <p class="small text-muted mb-2">Guru berikut terdaftar di database tapi belum diinputkan tugas mengajarnya sama sekali:</p>
                
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach($guru_belum_ada as $gb): ?>
                        <div class="d-flex align-items-center bg-white border border-warning rounded-pill px-3 py-1 shadow-sm">
                            <span class="small fw-bold text-dark me-2"><?= $gb['nama_guru'] ?></span>
                            
                            <?php if(!$is_locked): ?>
                                <button type="button" class="btn btn-sm btn-warning rounded-circle p-0 d-flex align-items-center justify-content-center btn-quick-add" 
                                        style="width: 20px; height: 20px;"
                                        data-id="<?= $gb['id_guru'] ?>"
                                        data-nama="<?= $gb['nama_guru'] ?>"
                                        title="Input Tugas Sekarang">
                                    <i class="fas fa-plus fa-xs text-white"></i>
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-sm btn-secondary rounded-circle p-0 d-flex align-items-center justify-content-center" 
                                        style="width: 20px; height: 20px; cursor: not-allowed;"
                                        disabled
                                        title="Terkunci">
                                    <i class="fas fa-lock fa-xs text-white"></i>
                                </button>
                            <?php endif; ?>
                            
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark m-0">Manajemen Beban Mengajar</h5>
        <button class="btn btn-sm btn-outline-info rounded-pill shadow-sm fw-bold px-3" type="button" data-bs-toggle="collapse" data-bs-target="#infoAturan" aria-expanded="false">
            <i class="fas fa-info-circle me-1"></i> Info Aturan & Ketentuan
        </button>
    </div>

    <div class="collapse mb-4" id="infoAturan">
        <div class="card card-body border-info bg-info bg-opacity-10 border-0 shadow-sm">
            <div class="d-flex align-items-start">
                <i class="fas fa-book-reader fa-2x text-info me-3 mt-1"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-2">Pedoman Beban Kerja Guru (Permendikbud No. 15 Tahun 2018)</h6>
                    <ul class="mb-0 small text-muted ps-3" style="list-style-type: circle;">
                        <li class="mb-1">Guru bersertifikasi wajib memenuhi beban kerja minimal <b>24 Jam Pelajaran (JP)</b> tatap muka per minggu.</li>
                        <li class="mb-1">Beban kerja maksimal yang disarankan adalah <b>40 JP</b> per minggu.</li>
                        <li>
                            Tugas Tambahan dihitung sebagai Ekuivalensi Jam:
                            <span class="badge bg-white text-dark border ms-1">Kepsek (24 JP)</span>
                            <span class="badge bg-white text-dark border ms-1">Waka/Ka.Lab/Ka.Perpus (12 JP)</span>
                            <span class="badge bg-white text-dark border ms-1">Wali Kelas (2 JP)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php if($is_locked): ?>
        <div class="alert alert-warning d-flex align-items-center mb-4 shadow-sm border-0 rounded-3 fade show">
            <div class="bg-warning bg-opacity-25 p-2 rounded-circle me-3 text-warning"><i class="fas fa-lock fa-lg"></i></div>
            <div>
                <h6 class="fw-bold mb-0">Mode Terkunci</h6>
                <small class="text-muted">Data dikunci karena jadwal sudah digenerate. Reset jadwal untuk membuka kunci.</small>
            </div>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 overflow-hidden rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
            <div>
                <h6 class="mb-0 text-success fw-bold"><i class="fas fa-list-alt me-2"></i> Data Pengampu</h6>
                <small class="text-muted">Distribusi guru dan mata pelajaran.</small>
            </div>
            
            <?php if(!$is_locked): ?>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary rounded-pill px-3 shadow-sm fw-bold btn-sm" data-bs-toggle="modal" data-bs-target="#modalSalin">
                        <i class="fas fa-copy me-2"></i> Salin Data
                    </button>

                    <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="fas fa-plus me-2"></i> Tambah Tugas
                    </button>
                </div>
            <?php else: ?>
                <button class="btn btn-secondary rounded-pill px-4 btn-sm" disabled><i class="fas fa-lock me-2"></i> Terkunci</button>
            <?php endif; ?>
        </div>
        
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-5 ms-auto">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 ps-3 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-2 py-2" placeholder="Cari Nip, Guru, Mapel, atau Kelas..." autocomplete="off">
                    </div>
                </div>
            </div>

            <form action="<?= base_url('pengampu/deleteMultiple') ?>" method="post" id="formDeleteMultiple">
                
                <div id="floatingAction" class="position-fixed bottom-0 end-0 m-4" style="z-index: 1050; display: none;">
                    <button type="button" class="btn btn-danger btn-lg shadow-lg rounded-pill px-4 fw-bold animate__animated animate__bounceIn" id="btnDeleteSelected">
                        <i class="fas fa-trash-alt me-2"></i> Hapus (<span id="countSelected">0</span>)
                    </button>
                </div>

                <div class="table-responsive" style="max-height: 650px; overflow-y: auto;">
                    <table class="table table-hover align-middle" id="tabelPengampu">
                        <thead class="bg-light text-secondary small text-uppercase sticky-top" style="z-index: 5;">
                            <tr>
                                <th width="5%" class="text-center py-3">
                                    <?php if(!$is_locked): ?>
                                        <input class="form-check-input check-all cursor-pointer" type="checkbox">
                                    <?php else: ?>No<?php endif; ?>
                                </th>
                                <th width="30%" class="py-3">Guru & Beban Kerja</th>
                                <th width="20%" class="py-3">Mata Pelajaran</th>
                                <th width="15%" class="py-3 text-center">Kelas</th>
                                <th width="15%" class="py-3 text-center">Durasi</th>
                                <th width="10%" class="text-center py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($daftar_pengampu)): ?>
                                <?php $no=1; foreach($daftar_pengampu as $p): 
                                    $total = $p['total_beban_kerja'];
                                    
                                    // Logic Badge Status
                                    $badgeClass = 'bg-danger bg-opacity-100'; 
                                    $iconStatus = 'fa-exclamation-circle';
                                    if($total >= 24) { $badgeClass = 'bg-success bg-opacity-100'; $iconStatus = 'fa-check-circle'; }
                                    if($total > 40) { $badgeClass = 'bg-warning text-dark'; $iconStatus = 'fa-fire'; } 
                                ?>
                                <tr class="data-row">
                                    <td class="text-center">
                                        <?php if(!$is_locked): ?>
                                            <input class="form-check-input check-item" type="checkbox" name="ids[]" value="<?= $p['id_pengampu'] ?>">
                                        <?php else: ?>
                                            <span class="text-muted small fw-bold"><?= $no++ ?></span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark"><?= $p['nama_guru'] ?></span>
                                            
                                            <?php 
                                                $isStruktural = (!empty($p['tugas_tambahan']) && $p['tugas_tambahan'] != 'Tidak Ada');
                                                $jamStruktural = 0;
                                                if($isStruktural): 
                                                    $jamStruktural = ($p['tugas_tambahan'] == 'Kepala Sekolah') ? 24 : 12;
                                            ?>
                                                <span class="badge bg-indigo text-primary border border-primary mt-1" style="width: fit-content; font-size: 9px; background-color: #e7f1ff;">
                                                    <i class="fas fa-user-tie me-1"></i> <?= $p['tugas_tambahan'] ?> (+<?= $jamStruktural ?> JP)
                                                </span>
                                            <?php endif; ?>

                                            <?php 
                                                $isWaliKelas = ($p['jam_ekuivalensi'] > $jamStruktural);
                                                if($isWaliKelas):
                                            ?>
                                                <span class="badge bg-warning text-dark border border-warning mt-1" style="width: fit-content; font-size: 9px; background-color: #fff3cd;">
                                                    <i class="fas fa-chalkboard-teacher me-1"></i> Wali Kelas (+2 JP)
                                                </span>
                                            <?php endif; ?>

                                            <div class="d-flex align-items-center mt-2 mb-1">
                                                <span class="small text-muted font-monospace me-2"><?= $p['nip'] ?></span>
                                                <button type="button" class="btn btn-link p-0 text-secondary btn-copy position-relative" 
                                                        data-clipboard-text="<?= $p['nip'] ?>" 
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top" 
                                                        title="Salin NIP"
                                                        style="text-decoration: none;">
                                                    <i class="far fa-copy fa-xs"></i>
                                                </button>
                                            </div>

                                            <div>
                                                <span class="badge rounded-pill <?= $badgeClass ?> me-1 shadow-sm" style="font-size: 10px; font-weight: 500;">
                                                    <i class="fas <?= $iconStatus ?> me-1"></i> Total: <?= $total ?> JP
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <span class="fw-bold text-primary" style="font-size: 0.95rem;"><?= $p['nama_mapel'] ?></span>
                                        <br>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border mt-1 font-monospace"><?= $p['kode_mapel'] ?></span>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-white text-dark border px-3 py-2 shadow-sm"><?= $p['nama_kelas'] ?></span>
                                    </td>

                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center justify-content-center bg-light border rounded-pill px-3 py-1">
                                            <span class="fw-bold fs-6 text-dark"><?= $p['jumlah_jam'] ?> JP</span>
                                        </div>
                                    </td>
                                    
                                    <td class="text-center">
                                        <?php if(!$is_locked): ?>
                                            <button type="button" class="btn btn-sm btn-light text-primary border rounded-circle shadow-sm btn-edit" 
                                                    style="width: 34px; height: 34px;"
                                                    data-id="<?= $p['id_pengampu'] ?>" 
                                                    data-guru="<?= $p['id_guru'] ?>" 
                                                    data-mapel="<?= $p['id_mapel'] ?>" 
                                                    data-kelas="<?= $p['id_kelas'] ?>" 
                                                    data-jam="<?= $p['jumlah_jam'] ?>" 
                                                    data-bs-toggle="modal" data-bs-target="#modalEdit"
                                                    title="Edit Data">
                                                <i class="fas fa-pen fa-xs"></i>
                                            </button>
                                        <?php else: ?>
                                            <i class="fas fa-lock text-muted opacity-25"></i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <div class="mb-3 opacity-25"><i class="fas fa-tasks fa-4x"></i></div>
                                        <h5 class="fw-bold">Belum ada beban mengajar</h5>
                                        <p class="small mb-0">Klik tombol <strong>Tambah Beban Mengajar</strong> untuk memploting guru ke kelas.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            
                            <tr id="noDataRow" style="display: none;">
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="mb-3 opacity-25"><i class="fas fa-search fa-3x"></i></div>
                                    <h6 class="fw-bold">Data tidak ditemukan</h6>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSalin" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-copy me-2"></i> Salin Data Beban Mengajar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('pengampu/salinData') ?>" method="post" id="formSalinData">
                <div class="modal-body p-4">
                    
                    <div class="alert alert-info border-0 shadow-sm rounded-3 mb-4">
                        <i class="fas fa-info-circle me-2"></i> Data akan disalin ke Periode Aktif: 
                        <br><strong class="ms-4"><?= $tahun_aktif ?> (<?= $sem_aktif ?>)</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Pilih Tahun Sumber</label>
                        <select name="sumber_tahun" id="sumber_tahun" class="form-select bg-light border-primary fw-bold">
                            <?php foreach($list_sumber_tahun as $row): ?>
                                <option value="<?= $row['tahun_ajaran'] ?>" <?= ($row['tahun_ajaran'] == $tahun_aktif) ? 'selected' : '' ?>>
                                    <?= $row['tahun_ajaran'] ?> 
                                    <?= ($row['tahun_ajaran'] == $tahun_aktif) ? '(Tahun Ini)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted" style="font-size: 10px;">*Menampilkan tahun yang terdata di sistem.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Pilih Semester Sumber</label>
                        <select name="sumber_semester" id="sumber_semester" class="form-select bg-light border-primary fw-bold">
                            <option value="Ganjil" <?= ($sem_aktif == 'Genap') ? 'selected' : '' ?>>Ganjil</option>
                            <option value="Genap" <?= ($sem_aktif == 'Ganjil') ? 'selected' : '' ?>>Genap</option>
                        </select>
                    </div>

                    <div id="salinWarning" class="alert alert-danger d-none animate__animated animate__fadeIn">
                        <i class="fas fa-exclamation-triangle me-2"></i> Sumber dan Target tidak boleh sama persis!
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" id="btnSubmitSalin">
                        <i class="fas fa-check me-1"></i> Proses Salin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus me-2"></i> Tambah Beban Mengajar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('pengampu/store') ?>" method="post">
                <div class="modal-body p-4">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Guru Pengampu</label>
                        <select name="id_guru" id="tambah_guru" class="form-select select2-guru border-primary bg-light fw-bold" required style="width: 100%;">
                            <option value="">-- Cari Nama Guru --</option>
                            <?php foreach($data_guru as $g): ?>
                                <option value="<?= $g['id_guru'] ?>"><?= $g['nama_guru'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <div id="tambah_smartInfo" class="mt-2 p-2 bg-info bg-opacity-10 border border-info rounded small" style="display:none;">
                            <div class="d-flex align-items-center text-primary">
                                <i class="fas fa-robot me-2"></i> 
                                <span id="tambah_bebanText" class="fw-bold">Beban: - JP</span>
                            </div>
                            <div id="tambah_mapelSaran" class="text-muted mt-1 fst-italic ms-4"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Mata Pelajaran</label>
                            <select name="id_mapel" id="tambah_mapel" class="form-select select2-mapel mapel-select" required style="width: 100%;">
                                <option value="" data-max="4">-- Cari Mapel --</option>
                                <?php foreach($data_mapel as $m): ?>
                                    <option value="<?= $m['id_mapel'] ?>" data-max="<?= $m['max_jam_per_minggu'] ?>">
                                        <?= $m['nama_mapel'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Jumlah Jam (JP)</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" onclick="adjustJam('tambah', -1)"><i class="fas fa-minus"></i></button>
                                <input type="number" name="jumlah_jam" id="tambah_jam" class="form-control text-center fw-bold fs-5" value="2" min="1" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="adjustJam('tambah', 1)" id="tambah_btnPlus"><i class="fas fa-plus"></i></button>
                            </div>
                            <small class="fw-bold text-danger mt-1 d-block" id="tambah_maxInfo" style="display:none;">Max: 4 JP</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-secondary small text-uppercase m-0">Pilih Kelas (Klik untuk memilih)</label>
                            <div>
                                <button type="button" class="btn btn-xs btn-link text-decoration-none p-0 me-2 small" onclick="selectAllClasses(true)">Pilih Semua</button>
                                <button type="button" class="btn btn-xs btn-link text-decoration-none p-0 text-danger small" onclick="selectAllClasses(false)">Reset</button>
                            </div>
                        </div>
                        
                        <div class="class-matrix-container">
                            <?php foreach($data_kelas as $k): ?>
                                <input type="checkbox" class="btn-check class-check" id="cls_<?= $k['id_kelas'] ?>" name="id_kelas[]" value="<?= $k['id_kelas'] ?>">
                                <label class="btn btn-outline-secondary btn-sm class-item-label" for="cls_<?= $k['id_kelas'] ?>">
                                    <?= $k['nama_kelas'] ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        
                        <div id="tambah_kelasWarning" class="text-danger small mt-2 fw-bold animate__animated animate__shakeX" style="display:none;">
                            <i class="fas fa-exclamation-triangle"></i> Guru ini sudah ada di salah satu kelas terpilih!
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" id="tambah_btnSubmit">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Edit Beban Mengajar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('pengampu/update') ?>" method="post">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_pengampu" id="edit_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Guru Pengampu</label>
                        <select name="id_guru" id="edit_guru" class="form-select select2-guru border-primary bg-light fw-bold" required style="width: 100%;">
                            <option value="">-- Cari Nama Guru --</option>
                            <?php foreach($data_guru as $g): ?>
                                <option value="<?= $g['id_guru'] ?>"><?= $g['nama_guru'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <div id="edit_smartInfo" class="mt-2 p-2 bg-info bg-opacity-10 border border-info rounded small" style="display:none;">
                            <div class="d-flex align-items-center text-primary">
                                <i class="fas fa-robot me-2"></i> 
                                <span id="edit_bebanText" class="fw-bold">Beban: - JP</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Mata Pelajaran</label>
                            <select name="id_mapel" id="edit_mapel" class="form-select select2-mapel mapel-select" required style="width: 100%;">
                                <option value="" data-max="4">-- Cari Mapel --</option>
                                <?php foreach($data_mapel as $m): ?>
                                    <option value="<?= $m['id_mapel'] ?>" data-max="<?= $m['max_jam_per_minggu'] ?>">
                                        <?= $m['nama_mapel'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Kelas (Edit 1 Data)</label>
                            <select name="id_kelas" id="edit_kelas" class="form-select select2-kelas" required style="width: 100%;">
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach($data_kelas as $k): ?>
                                    <option value="<?= $k['id_kelas'] ?>"><?= $k['nama_kelas'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Jumlah Jam (JP)</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" onclick="adjustJam('edit', -1)"><i class="fas fa-minus"></i></button>
                            <input type="number" name="jumlah_jam" id="edit_jam" class="form-control text-center fw-bold fs-5" value="2" min="1" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="adjustJam('edit', 1)" id="edit_btnPlus"><i class="fas fa-plus"></i></button>
                        </div>
                        <small class="fw-bold text-danger mt-1 d-block" id="edit_maxInfo" style="display:none;">Max: 4 JP</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" id="edit_btnSubmit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // 1. INIT SELECT2, RESET LOGIC, TOOLTIPS
    $(document).ready(function() {
        // Init Bootstrap Tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Fungsi Init Select2
        function initSelect2(parentId) {
            $('.select2-guru, .select2-mapel').select2({
                theme: 'bootstrap-5',
                dropdownParent: $(parentId),
                placeholder: "Ketik untuk mencari...",
                width: '100%',
                allowClear: true
            });
            $('.select2-kelas').select2({
                theme: 'bootstrap-5',
                dropdownParent: $(parentId),
                placeholder: "Pilih Kelas",
                width: '100%'
            });
        }

        // Init saat Modal Dibuka
        $('#modalTambah').on('shown.bs.modal', function () { initSelect2('#modalTambah'); });
        $('#modalEdit').on('shown.bs.modal', function () { initSelect2('#modalEdit'); });

        // === RESET FORM SAAT MODAL DITUTUP ===
        $('#modalTambah').on('hidden.bs.modal', function () {
            const form = this.querySelector('form');
            form.reset(); // Reset input native
            $('#tambah_guru').val('').trigger('change');
            $('#tambah_mapel').val('').trigger('change');
            document.querySelectorAll('.class-check').forEach(el => el.checked = false);
            $('#tambah_smartInfo').hide();
            $('#tambah_kelasWarning').hide();
            $('#tambah_maxInfo').hide();
            $('#tambah_jam').val(2);
            $('#tambah_btnPlus').prop('disabled', false);
            $('#tambah_btnSubmit').prop('disabled', false);
        });

        $('#modalEdit').on('hidden.bs.modal', function () {
            const form = this.querySelector('form');
            form.reset();
            $('#edit_guru').val('').trigger('change');
            $('#edit_mapel').val('').trigger('change');
            $('#edit_kelas').val('').trigger('change');
            $('#edit_smartInfo').hide();
            $('#edit_maxInfo').hide();
        });
    });

    // 2. KNOWLEDGE BASE (UNTUK FITUR PINTAR)
    const teacherKnowledge = {}; 

    function scanTableData() {
        document.querySelectorAll('.btn-edit').forEach(btn => {
            const idGuru = btn.getAttribute('data-guru');
            const idMapel = btn.getAttribute('data-mapel');
            const idKelas = btn.getAttribute('data-kelas');
            const jam = parseInt(btn.getAttribute('data-jam'));

            if (!teacherKnowledge[idGuru]) {
                teacherKnowledge[idGuru] = { totalJam: 0, mapelFreq: {}, classes: [] };
            }
            teacherKnowledge[idGuru].totalJam += jam;
            teacherKnowledge[idGuru].classes.push(idKelas);
            if (!teacherKnowledge[idGuru].mapelFreq[idMapel]) {
                teacherKnowledge[idGuru].mapelFreq[idMapel] = 0;
            }
            teacherKnowledge[idGuru].mapelFreq[idMapel]++;
        });
    }
    scanTableData();

    // 3. LOGIKA MODAL PINTAR (GURU CHANGE)
    $('.guru-select').on('change', function() {
        const mode = this.id.split('_')[0]; 
        const guruId = $(this).val();
        const infoBox = document.getElementById(mode + '_smartInfo');
        const textBeban = document.getElementById(mode + '_bebanText');
        const textSaran = document.getElementById(mode + '_mapelSaran');
        const mapelSelect = $('#' + mode + '_mapel');

        if (guruId && teacherKnowledge[guruId]) {
            const data = teacherKnowledge[guruId];
            infoBox.style.display = 'block';
            textBeban.innerText = `Total Beban Saat Ini: ${data.totalJam} JP`;
            
            // Auto-Select Mapel (Hanya di mode Tambah)
            let topMapel = null;
            let maxCount = -1;
            for (const [mId, count] of Object.entries(data.mapelFreq)) {
                if (count > maxCount) { maxCount = count; topMapel = mId; }
            }
            
            if (topMapel && mode === 'tambah') {
                mapelSelect.val(topMapel).trigger('change');
                if(textSaran) textSaran.innerHTML = `<i class="fas fa-magic text-warning me-1"></i> Auto-select mapel yg sering diajar.`;
            } else {
                if(textSaran) textSaran.innerHTML = '';
            }
        } else {
            infoBox.style.display = 'block';
            textBeban.innerText = "Belum ada beban mengajar (0 JP)";
            if(textSaran) textSaran.innerHTML = '<i class="fas fa-info-circle me-1"></i> Guru ini belum memiliki kelas.';
            if(mode === 'tambah') mapelSelect.val("").trigger('change');
        }
        
        if(mode === 'tambah') checkMatrixCollision();
    });

    // 4. LOGIKA MATRIX CLASS
    function selectAllClasses(check) {
        document.querySelectorAll('.class-check').forEach(el => {
            el.checked = check;
        });
        checkMatrixCollision();
    }

    document.querySelectorAll('.class-check').forEach(el => {
        el.addEventListener('change', checkMatrixCollision);
    });

    function checkMatrixCollision() {
        const guruId = $('#tambah_guru').val();
        const warning = document.getElementById('tambah_kelasWarning');
        const btn = document.getElementById('tambah_btnSubmit');
        let hasCollision = false;

        if (guruId && teacherKnowledge[guruId]) {
            document.querySelectorAll('.class-check:checked').forEach(el => {
                if (teacherKnowledge[guruId].classes.includes(el.value)) {
                    hasCollision = true;
                }
            });
        }

        if (hasCollision) {
            warning.style.display = 'block';
            btn.disabled = true;
        } else {
            warning.style.display = 'none';
            btn.disabled = false;
        }
    }

    // 5. ADJUST JAM LOGIC
    function adjustJam(mode, val) {
        const input = document.getElementById(mode + '_jam');
        const selectedData = $('#' + mode + '_mapel').select2('data');
        const maxJam = (selectedData && selectedData.length > 0) ? parseInt(selectedData[0].element.getAttribute('data-max')) : 4;

        let current = parseInt(input.value) || 0;
        let next = current + val;

        if(next < 1) next = 1;
        if(next > maxJam) {
            input.classList.add('is-invalid');
            setTimeout(() => input.classList.remove('is-invalid'), 300);
            return;
        }
        input.value = next;
        checkMaxLimit(mode, next, maxJam);
    }

    function checkMaxLimit(mode, val, max) {
        const btnPlus = document.getElementById(mode + '_btnPlus');
        const info = document.getElementById(mode + '_maxInfo');
        info.style.display = 'block';
        info.innerText = `Batas Kurikulum: ${max} JP`;
        if (val >= max) {
            btnPlus.disabled = true;
            info.classList.add('text-danger');
        } else {
            btnPlus.disabled = false;
            info.classList.remove('text-danger');
            info.classList.add('text-success');
        }
    }

    $('.mapel-select').on('change', function() {
        const mode = this.id.split('_')[0];
        const inputJam = document.getElementById(mode + '_jam');
        inputJam.value = 2; // Reset ke default
        const selectedData = $(this).select2('data');
        const max = (selectedData && selectedData.length > 0) ? parseInt(selectedData[0].element.getAttribute('data-max')) : 4;
        checkMaxLimit(mode, 2, max);
    });

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.getAttribute('data-id');
            document.getElementById('edit_jam').value = this.getAttribute('data-jam');
            $('#edit_guru').val(this.getAttribute('data-guru')).trigger('change');
            $('#edit_mapel').val(this.getAttribute('data-mapel')).trigger('change');
            $('#edit_kelas').val(this.getAttribute('data-kelas')).trigger('change');
        });
    });

    document.querySelectorAll('.btn-quick-add').forEach(btn => {
        btn.addEventListener('click', function() {
            const idGuru = this.getAttribute('data-id');
            const modal = new bootstrap.Modal(document.getElementById('modalTambah'));
            modal.show();
            setTimeout(() => {
                $('#tambah_guru').val(idGuru).trigger('change');
            }, 500);
        });
    });

    // 6. COPY CLIPBOARD DENGAN EFEK VISUAL (PERFECT)
    $('.btn-copy').on('click', function() {
        const btn = $(this);
        const text = btn.attr('data-clipboard-text');
        const icon = btn.find('i');
        const originalIcon = "far fa-copy"; 

        navigator.clipboard.writeText(text).then(() => {
            // Ubah Icon ke Centang Hijau + Animasi Bounce
            icon.removeClass('far fa-copy').addClass('fas fa-check text-success animate__animated animate__bounceIn');
            
            // Ubah Tooltip
            const tooltip = bootstrap.Tooltip.getInstance(btn);
            if(tooltip) {
                btn.attr('data-bs-original-title', 'Tersalin!');
                tooltip.show();
            }

            // Kembalikan setelah 1.5 detik
            setTimeout(() => {
                icon.removeClass('fas fa-check text-success animate__animated animate__bounceIn').addClass(originalIcon);
                if(tooltip) {
                    btn.attr('data-bs-original-title', 'Salin NIP');
                    tooltip.hide();
                }
            }, 1500);
        });
    });

    // 7. UTILS
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#tabelPengampu tbody tr.data-row');
        let hasData = false;
        rows.forEach(row => {
            let txt = row.textContent.toLowerCase();
            if(txt.includes(filter)) { row.style.display = ''; hasData = true; } else { row.style.display = 'none'; }
        });
        document.getElementById('noDataRow').style.display = hasData ? 'none' : 'table-row';
    });

    const checkAll = document.querySelector('.check-all');
    if(checkAll) {
        checkAll.addEventListener('change', function() {
            document.querySelectorAll('.check-item').forEach(c => c.checked = this.checked);
            updateFloating();
        });
    }
    document.querySelectorAll('.check-item').forEach(c => c.addEventListener('change', updateFloating));
    
    function updateFloating() {
        const count = document.querySelectorAll('.check-item:checked').length;
        const floatBtn = document.getElementById('floatingAction');
        if(count > 0) {
            floatBtn.style.display = 'block';
            document.getElementById('countSelected').innerText = count;
        } else {
            floatBtn.style.display = 'none';
        }
    }
    document.getElementById('btnDeleteSelected').addEventListener('click', function() {
        Swal.fire({
            title: 'Hapus data terpilih?', text: "Data tidak bisa dikembalikan!", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!'
        }).then((r) => { if(r.isConfirmed) document.getElementById('formDeleteMultiple').submit(); });
    });

    // 8. LOGIKA VALIDASI SALIN DATA
    const activeTahun = "<?= $tahun_aktif ?>"; 
    const activeSem   = "<?= $sem_aktif ?>";   
    
    function checkSalinValid() {
        const selTahun = $('#sumber_tahun').val();
        const selSem   = $('#sumber_semester').val();
        const btnSalin = $('#btnSubmitSalin');
        const warning  = $('#salinWarning');

        // VALIDASI: Hanya blokir jika TAHUN SAMA **DAN** SEMESTER SAMA
        if(selTahun === activeTahun && selSem === activeSem) {
            btnSalin.prop('disabled', true);
            warning.removeClass('d-none'); 
        } else {
            btnSalin.prop('disabled', false); 
            warning.addClass('d-none');
        }
    }

    // Jalankan pengecekan event
    $('#sumber_tahun, #sumber_semester').on('change', checkSalinValid);
    $('#modalSalin').on('shown.bs.modal', checkSalinValid);

    // SweetAlert Konfirmasi Salin
    $('#btnSubmitSalin').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Konfirmasi Salin?',
            text: "Data beban mengajar dari periode terpilih akan DIDUPLIKASI ke periode aktif saat ini.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Salin Sekarang!',
            confirmButtonColor: '#0d6efd'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formSalinData').submit();
            }
        });
    });

    <?php if(session()->getFlashdata('success')): ?>
        Swal.fire({icon: 'success', title: 'Berhasil', text: '<?= session()->getFlashdata('success') ?>', timer: 2000, showConfirmButton: false});
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        Swal.fire({icon: 'error', title: 'Gagal', html: '<?= session()->getFlashdata('error') ?>'});
    <?php endif; ?>
</script>

<?= $this->endSection(); ?>