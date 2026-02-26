<?= $this->extend('layout/template'); ?>

<?= $this->section('konten'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .nav-pills .nav-link.active {
        background-color: #f8f9fa !important;
        color: #198754 !important;
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
                <h6 class="fw-bold mb-1">Mode Terkunci</h6>
                <span class="small text-muted">Data Guru tidak dapat diubah karena <b>Jadwal Pelajaran sudah digenerate</b>.</span>
            </div>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 overflow-hidden rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
            <div>
                <h5 class="mb-0 text-success fw-bold"><i class="fas fa-chalkboard-teacher me-2"></i> Data Guru</h5>
                <p class="text-muted small mb-0">Kelola data guru, NIP, dan preferensi jadwal.</p>
            </div>
            
            <?php if(!$is_locked): ?>
                <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus me-2"></i> Tambah Guru
                </button>
            <?php else: ?>
                <button class="btn btn-secondary rounded-pill px-4" disabled title="Menu terkunci karena jadwal sudah ada">
                    <i class="fas fa-lock me-2"></i> Terkunci
                </button>
            <?php endif; ?>
        </div>
        
        <div class="card-body">
            
            <div class="row mb-4">
                <div class="col-md-5 ms-auto">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 ps-3 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-2 py-2" placeholder="Cari Nama Guru atau NIP..." autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelGuru">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th width="5%" class="text-center py-3">No</th>
                            <th width="20%" class="py-3">NIP</th>
                            <th class="py-3">Nama Lengkap</th>
                            <th class="py-3">Request Libur (Soft Constraint)</th>
                            <th width="15%" class="text-center py-3">Status / Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($daftar_guru)): ?>
                            <?php $no=1; foreach($daftar_guru as $g): ?>
                            <tr class="data-row">
                                <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                
                                <td>
                                    <div class="d-flex align-items-center bg-light border rounded px-2 py-1 shadow-sm" style="width: fit-content;">
                                        <span class="fw-bold text-dark me-2 font-monospace small">
                                            <?= empty($g['nip']) ? '-' : $g['nip'] ?>
                                        </span>
                                        <button class="btn btn-sm btn-link text-secondary p-0 btn-copy" 
                                                data-clipboard-text="<?= empty($g['nip']) ? '-' : $g['nip'] ?>" 
                                                data-bs-toggle="tooltip" 
                                                title="Salin NIP">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </td>
                                
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark"><?= $g['nama_guru'] ?></span>
                                        
                                        <?php if(isset($g['tugas_tambahan']) && $g['tugas_tambahan'] != 'Tidak Ada'): ?>
                                            <small class="text-primary fw-bold mt-1" style="font-size: 11px;">
                                                <i class="fas fa-star me-1"></i> <?= $g['tugas_tambahan'] ?>
                                            </small>
                                        <?php else: ?>
                                            <small class="text-muted mt-1" style="font-size: 11px;">
                                                <i class="fas fa-user-tie me-1"></i> Guru Mapel
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                
                                <td>
                                    <?php if($g['hari_tidak_bersedia']): ?>
                                        <?php 
                                            $hariLibur = explode(',', $g['hari_tidak_bersedia']);
                                            foreach($hariLibur as $h):
                                        ?>
                                            <span class="badge bg-danger shadow-sm me-1 mb-1 border border-danger bg-opacity-75">
                                                <i class="fas fa-calendar-times me-1"></i> <?= $h ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> Bersedia Tiap Hari
                                        </span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-center">
                                    <?php if($is_locked): ?>
                                        <span class="badge bg-secondary py-2 px-3 shadow-sm">
                                            <i class="fas fa-lock me-1"></i> Locked
                                        </span>
                                    <?php elseif($g['jumlah_beban'] > 0): ?>
                                        <span class="badge bg-info text-dark py-2 px-3 shadow-sm border border-info" 
                                            data-bs-toggle="tooltip" 
                                            title="Guru ini sedang aktif mengajar (<?= $g['jumlah_beban'] ?> Mapel).">
                                            <i class="fas fa-chalkboard-teacher me-1"></i> Aktif Mengajar
                                        </span>
                                    <?php else: ?>
                                        <div class="btn-group shadow-sm rounded" role="group">
                                            <button class="btn btn-sm btn-light text-primary border btn-edit" 
                                                    data-id="<?= $g['id_guru'] ?>"
                                                    data-nip="<?= empty($g['nip']) ? '-' : $g['nip'] ?>"
                                                    data-nama="<?= $g['nama_guru'] ?>"
                                                    data-tugas="<?= $g['tugas_tambahan'] ?? 'Tidak Ada' ?>"
                                                    data-hari="<?= $g['hari_tidak_bersedia'] ?>"
                                                    data-bs-toggle="modal" data-bs-target="#modalEdit"
                                                    title="Edit Data">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <a href="<?= base_url('guru/delete/'.$g['id_guru']) ?>" class="btn btn-sm btn-light text-danger border btn-hapus" title="Hapus Data">
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
                                    <div class="mb-3 opacity-25"><i class="fas fa-folder-open fa-4x"></i></div>
                                    <h5 class="fw-bold">Belum ada data guru</h5>
                                    <p class="small mb-0">Klik tombol <strong>Tambah Guru</strong> untuk mengisi data.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        
                        <tr id="noDataRow" style="display: none;">
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="mb-3 opacity-50"><i class="fas fa-search fa-3x"></i></div>
                                <h5 class="fw-bold">Data tidak ditemukan</h5>
                                <small>Coba kata kunci lain atau tambahkan data guru baru.</small>
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
                <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i> Tambah Guru Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('guru/store') ?>" method="post">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">NIP (Nomor Induk)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-id-card text-muted"></i></span>
                            <input type="text" name="nip" class="form-control" placeholder="Masukkan NIP..." required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Nama Lengkap & Gelar</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" name="nama_guru" class="form-control" placeholder="Cth: Budi Santoso, S.Pd" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Tugas Tambahan</label>
                        <select name="tugas_tambahan" class="form-select bg-light">
                            <option value="Tidak Ada">Tidak Ada (Guru Mapel Biasa)</option>
                            <option value="Kepala Sekolah">Kepala Sekolah (+24 JP)</option>
                            <option value="Waka">Wakil Kepala Sekolah (+12 JP)</option>
                            <option value="Kepala Lab">Kepala Laboratorium (+12 JP)</option>
                            <option value="Kepala Perpus">Kepala Perpustakaan (+12 JP)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase mb-3">Request Hari Libur (Klik untuk memilih)</label>
                        <div class="d-block">
                            <div class="row g-2" id="chkContainerTambah"> 
                                <?php 
                                $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                foreach($hari as $h): 
                                ?>
                                <div class="col-4"> 
                                    <input type="checkbox" class="btn-check" name="hari_tidak_bersedia[]" id="tambah_<?= $h ?>" value="<?= $h ?>" autocomplete="off">
                                    <label class="btn btn-outline-danger w-100 py-3 shadow-sm border-2 fw-bold d-flex flex-column align-items-center justify-content-center" for="tambah_<?= $h ?>">
                                        <i class="fas fa-calendar-day mb-1 opacity-50"></i>
                                        <span><?= $h ?></span>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block text-center bg-light p-2 rounded">
                            <i class="fas fa-info-circle text-danger"></i> Tombol <b>MERAH</b> berarti guru <b>TIDAK BISA</b> mengajar (Max 2 hari).
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-edit me-2"></i> Edit Data Guru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('guru/update') ?>" method="post">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_guru" id="edit_id_guru">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">NIP</label>
                        <input type="text" name="nip" id="edit_nip" class="form-control" placeholder="Kosongkan atau isi (-) jika tidak ada">
                        <small class="text-muted fst-italic ms-1">
                            <i class="fas fa-info-circle me-1"></i> Kosongkan atau isi strip (-) jika Non-PNS.
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Nama Lengkap</label>
                        <input type="text" name="nama_guru" id="edit_nama" class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small text-uppercase">Tugas Tambahan</label>
                        <select name="tugas_tambahan" id="edit_tugas" class="form-select bg-light">
                            <option value="Tidak Ada">Tidak Ada (Guru Biasa)</option>
                            <option value="Kepala Sekolah">Kepala Sekolah (+24 JP)</option>
                            <option value="Waka">Wakil Kepala Sekolah (+12 JP)</option>
                            <option value="Kepala Lab">Kepala Laboratorium (+12 JP)</option>
                            <option value="Kepala Perpus">Kepala Perpustakaan (+12 JP)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small text-uppercase mb-3">Request Hari Libur</label>
                        <div class="d-block">
                            <div class="row g-2" id="chkContainerEdit">
                                <?php foreach($hari as $h): ?>
                                <div class="col-4">
                                    <input type="checkbox" class="btn-check checkbox-edit" name="hari_tidak_bersedia[]" id="edit_<?= $h ?>" value="<?= $h ?>" autocomplete="off">
                                    <label class="btn btn-outline-danger w-100 py-3 shadow-sm border-2 fw-bold d-flex flex-column align-items-center justify-content-center" for="edit_<?= $h ?>">
                                        <i class="fas fa-calendar-times mb-1 opacity-50"></i>
                                        <span><?= $h ?></span>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // 1. FITUR PENCARIAN
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#tabelGuru tbody tr.data-row');
        let hasData = false;

        rows.forEach(row => {
            let nip = row.cells[1].textContent.toLowerCase();
            let nama = row.cells[2].textContent.toLowerCase();

            if (nip.includes(filter) || nama.includes(filter)) {
                row.style.display = '';
                hasData = true;
            } else {
                row.style.display = 'none';
            }
        });
        document.getElementById('noDataRow').style.display = hasData ? 'none' : 'table-row';
    });

    // 2. FITUR COPY NIP
    const copyBtns = document.querySelectorAll('.btn-copy');
    const Toast = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true
    });

    copyBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const nip = this.getAttribute('data-clipboard-text');
            navigator.clipboard.writeText(nip).then(() => {
                const icon = this.querySelector('i');
                icon.className = 'fas fa-check text-success';
                Toast.fire({ icon: 'success', title: 'NIP disalin!' });
                setTimeout(() => { icon.className = 'far fa-copy'; }, 2000);
            });
        });
    });

    // 3. LIMIT CHECKBOX 2 HARI
    function batasiCheckbox(containerID) {
        const container = document.getElementById(containerID);
        const checkboxes = container.querySelectorAll('input[type="checkbox"]');

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const checkedCount = container.querySelectorAll('input[type="checkbox"]:checked').length;
                if (checkedCount > 2) {
                    this.checked = false;
                    Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: 'Maksimal libur 2 hari!', showConfirmButton: false, timer: 1500 });
                }
                updateLimitVisuals(container, 2);
            });
        });
    }

    function updateLimitVisuals(container, max) {
        const boxes = container.querySelectorAll('input[type="checkbox"]');
        const count = container.querySelectorAll('input[type="checkbox"]:checked').length;
        boxes.forEach(box => {
            if(!box.checked) {
                if(count >= max) {
                    box.disabled = true;
                    box.nextElementSibling.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    box.disabled = false;
                    box.nextElementSibling.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        });
    }

    // 4. POPULATE EDIT
    const editBtns = document.querySelectorAll('.btn-edit');
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id_guru').value = this.getAttribute('data-id');
            document.getElementById('edit_nip').value = this.getAttribute('data-nip');
            document.getElementById('edit_nama').value = this.getAttribute('data-nama');
            document.getElementById('edit_tugas').value = this.getAttribute('data-tugas'); // Populate Tugas
            
            const hariString = this.getAttribute('data-hari');
            const container = document.getElementById('chkContainerEdit');
            
            container.querySelectorAll('input').forEach(cb => cb.checked = false); // Reset

            if (hariString) {
                const hariArray = hariString.split(',');
                hariArray.forEach(hari => {
                    let checkbox = document.getElementById('edit_' + hari);
                    if(checkbox) checkbox.checked = true;
                });
            }
            updateLimitVisuals(container, 2);
        });
    });

    // Init Logic
    ['modalTambah', 'modalEdit'].forEach(id => {
        const modal = document.getElementById(id);
        modal.addEventListener('shown.bs.modal', function () {
            batasiCheckbox(id == 'modalTambah' ? 'chkContainerTambah' : 'chkContainerEdit');
            updateLimitVisuals(document.getElementById(id == 'modalTambah' ? 'chkContainerTambah' : 'chkContainerEdit'), 2);
        });
    });

    // 5. DELETE CONFIRM
    const hapusBtns = document.querySelectorAll('.btn-hapus');
    hapusBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            Swal.fire({
                title: 'Hapus Guru ini?', text: "Data tidak bisa dikembalikan!", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => { if (result.isConfirmed) window.location.href = href; });
        });
    });

    <?php if(session()->getFlashdata('success')): ?>
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: '<?= session()->getFlashdata('success') ?>', timer: 2000, showConfirmButton: false });
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        Swal.fire({ icon: 'error', title: 'Gagal!', html: '<?= session()->getFlashdata('error') ?>' });
    <?php endif; ?>
</script>

<?= $this->endSection(); ?>