<?= $this->extend('layout/template'); ?>

<?= $this->section('konten'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid">

    <div id="existingData" 
         data-names='<?= json_encode(array_column($daftar_mapel, 'nama_mapel')) ?>' 
         data-codes='<?= json_encode(array_column($daftar_mapel, 'kode_mapel')) ?>'>
    </div>

    <?php if($is_locked): ?>
        <div class="alert alert-warning d-flex align-items-center mb-4 shadow-sm border-0 rounded-3">
            <div class="bg-warning bg-opacity-25 p-3 rounded-circle me-3 text-warning">
                <i class="fas fa-lock fa-lg"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1">Mode Terkunci</h6>
                <span class="small text-muted">Data Mata Pelajaran tidak dapat diubah karena <b>Jadwal Pelajaran sudah digenerate</b>.</span>
            </div>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 overflow-hidden rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
            <div>
                <h5 class="mb-0 text-success fw-bold"><i class="fas fa-book me-2"></i> Data Mata Pelajaran</h5>
                <p class="text-muted small mb-0">Atur kurikulum, jenis, dan beban jam mata pelajaran.</p>
            </div>
            
            <?php if(!$is_locked): ?>
                <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus me-2"></i> Tambah Mapel
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
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-2 py-2" placeholder="Cari Mapel atau Kode..." autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelMapel">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th width="5%" class="text-center py-3">Kode</th>
                            <th class="py-3">Nama Mata Pelajaran</th>
                            <th class="py-3">Kelompok & Ruangan</th>
                            <th class="py-3 text-center">Beban Kerja (Max)</th>
                            <th width="15%" class="text-center py-3">Status / Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($daftar_mapel)): ?>
                            <?php foreach($daftar_mapel as $m): ?>
                            <tr class="data-row">
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border font-monospace shadow-sm px-2 py-1"><?= $m['kode_mapel'] ?></span>
                                </td>
                                
                                <td>
                                    <span class="fw-bold text-dark d-block"><?= $m['nama_mapel'] ?></span>
                                    <small class="text-muted">
                                        <?= ($m['jenis'] == 'Wajib') ? 'Kurikulum Wajib' : (($m['jenis'] == 'Agama') ? 'Mapel Agama' : 'Peminatan') ?>
                                    </small>
                                </td>
                                
                                <td>
                                    <?php 
                                        $bgKelompok = 'bg-secondary';
                                        $iconKelompok = 'fa-book';
                                        if($m['kelompok'] == 'Teori') { $bgKelompok = 'bg-primary'; $iconKelompok = 'fa-book-reader'; }
                                        elseif($m['kelompok'] == 'Olahraga') { $bgKelompok = 'bg-warning text-dark'; $iconKelompok = 'fa-running'; }
                                        elseif($m['kelompok'] == 'Praktek Komputer') { $bgKelompok = 'bg-info text-dark'; $iconKelompok = 'fa-laptop-code'; }
                                        elseif($m['kelompok'] == 'Praktek IPA') { $bgKelompok = 'bg-success'; $iconKelompok = 'fa-flask'; }
                                    ?>
                                    <span class="badge <?= $bgKelompok ?> bg-opacity-75 shadow-sm">
                                        <i class="fas <?= $iconKelompok ?> me-1"></i> <?= $m['kelompok'] ?>
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="fw-bold text-dark"><?= $m['max_jam_per_minggu'] ?> JP</span>
                                    <span class="text-muted small">/minggu</span>
                                </td>
                                
                                <td class="text-center">
                                    <?php if($is_locked): ?>
                                        <span class="badge bg-secondary py-2 px-3 shadow-sm"><i class="fas fa-lock me-1"></i> Locked</span>
                                    <?php elseif($m['jumlah_pengampu'] > 0): ?>
                                        <span class="badge bg-info text-dark py-2 px-3 shadow-sm border border-info" 
                                              data-bs-toggle="tooltip" 
                                              title="Mapel ini sedang diajarkan oleh Guru. Hapus penugasan dulu untuk mengedit.">
                                              <i class="fas fa-chalkboard-teacher me-1"></i> Digunakan
                                        </span>
                                    <?php else: ?>
                                        <div class="btn-group shadow-sm rounded" role="group">
                                            <button class="btn btn-sm btn-light text-primary border btn-edit" 
                                                    data-id="<?= $m['id_mapel'] ?>"
                                                    data-kode="<?= $m['kode_mapel'] ?>"
                                                    data-nama="<?= $m['nama_mapel'] ?>"
                                                    data-jenis="<?= $m['jenis'] ?>"
                                                    data-kelompok="<?= $m['kelompok'] ?>"
                                                    data-jam="<?= $m['max_jam_per_minggu'] ?>"
                                                    data-bs-toggle="modal" data-bs-target="#modalEdit"
                                                    title="Edit Mapel">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <a href="<?= base_url('mapel/delete/'.$m['id_mapel']) ?>" class="btn btn-sm btn-light text-danger border btn-hapus" title="Hapus Mapel">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="mb-3 opacity-25"><i class="fas fa-book fa-4x"></i></div>
                                    <h5 class="fw-bold">Belum ada data mata pelajaran</h5>
                                    <p class="small mb-0">Silakan tambahkan data mapel baru melalui tombol di atas.</p>
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

