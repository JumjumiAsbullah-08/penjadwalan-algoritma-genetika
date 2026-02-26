<?= $this->extend('layout/template'); ?>

<?= $this->section('konten'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid">

    <div id="existingData" data-names='<?= json_encode(array_column($daftar_kelas, 'nama_kelas')) ?>'></div>

    <?php if($is_locked): ?>
        <div class="alert alert-warning d-flex align-items-center mb-4 shadow-sm border-0 rounded-3">
            <div class="bg-warning bg-opacity-25 p-3 rounded-circle me-3 text-warning"><i class="fas fa-lock fa-lg"></i></div>
            <div>
                <h6 class="fw-bold mb-1">Mode Terkunci</h6>
                <span class="small text-muted">Data Kelas tidak dapat diubah karena <b>Jadwal Pelajaran sudah digenerate</b>.</span>
            </div>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 overflow-hidden rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
            <div>
                <h5 class="mb-0 text-success fw-bold"><i class="fas fa-users me-2"></i> Data Kelas & Wali</h5>
                <p class="text-muted small mb-0">Manajemen rombongan belajar dan homebase.</p>
            </div>
            
            <?php if(!$is_locked): ?>
                <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus me-2"></i> Tambah Kelas
                </button>
            <?php else: ?>
                <button class="btn btn-secondary rounded-pill px-4" disabled><i class="fas fa-lock me-2"></i> Terkunci</button>
            <?php endif; ?>
        </div>
        
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-5 ms-auto">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 ps-3 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-2 py-2" placeholder="Cari Kelas atau Wali..." autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelKelas">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th width="5%" class="text-center py-3">No</th>
                            <th width="20%" class="py-3">Nama Kelas</th>
                            <th class="py-3">Wali Kelas</th>
                            <th width="20%" class="py-3">Homebase (Ruang)</th>
                            <th width="15%" class="text-center py-3">Status / Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($daftar_kelas)): ?>
                            <?php $no=1; foreach($daftar_kelas as $k): ?>
                            <tr class="data-row">
                                <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light border rounded px-3 py-2 me-3 shadow-sm" style="min-width: 90px; text-align:center;">
                                            <span class="fw-bold text-dark fs-6 font-monospace"><?= $k['nama_kelas'] ?></span>
                                        </div>
                                        <div>
                                            <?php 
                                                $kls = $k['nama_kelas'];
                                                if(strpos($kls, 'IPA') !== false) echo '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary" style="font-size: 10px;">MIPA</span>';
                                                else if(strpos($kls, 'IPS') !== false) echo '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning" style="font-size: 10px;">IPS</span>';
                                                else if(strpos($kls, 'AGAMA') !== false) echo '<span class="badge bg-success bg-opacity-10 text-success border border-success" style="font-size: 10px;">AGAMA</span>';
                                                else echo '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary" style="font-size: 10px;">UMUM</span>';
                                            ?>
                                            <div class="small text-muted mt-1"><i class="fas fa-users me-1"></i> <?= $k['jumlah_siswa'] ?> Siswa</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if($k['nama_wali']): ?>
                                        <div class="d-flex align-items-center text-secondary">
                                            <i class="fas fa-user-tie me-2 text-success"></i> <span class="fw-bold"><?= $k['nama_wali'] ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border border-dashed py-2 px-3"><i class="fas fa-exclamation-circle me-1"></i> Belum diset</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($k['nama_homebase']): ?>
                                        <div class="d-flex align-items-center text-dark">
                                            <i class="fas fa-door-open me-2 text-primary"></i> 
                                            <span class="fw-bold"><?= $k['nama_homebase'] ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="fas fa-times me-1"></i> Belum Ada</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($is_locked): ?>
                                        <span class="badge bg-secondary py-2 px-3 shadow-sm"><i class="fas fa-lock me-1"></i> Locked</span>
                                    <?php else: ?>
                                        <div class="btn-group shadow-sm rounded" role="group">
                                            <button class="btn btn-sm btn-light text-primary border btn-edit" 
                                                    data-id="<?= $k['id_kelas'] ?>"
                                                    data-nama="<?= $k['nama_kelas'] ?>"
                                                    data-jumlah="<?= $k['jumlah_siswa'] ?>"
                                                    data-wali="<?= $k['id_guru_wali'] ?>" 
                                                    data-homebase="<?= $k['id_ruang_homebase'] ?>" 
                                                    data-terpakai="<?= $k['terpakai'] ?>"
                                                    data-bs-toggle="modal" data-bs-target="#modalEdit" title="Edit Kelas">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <?php if($k['terpakai'] > 0): ?>
                                                <button class="btn btn-sm btn-light text-muted border" disabled title="Kelas sedang aktif. Tidak bisa dihapus."><i class="fas fa-ban"></i></button>
                                            <?php else: ?>
                                                <a href="<?= base_url('kelas/delete/'.$k['id_kelas']) ?>" class="btn btn-sm btn-light text-danger border btn-hapus" title="Hapus Kelas"><i class="fas fa-trash-alt"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="mb-3 opacity-25"><i class="fas fa-school fa-4x"></i></div>
                                    <h5 class="fw-bold">Belum ada data kelas</h5>
                                    <p class="small mb-0">Klik tombol <strong>Tambah Kelas</strong> untuk memulai.</p>
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
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> Tambah Kelas Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('kelas/store') ?>" method="post" id="formTambah">
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded mb-3 border border-success border-opacity-25">
                        <label class="form-label fw-bold text-success small text-uppercase mb-2"><i class="fas fa-magic me-1"></i> Generator Nama Kelas</label>
                        <div class="row g-2">
                            <div class="col-4"><select class="form-select gen-input" id="genJenjang"><option value="X">X (10)</option><option value="XI">XI (11)</option><option value="XII">XII (12)</option></select></div>
                            <div class="col-4"><select class="form-select gen-input" id="genJurusan"><option value="IPA">IPA</option><option value="IPS">IPS</option><option value="AGAMA">AGAMA</option><option value="BHS">BAHASA</option></select></div>
                            <div class="col-4"><select class="form-select gen-input" id="genUrut"><?php for($i=1; $i<=10; $i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?></select></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Nama Kelas (Hasil)</label>
                        <input type="text" name="nama_kelas" id="inputNamaKelas" class="form-control fw-bold fs-5 text-center text-uppercase" required readonly>
                        <div id="feedbackNama" class="invalid-feedback" style="display:none"></div>
                        <div class="form-check mt-2"><input class="form-check-input" type="checkbox" id="checkManual"><label class="form-check-label small text-muted" for="checkManual">Input Manual</label></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Homebase (Ruang Kelas)</label>
                        <select name="id_ruang_homebase" class="form-select bg-light" required>
                            <option value="">-- Pilih Ruangan --</option>
                            <?php foreach($daftar_ruangan as $r): ?>
                                <option value="<?= $r['id_ruangan'] ?>"><?= $r['nama_ruangan'] ?> (Kapasitas: <?= $r['kapasitas'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted" style="font-size: 11px;">*Wajib diisi agar siswa tidak pindah-pindah.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Wali Kelas</label>
                            <select name="id_guru_wali" class="form-select"><option value="">-- Belum Ada --</option><?php foreach($daftar_guru as $g): ?><option value="<?= $g['id_guru'] ?>"><?= $g['nama_guru'] ?></option><?php endforeach; ?></select>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Jml Siswa</label>
                            <input type="number" name="jumlah_siswa" class="form-control" value="30" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" id="btnSimpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Edit Data Kelas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('kelas/update') ?>" method="post">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_kelas" id="edit_id_kelas">
                    
                    <div id="alertLocked" class="alert alert-warning border-0 d-flex align-items-center mb-3" style="display:none;">
                        <i class="fas fa-lock me-2 text-warning"></i>
                        <small class="text-dark">Nama & Jumlah Siswa <b>terkunci</b> karena sedang aktif.</small>
                    </div>

                    <div class="p-3 bg-light rounded mb-3 border border-primary border-opacity-25" id="blockGeneratorEdit">
                        <label class="form-label fw-bold text-primary small text-uppercase mb-2">Edit Nama Kelas</label>
                        <div class="row g-2">
                            <div class="col-4"><select class="form-select edit-gen" id="editGenJenjang"><option value="X">X (10)</option><option value="XI">XI (11)</option><option value="XII">XII (12)</option></select></div>
                            <div class="col-4"><select class="form-select edit-gen" id="editGenJurusan"><option value="IPA">IPA</option><option value="IPS">IPS</option><option value="AGAMA">AGAMA</option><option value="BHS">BAHASA</option></select></div>
                            <div class="col-4"><select class="form-select edit-gen" id="editGenUrut"><?php for($i=1; $i<=10; $i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?></select></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Nama Kelas</label>
                        <input type="text" name="nama_kelas" id="edit_nama" class="form-control fw-bold fs-5 text-center text-uppercase" required readonly>
                        <div id="editFeedbackNama" class="invalid-feedback" style="display:none"></div>
                        <div class="form-check mt-2" id="divManualEdit">
                            <input class="form-check-input" type="checkbox" id="editCheckManual">
                            <label class="form-check-label small text-muted" for="editCheckManual">Mode Manual</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Homebase (Ruang Kelas)</label>
                        <select name="id_ruang_homebase" id="edit_homebase" class="form-select bg-light" required>
                            <option value="">-- Pilih Ruangan --</option>
                            <?php foreach($daftar_ruangan as $r): ?>
                                <option value="<?= $r['id_ruangan'] ?>"><?= $r['nama_ruangan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Wali Kelas</label>
                            <select name="id_guru_wali" id="edit_wali" class="form-select border-primary">
                                <option value="">-- Belum Ada --</option>
                                <?php foreach($daftar_guru as $g): ?>
                                    <option value="<?= $g['id_guru'] ?>"><?= $g['nama_guru'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Jml Siswa</label>
                            <input type="number" name="jumlah_siswa" id="edit_jumlah" class="form-control" required>
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

    // --- LOGIKA GENERATOR TAMBAH ---
    const genInputs = document.querySelectorAll('.gen-input');
    const inputNama = document.getElementById('inputNamaKelas');
    const checkManual = document.getElementById('checkManual');
    const btnSimpan = document.getElementById('btnSimpan');
    const feedbackNama = document.getElementById('feedbackNama');

    function generateName() {
        if(checkManual.checked) return;
        inputNama.value = `${document.getElementById('genJenjang').value}-${document.getElementById('genJurusan').value}-${document.getElementById('genUrut').value}`;
        checkDuplicate(inputNama, feedbackNama, btnSimpan);
    }
    genInputs.forEach(i => i.addEventListener('change', generateName));
    checkManual.addEventListener('change', function() {
        inputNama.readOnly = !this.checked;
        if(this.checked) { inputNama.focus(); inputNama.classList.remove('text-center'); } 
        else { inputNama.classList.add('text-center'); generateName(); }
    });
    inputNama.addEventListener('input', () => checkDuplicate(inputNama, feedbackNama, btnSimpan));
    generateName();

    // --- LOGIKA GENERATOR EDIT ---
    const editGenInputs = document.querySelectorAll('.edit-gen');
    const editNama = document.getElementById('edit_nama');
    const editCheckManual = document.getElementById('editCheckManual');
    const btnUpdate = document.getElementById('btnUpdate');
    const editFeedbackNama = document.getElementById('editFeedbackNama');
    const alertLocked = document.getElementById('alertLocked');
    let originNama = '';

    function generateNameEdit() {
        if(editCheckManual.checked) return;
        editNama.value = `${document.getElementById('editGenJenjang').value}-${document.getElementById('editGenJurusan').value}-${document.getElementById('editGenUrut').value}`;
        checkDuplicate(editNama, editFeedbackNama, btnUpdate, originNama);
    }
    editGenInputs.forEach(i => i.addEventListener('change', generateNameEdit));
    editCheckManual.addEventListener('change', function() {
        editNama.readOnly = !this.checked;
        if(this.checked) { editNama.classList.remove('text-center'); editNama.focus(); }
        else { editNama.classList.add('text-center'); generateNameEdit(); }
    });
    editNama.addEventListener('input', () => checkDuplicate(editNama, editFeedbackNama, btnUpdate, originNama));

    // --- POPULATE EDIT MODAL ---
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            let id = this.getAttribute('data-id');
            let nama = this.getAttribute('data-nama');
            let jumlah = this.getAttribute('data-jumlah');
            let wali = this.getAttribute('data-wali');
            let homebase = this.getAttribute('data-homebase'); // NEW
            let terpakai = parseInt(this.getAttribute('data-terpakai'));

            document.getElementById('edit_id_kelas').value = id;
            document.getElementById('edit_jumlah').value = jumlah;
            document.getElementById('edit_wali').value = wali || "";
            document.getElementById('edit_homebase').value = homebase || ""; // FILL HOMEBASE
            
            editNama.value = nama;
            originNama = nama;
            editNama.classList.remove('is-invalid');
            editFeedbackNama.style.display = 'none';
            btnUpdate.disabled = false;

            // Auto-Detect Dropdown values
            let parts = nama.split('-');
            if(parts.length === 3 && ['X','XI','XII'].includes(parts[0])) {
                document.getElementById('editGenJenjang').value = parts[0];
                document.getElementById('editGenJurusan').value = parts[1];
                document.getElementById('editGenUrut').value = parts[2];
                editCheckManual.checked = false;
                editNama.readOnly = true;
                editNama.classList.add('text-center');
            } else {
                editCheckManual.checked = true;
                editNama.readOnly = false;
                editNama.classList.remove('text-center');
            }

            // KUNCI JIKA TERPAKAI (KECUALI HOMEBASE & WALI)
            if (terpakai > 0) {
                alertLocked.style.display = 'flex';
                editGenInputs.forEach(i => i.disabled = true);
                editCheckManual.disabled = true;
                editNama.readOnly = true;
                document.getElementById('edit_jumlah').readOnly = true;
                document.getElementById('blockGeneratorEdit').classList.add('opacity-50');
            } else {
                alertLocked.style.display = 'none';
                editGenInputs.forEach(i => i.disabled = false);
                editCheckManual.disabled = false;
                if(editCheckManual.checked) editNama.readOnly = false;
                document.getElementById('edit_jumlah').readOnly = false;
                document.getElementById('blockGeneratorEdit').classList.remove('opacity-50');
            }
        });
    });

    // --- REUSABLE VALIDATION ---
    function checkDuplicate(elInput, elFeedback, elBtn, origin = '') {
        let clean = elInput.value.toLowerCase().trim();
        if (existingNames.includes(clean) && clean !== origin.toLowerCase()) {
            elInput.classList.add('is-invalid');
            elFeedback.style.display = 'block';
            elFeedback.innerHTML = 'Nama Kelas sudah ada!';
            elBtn.disabled = true;
        } else {
            elInput.classList.remove('is-invalid');
            elFeedback.style.display = 'none';
            elBtn.disabled = false;
        }
    }

    // --- RESET & SEARCH ---
    document.getElementById('modalTambah').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formTambah').reset();
        checkManual.checked = false;
        inputNama.readOnly = true;
        inputNama.classList.add('text-center');
        inputNama.classList.remove('is-invalid');
        feedbackNama.style.display = 'none';
        btnSimpan.disabled = false;
        generateName();
    });

    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#tabelKelas tbody tr.data-row');
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
            Swal.fire({title: 'Hapus Kelas?', text: "Data tidak bisa dikembalikan!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!'}).then((r) => { if (r.isConfirmed) window.location.href = href; });
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