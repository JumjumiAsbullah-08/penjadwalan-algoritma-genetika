<?php

namespace App\Controllers;

use App\Models\PengampuModel;
use App\Models\GuruModel;
use App\Models\MapelModel;
use App\Models\KelasModel;
use App\Models\JadwalModel;
use App\Models\KonfigurasiModel; // TAMBAHAN 1: Load Model Konfigurasi

class Pengampu extends BaseController
{
    protected $pengampuModel;
    protected $guruModel;
    protected $mapelModel;
    protected $kelasModel;
    protected $jadwalModel;
    protected $konfigModel; // TAMBAHAN 2: Properti Model

    public function __construct()
    {
        $this->pengampuModel = new PengampuModel();
        $this->guruModel     = new GuruModel();
        $this->mapelModel    = new MapelModel();
        $this->kelasModel    = new KelasModel();
        $this->jadwalModel   = new JadwalModel();
        $this->konfigModel   = new KonfigurasiModel(); // TAMBAHAN 3: Init
    }

    public function index()
    {
        // 1. AMBIL INFO TAHUN & SEMESTER AKTIF (TARGET)
        $info    = $this->konfigModel->getInfoAktif();
        $tahun   = $info['tahun'];
        $sem     = $info['semester'];

        $is_locked = $this->jadwalModel->countAllResults() > 0;
        
        // 2. DATA UTAMA (TAMPILKAN SESUAI TAHUN AKTIF)
        $dataPengampu = $this->pengampuModel
            ->select('pengampu.*, guru.nama_guru, guru.nip, guru.tugas_tambahan, mapel.nama_mapel, mapel.kode_mapel, kelas.nama_kelas')
            ->join('guru', 'guru.id_guru = pengampu.id_guru')
            ->join('mapel', 'mapel.id_mapel = pengampu.id_mapel')
            ->join('kelas', 'kelas.id_kelas = pengampu.id_kelas')
            ->where('pengampu.tahun_ajaran', $tahun)
            ->where('pengampu.semester', $sem)
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->orderBy('mapel.nama_mapel', 'ASC')
            ->findAll();

        // 3. LOGIKA SUMBER DATA (CEK TABEL PENGAMPU) - PERBAIKAN DI SINI
        // Ambil daftar tahun unik yang SUDAH PERNAH DIINPUT di database
        $riwayatDB = $this->pengampuModel
            ->select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'DESC')
            ->findAll();
            
        // Validasi: Jika database kosong (belum pernah input sama sekali),
        // Kita isi dengan tahun aktif supaya dropdown tidak kosong melompong (fallback).
        if (empty($riwayatDB)) {
            $riwayatDB[] = ['tahun_ajaran' => $tahun];
        }

        // --- (Bagian Hitung Beban Kerja tetap sama) ---
        foreach ($dataPengampu as &$p) {
             $jamTatapMuka = $this->pengampuModel
                ->where('id_guru', $p['id_guru'])
                ->where('tahun_ajaran', $tahun)
                ->where('semester', $sem)
                ->selectSum('jumlah_jam')
                ->first()['jumlah_jam'] ?? 0;
             
             $ekuivalensi = 0;
             if (in_array($p['tugas_tambahan'], ['Waka', 'Kepala Lab', 'Kepala Perpus'])) $ekuivalensi += 12; 
             elseif ($p['tugas_tambahan'] == 'Kepala Sekolah') $ekuivalensi += 24; 
             
             $isWaliKelas = $this->kelasModel->where('id_guru_wali', $p['id_guru'])->countAllResults();
             if ($isWaliKelas > 0) $ekuivalensi += 2; 

             $p['jam_tatap_muka'] = $jamTatapMuka;
             $p['jam_ekuivalensi'] = $ekuivalensi;
             $p['total_beban_kerja'] = $jamTatapMuka + $ekuivalensi;
             $p['status_sertifikasi'] = ($p['total_beban_kerja'] >= 24) ? 'AMAN' : 'KURANG';
        }

        $data = [
            'daftar_pengampu' => $dataPengampu,
            'data_guru'       => $this->guruModel->orderBy('nama_guru', 'ASC')->findAll(),
            'data_mapel'      => $this->mapelModel->orderBy('nama_mapel', 'ASC')->findAll(),
            'data_kelas'      => $this->kelasModel->orderBy('nama_kelas', 'ASC')->findAll(),
            'is_locked'       => $is_locked,
            'tahun_aktif'     => $tahun,
            'sem_aktif'       => $sem,
            
            // KIRIM DATA SUMBER NYATA KE VIEW
            'list_sumber_tahun' => $riwayatDB 
        ];