<datalist id="bankMapel">
    <option value="Al-Quran Hadits">
    <option value="Akidah Akhlak">
    <option value="Fiqih">
    <option value="Sejarah Kebudayaan Islam">
    <option value="Bahasa Arab">
    <option value="Pendidikan Pancasila">
    <option value="Bahasa Indonesia">
    <option value="Bahasa Inggris">
    <option value="Matematika Wajib">
    <option value="Matematika Peminatan">
    <option value="Sejarah Indonesia">
    <option value="Seni Budaya">
    <option value="Prakarya & Kewirausahaan">
    <option value="PJOK">
    <option value="Informatika">
    <option value="Biologi">
    <option value="Fisika">
    <option value="Kimia">
    <option value="Ekonomi">
    <option value="Geografi">
    <option value="Sosiologi">
</datalist>

<div class="modal fade" id="modalTambah" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> Tambah Mapel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('mapel/store') ?>" method="post" id="formTambah">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Nama Mata Pelajaran</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-book text-muted"></i></span>
                            <input type="text" name="nama_mapel" id="inputNamaMapel" class="form-control" list="bankMapel" placeholder="Ketik untuk auto-suggest..." required autocomplete="off">
                        </div>
                        <div id="feedbackNama" class="invalid-feedback" style="display:none"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Kode (3 Huruf)</label>
                            <input type="text" name="kode_mapel" id="inputKodeMapel" class="form-control font-monospace text-uppercase" placeholder="AUTO" required maxlength="5">
                             <div id="feedbackKode" class="invalid-feedback" style="display:none"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Jenis Kurikulum</label>
                            <select name="jenis" class="form-select" id="inputJenisMapel">
                                <option value="Wajib">Umum / Wajib</option>
                                <option value="Agama">Agama (Khas)</option>
                                <option value="Peminatan">Peminatan</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Max Jam (JP)</label>
                            <input type="number" name="max_jam_per_minggu" id="inputMaxJam" class="form-control" value="4" min="1" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Kelompok Mapel (Penentu Ruangan)</label>
                        <select name="kelompok" class="form-select bg-light" id="inputKelompok">
                            <option value="Teori">Teori (Kelas Biasa)</option>
                            <option value="Olahraga">Olahraga (Lapangan)</option>
                            <option value="Praktek Komputer">Praktek Komputer (Lab Kom)</option>
                            <option value="Praktek IPA">Praktek IPA (Lab IPA)</option>
                            <option value="Mulok">Muatan Lokal</option>
                        </select>
                        <small class="text-muted" style="font-size: 11px;">*Pilih 'Olahraga' agar otomatis dijadwalkan di Lapangan.</small>
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
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Edit Mapel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('mapel/update') ?>" method="post">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_mapel" id="edit_id_mapel">

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" id="edit_nama" class="form-control" list="bankMapel" required autocomplete="off">
                        <div id="editFeedbackNama" class="invalid-feedback" style="display:none"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Kode Mapel</label>
                            <input type="text" name="kode_mapel" id="edit_kode" class="form-control font-monospace text-uppercase" required maxlength="5">
                            <div id="editFeedbackKode" class="invalid-feedback" style="display:none"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Kurikulum</label>
                            <select name="jenis" id="edit_jenis" class="form-select">
                                <option value="Wajib">Umum / Wajib</option>
                                <option value="Agama">Agama (Khas)</option>
                                <option value="Peminatan">Peminatan</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Max Jam</label>
                            <input type="number" name="max_jam_per_minggu" id="edit_jam" class="form-control" min="1" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Kelompok Mapel</label>
                        <select name="kelompok" id="edit_kelompok" class="form-select bg-light">
                            <option value="Teori">Teori (Kelas Biasa)</option>
                            <option value="Olahraga">Olahraga (Lapangan)</option>
                            <option value="Praktek Komputer">Praktek Komputer (Lab Kom)</option>
                            <option value="Praktek IPA">Praktek IPA (Lab IPA)</option>
                            <option value="Mulok">Muatan Lokal</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" id="btnUpdate">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // =============================================
    // 1. DATA EKSISTING (Untuk Pengecekan Duplikat)
    // =============================================
    const dataContainer = document.getElementById('existingData');
    const existingNames = JSON.parse(dataContainer.getAttribute('data-names')).map(n => n.toLowerCase());
    const existingCodes = JSON.parse(dataContainer.getAttribute('data-codes')).map(c => c.toLowerCase());

    // =============================================
    // 2. FUNGSI LOGIKA PINTAR (REUSABLE)
    // =============================================
    function generateSmartCode(text) {
        if (!text) return '';
        let clean = text.replace(/[^a-zA-Z]/g, '').toUpperCase();
        let code = clean.charAt(0);
        let consonants = clean.substring(1).replace(/[AEIOU]/g, '');
        code += consonants;
        if (code.length < 3) code = clean; 
        return code.substring(0, 3);
    }

    function guessJenis(text) {
        if(text.match(/Quran|Fiqih|Akidah|Islam|Arab/i)) return 'Agama';
        else if(text.match(/Biologi|Kimia|Fisika|Ekonomi|Sosiologi|Geografi/i)) return 'Peminatan';
        return 'Wajib';
    }

    // Auto-Select Kelompok berdasarkan Nama (Fitur Baru)
    function guessKelompok(text) {
        if(text.match(/PJOK|Penjas|Olahraga/i)) return 'Olahraga';
        if(text.match(/TIK|Informatika|Komputer/i)) return 'Praktek Komputer';
        if(text.match(/Prakarya/i)) return 'Mulok'; // Atau Teori, tergantung kebijakan
        return 'Teori';
    }

    // =============================================
    // 3. LOGIKA MODAL TAMBAH
    // =============================================
    const inputNama = document.getElementById('inputNamaMapel');
    const inputKode = document.getElementById('inputKodeMapel');
    const inputJenis = document.getElementById('inputJenisMapel');
    const inputKelompok = document.getElementById('inputKelompok'); // New
    const btnSimpan = document.getElementById('btnSimpan');
    const feedbackNama = document.getElementById('feedbackNama');
    const feedbackKode = document.getElementById('feedbackKode');

    function handleInputTambah() {
        let val = inputNama.value;
        let cleanName = val.toLowerCase().trim();

        // 1. Auto Fill
        if(val.length > 0) {
            inputKode.value = generateSmartCode(val);
            inputJenis.value = guessJenis(val);
            inputKelompok.value = guessKelompok(val); // Auto-Select Kelompok
        } else {
            inputKode.value = '';
        }

        // 2. Validasi Nama
        if (existingNames.includes(cleanName) && cleanName !== '') {
            inputNama.classList.add('is-invalid');
            feedbackNama.style.display = 'block';
            feedbackNama.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Mata Pelajaran ini sudah ada!';
        } else {
            inputNama.classList.remove('is-invalid');
            feedbackNama.style.display = 'none';
        }

        // 3. Validasi Kode
        let cleanKode = inputKode.value.toLowerCase().trim();
        if (existingCodes.includes(cleanKode) && cleanKode !== '') {
            inputKode.classList.add('is-invalid');
            feedbackKode.style.display = 'block';
            feedbackKode.innerHTML = 'Kode sudah dipakai!';
        } else {
            inputKode.classList.remove('is-invalid');
            feedbackKode.style.display = 'none';
        }

        // 4. Button State
        if (inputNama.classList.contains('is-invalid') || inputKode.classList.contains('is-invalid') || val === '') {
            btnSimpan.disabled = true;
        } else {
            btnSimpan.disabled = false;
        }
    }

    inputNama.addEventListener('input', handleInputTambah);
    inputKode.addEventListener('input', function() {
        let cleanKode = this.value.toLowerCase().trim();
        if (existingCodes.includes(cleanKode) && cleanKode !== '') {
            this.classList.add('is-invalid');
            feedbackKode.style.display = 'block';
            feedbackKode.innerHTML = 'Kode sudah dipakai!';
            btnSimpan.disabled = true;
        } else {
            this.classList.remove('is-invalid');
            feedbackKode.style.display = 'none';
            if(!inputNama.classList.contains('is-invalid')) btnSimpan.disabled = false;
        }
    });

    // =============================================
    // 4. LOGIKA MODAL EDIT (DENGAN VALIDASI DUPLIKAT)
    // =============================================
    const editNama = document.getElementById('edit_nama');
    const editKode = document.getElementById('edit_kode');
    const editJenis = document.getElementById('edit_jenis');
    const editKelompok = document.getElementById('edit_kelompok'); // New
    const editJam = document.getElementById('edit_jam'); // New
    const btnUpdate = document.getElementById('btnUpdate');
    const editFeedbackNama = document.getElementById('editFeedbackNama');
    const editFeedbackKode = document.getElementById('editFeedbackKode');

    let originNama = '';
    let originKode = '';

    function handleInputEdit() {
        let val = editNama.value;
        let cleanName = val.toLowerCase().trim();

        // 1. Auto Fill (Smart Logic saat edit)
        if(val.length > 0 && val !== originNama) {
            editKode.value = generateSmartCode(val);
            editJenis.value = guessJenis(val);
            editKelompok.value = guessKelompok(val);
        }

        // 2. Validasi Duplikat Nama
        if (existingNames.includes(cleanName) && cleanName !== originNama.toLowerCase() && cleanName !== '') {
            editNama.classList.add('is-invalid');
            editFeedbackNama.style.display = 'block';
            editFeedbackNama.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Mata Pelajaran ini sudah ada!';
            btnUpdate.disabled = true;
        } else {
            editNama.classList.remove('is-invalid');
            editFeedbackNama.style.display = 'none';
            if(!editKode.classList.contains('is-invalid')) btnUpdate.disabled = false;
        }
        checkEditKode();
    }

    function checkEditKode() {
        let cleanKode = editKode.value.toLowerCase().trim();
        if (existingCodes.includes(cleanKode) && cleanKode !== originKode.toLowerCase() && cleanKode !== '') {
            editKode.classList.add('is-invalid');
            editFeedbackKode.style.display = 'block';
            editFeedbackKode.innerHTML = 'Kode sudah dipakai!';
            btnUpdate.disabled = true;
        } else {
            editKode.classList.remove('is-invalid');
            editFeedbackKode.style.display = 'none';
            if(!editNama.classList.contains('is-invalid')) btnUpdate.disabled = false;
        }
    }

    editNama.addEventListener('input', handleInputEdit);
    editKode.addEventListener('input', checkEditKode);


    // =============================================
    // 5. RESET & POPULATE DATA
    // =============================================
    
    // Reset Modal Tambah
    document.getElementById('modalTambah').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formTambah').reset();
        inputNama.classList.remove('is-invalid');
        inputKode.classList.remove('is-invalid');
        feedbackNama.style.display = 'none';
        feedbackKode.style.display = 'none';
        btnSimpan.disabled = false;
    });

    // Populate Modal Edit
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id_mapel').value = this.getAttribute('data-id');
            let nama = this.getAttribute('data-nama');
            let kode = this.getAttribute('data-kode');
            let jenis = this.getAttribute('data-jenis');
            let kelompok = this.getAttribute('data-kelompok'); // New
            let jam = this.getAttribute('data-jam'); // New

            editNama.value = nama;
            editKode.value = kode;
            editJenis.value = jenis;
            editKelompok.value = kelompok; // Fill
            editJam.value = jam; // Fill

            originNama = nama;
            originKode = kode;

            editNama.classList.remove('is-invalid');
            editKode.classList.remove('is-invalid');
            editFeedbackNama.style.display = 'none';
            editFeedbackKode.style.display = 'none';
            btnUpdate.disabled = false;
        });
    });

    // =============================================
    // 6. FITUR LAIN (SEARCH & DELETE)
    // =============================================
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#tabelMapel tbody tr.data-row');
        let hasData = false;
        rows.forEach(row => {
            let txt = row.textContent.toLowerCase();
            if(txt.includes(filter)) { row.style.display = ''; hasData = true; } 
            else { row.style.display = 'none'; }
        });
        document.getElementById('noDataRow').style.display = hasData ? 'none' : 'table-row';
    });

    document.querySelectorAll('.btn-hapus').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            Swal.fire({
                title: 'Hapus Mapel?', text: "Data tidak bisa dikembalikan!", icon: 'warning', 
                showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!'
            }).then((r) => { if (r.isConfirmed) window.location.href = href; });
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