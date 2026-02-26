<?php

namespace App\Controllers;

use App\Models\GuruModel;
use App\Models\MapelModel;
use App\Models\KelasModel;
use App\Models\RuanganModel;
use App\Models\JamModel;
use App\Models\PengampuModel;
use App\Models\KonfigurasiModel;

class TesGenetika extends BaseController
{
    // Kita panggil model manual disini agar independen
    protected $guruModel;
    protected $mapelModel;
    protected $kelasModel;
    protected $ruanganModel;
    protected $jamModel;
    protected $pengampuModel;
    protected $konfigModel;

    public function __construct()
    {
        $this->guruModel     = new GuruModel();
        $this->mapelModel    = new MapelModel();
        $this->kelasModel    = new KelasModel();
        $this->ruanganModel  = new RuanganModel();
        $this->jamModel      = new JamModel();
        $this->pengampuModel = new PengampuModel();
        $this->konfigModel   = new KonfigurasiModel();
    }

    public function index()
    {
        // Respon Default
        $response = [
            'status' => 'error',
            'title'  => 'Gagal',
            'text'   => 'Terjadi kesalahan tidak diketahui.',
            'icon'   => 'error'
        ];

        try {
            // 1. AMBIL INFO PERIODE AKTIF
            $info = $this->konfigModel->getInfoAktif();
            $tahun = $info['tahun'];
            $sem   = $info['semester'];

            // 2. CEK KONEKSI & DATA MASTER
            $jmlGuru  = $this->guruModel->countAllResults();
            $jmlMapel = $this->mapelModel->countAllResults();
            $jmlKelas = $this->kelasModel->countAllResults();
            $jmlRuang = $this->ruanganModel->countAllResults();
            $jmlJam   = $this->jamModel->where('is_istirahat', 0)->countAllResults();

            if ($jmlGuru == 0 || $jmlMapel == 0 || $jmlKelas == 0 || $jmlRuang == 0) {
                throw new \Exception("Data Master (Guru, Mapel, Kelas, atau Ruangan) masih ada yang kosong. Harap lengkapi Data Master terlebih dahulu.");
            }

            if ($jmlJam == 0) {
                throw new \Exception("Slot Jam Pelajaran belum diatur. Silakan atur di menu Data Jam.");
            }

            // 3. CEK BEBAN MENGAJAR (SESUAI PERIODE)
            $beban = $this->pengampuModel
                ->where('tahun_ajaran', $tahun)
                ->where('semester', $sem)
                ->countAllResults();

            if ($beban == 0) {
                throw new \Exception("Belum ada data Beban Mengajar untuk periode aktif ($tahun - $sem). Silakan input atau 'Salin Data' di menu Beban Mengajar.");
            }

            // 4. SIMULASI LOGIKA (CEK PENGAMPU VALID)
            // Cek apakah ada mapel yang jamnya melebihi slot harian (validasi logika)
            // Misal slot per hari cuma 8, tapi ada mapel 10 jam (mustahil)
            // (Ini contoh validasi cerdas)
            
            // Jika sampai sini, berarti aman
            $response = [
                'status' => 'success',
                'title'  => 'Mesin Sehat!',
                'text'   => "Diagnostik Berhasil.\n\n" .
                            "✅ Database Terhubung\n" .
                            "✅ Data Master Lengkap\n" .
                            "✅ Ditemukan $beban Data Beban Mengajar ($tahun $sem)\n" .
                            "✅ Siap melakukan Generate Jadwal.",
                'icon'   => 'success'
            ];

        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'title'  => 'Masalah Ditemukan!',
                'text'   => $e->getMessage(),
                'icon'   => 'warning'
            ];
        }

        return $this->response->setJSON($response);
    }
}