<?php

namespace App\Controllers;

use App\Models\JadwalModel;
use App\Models\KelasModel;
use App\Models\JamModel;
use App\Models\KonfigurasiModel;
use App\Models\UserModel;

class Hasil extends BaseController
{
    protected $jadwalModel;
    protected $kelasModel;
    protected $jamModel;
    protected $konfigModel;
    protected $userModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalModel();
        $this->kelasModel  = new KelasModel();
        $this->jamModel    = new JamModel();
        $this->konfigModel = new KonfigurasiModel();
        $this->userModel   = new UserModel();
    }

    public function index()
    {
        // 1. Ambil Info Penting (Status, Tahun, Semester)
        // Kita panggil getInfoAktif agar tahu ini jadwal kapan
        $infoAktif    = $this->konfigModel->getInfoAktif();
        $statusJadwal = $this->konfigModel->getStatus();

        // 2. Ambil Data Jadwal Mentah
        // Filter berdasarkan Tahun Aktif (Opsional: Jika getJadwalLengkap sudah filter, aman)
        // Kalau belum, method getJadwalLengkap perlu update filter juga
        $jadwalRaw = $this->jadwalModel->getJadwalLengkap(); 
        
        $dataKelas = $this->kelasModel->orderBy('nama_kelas', 'ASC')->findAll();
        $dataJam   = $this->jamModel->orderBy('jam_ke', 'ASC')->findAll();

        // 3. Grouping Data Matriks
        $jadwalGrid = [];
        foreach ($jadwalRaw as $row) {
            $jadwalGrid[$row['nama_kelas']][$row['hari']][$row['jam_ke']] = [
                'mapel'      => $row['nama_mapel'],
                'kode_mapel' => $row['kode_mapel'],
                'guru'       => $row['nama_guru'],
                'ruang'      => $row['nama_ruangan'],
                'warna'      => $this->getColorByMapel($row['nama_mapel'])
            ];
        }

        // 4. Susun Info Waktu
        $maxJam = $this->jamModel->selectMax('jam_ke')->first()['jam_ke'] ?? 9;
        $jamIstirahat = [];
        $waktuJam = []; 

        foreach($dataJam as $j) {
            $rangeWaktu = date('H:i', strtotime($j['waktu_mulai'])) . ' - ' . date('H:i', strtotime($j['waktu_selesai']));
            if($j['is_istirahat'] == 1) {
                $jamIstirahat[$j['jam_ke']] = $rangeWaktu;
            } else {
                $waktuJam[$j['jam_ke']] = $rangeWaktu;
            }
        }

        // 5. Data Kepsek
        $userKepsek = $this->userModel->where('role', 'kepsek')->first();
        $namaKepsek = $userKepsek ? $userKepsek['nama_lengkap'] : 'Kepala Sekolah';
        $nipKepsek  = '-'; 

        $data = [
            'judul'         => 'Hasil Penjadwalan', // Tambahan
            'data_kelas'    => $dataKelas,
            'jadwal'        => $jadwalGrid,
            'max_jam'       => $maxJam,
            'hari_list'     => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            'istirahat'     => $jamIstirahat,
            'waktu_jam'     => $waktuJam,
            // Info Status & Periode
            'status_jadwal' => $statusJadwal,
            'tahun_ajaran'  => $infoAktif['tahun'],
            'semester'      => $infoAktif['semester'],
            // Data Kepsek
            'nama_kepsek'   => $namaKepsek,
            'nip_kepsek'    => $nipKepsek
        ];

        return view('hasil/index', $data);
    }

    // --- FITUR WORKFLOW ---
    
    public function ajukan() {
        $this->konfigModel->setStatus('pending');
        return redirect()->to('/hasil')->with('success', 'Jadwal berhasil diajukan ke Kepala Sekolah.');
    }

    public function batal_ajukan() {
        // Balik ke Draft jika admin salah klik
        $this->konfigModel->setStatus('draft');
        return redirect()->to('/hasil')->with('success', 'Pengajuan dibatalkan. Jadwal kembali ke status Draft.');
    }

    // 3. KEPSEK MENYETUJUI (APPROVE)
    public function approve()
    {
        // Validasi: Pastikan yang klik adalah Kepsek atau Admin (jaga-jaga)
        if(session()->get('role') != 'kepsek' && session()->get('role') != 'admin') {
             return redirect()->to('/hasil')->with('error', 'Anda tidak memiliki wewenang.');
        }
        
        $this->konfigModel->setStatus('approved');
        $this->konfigModel->clearCatatan();
        return redirect()->to('/hasil')->with('success', 'Terima kasih. Jadwal telah DISETUJUI dan siap diterbitkan oleh Admin.');
    }

   public function reset_status()
    {
        $role = session()->get('role');
        
        // 1. TANGKAP ALASAN DARI URL
        $alasan = $this->request->getGet('alasan'); 

        // 2. UBAH STATUS JADI DRAFT
        $this->konfigModel->setStatus('draft');
        
        // 3. JIKA KEPSEK MENOLAK & ADA ALASAN, SIMPAN KE DATABASE
        if($role == 'kepsek' && !empty($alasan)) {
            $this->konfigModel->setCatatan($alasan); // Panggil fungsi di Model
            $pesan = 'Jadwal ditolak. Catatan revisi telah dikirim ke Admin.';
        } else {
            // Kalau Admin yang klik reset manual
            $pesan = 'Status jadwal direset ke Draft.';
        }

        return redirect()->to('/hasil')->with('success', $pesan);
    }

    // 5. ADMIN PUBLISH (LIVE)
    public function publish()
    {
        // Logika pindah ke arsip ada di controller Arsip, 
        // tapi di sini kita ubah status config-nya saja
        $this->konfigModel->setStatus('published');
        // Redirectnya biasanya diarahkan ke proses arsip dulu baru ke hasil
        // Tapi kalau mau simpel ubah status aja:
        return redirect()->to('/hasil')->with('success', 'Jadwal berhasil DITERBITKAN (Go Live).');
    }

    public function batal_publish() {
        // Jika batal publish, kembalikan ke Approved (bukan Draft, karena sudah disetujui)
        $this->konfigModel->setStatus('approved');
        return redirect()->to('/hasil')->with('success', 'Jadwal ditarik dari peredaran (Unpublish). Status kembali Approved.');
    }

    // Helper Warna
    private function getColorByMapel($namaMapel) {
        $colors = ['#e3f2fd', '#e8f5e9', '#fff3e0', '#f3e5f5', '#e0f7fa', '#fce4ec', '#f1f8e9', '#fff8e1'];
        $index = crc32($namaMapel) % count($colors);
        return $colors[$index];
    }
}