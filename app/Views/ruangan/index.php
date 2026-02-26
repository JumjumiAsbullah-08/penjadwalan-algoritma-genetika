<?= $this->extend('layout/template'); ?>

<?= $this->section('konten'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid">

    <div id="existingData" data-names='<?= json_encode(array_column($daftar_ruangan, 'nama_ruangan')) ?>'></div>

    <?php if($is_locked): ?>
        <div class="alert alert-warning d-flex align-items-center mb-4 shadow-sm border-0 rounded-3 fade show">
            <div class="bg-warning bg-opacity-25 p-3 rounded-circle me-3 text-warning">
                <i class="fas fa-lock fa-lg"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1">Mode Terkunci</h6>
                <span class="small text-muted">Data Ruangan tidak dapat diubah karena <b>Jadwal Pelajaran sudah digenerate</b>.</span>
            </div>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 overflow-hidden rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
            <div>
                <h5 class="mb-0 text-success fw-bold"><i class="fas fa-door-open me-2"></i> Data Ruangan</h5>
                <p class="text-muted small mb-0">Manajemen fasilitas dan tempat belajar.</p>
            </div>
            
            <?php if(!$is_locked): ?>
                <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus me-2"></i> Tambah Ruangan
                </button>
            <?php else: ?>
                <button class="btn btn-secondary rounded-pill px-4" disabled title="Menu terkunci"><i class="fas fa-lock me-2"></i> Terkunci</button>
            <?php endif; ?>
        </div>
        
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-5 ms-auto">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 ps-3 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-2 py-2" placeholder="Cari Ruangan..." autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelRuangan">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th width="5%" class="text-center py-3">No</th>
                            <th class="py-3">Nama Ruangan</th>
                            <th class="py-3">Jenis / Fungsi</th>
                            <th class="py-3">Kapasitas</th>
                            <th width="15%" class="text-center py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($daftar_ruangan)): ?>
                            <?php $no=1; foreach($daftar_ruangan as $r): ?>
                            <tr class="data-row">
                                <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                
                                <td><span class="fw-bold text-dark fs-5 font-monospace"><?= $r['nama_ruangan'] ?></span></td>
                                
                                <td>
                                    <?php if($r['jenis'] == 'Laboratorium'): ?>
                                        <span class="badge bg-warning text-dark bg-opacity-75 shadow-sm"><i class="fas fa-flask me-1"></i> Lab IPA</span>
                                    <?php elseif($r['jenis'] == 'Lab Komputer'): ?>
                                        <span class="badge bg-info text-dark bg-opacity-75 shadow-sm"><i class="fas fa-desktop me-1"></i> Lab Komputer</span>
                                    <?php elseif($r['jenis'] == 'Lapangan'): ?>
                                        <span class="badge bg-success bg-opacity-75 shadow-sm"><i class="fas fa-futbol me-1"></i> Lapangan</span>
                                    <?php elseif($r['jenis'] == 'Perpustakaan'): ?>
                                        <span class="badge bg-danger bg-opacity-75 shadow-sm"><i class="fas fa-book-reader me-1"></i> Perpustakaan</span>
                                    <?php elseif($r['jenis'] == 'Aula'): ?>
                                        <span class="badge bg-secondary shadow-sm"><i class="fas fa-warehouse me-1"></i> Aula</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary bg-opacity-75 shadow-sm"><i class="fas fa-chair me-1"></i> Kelas Teori</span>
                                    <?php endif; ?>
                                </td>

                                <td><span class="badge bg-light text-dark border px-3"><i class="fas fa-users me-2 text-info"></i> <?= $r['kapasitas'] ?> Kursi</span></td>
                                
                                <td class="text-center">
                                    <div class="btn-group shadow-sm rounded" role="group">
                                        <?php if($is_locked): ?>
                                            <button class="btn btn-sm btn-light text-muted border" disabled><i class="fas fa-pen"></i></button>
                                            <button class="btn btn-sm btn-light text-muted border" disabled><i class="fas fa-trash-alt"></i></button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-light text-primary border btn-edit" 
                                                    data-id="<?= $r['id_ruangan'] ?>"
                                                    data-nama="<?= $r['nama_ruangan'] ?>"
                                                    data-jenis="<?= $r['jenis'] ?>"
                                                    data-kapasitas="<?= $r['kapasitas'] ?>"
                                                    data-bs-toggle="modal" data-bs-target="#modalEdit" title="Edit Ruangan">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <a href="<?= base_url('ruangan/delete/'.$r['id_ruangan']) ?>" class="btn btn-sm btn-light text-danger border btn-hapus" title="Hapus Ruangan">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="mb-3 opacity-25"><i class="fas fa-door-open fa-4x"></i></div>
                                    <h5 class="fw-bold">Belum ada data ruangan</h5>
                                    <p class="small mb-0">Klik tombol <strong>Tambah Ruangan</strong> untuk mengisi lokasi belajar.</p>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <tr id="noDataRow" style="display: none;">
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="mb-3 opacity-50"><i class="fas fa-search fa-3x"></i></div>
                                <h5 class="fw-bold">Data tidak ditemukan</h5>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> Tambah Ruangan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('ruangan/store') ?>" method="post" id="formTambah">
                <div class="modal-body p-4">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Nama Ruangan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-door-closed text-muted"></i></span>
                            <input type="text" name="nama_ruangan" id="inputNama" class="form-control fw-bold fs-5 text-uppercase" placeholder="Cth: R.101 atau LAB BIO" required autocomplete="off">
                        </div>
                        <div id="feedbackNama" class="invalid-feedback" style="display:none"></div>
                        <small class="text-muted fst-italic">Ketik "Lab", "Lapangan", "Perpus" atau "Aula", jenis akan otomatis terpilih.</small>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Jenis</label>
                            <select name="jenis" id="inputJenis" class="form-select bg-light">
                                <option value="Teori">Teori (Kelas Biasa)</option>
                                <option value="Lab Komputer">Lab Komputer</option>
                                <option value="Laboratorium">Lab IPA (Fis/Kim/Bio)</option>
                                <option value="Lapangan">Lapangan Olahraga</option>
                                <option value="Perpustakaan">Perpustakaan</option>
                                <option value="Aula">Aula Serbaguna</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Kapasitas</label>
                            <input type="number" name="kapasitas" id="inputKapasitas" class="form-control" value="32" required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small text-muted">Quick Set Kapasitas:</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-cap-add" data-val="20">20</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-cap-add" data-val="30">30</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-cap-add" data-val="32">32</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-cap-add" data-val="36">36</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-cap-add" data-val="40">40</button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" id="btnSimpan">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Edit Ruangan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('ruangan/update') ?>" method="post">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_ruangan" id="edit_id">

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Nama Ruangan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-door-open text-muted"></i></span>
                            <input type="text" name="nama_ruangan" id="edit_nama" class="form-control fw-bold fs-5 text-uppercase" required autocomplete="off">
                        </div>
                        <div id="editFeedbackNama" class="invalid-feedback" style="display:none"></div>
                        <small class="text-muted fst-italic">Edit nama untuk auto-detect jenis ruangan.</small>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Jenis</label>
                            <select name="jenis" id="edit_jenis" class="form-select bg-light">
                                <option value="Teori">Teori (Kelas Biasa)</option>
                                <option value="Lab Komputer">Lab Komputer</option>
                                <option value="Laboratorium">Lab IPA (Fis/Kim/Bio)</option>
                                <option value="Lapangan">Lapangan Olahraga</option>
                                <option value="Perpustakaan">Perpustakaan</option>
                                <option value="Aula">Aula Serbaguna</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Kapasitas</label>
                            <input type="number" name="kapasitas" id="edit_kapasitas" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small text-muted">Quick Set Kapasitas:</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-cap-edit" data-val="20">20</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-cap-edit" data-val="30">30</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-cap-edit" data-val="32">32</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-cap-edit" data-val="36">36</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-cap-edit" data-val="40">40</button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" id="btnUpdate">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const existingNames = JSON.parse(document.getElementById('existingData').getAttribute('data-names')).map(n => n.toLowerCase());

    // --- REUSABLE FUNCTIONS (LOGIC PINTAR - UPDATED) ---
    // 1. Logic Auto Detect Jenis
    function detectJenis(nameInput, typeSelect) {
        let val = nameInput.value;
        if(val.match(/LAB.*KOM|KOMPUTER|TIK/i)) typeSelect.value = 'Lab Komputer'; // Prioritas Lab Kom
        else if(val.match(/LAB|IPA|BIO|FIS|KIM/i)) typeSelect.value = 'Laboratorium';
        else if(val.match(/LAP|BASKET|VOLI|FUTSAL|UPACARA/i)) typeSelect.value = 'Lapangan';
        else if(val.match(/PERPUS|BACA/i)) typeSelect.value = 'Perpustakaan';
        else if(val.match(/AULA|GSG/i)) typeSelect.value = 'Aula';
        else typeSelect.value = 'Teori';
    }

    // 2. Logic Validasi Duplikat
    function checkDuplicate(nameInput, feedbackEl, btnEl, origin = '') {
        let clean = nameInput.value.toLowerCase().trim();
        if(existingNames.includes(clean) && clean !== origin.toLowerCase()) {
            nameInput.classList.add('is-invalid');
            feedbackEl.style.display = 'block';
            feedbackEl.innerHTML = 'Nama Ruangan sudah ada!';
            btnEl.disabled = true;
        } else {
            nameInput.classList.remove('is-invalid');
            feedbackEl.style.display = 'none';
            btnEl.disabled = false;
        }
    }

    // --- LOGIKA MODAL TAMBAH ---
    const inputNama = document.getElementById('inputNama');
    const inputJenis = document.getElementById('inputJenis');
    const inputKapasitas = document.getElementById('inputKapasitas');
    const btnSimpan = document.getElementById('btnSimpan');
    const feedbackNama = document.getElementById('feedbackNama');

    inputNama.addEventListener('input', function() {
        detectJenis(this, inputJenis);
        checkDuplicate(this, feedbackNama, btnSimpan);
    });

    document.querySelectorAll('.btn-cap-add').forEach(btn => {
        btn.addEventListener('click', function() { inputKapasitas.value = this.getAttribute('data-val'); });
    });

    // --- LOGIKA MODAL EDIT ---
    const editNama = document.getElementById('edit_nama');
    const editJenis = document.getElementById('edit_jenis');
    const editKapasitas = document.getElementById('edit_kapasitas');
    const btnUpdate = document.getElementById('btnUpdate');
    const editFeedbackNama = document.getElementById('editFeedbackNama');
    let originNama = '';

    // Init Modal Edit
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.getAttribute('data-id');
            
            let nama = this.getAttribute('data-nama');
            editNama.value = nama;
            originNama = nama; // Simpan nama asli
            
            editJenis.value = this.getAttribute('data-jenis');
            editKapasitas.value = this.getAttribute('data-kapasitas');

            // Reset Error
            editNama.classList.remove('is-invalid');
            editFeedbackNama.style.display = 'none';
            btnUpdate.disabled = false;
        });
    });

    // Edit Input Listener
    editNama.addEventListener('input', function() {
        detectJenis(this, editJenis); // Fitur pintar juga jalan di edit
        checkDuplicate(this, editFeedbackNama, btnUpdate, originNama);
    });

    // Quick Cap Edit
    document.querySelectorAll('.btn-cap-edit').forEach(btn => {
        btn.addEventListener('click', function() { editKapasitas.value = this.getAttribute('data-val'); });
    });

    // --- COMMON UTILS ---
    document.getElementById('modalTambah').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formTambah').reset();
        inputNama.classList.remove('is-invalid');
        feedbackNama.style.display = 'none';
        btnSimpan.disabled = false;
    });

    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#tabelRuangan tbody tr.data-row');
        let hasData = false;
        rows.forEach(row => {
            let txt = row.textContent.toLowerCase();
            if(txt.includes(filter)) { row.style.display = ''; hasData = true; } else { row.style.display = 'none'; }
        });
        document.getElementById('noDataRow').style.display = hasData ? 'none' : 'table-row';
    });

    document.querySelectorAll('.btn-hapus').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            Swal.fire({title: 'Hapus Ruangan?', text: "Data tidak bisa dikembalikan!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!'}).then((r) => { if (r.isConfirmed) window.location.href = href; });
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