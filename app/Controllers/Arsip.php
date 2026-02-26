<?php

namespace App\Controllers;

use App\Models\JadwalModel;
use App\Models\KonfigurasiModel;
use CodeIgniter\Controller;

class Arsip extends Controller
{
    protected $jadwalModel;
    protected $konfigModel;
    protected $db;

    public function __construct()
    {
        $this->jadwalModel = new JadwalModel();
        $this->konfigModel = new KonfigurasiModel();
        $this->db = \Config\Database::connect();
    }

    // [UPDATE] MENAMPILKAN STATUS AKTIF
    public function index()
    {
        $listArsip = $this->db->table('riwayat_jadwal')
            ->select('tahun_ajaran, semester, MAX(tgl_diarsipkan) as tgl_diarsipkan, COUNT(*) as jumlah_slot')
            ->groupBy('tahun_ajaran, semester')
            ->orderBy('tgl_diarsipkan', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title'           => 'Riwayat Jadwal',
            'list_arsip'      => $listArsip,
            'info_aktif'      => $this->konfigModel->getInfoAktif(),
            
            // [TAMBAHAN] Kirim status konfigurasi saat ini
            'status_saat_ini' => $this->konfigModel->getStatus() 
        ];

        return view('arsip/index', $data);
    }

    // [KODE LAMA BAPAK] FUNGSI "2 IN 1": SIMPAN PERMANEN & PUBLISH
    public function publish()
    {
        $info  = $this->konfigModel->getInfoAktif();
        $tahun = $info['tahun'];
        $sem   = $info['semester'];

        $jadwalAktif = $this->jadwalModel->findAll();
        if (empty($jadwalAktif)) {
            return redirect()->to('/hasil')->with('error', 'Gagal Publish! Jadwal masih kosong.');
        }

        // Cek Double Arsip
        $cekArsip = $this->db->table('riwayat_jadwal')
                        ->where('tahun_ajaran', $tahun)
                        ->where('semester', $sem)
                        ->countAllResults();

        if ($cekArsip > 0) {
            // Arahkan user untuk menghapus dulu di menu Arsip
            return redirect()->to('/hasil')->with('error', "Gagal! Jadwal untuk $tahun ($sem) sudah pernah di-publish. Silakan hapus data di menu Riwayat Jadwal jika ingin mempublish ulang.");
        }

        // EKSEKUSI
        $this->db->transStart();

        $dataArsip = [];
        foreach ($jadwalAktif as $row) {
            $dataArsip[] = [
                'id_pengampu'    => $row['id_pengampu'],
                'id_ruangan'     => $row['id_ruangan'],
                'hari'           => $row['hari'],
                'jam_ke'         => $row['jam_ke'],
                'tahun_ajaran'   => $tahun,
                'semester'       => $sem,
                'tgl_diarsipkan' => date('Y-m-d H:i:s')
            ];
        }
        $this->db->table('riwayat_jadwal')->insertBatch($dataArsip);
        $this->konfigModel->setStatus('published');

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return redirect()->to('/hasil')->with('error', 'Terjadi kesalahan sistem.');
        } else {
            return redirect()->to('/hasil')->with('success', "SELAMAT! Jadwal berhasil DI-PUBLISH ke Website Siswa.");
        }
    }

    // [FIXED] FUNGSI DELETE YANG PASTI BERHASIL
    public function delete(...$params)
    {
        // 1. PROTEKSI: HANYA ADMIN
        if (session()->get('role') != 'admin') {
            return redirect()->to('/arsip')->with('error', 'Akses Ditolak.');
        }

        // 2. GABUNGKAN SEMUA PARAMETER URL JADI SATU STRING
        // Contoh params masuk: ['2025', '2026', 'Genap']
        // Jika di URL arsip/delete/2025/2026/Genap
        
        // Ambil elemen terakhir sebagai Semester
        $semester = array_pop($params); // Mengambil "Genap"
        
        // Sisanya digabung kembali dengan garis miring sebagai Tahun
        $tahun = implode('/', $params); // Menggabungkan "2025" dan "2026" jadi "2025/2026"

        // Debugging (Opsional: Kalau mau cek hasilnya, uncomment baris bawah ini)
        // dd($tahun, $semester); 

        // 3. HAPUS DATA (Pastikan urutan where benar)
        $this->db->table('riwayat_jadwal')
                 ->where('tahun_ajaran', $tahun)
                 ->where('semester', $semester)
                 ->delete();

        // 4. RESET STATUS JIKA PERIODE AKTIF
        $info = $this->konfigModel->getInfoAktif();
        if($info['tahun'] == $tahun && $info['semester'] == $semester) {
            $this->konfigModel->setStatus('approved'); 
        }

        return redirect()->to('/arsip')->with('success', "Data arsip $tahun ($semester) berhasil dihapus permanen.");
    }
}