        return view('pengampu/index', $data);
    }

    public function store()
    {
        if ($this->jadwalModel->countAllResults() > 0) return redirect()->to('/pengampu')->with('error', 'Terkunci.');

        // AMBIL INFO AKTIF
        $info = $this->konfigModel->getInfoAktif();

        // VALIDASI INPUT
        // Kita terima array id_kelas[] dari checkbox
        if (!$this->validate([
            'id_guru' => 'required', 
            'id_mapel' => 'required', 
            // 'id_kelas' => 'required', // Bisa array jadi validasi manual saja
            'jumlah_jam' => 'required|integer'
        ])) { return redirect()->to('/pengampu')->with('error', 'Data tidak valid.'); }

        $guru  = $this->request->getVar('id_guru');
        $mapel = $this->request->getVar('id_mapel');
        $kelas = $this->request->getVar('id_kelas'); // Ini Array
        $jam   = $this->request->getVar('jumlah_jam');

        // Pastikan kelas terpilih
        if(empty($kelas)) {
            return redirect()->to('/pengampu')->with('error', 'Harap pilih minimal satu kelas.');
        }

        // VALIDASI 2: CEK KURIKULUM
        $dataMapel = $this->mapelModel->find($mapel);
        if ($dataMapel['max_jam_per_minggu'] > 0 && $jam > $dataMapel['max_jam_per_minggu']) {
            return redirect()->to('/pengampu')->with('error', "<b>Melanggar Kurikulum!</b><br>Maksimal jam untuk " . $dataMapel['nama_mapel'] . " adalah " . $dataMapel['max_jam_per_minggu'] . " JP.");
        }

        // PROSES SIMPAN BANYAK KELAS
        $batchData = [];
        $duplicateCount = 0;

        foreach($kelas as $kID) {
            // Cek Duplicate per kelas
            $exists = $this->pengampuModel
                ->where('id_kelas', $kID)
                ->where('id_mapel', $mapel)
                ->where('tahun_ajaran', $info['tahun']) // Cek tahun
                ->where('semester', $info['semester'])  // Cek semester
                ->first();

            if(!$exists) {
                $batchData[] = [
                    'id_guru'      => $guru,
                    'id_mapel'     => $mapel,
                    'id_kelas'     => $kID,
                    'jumlah_jam'   => $jam,
                    // AUTO ISI TAHUN & SEMESTER
                    'tahun_ajaran' => $info['tahun'],
                    'semester'     => $info['semester']
                ];
            } else {
                $duplicateCount++;
            }
        }

        if(!empty($batchData)) {
            $this->pengampuModel->insertBatch($batchData);
            $msg = "Berhasil menyimpan " . count($batchData) . " data.";
            if($duplicateCount > 0) $msg .= " ($duplicateCount data dilewati karena duplikat).";
            return redirect()->to('/pengampu')->with('success', $msg);
        } else {
            return redirect()->to('/pengampu')->with('error', 'Semua data yang dipilih sudah ada (Duplikat).');
        }
    }

    public function update()
    {
        if ($this->jadwalModel->countAllResults() > 0) {
            return redirect()->to('/pengampu')->with('error', 'Data terkunci.');
        }

        // AMBIL INFO AKTIF
        $info = $this->konfigModel->getInfoAktif();

        if (!$this->validate([
            'id_pengampu' => 'required',
            'id_guru' => 'required',
            'id_mapel' => 'required',
            'id_kelas' => 'required',
            'jumlah_jam' => 'required|integer'
        ])) {
            return redirect()->to('/pengampu')->with('error', 'Data input tidak lengkap.');
        }

        $id = $this->request->getVar('id_pengampu');
        $guru  = $this->request->getVar('id_guru');
        $mapel = $this->request->getVar('id_mapel');
        $kelas = $this->request->getVar('id_kelas');
        $jam = $this->request->getVar('jumlah_jam');

        // VALIDASI 1: DUPLICATE ENTRY (DENGAN TAHUN AKTIF)
        $exists = $this->pengampuModel
            ->where('id_kelas', $kelas)
            ->where('id_mapel', $mapel)
            ->where('id_pengampu !=', $id)
            ->where('tahun_ajaran', $info['tahun']) // Cek tahun
            ->where('semester', $info['semester'])  // Cek semester
            ->first();

        if ($exists) {
            return redirect()->to('/pengampu')->with('error', '<b>Gagal Update!</b> Mapel tersebut sudah ada pengajarnya di kelas ini.');
        }

        // VALIDASI 2: CEK KURIKULUM
        $dataMapel = $this->mapelModel->find($mapel);
        if ($dataMapel && $dataMapel['max_jam_per_minggu'] > 0) {
            if ($jam > $dataMapel['max_jam_per_minggu']) {
                return redirect()->to('/pengampu')->with('error', "<b>Melanggar Aturan Kurikulum!</b><br>Maksimal jam mengajar untuk mapel <b>" . $dataMapel['nama_mapel'] . "</b> adalah <b>" . $dataMapel['max_jam_per_minggu'] . " JP</b>.");
            }
        }

        // UPDATE (Tahun & Semester TIDAK diubah saat edit, tetap ikut data aslinya atau bisa dipaksa ikut aktif)
        // Disini amannya kita update data intinya saja.
        $this->pengampuModel->save([
            'id_pengampu' => $id,
            'id_guru'     => $guru,
            'id_mapel'    => $mapel,
            'id_kelas'    => $kelas,
            'jumlah_jam'  => $jam
        ]);

        return redirect()->to('/pengampu')->with('success', 'Data berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ($this->jadwalModel->countAllResults() > 0) return redirect()->to('/pengampu')->with('error', 'Terkunci.');
        
        $this->pengampuModel->delete($id);
        return redirect()->to('/pengampu')->with('success', 'Data dihapus.');
    }

    public function deleteMultiple()
    {
        if ($this->jadwalModel->countAllResults() > 0) return redirect()->to('/pengampu')->with('error', 'Terkunci.');

        $ids = $this->request->getVar('ids');
        if ($ids) {
            $this->pengampuModel->whereIn('id_pengampu', $ids)->delete();
            return redirect()->to('/pengampu')->with('success', count($ids) . ' Data berhasil dihapus.');
        }
        return redirect()->to('/pengampu');
    }

    // --- FITUR BARU: SALIN DATA (IMPORT) ---
    public function salinData()
    {
        // 1. Target = Konfigurasi Aktif
        $infoTarget = $this->konfigModel->getInfoAktif();
        $targetTahun = $infoTarget['tahun'];
        $targetSem   = $infoTarget['semester'];

        // 2. Sumber = Inputan User
        $sumberTahun = $this->request->getPost('sumber_tahun');
        $sumberSem   = $this->request->getPost('sumber_semester');

        // Validasi
        if ($sumberTahun == $targetTahun && $sumberSem == $targetSem) {
            return redirect()->to('/pengampu')->with('error', 'Gagal Salin! Sumber dan Target tidak boleh sama.');
        }

        // 3. Cek apakah di Target sudah ada data?
        $cekAda = $this->pengampuModel
            ->where('tahun_ajaran', $targetTahun)
            ->where('semester', $targetSem)
            ->countAllResults();

        if ($cekAda > 0) {
            // Opsional: Bisa redirect error, atau lanjut menimpa.
            // Amannya kita minta user kosongkan dulu manual.
            return redirect()->to('/pengampu')->with('error', "Gagal Salin! Data tahun aktif ($targetTahun - $targetSem) TIDAK KOSONG. Silakan hapus data lama terlebih dahulu.");
        }

        // 4. Ambil Data Sumber
        $dataSumber = $this->pengampuModel
            ->where('tahun_ajaran', $sumberTahun)
            ->where('semester', $sumberSem)
            ->findAll();

        if (empty($dataSumber)) {
            return redirect()->to('/pengampu')->with('error', "Data sumber ($sumberTahun - $sumberSem) Kosong/Tidak Ditemukan.");
        }

        // 5. Proses Salin
        $dataBaru = [];
        foreach ($dataSumber as $row) {
            $dataBaru[] = [
                'id_guru'      => $row['id_guru'],
                'id_mapel'     => $row['id_mapel'],
                'id_kelas'     => $row['id_kelas'],
                'jumlah_jam'   => $row['jumlah_jam'],
                // GANTI DENGAN TAHUN AKTIF
                'tahun_ajaran' => $targetTahun,
                'semester'     => $targetSem
            ];
        }

        $this->pengampuModel->insertBatch($dataBaru);

        return redirect()->to('/pengampu')->with('success', "Sukses menyalin " . count($dataBaru) . " data beban mengajar.");
    }

    // --- FITUR BARU: KOSONGKAN DATA AKTIF ---
    // (Bisa dipanggil via tombol Kosongkan jika user mau reset tahun aktif)
    public function kosongkanData()
    {
        $info = $this->konfigModel->getInfoAktif();
        
        $this->pengampuModel
            ->where('tahun_ajaran', $info['tahun'])
            ->where('semester', $info['semester'])
            ->delete();

        return redirect()->to('/pengampu')->with('success', "Data beban mengajar tahun aktif berhasil dikosongkan.");
    }
}