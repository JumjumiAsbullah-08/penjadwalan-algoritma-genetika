<?php

namespace App\Controllers;

use App\Models\GuruModel;
use App\Models\MapelModel;
use App\Models\KelasModel;
use App\Models\RuanganModel;
use App\Models\JamModel;
use App\Models\PengampuModel;
use App\Models\JadwalModel;
use App\Models\KonfigurasiModel;
use App\Models\TrackModel; // [BARU] Load Model Track

class Generate extends BaseController
{
    // --- LOAD MODELS ---
    protected $guruModel;
    protected $mapelModel;
    protected $kelasModel;
    protected $ruanganModel;
    protected $jamModel;
    protected $pengampuModel;
    protected $jadwalModel;
    protected $konfigModel;
    protected $trackModel; // [BARU] Properti Track Model

    // --- KONFIGURASI GENETIKA (PARAMETER UTAMA) ---
    protected $populasiSize  = 80;     // Populasi lebih besar agar variasi lebih banyak
    protected $maxGenerasi   = 800;    // Generasi lebih panjang untuk mencari solusi terbaik
    protected $crossoverRate = 0.85;   // Peluang kawin silang tinggi
    protected $mutationRate  = 0.40;   // Mutasi 40% agar tidak terjebak di solusi lokal

    public function __construct()
    {
        $this->guruModel     = new GuruModel();
        $this->mapelModel    = new MapelModel();
        $this->kelasModel    = new KelasModel();
        $this->ruanganModel  = new RuanganModel();
        $this->jamModel      = new JamModel();
        $this->pengampuModel = new PengampuModel();
        $this->jadwalModel   = new JadwalModel();
        $this->konfigModel   = new KonfigurasiModel();
        $this->trackModel    = new TrackModel(); // [BARU] Init Model Track
        
        // Timeout 15 Menit (Proses Genetika Berat)
        set_time_limit(900); 
    }

    // =====================================================================
    // 1. DASHBOARD & CHECK DATA
    // =====================================================================
    public function index()
    {
        // AMBIL INFO TAHUN & SEMESTER AKTIF
        $infoAktif = $this->konfigModel->getInfoAktif();
        $tahun     = $infoAktif['tahun'];
        $semester  = $infoAktif['semester'];
        

        $status = [
            'guru'     => $this->guruModel->countAllResults(),
            'mapel'    => $this->mapelModel->countAllResults(),
            'kelas'    => $this->kelasModel->countAllResults(),
            'ruangan'  => $this->ruanganModel->countAllResults(),
            'jam'      => $this->jamModel->where('is_istirahat', 0)->countAllResults(),
            // Hitung Pengampu HANYA yang sesuai tahun & semester aktif
            'pengampu' => $this->pengampuModel
                            ->where('tahun_ajaran', $tahun)
                            ->where('semester', $semester)
                            ->countAllResults(),
            'jadwal'   => $this->jadwalModel->countAllResults()
        ];

        // Analisa Kapasitas (Filter juga sesuai tahun aktif)
        $totalJamButuh = $this->pengampuModel
                            ->selectSum('jumlah_jam')
                            ->where('tahun_ajaran', $tahun)
                            ->where('semester', $semester)
                            ->first()['jumlah_jam'] ?? 0;

        $totalSlotJam  = $status['jam'];
        $totalRuang    = $status['ruangan'];
        $kapasitas     = $totalSlotJam * $totalRuang;

        $data = [
            'status'    => $status,
            'analisa'   => [
                'butuh' => $totalJamButuh,
                'punya' => $kapasitas,
                'aman'  => $kapasitas >= $totalJamButuh
            ],
            // Kirim info aktif ke view supaya admin tahu
            'tahun_aktif' => $tahun,
            'sem_aktif'   => $semester,
            'status_jadwal' => $this->konfigModel->getStatus()
        ];

        return view('generate/index', $data);
    }

