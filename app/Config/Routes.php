<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =========================================================================
// 1. RUTE PUBLIK & SHARED (Bisa diakses Admin & Kepsek/Umum)
// =========================================================================

$routes->get('/', 'Home::index'); 

// Authentication
$routes->get('auth', 'Auth::index');        // Halaman Login
$routes->post('auth/login', 'Auth::login'); // Proses Login
$routes->get('auth/logout', 'Auth::logout'); // Proses Logout
$routes->get('logout', 'Auth::logout');      // Alias Logout

// Dashboard (Bisa diakses Admin & Kepsek)
$routes->get('dashboard', 'Dashboard::index');

// Lihat Hasil Jadwal (Kepsek perlu lihat ini)
$routes->get('hasil', 'Hasil::index');
$routes->get('hasil/print', 'Hasil::print');
$routes->get('hasil/approve', 'Hasil::approve');       // Kepsek Setuju
$routes->get('hasil/reset_status', 'Hasil::reset_status');
$routes->get('arsip', 'Arsip::index');

// Laporan (Kepsek perlu cetak laporan)
$routes->get('laporan', 'Laporan::index');
$routes->get('laporan/download', 'Laporan::download');

// =========================================================================
// 2. RUTE KHUSUS ADMIN (DIPROTEKSI FILTER 'is_admin')
// =========================================================================
// Semua rute di dalam grup ini HANYA bisa diakses jika role = 'admin'.
// Jika Kepsek mencoba akses, akan ditendang ke Dashboard.

$routes->group('', ['filter' => 'is_admin'], function($routes) {

    // --- DIAGNOSTIK ---
    $routes->get('TesGenetika', 'TesGenetika::index');

    // --- DATA MASTER: GURU ---
    $routes->get('guru', 'Guru::index');
    $routes->post('guru/store', 'Guru::store');
    $routes->post('guru/update', 'Guru::update');
    $routes->get('guru/delete/(:num)', 'Guru::delete/$1');

    // --- DATA MASTER: MAPEL ---
    $routes->get('mapel', 'Mapel::index');
    $routes->post('mapel/store', 'Mapel::store');
    $routes->post('mapel/update', 'Mapel::update');
    $routes->get('mapel/delete/(:num)', 'Mapel::delete/$1');

    // --- DATA MASTER: KELAS ---
    $routes->get('kelas', 'Kelas::index');
    $routes->post('kelas/store', 'Kelas::store');
    $routes->post('kelas/update', 'Kelas::update');
    $routes->get('kelas/delete/(:num)', 'Kelas::delete/$1');

    // --- DATA MASTER: RUANGAN ---
    $routes->get('ruangan', 'Ruangan::index');
    $routes->post('ruangan/store', 'Ruangan::store');
    $routes->post('ruangan/update', 'Ruangan::update');
    $routes->get('ruangan/delete/(:num)', 'Ruangan::delete/$1');

    // --- DATA MASTER: JAM PELAJARAN ---
    $routes->get('jam_pelajaran', 'JamPelajaran::index');
    $routes->post('jampelajaran/store', 'JamPelajaran::store');
    $routes->post('jampelajaran/update', 'JamPelajaran::update');
    $routes->post('jampelajaran/generate', 'JamPelajaran::generate');
    $routes->post('jampelajaran/deleteMultiple', 'JamPelajaran::deleteMultiple');

    // --- PENJADWALAN: BEBAN MENGAJAR (PENGAMPU) ---
    $routes->get('pengampu', 'Pengampu::index');
    $routes->post('pengampu/store', 'Pengampu::store');
    $routes->post('pengampu/update', 'Pengampu::update');
    $routes->get('pengampu/delete/(:num)', 'Pengampu::delete/$1');
    $routes->post('pengampu/deleteMultiple', 'Pengampu::deleteMultiple');
    $routes->post('pengampu/salinData', 'Pengampu::salinData');

    // --- PENJADWALAN: GENERATE ALGORITMA ---
    $routes->get('generate', 'Generate::index');
    $routes->post('generate/process', 'Generate::process');
    $routes->get('generate/reset', 'Generate::reset');

    // --- PENJADWALAN: ANALISIS PERHITUNGAN ---
    $routes->get('perhitungan', 'Perhitungan::index');

    // --- PENGATURAN SISTEM ---
    $routes->get('pengaturan', 'Pengaturan::index');
    $routes->post('pengaturan/update', 'Pengaturan::update');

    // --- MANAJEMEN HASIL (YANG BERSIFAT AKSI) ---
    $routes->get('hasil/ajukan', 'Hasil::ajukan');
    $routes->get('hasil/approve', 'Hasil::approve'); // Jika approve dilakukan admin (opsional)
    $routes->get('hasil/reset_status', 'Hasil::reset_status');
    $routes->get('hasil/publish', 'Hasil::publish');
    $routes->get('hasil/batal_publish', 'Hasil::batal_publish');
    $routes->get('hasil/batal_ajukan', 'Hasil::batal_ajukan');
    
    // --- ARSIP ---
    $routes->get('arsip/publish', 'Arsip::publish');
    // Rute Hapus dengan 2 Parameter (Tahun & Semester)
    $routes->get('arsip/delete/(:any)', 'Arsip::delete/$1');
}); 
// AKHIR DARI GRUP ADMIN