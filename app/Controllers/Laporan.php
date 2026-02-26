<?php

namespace App\Controllers;

use App\Models\GuruModel;
use App\Models\MapelModel;
use App\Models\KelasModel;
use App\Models\RuanganModel;
use App\Models\JamModel;
use App\Models\PengampuModel;
use App\Models\UserModel; // 1. TAMBAHKAN MODEL USER

class Laporan extends BaseController
{
    protected $guruModel;
    protected $mapelModel;
    protected $kelasModel;
    protected $ruanganModel;
    protected $jamModel;
    protected $pengampuModel;
    protected $userModel; // 2. TAMBAHKAN PROPERTI

    public function __construct()
    {
        $this->guruModel     = new GuruModel();
        $this->mapelModel    = new MapelModel();
        $this->kelasModel    = new KelasModel();
        $this->ruanganModel  = new RuanganModel();
        $this->jamModel      = new JamModel();
        $this->pengampuModel = new PengampuModel();
        $this->userModel     = new UserModel(); // 3. INISIALISASI MODEL USER
    }

    public function index()
    {
        return view('laporan/index');
    }

    public function download()
    {
        $jenis  = $this->request->getGet('jenis');  
        $format = $this->request->getGet('format'); 

        $data = [];
        $judul = '';
        $kolom = []; 

        // --- LOGIKA PENGAMBILAN DATA UTAMA ---
        switch ($jenis) {
            case 'guru':
                $data  = $this->guruModel->orderBy('nama_guru', 'ASC')->findAll();
                $judul = 'DATA GURU DAN PEGAWAI';
                $kolom = ['No', 'NIP', 'Nama Guru', 'Tugas Tambahan']; 
                break;

            case 'mapel':
                $data  = $this->mapelModel->orderBy('nama_mapel', 'ASC')->findAll();
                $judul = 'DATA MATA PELAJARAN';
                $kolom = ['No', 'Kode Mapel', 'Nama Mata Pelajaran', 'Kelompok', 'Max JP/Minggu'];
                break;

            case 'kelas':
                $data = $this->kelasModel
                    ->select('kelas.*, guru.nama_guru as wali_kelas, ruangan.nama_ruangan as homebase')
                    ->join('guru', 'guru.id_guru = kelas.id_guru_wali', 'left')
                    ->join('ruangan', 'ruangan.id_ruangan = kelas.id_ruang_homebase', 'left')
                    ->orderBy('kelas.nama_kelas', 'ASC')
                    ->findAll();
                $judul = 'DATA KELAS DAN WALI KELAS';
                $kolom = ['No', 'Nama Kelas', 'Wali Kelas', 'Ruang Homebase', 'Jml Siswa'];
                break;

            case 'ruangan':
                $data  = $this->ruanganModel->orderBy('nama_ruangan', 'ASC')->findAll();
                $judul = 'DATA RUANGAN & FASILITAS';
                $kolom = ['No', 'Nama Ruangan', 'Jenis Ruangan', 'Kapasitas'];
                break;

            case 'jam':
                $data = $this->jamModel->findAll(); 
                $urutanHari = [
                    'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 
                    'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7
                ];
                usort($data, function($a, $b) use ($urutanHari) {
                    $hariA = $urutanHari[$a['hari']] ?? 99; 
                    $hariB = $urutanHari[$b['hari']] ?? 99;
                    if ($hariA == $hariB) return $a['jam_ke'] - $b['jam_ke']; 
                    return $hariA - $hariB; 
                });
                $judul = 'DATA SLOT WAKTU (JAM PELAJARAN)';
                $kolom = ['No', 'Hari', 'Waktu'];
                break;

            case 'pengampu':
                $data = $this->pengampuModel
                    ->select('pengampu.*, guru.nama_guru, mapel.nama_mapel, kelas.nama_kelas')
                    ->join('guru', 'guru.id_guru = pengampu.id_guru')
                    ->join('mapel', 'mapel.id_mapel = pengampu.id_mapel')
                    ->join('kelas', 'kelas.id_kelas = pengampu.id_kelas')
                    ->orderBy('guru.nama_guru', 'ASC')
                    ->findAll();
                $judul = 'LAPORAN BEBAN MENGAJAR (DISTRIBUSI)';
                $kolom = ['No', 'Nama Guru', 'Mata Pelajaran', 'Kelas', 'Jumlah Jam'];
                break;

            default:
                return redirect()->to('/laporan');
        }

        // --- 4. LOGIKA AMBIL NAMA KEPSEK (SMART LOOKUP) ---
        
        $namaKepsek = '.........................';
        $nipKepsek  = '-';

        // A. Ambil dari Tabel User (Prioritas 1: Akun Login)
        $userKepsek = $this->userModel->where('role', 'kepsek')->first();

        if ($userKepsek) {
            $namaKepsek = $userKepsek['nama_lengkap'];

            // B. Cari NIP di Tabel Guru berdasarkan Nama dari User
            $dataGuru = $this->guruModel->where('nama_guru', $namaKepsek)->first();
            
            if ($dataGuru) {
                // Jika ketemu, ambil NIP-nya
                $nipKepsek = $dataGuru['nip'];
            } else {
                // C. (Fallback) Jika nama tidak cocok, cari Guru yang jabatannya 'Kepala Sekolah'
                $guruJabat = $this->guruModel->where('tugas_tambahan', 'Kepala Sekolah')->first();
                if ($guruJabat) {
                    $namaKepsek = $guruJabat['nama_guru']; // Pakai nama resmi dari tabel guru
                    $nipKepsek  = $guruJabat['nip'];
                }
            }
        } else {
            // D. Jika user kepsek belum dibuat, cari langsung di tabel Guru
            $guruJabat = $this->guruModel->where('tugas_tambahan', 'Kepala Sekolah')->first();
            if ($guruJabat) {
                $namaKepsek = $guruJabat['nama_guru'];
                $nipKepsek  = $guruJabat['nip'];
            }
        }
        
        // --- END LOGIKA KEPSEK ---

        $payload = [
            'judul'       => $judul,
            'data'        => $data,
            'kolom'       => $kolom,
            'jenis'       => $jenis,
            // Kirim Data Kepsek ke View
            'nama_kepsek' => $namaKepsek,
            'nip_kepsek'  => $nipKepsek
        ];
        if ($format == 'excel') {
            $filename = "Laporan_" . str_replace(' ', '_', $judul) . "_" . date('Ymd') . ".xls";
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            return view('laporan/template_excel', $payload);
        }

        return view('laporan/template_print', $payload);
    }
}