    // =====================================================================
    // 2. CORE ENGINE ALGORITMA GENETIKA (PROCESS)
    // =====================================================================
    public function process()
    {
        // 0. AMBIL INFO TAHUN & SEMESTER AKTIF
        $infoAktif = $this->konfigModel->getInfoAktif();
        $tahun     = $infoAktif['tahun'];
        $semester  = $infoAktif['semester'];

        // A. PERSIAPAN DATA LENGKAP (JOIN SEMUA TABEL)
        // FILTER: Hanya ambil data pengampu untuk tahun & semester ini
        $dataPengampu = $this->pengampuModel
            ->select('pengampu.*, mapel.kelompok, mapel.nama_mapel, kelas.id_ruang_homebase')
            ->join('mapel', 'mapel.id_mapel = pengampu.id_mapel')
            ->join('kelas', 'kelas.id_kelas = pengampu.id_kelas')
            ->where('pengampu.tahun_ajaran', $tahun) 
            ->where('pengampu.semester', $semester)
            ->findAll();

        $dataJam      = $this->jamModel->where('is_istirahat', 0)->orderBy('hari, jam_ke', 'ASC')->findAll();
        $dataRuangan  = $this->ruanganModel->findAll();

        if (empty($dataPengampu)) {
            return redirect()->to('/generate')->with('error', "Data Beban Mengajar untuk Tahun <b>$tahun ($semester)</b> KOSONG. Silakan input data terlebih dahulu.");
        }

        if (empty($dataJam) || empty($dataRuangan)) {
            return redirect()->to('/generate')->with('error', 'Data Jam atau Ruangan belum diisi.');
        }

        // B. ENCODING CERDAS (MEMECAH SKS JADI BLOK)
        // 4 JP -> [2 JP, 2 JP] agar jadwal tidak "Eceran"
        $templateKromosom = $this->encodingCerdas($dataPengampu);

        // C. INISIALISASI POPULASI
        $populasi = $this->inisialisasiPopulasi($templateKromosom, $dataJam, $dataRuangan);
        $solusiDitemukan = false;
        $jadwalTerbaik = null;

        // --- PERSIAPAN HISTORY LOG ---
        $historyLog = [];
        // -----------------------------

        // D. LOOPING EVOLUSI (GENERASI)
        for ($generasi = 1; $generasi <= $this->maxGenerasi; $generasi++) {
            
            // 1. Hitung Fitness dengan Penalti Ketat
            $populasi = $this->hitungFitness($populasi);

            // 2. Urutkan Fitness Terbaik
            usort($populasi, function($a, $b) {
                return $b['fitness'] <=> $a['fitness'];
            });

            $jadwalTerbaik = $populasi[0];

            // --- CATAT LOG UNTUK PERHITUNGAN ---
            // Simpan setiap 10 generasi atau jika sudah selesai atau generasi 1
            if ($generasi == 1 || $generasi % 10 == 0 || $jadwalTerbaik['fitness'] >= 1) {
                // Hitung estimasi konflik (kebalikan dari fitness)
                $estimasiKonflik = ($jadwalTerbaik['fitness'] > 0) ? (1 / $jadwalTerbaik['fitness']) - 1 : 999;
                
                $historyLog[] = [
                    'generasi' => $generasi,
                    'fitness'  => $jadwalTerbaik['fitness'],
                    'konflik'  => round($estimasiKonflik)
                ];
            }
            // ------------------------------------------

            // Jika Fitness 1.0 (Sempurna) -> Stop
            if ($jadwalTerbaik['fitness'] >= 1) {
                $solusiDitemukan = true;
                break;
            }

            // 3. Regenerasi (Elitism + Crossover + Mutasi)
            $populasiBaru = [];
            $populasiBaru[] = $populasi[0]; // Elitism (Juara bertahan selalu ikut)

            while (count($populasiBaru) < $this->populasiSize) {
                $induk1 = $this->seleksiRoulette($populasi);
                $induk2 = $this->seleksiRoulette($populasi);

                // Crossover
                if (rand(0, 100) / 100 <= $this->crossoverRate) {
                    $anak = $this->crossover($induk1, $induk2);
                } else {
                    $anak = $induk1;
                }

                // Mutasi
                $anak = $this->mutasi($anak, $dataJam, $dataRuangan);
                $populasiBaru[] = $anak;
            }

            $populasi = $populasiBaru;
        }

        // --- SIMPAN DATA LOG KE SESSION (CACHE) ---
        session()->set('history_ga', $historyLog);
        if (!empty($jadwalTerbaik['kromosom'])) {
            // Ambil sample 5 gen teratas
            $sampleKromosom = array_slice($jadwalTerbaik['kromosom'], 0, 5);
            session()->set('last_best_chromosom', $sampleKromosom);
        }

        // ============================================================
        // [BARU] SIMPAN REKAMAN PERHITUNGAN KE DATABASE (PERMANEN)
        // ============================================================
        
        // 1. Hapus track lama di periode ini (supaya bersih)
        $this->trackModel->where('tahun_ajaran', $tahun)
                         ->where('semester', $semester)
                         ->delete();

        // 2. Simpan track baru
        // Kita simpan FULL sampel kromosom jika perlu, atau sebagian saja
        // Disini saya simpan sampel 50 item supaya tidak terlalu berat DB nya, tapi cukup untuk demo
        $kromosomSave = !empty($jadwalTerbaik['kromosom']) ? array_slice($jadwalTerbaik['kromosom'], 0, 50) : [];

        $this->trackModel->save([
            'tahun_ajaran'  => $tahun,
            'semester'      => $semester,
            'history_log'   => json_encode($historyLog), 
            'best_kromosom' => json_encode($kromosomSave), 
            'durasi_proses' => date('H:i:s'),
            'created_at'    => date('Y-m-d H:i:s')
        ]);
        // ============================================================

        // E. DECODING & SIMPAN HASIL KE TABEL JADWAL
        $this->simpanJadwal($jadwalTerbaik['kromosom'], $tahun, $semester);

        $msg = $solusiDitemukan 
            ? "Jadwal Sempurna Ditemukan pada Generasi ke-$generasi!" 
            : "Batas Generasi Tercapai. Fitness Terbaik: " . number_format($jadwalTerbaik['fitness'], 4);

        return redirect()->to('/hasil')->with('success', $msg);
    }

