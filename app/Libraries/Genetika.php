<?php

namespace App\Libraries;

use App\Models\GuruModel;
use App\Models\MapelModel;
use App\Models\KelasModel;
use App\Models\RuanganModel;
use App\Models\JamPelajaranModel;
use App\Models\PengampuModel;

class Genetika
{
    protected $dataGuru;
    protected $dataMapel;
    protected $dataKelas;
    protected $dataRuangan;
    protected $dataJam;
    protected $dataPengampu;

    // Settingan Algoritma
    protected $populasi = 10;      // Jumlah populasi
    protected $maxGenerasi = 1000; // Maksimal percobaan (tambahkan ini)
    protected $mutasiRate = 0.4;

    public function __construct()
    {
        $this->ambilDataDatabase();
    }

    private function ambilDataDatabase()
    {
        $guruModel = new GuruModel();
        $this->dataGuru = $guruModel->findAll();

        $mapelModel = new MapelModel();
        $this->dataMapel = $mapelModel->findAll();

        $kelasModel = new KelasModel();
        $this->dataKelas = $kelasModel->findAll();

        $ruanganModel = new RuanganModel();
        $this->dataRuangan = $ruanganModel->findAll();

        $jamModel = new JamPelajaranModel();
        $this->dataJam = $jamModel->findAll();

        $pengampuModel = new PengampuModel();
        $this->dataPengampu = $pengampuModel->findAll();
    }

    // --- LOGIKA UTAMA 1: INISIALISASI POPULASI ---
    public function inisialisasi()
    {
        // Cek data kosong untuk keamanan
        if (empty($this->dataPengampu) || empty($this->dataJam)) {
            return [];
        }

        $populasiJadwal = [];

        // Buat individu sebanyak settingan $populasi (misal 10)
        for ($i = 0; $i < $this->populasi; $i++) {
            $populasiJadwal[] = $this->generateKromosom();
        }

        return $populasiJadwal;
    }

