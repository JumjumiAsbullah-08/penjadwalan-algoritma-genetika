<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Administrator - MAN Sipagimbar</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet"/>

    <style>
        :root {
            --primary-color: #00684a;
            --accent-color: #f7c873;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #004d3d 50%, #00684a 100%);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .bg-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
            animation: pulseGlow 8s infinite alternate;
        }

        @keyframes pulseGlow {
            0% { transform: translate(-50%, -50%) scale(0.8); opacity: 0.5; }
            100% { transform: translate(-50%, -50%) scale(1.2); opacity: 1; }
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            padding: 40px;
            position: relative;
            z-index: 10;
        }

        .brand-icon {
            width: 70px;
            height: 70px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(0, 104, 74, 0.3);
            color: #fff;
            font-size: 30px;
        }

        .login-title {
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }
        
        .login-subtitle {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }

        .form-control {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 15px;
            font-weight: 600;
            color: #334155;
            transition: all 0.3s;
        }
        .form-control:focus {
            background: #fff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(0, 104, 74, 0.1);
        }
        .form-label {
            font-weight: 700;
            font-size: 0.85rem;
            color: #475569;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .btn-submit {
            background: var(--primary-color);
            border: none;
            color: white;
            padding: 14px;
            border-radius: 12px;
            font-weight: 800;
            letter-spacing: 0.5px;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background: #004d3d;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(0, 104, 74, 0.4);
        }

        .link-back {
            color: #64748b;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .link-back:hover {
            color: var(--primary-color);
            transform: translateX(-5px);
        }
    </style>
  </head>
  <body>

    <div class="bg-glow"></div>

    <?php 
        // CEK APAKAH ADA ERROR DARI SESSION?
        // Jika ada (msg atau error), variable $animasi jadi KOSONG (Statis).
        // Jika tidak ada, variable $animasi jadi 'animate__zoomIn'.
        $hasError = session()->getFlashdata('msg') || session()->getFlashdata('error');
        $animasiCard = $hasError ? '' : 'animate__zoomIn';
        $animasiIcon = $hasError ? '' : 'animate__bounceIn delay-1s';
    ?>

    <div class="login-card animate__animated <?= $animasiCard ?>">
        
        <div class="text-center">
            <div class="brand-icon animate__animated <?= $animasiIcon ?>">
                <i class="fas fa-user-shield"></i>
            </div>
            <h3 class="login-title">Admin Portal</h3>
            <p class="login-subtitle">Silakan login untuk mengelola Jadwal Pelajaran</p>
        </div>

        <form action="<?= base_url('auth/login') ?>" method="post">
            
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-user me-2 text-muted"></i> Username</label>
                <input type="text" name="username" class="form-control" placeholder="Ketik username Anda" required autofocus>
            </div>
            
            <div class="mb-4">
                <label class="form-label"><i class="fas fa-key me-2 text-muted"></i> Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-submit">
                    MASUK DASHBOARD <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </div>

        </form>
        
        <div class="text-center mt-4 pt-3 border-top">
            <a href="<?= base_url('/') ?>" class="link-back">
                <i class="fas fa-long-arrow-alt-left"></i> Kembali ke Halaman Utama
            </a>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // JIKA ADA PESAN ERROR ('msg' atau 'error'), TAMPILKAN SWEETALERT
        // Card di atas otomatis statis karena logika PHP tadi
        <?php if(session()->getFlashdata('msg')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Akses Ditolak',
                text: '<?= session()->getFlashdata('msg') ?>',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Coba Lagi'
            });
        <?php endif; ?>

        <?php if(session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Login',
                text: '<?= session()->getFlashdata('error') ?>',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Tutup'
            });
        <?php endif; ?>

        <?php if(session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= session()->getFlashdata('success') ?>',
                confirmButtonColor: '#00684a',
                timer: 3000
            });
        <?php endif; ?>
    </script>

  </body>
</html>