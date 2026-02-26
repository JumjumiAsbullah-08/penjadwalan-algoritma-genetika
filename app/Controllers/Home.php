<?php

namespace App\Controllers;

use App\Models\JadwalModel;
use App\Models\KelasModel;
use App\Models\GuruModel;
use App\Models\JamModel;
use App\Models\KonfigurasiModel;

class Home extends BaseController
{
    protected $jadwalModel;
    protected $kelasModel;
    protected $guruModel;
    protected $jamModel;
    protected $konfigModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalModel();
        $this->kelasModel  = new KelasModel();
        $this->guruModel   = new GuruModel();
        $this->jamModel    = new JamModel();
        $this->konfigModel = new KonfigurasiModel();
    }

    public function index()
    {
        // 1. AMBIL KONFIGURASI AKTIF (PENTING)
        $info = $this->konfigModel->getInfoAktif();
        $tahunAktif = $info['tahun'];
        $semAktif   = $info['semester'];
        
        // Cek Status Publish
        $statusJadwal = $this->konfigModel->getStatus();
        
        // Data dasar untuk Dropdown Filter & View
        $data = [
            'title'         => 'Jadwal Pelajaran Digital',
            'tahun_aktif'   => $tahunAktif, // Kirim ke View (Agar Header Berubah)
            'sem_aktif'     => $semAktif,   // Kirim ke View (Agar Header Berubah)
            'status_jadwal' => $statusJadwal,
            'list_kelas'    => $this->kelasModel->orderBy('nama_kelas', 'ASC')->findAll(),
            'list_guru'     => $this->guruModel->orderBy('nama_guru', 'ASC')->findAll(),
            'filter_type'   => null, 
            'selected_id'   => null,
            'jadwal_tampil' => [],
            'info_target'   => '',   
            'hari_list'     => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            'waktu_jam'     => $this->getWaktuJam(), // Pastikan method ini ada di controller
            'istirahat'     => $this->getIstirahat(), // Pastikan method ini ada di controller
            'max_jam'       => 9
        ];

        // 2. Logika Filter (Hanya jalan jika status PUBLISHED atau Admin Login)
        $is_preview = session()->get('logged_in'); 

        if ($statusJadwal == 'published' || $is_preview) {
            
            $filterKelas = $this->request->getGet('kelas');
            $filterGuru  = $this->request->getGet('guru');

            // --- QUERY PENTING: FILTER BERDASARKAN TAHUN AKTIF ---
            // Kita tidak pakai getJadwalLengkap() mentah agar data tahun lalu tidak ikut
            $this->jadwalModel
                ->select('jadwal.*, pengampu.id_guru, pengampu.id_kelas, guru.nama_guru, kelas.nama_kelas, mapel.nama_mapel, ruangan.nama_ruangan')
                ->join('pengampu', 'pengampu.id_pengampu = jadwal.id_pengampu')
                ->join('guru', 'guru.id_guru = pengampu.id_guru')
                ->join('mapel', 'mapel.id_mapel = pengampu.id_mapel')
                ->join('kelas', 'kelas.id_kelas = pengampu.id_kelas')
                ->join('ruangan', 'ruangan.id_ruangan = jadwal.id_ruangan')
                // FILTER INI WAJIB ADA:
                ->where('jadwal.tahun_ajaran', $tahunAktif)
                ->where('jadwal.semester', $semAktif);

            // Eksekusi Query
            $rawJadwal = $this->jadwalModel->findAll();
            $hasilFilter = [];

            // A. JIKA FILTER KELAS
            if (!empty($filterKelas)) {
                $data['filter_type'] = 'kelas';
                $data['selected_id'] = $filterKelas;
                
                $kelasInfo = $this->kelasModel->find($filterKelas);
                $data['info_target'] = $kelasInfo ? $kelasInfo['nama_kelas'] : '';

                foreach ($rawJadwal as $row) {
                    if ($row['id_kelas'] == $filterKelas) {
                        $hasilFilter[$row['hari']][$row['jam_ke']] = [
                            'mapel' => $row['nama_mapel'],
                            'guru'  => $row['nama_guru'], 
                            'ruang' => $row['nama_ruangan'],
                            'warna' => $this->getColor($row['nama_mapel']) // Pastikan method ini ada
                        ];
                    }
                }
            }
            // B. JIKA FILTER GURU
            elseif (!empty($filterGuru)) {
                $data['filter_type'] = 'guru';
                $data['selected_id'] = $filterGuru;

                $guruInfo = $this->guruModel->find($filterGuru);
                $data['info_target'] = $guruInfo ? $guruInfo['nama_guru'] : '';

                foreach ($rawJadwal as $row) {
                    if ($row['id_guru'] == $filterGuru) {
                        $hasilFilter[$row['hari']][$row['jam_ke']] = [
                            'mapel' => $row['nama_mapel'],
                            'guru'  => $row['nama_kelas'], // Tampilkan Kelas jika filter guru
                            'ruang' => $row['nama_ruangan'],
                            'warna' => $this->getColor($row['nama_mapel'])
                        ];
                    }
                }
            }

            $data['jadwal_tampil'] = $hasilFilter;
        }

        return view('landing_page', $data);
    }

    // --- HELPER SEDERHANA ---
    private function getWaktuJam() {
        $jamDb = $this->jamModel->where('is_istirahat', 0)->findAll();
        $waktu = [];
        foreach($jamDb as $j) $waktu[$j['jam_ke']] = date('H:i', strtotime($j['waktu_mulai'])) . '-' . date('H:i', strtotime($j['waktu_selesai']));
        return $waktu;
    }

    private function getIstirahat() {
        $istDb = $this->jamModel->where('is_istirahat', 1)->findAll();
        $ist = [];
        foreach($istDb as $i) {
            // Mapping manual sederhana berdasarkan data jam sebelumnya
            if(strtotime($i['waktu_mulai']) < strtotime('11:00')) $ist[4] = '09:30-10:00'; 
            else $ist[7] = '12:00-12:30';
        }
        return $ist;
    }

    private function getColor($str) {
        $colors = ['#e8f5e9', '#e3f2fd', '#fff3e0', '#f3e5f5', '#e0f7fa'];
        return $colors[crc32($str) % count($colors)];
    }
}