    // =====================================================================
    // HELPER FUNCTIONS (OTAK ALGORITMA)
    // =====================================================================

    // 1. ENCODING CERDAS (Grouping JP)
    private function encodingCerdas($dataPengampu)
    {
        $kromosom = [];
        foreach ($dataPengampu as $p) {
            $sisaJam = $p['jumlah_jam'];

            // Pecah SKS menjadi Blok maksimal 2-3 jam
            // Contoh: 4 JP -> 2, 2. | 3 JP -> 3. | 5 JP -> 3, 2.
            while ($sisaJam > 0) {
                $durasiBlok = ($sisaJam >= 4) ? 2 : $sisaJam; // Jika >=4 jam, potong 2. Sisanya ambil semua.
                
                // Jika mapel PRAKTEK/OLAHARAGA, blok harus panjang (minimal 2 atau 3)
                if ($p['kelompok'] != 'Teori' && $sisaJam >= 3) {
                    $durasiBlok = 3; 
                }

                $kromosom[] = [
                    'id_pengampu'     => $p['id_pengampu'],
                    'id_guru'         => $p['id_guru'],
                    'id_kelas'        => $p['id_kelas'],
                    'kelompok'        => $p['kelompok'],        // Teori, Olahraga, Lab
                    'homebase'        => $p['id_ruang_homebase'],// Ruang Kelas Asli
                    'durasi'          => $durasiBlok,           // Berapa jam berturut-turut?
                    // Slot Waktu & Ruang akan diacak
                ];

                $sisaJam -= $durasiBlok;
            }
        }
        return $kromosom;
    }

    // 2. INISIALISASI POPULASI (Acak tapi Terarah)
    private function inisialisasiPopulasi($template, $dataJam, $dataRuangan)
    {
        $populasi = [];
        
        // Cache Ruangan per Jenis untuk mempercepat pencarian
        $ruangByJenis = [];
        foreach($dataRuangan as $r) {
            $ruangByJenis[$r['jenis']][] = $r;
        }

        for ($i = 0; $i < $this->populasiSize; $i++) {
            $individu = ['kromosom' => [], 'fitness' => 0];
            
            foreach ($template as $gen) {
                // A. PILIH RUANGAN YANG SESUAI JENIS MAPEL
                $targetJenis = 'Teori'; // Default
                if ($gen['kelompok'] == 'Olahraga') $targetJenis = 'Lapangan';
                else if ($gen['kelompok'] == 'Praktek Komputer') $targetJenis = 'Lab Komputer';
                else if ($gen['kelompok'] == 'Praktek IPA') $targetJenis = 'Laboratorium';

                // Jika Teori, WAJIB di Homebase Kelas (Fixed Class)
                if ($targetJenis == 'Teori' && !empty($gen['homebase'])) {
                    $gen['id_ruangan'] = $gen['homebase'];
                } else {
                    // Cari ruangan sesuai jenis, kalau habis ambil sembarang (fallback)
                    $listRuang = $ruangByJenis[$targetJenis] ?? $dataRuangan;
                    $ruangAcak = $listRuang[array_rand($listRuang)];
                    $gen['id_ruangan'] = $ruangAcak['id_ruangan'];
                }

                // B. PILIH JAM YANG MUAT UNTUK DURASI BLOK
                // Cari slot jam yang berurutan dalam satu hari
                $validSlot = false;
                while (!$validSlot) {
                    $startIdx = array_rand($dataJam);
                    $startJam = $dataJam[$startIdx];
                    
                    // Cek apakah slot berikutnya masih di hari yang sama?
                    $endIdx = $startIdx + $gen['durasi'] - 1;
                    
                    if (isset($dataJam[$endIdx]) && $dataJam[$endIdx]['hari'] == $startJam['hari']) {
                        // Slot Valid (Tidak nyebrang hari)
                        $gen['idx_jam_mulai'] = $startIdx; // Simpan index array jam
                        $gen['hari']          = $startJam['hari'];
                        $gen['jam_ke']        = $startJam['jam_ke']; // Jam mulai
                        $validSlot = true;
                    }
                }
                
                $individu['kromosom'][] = $gen;
            }
            $populasi[] = $individu;
        }
        return $populasi;
    }

