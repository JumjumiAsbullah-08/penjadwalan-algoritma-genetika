<?php

namespace App\Controllers;

use App\Models\GuruModel;
use App\Models\MapelModel;
use App\Models\KelasModel;
use App\Models\RuanganModel;
use App\Models\JamModel;
use App\Models\PengampuModel;
use App\Models\JadwalModel;
use App\Models\KonfigurasiModel; // TAMBAHAN 1: Load Model Konfigurasi

class Dashboard extends BaseController
{
    protected $guruModel;
    protected $mapelModel;
    protected $kelasModel;
    protected $ruanganModel;
    protected $jamModel;
    protected $pengampuModel;
    protected $jadwalModel;
    protected $konfigModel; // TAMBAHAN 2: Properti Model

    public function __construct()
    {
        $this->guruModel     = new GuruModel();
        $this->mapelModel    = new MapelModel();
        $this->kelasModel    = new KelasModel();
        $this->ruanganModel  = new RuanganModel();
        $this->jamModel      = new JamModel();
        $this->pengampuModel = new PengampuModel();
        $this->jadwalModel   = new JadwalModel();
        $this->konfigModel   = new KonfigurasiModel(); // TAMBAHAN 3: Init Model
    }

    public function index()
    {
        // 1. Cek Login
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        // 2. Ambil Info Tahun & Semester Aktif
        $info = $this->konfigModel->getInfoAktif();
        $tahunAktif = $info['tahun'];
        $semAktif   = $info['semester'];

        // 3. Siapkan Data untuk View
        // Nama variabel disesuaikan dengan View dashboard.php yang baru
        $data = [
            'title'        => 'Dashboard Admin',
            'tahun_aktif'  => $tahunAktif,
            'sem_aktif'    => $semAktif,
            
            // Statistik Master Data
            'total_guru'   => $this->guruModel->countAllResults(),
            'total_kelas'  => $this->kelasModel->countAllResults(),
            'total_mapel'  => $this->mapelModel->countAllResults(),
            'total_ruangan' => $this->ruanganModel->countAllResults(), 
            'total_jadwal'  => $this->jadwalModel->countAllResults(),
            
            // Cek apakah Jadwal untuk periode ini sudah ada isinya?
            // Kita filter berdasarkan tahun & semester aktif
            'total_jadwal' => $this->jadwalModel
                                ->where('tahun_ajaran', $tahunAktif)
                                ->where('semester', $semAktif)
                                ->countAllResults()
        ];

        return view('dashboard', $data); 
    }
}