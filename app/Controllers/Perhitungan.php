<?php

namespace App\Controllers;

use App\Models\GuruModel;
use App\Models\MapelModel;
use App\Models\KelasModel;
use App\Models\RuanganModel;
use App\Models\JamModel;
use App\Models\PengampuModel;
use App\Models\KonfigurasiModel; // TAMBAHAN: Untuk cek periode aktif
use App\Models\TrackModel;       // TAMBAHAN: Untuk ambil data permanen

class Perhitungan extends BaseController
{
    protected $guruModel;
    protected $mapelModel;
    protected $kelasModel;
    protected $ruanganModel;
    protected $jamModel;
    protected $pengampuModel;
    protected $konfigModel;
    protected $trackModel;

    public function __construct()
    {
        $this->guruModel     = new GuruModel();
        $this->mapelModel    = new MapelModel();
        $this->kelasModel    = new KelasModel();
        $this->ruanganModel  = new RuanganModel();
        $this->jamModel      = new JamModel();
        $this->pengampuModel = new PengampuModel();
        
        // Load Model Tambahan
        $this->konfigModel   = new KonfigurasiModel();
        $this->trackModel    = new TrackModel();
    }

    public function index()
    {
        // 1. AMBIL INFO PERIODE AKTIF
        $info  = $this->konfigModel->getInfoAktif();
        $tahun = $info['tahun'];
        $sem   = $info['semester'];

        // 2. LOGIKA PENGAMBILAN DATA (SESSION vs DATABASE)
        $historyData = session()->get('history_ga');
        $bestKromosom = session()->get('last_best_chromosom');

        // Jika Session Kosong (Misal habis Logout), Coba Cari di Database
        if (empty($historyData)) {
            $dbTrack = $this->trackModel->getTrack($tahun, $sem);
            
            if ($dbTrack) {
                // Restore Data dari Database
                $historyData  = json_decode($dbTrack['history_log'], true);
                
                // Decode Kromosom (Jika disimpan di DB) atau kita biarkan null jika tidak perlu detail banget
                // Idealnya 'best_kromosom' juga disimpan di DB agar Tab Evolusi jalan
                $bestKromosom = json_decode($dbTrack['best_kromosom'], true); 

                // Kembalikan ke Session biar akses berikutnya cepat
                session()->set('history_ga', $historyData);
                session()->set('last_best_chromosom', $bestKromosom);
            }
        }

        // Jika Masih Kosong Juga (Berarti belum pernah generate sama sekali)
        if (empty($historyData)) {
            return redirect()->to('/generate')->with('error', 'Data perhitungan tidak ditemukan. Silakan Generate Jadwal terlebih dahulu!');
        }

        // ============================================================
        // MULAI MENYIAPKAN DATA UNTUK TAMPILAN (VISUALISASI)
        // ============================================================

        // A. DATA UNTUK TAB ENCODING (Ambil 3 Data Pengampu Teratas dari DB)
        // Kita filter berdasarkan tahun aktif agar relevan
        $rawPengampu = $this->pengampuModel
            ->select('pengampu.*, guru.nama_guru, mapel.nama_mapel, kelas.nama_kelas')
            ->join('guru', 'guru.id_guru = pengampu.id_guru')
            ->join('mapel', 'mapel.id_mapel = pengampu.id_mapel')
            ->join('kelas', 'kelas.id_kelas = pengampu.id_kelas')
            ->where('pengampu.tahun_ajaran', $tahun) // Filter Tahun
            ->where('pengampu.semester', $sem)       // Filter Semester
            ->limit(3) 
            ->findAll();

        // B. DATA UNTUK TAB INISIALISASI (Simulasi Assign Random)
        $dataJam   = $this->jamModel->where('is_istirahat', 0)->findAll();
        $dataRuang = $this->ruanganModel->findAll();
        
        $sampleInisialisasi = [];
        if(!empty($dataJam) && !empty($dataRuang)) {
            foreach($rawPengampu as $p) {
                $jamAcak   = $dataJam[array_rand($dataJam)];
                $ruangAcak = $dataRuang[array_rand($dataRuang)];
                
                $sampleInisialisasi[] = [
                    'guru'   => $p['nama_guru'],
                    'mapel'  => $p['nama_mapel'],
                    'kelas'  => $p['nama_kelas'],
                    'durasi' => ($p['jumlah_jam'] >= 4) ? 2 : $p['jumlah_jam'], 
                    'hari'   => $jamAcak['hari'],
                    'jam'    => $jamAcak['jam_ke'],
                    'ruang'  => $ruangAcak['nama_ruangan']
                ];
            }
        }

        // C. DATA UNTUK TAB SELEKSI (Simulasi Roulette Wheel)
        // Kita buat dummy data statis untuk edukasi user
        $simulasiSeleksi = [
            ['id' => 'Individu-1 (Terbaik)', 'fitness' => 0.9, 'prob' => 0],
            ['id' => 'Individu-2', 'fitness' => 0.5, 'prob' => 0],
            ['id' => 'Individu-3', 'fitness' => 0.2, 'prob' => 0],
            ['id' => 'Individu-4', 'fitness' => 0.1, 'prob' => 0],
        ];
        $totalF = array_sum(array_column($simulasiSeleksi, 'fitness'));
        foreach($simulasiSeleksi as &$s) {
            $s['prob'] = ($totalF > 0) ? ($s['fitness'] / $totalF) * 100 : 0;
        }

        // D. DATA UNTUK TAB EVOLUSI (Crossover & Mutasi)
        // Kita ambil sampel dari $bestKromosom yang sudah kita restore
        $sampleGen = [];
        if (!empty($bestKromosom)) {
            // Ambil maksimal 2 gen untuk contoh
            $limit = min(count($bestKromosom), 2);
            for($i=0; $i<$limit; $i++) {
                $k = $bestKromosom[$i];
                // Cek data guru (antisipasi jika data master terhapus)
                $guru = $this->guruModel->find($k['id_guru']);
                $namaGuru = $guru ? $guru['nama_guru'] : 'Guru #'.$k['id_guru'];
                
                $sampleGen[] = [
                    'kode'  => 'G-'.$k['id_guru'],
                    'nama'  => $namaGuru,
                    'hari'  => $k['hari'] ?? '-',
                    'jam'   => $k['jam_ke'] ?? '-',
                    'ruang' => $k['id_ruangan'] ?? '-'
                ];
            }
        }

        // E. KIRIM SEMUA KE VIEW
        $data = [
            'title'           => 'Analisis Algoritma',
            'tahun_aktif'     => $tahun,
            'sem_aktif'       => $sem,
            
            // Data Tabulasi
            'tab_encoding'    => $rawPengampu,
            'tab_inisialisasi'=> $sampleInisialisasi,
            'tab_seleksi'     => $simulasiSeleksi,
            'tab_evolusi'     => $sampleGen,
            
            // Data Grafik (PENTING)
            'history'         => $historyData,
            
            // Ambil Fitness Terakhir
            'final_fitness'   => !empty($historyData) ? end($historyData)['fitness'] : 0
        ];

        return view('perhitungan/index', $data);
    }
}