    // Fungsi pembantu: Membuat 1 Jadwal Acak (Kromosom)
    private function generateKromosom()
    {
        $jadwal = [];

        // Loop setiap tugas mengajar (misal: Pak Budi ajar MTK 2 jam)
        foreach ($this->dataPengampu as $p) {
            
            // Loop sebanyak jumlah jam (kalau 2 jam, kita buat 2 slot jadwal)
            for ($j = 0; $j < $p['jumlah_jam']; $j++) {
                
                // PILIH JAM & RUANGAN SECARA ACAK (RANDOM)
                $jamAcak = $this->dataJam[array_rand($this->dataJam)];
                $ruanganAcak = $this->dataRuangan[array_rand($this->dataRuangan)];

                // Simpan ke array jadwal
                $jadwal[] = [
                    'id_pengampu' => $p['id_pengampu'],
                    'id_guru'     => $p['id_guru'],   // Simpan untuk cek bentrok nanti
                    'id_kelas'    => $p['id_kelas'],  // Simpan untuk cek bentrok nanti
                    'id_jam'      => $jamAcak['id_jam'],
                    'id_ruangan'  => $ruanganAcak['id_ruangan'],
                    
                    // Info tambahan biar enak dibaca saat testing
                    'info'        => 'Guru ID ' . $p['id_guru'] . ' di Kelas ID ' . $p['id_kelas']
                ];
            }
        }
        return $jadwal;
    }
    // --- LOGIKA UTAMA 2: HITUNG FITNESS (Penilai Kualitas) ---
    // Fungsi ini menerima 1 jadwal lengkap, dan menghitung skornya
    public function hitungFitness($jadwal)
    {
        $penalti = 0; // Jumlah bentrok

        // Kita bandingkan setiap slot jadwal dengan slot lainnya
        $jumlahSlot = count($jadwal);

        for ($i = 0; $i < $jumlahSlot; $i++) {
            for ($j = $i + 1; $j < $jumlahSlot; $j++) {
                
                $genA = $jadwal[$i];
                $genB = $jadwal[$j];

                // CEK APAKAH JAMNYA SAMA?
                if ($genA['id_jam'] == $genB['id_jam']) {
                    
                    // KASUS 1: GURU BENTROK
                    // Satu guru mengajar di dua tempat berbeda pada jam yang sama
                    if ($genA['id_guru'] == $genB['id_guru']) {
                        $penalti++;
                    }

                    // KASUS 2: KELAS BENTROK
                    // Satu kelas dapat dua pelajaran pada jam yang sama
                    if ($genA['id_kelas'] == $genB['id_kelas']) {
                        $penalti++;
                    }

                    // KASUS 3: RUANGAN BENTROK
                    // Satu ruangan dipakai dua kelas berbeda
                    if ($genA['id_ruangan'] == $genB['id_ruangan']) {
                         $penalti++;
                    }
                }
            }
        }

        // Rumus Fitness sesuai Proposal Skripsi (1 / 1 + Penalti)
        $nilaiFitness = 1 / (1 + $penalti);

        return [
            'penalti' => $penalti,
            'fitness' => $nilaiFitness
        ];
    }
    // --- LOGIKA UTAMA 3: MUTASI (Mengubah Acak) ---
    private function mutasi($jadwal)
    {
        // Tentukan berapa banyak gen yang mau diubah (misal 10% dari total slot)
        $jumlahGen = count($jadwal);
        $jumlahMutasi = floor($jumlahGen * 0.1); 

        for ($i = 0; $i < $jumlahMutasi; $i++) {
            // Pilih nomor urut gen yang mau diacak
            $indexRandom = rand(0, $jumlahGen - 1);

            // Ganti Jam dan Ruangan dengan yang baru (Acak lagi)
            $jamBaru = $this->dataJam[array_rand($this->dataJam)];
            $ruanganBaru = $this->dataRuangan[array_rand($this->dataRuangan)];

            $jadwal[$indexRandom]['id_jam'] = $jamBaru['id_jam'];
            $jadwal[$indexRandom]['id_ruangan'] = $ruanganBaru['id_ruangan'];
        }
        return $jadwal;
    }
    // --- LOGIKA UTAMA 4: CROSSOVER (Kawin Silang) ---
    private function crossover($induk1, $induk2)
    {
        $titikPotong = rand(1, count($induk1) - 2);
        
        // Ambil bagian awal dari Induk 1
        $bagian1 = array_slice($induk1, 0, $titikPotong);
        // Ambil bagian sisanya dari Induk 2
        $bagian2 = array_slice($induk2, $titikPotong);

        // Gabungkan
        return array_merge($bagian1, $bagian2);
    }
    // --- FUNGSI PEMANGGIL UTAMA (THE ENGINE) ---
    public function jalankan()
    {
        // 1. Buat Populasi Awal
        $populasi = $this->inisialisasi();
        
        $solusiTerbaik = null;
        $fitnessTerbaik = 0;

        // 2. LOOPING GENERASI (Evolusi)
        for ($generasi = 1; $generasi <= $this->maxGenerasi; $generasi++) {
            
            // Hitung Fitness semua individu dalam populasi ini
            $daftarFitness = [];
            foreach ($populasi as $key => $individu) {
                $hasil = $this->hitungFitness($individu);
                $daftarFitness[$key] = $hasil['fitness'];

                // Cek apakah sudah ketemu solusi sempurna (Fitness = 1)?
                if ($hasil['fitness'] == 1) {
                    return [
                        'generasi' => $generasi,
                        'jadwal' => $individu,
                        'fitness' => 1,
                        'pesan' => "Solusi Ditemukan pada Generasi ke-$generasi"
                    ];
                }
            }

            // Cari yang terbaik di generasi ini untuk disimpan (Elitism)
            arsort($daftarFitness); // Urutkan dari nilai tertinggi
            $indexTerbaik = array_key_first($daftarFitness);
            
            $solusiTerbaik = $populasi[$indexTerbaik];
            $fitnessTerbaik = $daftarFitness[$indexTerbaik];

            // --- SELEKSI & REGENERASI ---
            $populasiBaru = [];
            
            // Masukkan 2 individu terbaik langsung ke generasi berikutnya (Elitism)
            $populasiBaru[] = $solusiTerbaik;
            $populasiBaru[] = $populasi[$indexTerbaik]; 

            // Sisanya kita isi dengan anak hasil Crossover & Mutasi
            while (count($populasiBaru) < $this->populasi) {
                // Pilih 2 induk secara acak (Tournament Selection simpel)
                $induk1 = $populasi[array_rand($populasi)];
                $induk2 = $populasi[array_rand($populasi)];

                // Kawinkan
                $anak = $this->crossover($induk1, $induk2);

                // Mutasi (Beri kemungkinan mutasi agar variatif)
                if (rand(0, 100) / 100 <= $this->mutasiRate) {
                    $anak = $this->mutasi($anak);
                }

                $populasiBaru[] = $anak;
            }

            // Ganti populasi lama dengan yang baru
            $populasi = $populasiBaru;
        }

        // Jika sampai maxGenerasi tidak ketemu yang sempurna, kembalikan yang terbaik saat ini
        return [
            'generasi' => $this->maxGenerasi,
            'jadwal' => $solusiTerbaik,
            'fitness' => $fitnessTerbaik,
            'pesan' => "Batas Generasi Habis. Ini hasil terbaik yang ditemukan."
        ];
    }
}