    // 3. HITUNG FITNESS (CRITICAL PART)
    private function hitungFitness($populasi)
    {
        foreach ($populasi as &$individu) {
            $penalti = 0;
            $jadwal = $individu['kromosom'];
            $n = count($jadwal);

            // MATRIKS UNTUK CEK BENTROK CEPAT
            // Format: [hari][jam][guru_id] = true
            $guruSlot = [];
            $kelasSlot = [];
            $ruangSlot = [];

            foreach ($jadwal as $gen) {
                // Loop sepanjang durasi blok (misal 2 jam)
                for ($d = 0; $d < $gen['durasi']; $d++) {
                    // Karena kita simpan index jam, kita bisa hitung jam berikutnya
                    // Asumsi: dataJam diurutkan per hari, jadi index+1 adalah jam berikutnya
                    $currentJamIdx = $gen['idx_jam_mulai'] + $d;
                    
                    // Key Unik untuk Hash Map
                    $key = $currentJamIdx; // Index jam global (0-40)
                    
                    // 1. CEK BENTROK GURU
                    if (isset($guruSlot[$key][$gen['id_guru']])) {
                        $penalti += 10; // Dosa Besar
                    }
                    $guruSlot[$key][$gen['id_guru']] = true;

                    // 2. CEK BENTROK KELAS
                    if (isset($kelasSlot[$key][$gen['id_kelas']])) {
                        $penalti += 10; // Dosa Besar
                    }
                    $kelasSlot[$key][$gen['id_kelas']] = true;

                    // 3. CEK BENTROK RUANGAN
                    if (isset($ruangSlot[$key][$gen['id_ruangan']])) {
                        $penalti += 10; // Dosa Besar
                    }
                    $ruangSlot[$key][$gen['id_ruangan']] = true;
                }
            }

            // Rumus Fitness: Semakin kecil penalti, semakin besar fitness
            $individu['fitness'] = 1.0 / (1.0 + $penalti);
        }
        return $populasi;
    }

    // 4. SELEKSI ROULETTE
    private function seleksiRoulette($populasi)
    {
        $sumFitness = 0;
        foreach ($populasi as $ind) $sumFitness += $ind['fitness'];
        
        $rand = rand(0, 1000) / 1000 * $sumFitness;
        $curr = 0;
        foreach ($populasi as $ind) {
            $curr += $ind['fitness'];
            if ($curr >= $rand) return $ind;
        }
        return $populasi[0];
    }

    // 5. CROSSOVER
    private function crossover($p1, $p2)
    {
        $child = $p1;
        $cutPoint = rand(1, count($p1['kromosom']) - 2);
        
        for ($i = $cutPoint; $i < count($p1['kromosom']); $i++) {
            $child['kromosom'][$i] = $p2['kromosom'][$i];
        }
        return $child;
    }

