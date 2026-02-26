<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Penjadwalan - MAN Sipagimbar</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #064e3b 0%, #047857 100%);
            color: white;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar-brand { font-weight: 700; letter-spacing: 1px; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar a { color: rgba(255,255,255,0.8); text-decoration: none; display: block; padding: 12px 20px; margin: 5px 10px; border-radius: 8px; transition: all 0.3s; font-size: 0.95rem; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.2); color: #fff; transform: translateX(5px); }
        .sidebar i { width: 25px; text-align: center; }
        .main-content { width: 100%; padding: 20px; }
        .navbar-custom { background-color: white; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.05); padding: 15px 25px; margin-bottom: 25px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); transition: transform 0.3s; }
        .card-hover:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<div class="d-flex">
    
    <?= $this->include('layout/sidebar'); ?>

    <div class="main-content">
        
        <?= $this->include('layout/navbar'); ?>

        <?= $this->renderSection('konten'); ?>
        
        <footer class="mt-5 text-center text-muted small">
            &copy; 2026 MAN Tapanuli Selatan - Created by Meini Syakinah Ritonga
        </footer>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {

        // =======================================================
        // NOTIFIKASI LOGIN (TOAST)
        // =======================================================
        <?php if (session()->getFlashdata('login_sukses')) : ?>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'success',
                title: '<?= session()->getFlashdata('login_sukses'); ?>'
            });
        <?php endif; ?>

        // =======================================================
        // NOTIFIKASI ERROR GLOBAL
        // =======================================================
        <?php if (session()->getFlashdata('error')) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: '<?= session()->getFlashdata('error'); ?>',
                confirmButtonColor: '#d33'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= session()->getFlashdata('success'); ?>',
                timer: 3000,
                showConfirmButton: false
            });
        <?php endif; ?>

        // =======================================================
        // 3. KONFIRMASI LOGOUT (FIXED)
        // =======================================================
        // Gunakan $(document).on agar aman walau dipanggil dari manapun
        $(document).on('click', '#btnLogout', function(e) {
            e.preventDefault(); // Mencegah link pindah langsung
            const href = $(this).attr('href');

            Swal.fire({
                title: 'Yakin ingin keluar?',
                text: "Sesi Anda akan diakhiri.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.location.href = href;
                }
            });
        });

    });
</script>

</body>
</html>