    // 6. MUTASI (Modifikasi Random)
    private function mutasi($individu, $dataJam, $dataRuangan)
    {
        // Cache Ruangan
        $ruangByJenis = [];
        foreach($dataRuangan as $r) $ruangByJenis[$r['jenis']][] = $r;

        foreach ($individu['kromosom'] as &$gen) {
            if (rand(0, 100) / 100 <= $this->mutationRate) {
                
                // Mutasi Waktu (Cari slot durasi valid)
                $valid = false;
                while (!$valid) {
                    $startIdx = array_rand($dataJam);
                    $endIdx = $startIdx + $gen['durasi'] - 1;
                    if (isset($dataJam[$endIdx]) && $dataJam[$endIdx]['hari'] == $dataJam[$startIdx]['hari']) {
                        $gen['idx_jam_mulai'] = $startIdx;
                        $gen['hari'] = $dataJam[$startIdx]['hari'];
                        $gen['jam_ke'] = $dataJam[$startIdx]['jam_ke'];
                        $valid = true;
                    }
                }

                // Mutasi Ruangan (Sesuai Jenis)
                $targetJenis = 'Teori';
                if ($gen['kelompok'] == 'Olahraga') $targetJenis = 'Lapangan';
                else if ($gen['kelompok'] == 'Praktek Komputer') $targetJenis = 'Lab Komputer';
                else if ($gen['kelompok'] == 'Praktek IPA') $targetJenis = 'Laboratorium';

                if ($targetJenis == 'Teori' && !empty($gen['homebase'])) {
                    $gen['id_ruangan'] = $gen['homebase']; // Kembalikan ke homebase
                } else {
                    $list = $ruangByJenis[$targetJenis] ?? $dataRuangan;
                    $gen['id_ruangan'] = $list[array_rand($list)]['id_ruangan'];
                }
            }
        }
        return $individu;
    }

    // 7. SIMPAN HASIL (DECODING BLOK -> JAM PER JAM)
    private function simpanJadwal($kromosom, $tahun, $semester)
    {
        // PERBAIKAN: JANGAN PAKAI TRUNCATE!
        // Hapus hanya data yang tahun & semesternya sama dengan yang sedang diproses.
        $this->jadwalModel
             ->where('tahun_ajaran', $tahun)
             ->where('semester', $semester)
             ->delete();
        
        $dataJam = $this->jamModel->where('is_istirahat', 0)->orderBy('hari, jam_ke', 'ASC')->findAll();
        
        foreach ($kromosom as $blok) {
            // Blok jadwal punya durasi (misal 2 jam)
            // Kita harus simpan 2 baris data di tabel jadwal
            for ($d = 0; $d < $blok['durasi']; $d++) {
                $idx = $blok['idx_jam_mulai'] + $d;
                $jamInfo = $dataJam[$idx];

                $this->jadwalModel->save([
                    'id_pengampu'  => $blok['id_pengampu'],
                    'hari'         => $jamInfo['hari'],
                    'jam_ke'       => $jamInfo['jam_ke'],
                    'id_ruangan'   => $blok['id_ruangan'],
                    'tahun_ajaran' => $tahun,    // Pastikan ini tersimpan
                    'semester'     => $semester  // Pastikan ini tersimpan
                ]);
            }
        }
        $this->konfigModel->setStatus('draft'); 
    }
    
    // Update juga fungsi reset agar lebih aman
    public function reset()
    {
        // 1. CEK STATUS SAAT INI
        $statusSaatIni = $this->konfigModel->getStatus(); // draft, pending, approved, published

        // 2. GEMBOK PENGAMAN: JIKA SEDANG PUBLISHED, DILARANG RESET
        // Admin harus unpublish dulu lewat menu Hasil agar siswa tidak bingung
        if ($statusSaatIni == 'published') {
            return redirect()->to('/generate')->with('error', '<b>AKSES DITOLAK!</b><br>Jadwal sedang TAYANG (Live).<br>Silakan lakukan <b>"Tarik Jadwal"</b> terlebih dahulu di menu Lihat Hasil.');
        }

        // 3. JIKA AMAN (Draft/Pending/Approved), BARU KITA RESET
        $info = $this->konfigModel->getInfoAktif();
        
        // Hapus Data Jadwal di Database
        $this->jadwalModel
             ->where('tahun_ajaran', $info['tahun'])
             ->where('semester', $info['semester'])
             ->delete();
        
        // 4. RESET STATUS & BERSIHKAN CATATAN
        // Kita ubah jadi 'draft' agar jelas
        $this->konfigModel->setStatus('-'); 
        
        // PENTING: Hapus catatan revisi lama dari Kepsek (jika ada) biar bersih
        $this->konfigModel->clearCatatan(); 

        return redirect()->to('/generate')->with('success', 'Jadwal berhasil direset. Silakan generate ulang.');
    }
}