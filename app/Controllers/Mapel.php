<?php

namespace App\Controllers;

use App\Models\MapelModel;
use App\Models\JadwalModel;
use App\Models\PengampuModel;

class Mapel extends BaseController
{
    protected $mapelModel;
    protected $jadwalModel;
    protected $pengampuModel;

    public function __construct()
    {
        $this->mapelModel = new MapelModel();
        $this->jadwalModel = new JadwalModel();
        $this->pengampuModel = new PengampuModel();
    }

    public function index()
    {
        // 1. GLOBAL LOCK
        $is_schedule_exist = $this->jadwalModel->countAllResults() > 0;

        // 2. AMBIL DATA
        $daftarMapel = $this->mapelModel
            ->select('mapel.*')
            ->select('(SELECT COUNT(*) FROM pengampu WHERE pengampu.id_mapel = mapel.id_mapel) as jumlah_pengampu')
            ->orderBy('kelompok', 'ASC') // Sort by Kelompok dulu (Olahraga/Teori/dll)
            ->orderBy('nama_mapel', 'ASC')
            ->findAll();

        $data = [
            'daftar_mapel' => $daftarMapel,
            'is_locked'    => $is_schedule_exist
        ];
        return view('mapel/index', $data);
    }

    // --- SIMPAN DATA BARU ---
    public function store()
    {
        if ($this->jadwalModel->countAllResults() > 0) {
            session()->setFlashdata('error', '<b>Akses Ditolak!</b><br>Jadwal sudah digenerate.');
            return redirect()->to('/mapel');
        }

        // VALIDASI
        if (!$this->validate([
            'kode_mapel' => [
                'rules' => 'required|is_unique[mapel.kode_mapel]|min_length[3]',
                'errors' => ['is_unique' => 'Kode Mapel sudah ada!', 'min_length' => 'Kode minimal 3 huruf.']
            ],
            'nama_mapel' => [
                'rules' => 'required|is_unique[mapel.nama_mapel]',
                'errors' => ['is_unique' => 'Nama Mata Pelajaran ini sudah terdaftar.']
            ],
            'max_jam_per_minggu' => 'required|integer'
        ])) {
            session()->setFlashdata('error', 'Gagal Validasi: ' . \Config\Services::validation()->listErrors());
            return redirect()->to('/mapel')->withInput();
        }

        // SIMPAN KE DATABASE (Update Kolom Baru)
        $this->mapelModel->save([
            'kode_mapel'         => strtoupper($this->request->getVar('kode_mapel')),
            'nama_mapel'         => $this->request->getVar('nama_mapel'),
            'jenis'              => $this->request->getVar('jenis'),      // Kurikulum (Wajib A/B)
            'kelompok'           => $this->request->getVar('kelompok'),   // Penentu Ruangan (Teori/Olahraga)
            'max_jam_per_minggu' => $this->request->getVar('max_jam_per_minggu'), // Batas JP
        ]);

        session()->setFlashdata('success', 'Mata Pelajaran berhasil ditambahkan.');
        return redirect()->to('/mapel');
    }

    // --- UPDATE DATA ---
    public function update()
    {
        if ($this->jadwalModel->countAllResults() > 0) {
            session()->setFlashdata('error', '<b>Akses Ditolak!</b><br>Data terkunci.');
            return redirect()->to('/mapel');
        }

        $id = $this->request->getVar('id_mapel');
        
        // Cek Beban Mengajar (Lock Safety)
        $terpakai = $this->pengampuModel->where('id_mapel', $id)->countAllResults();
        
        // VALIDASI
        if (!$this->validate([
            'kode_mapel' => 'required|min_length[3]|is_unique[mapel.kode_mapel,id_mapel,'.$id.']',
            'nama_mapel' => 'required|is_unique[mapel.nama_mapel,id_mapel,'.$id.']',
            'max_jam_per_minggu' => 'required|integer'
        ])) {
            session()->setFlashdata('error', 'Gagal Validasi: Kode/Nama sudah dipakai atau Jam tidak valid.');
            return redirect()->to('/mapel');
        }

        // SIMPAN UPDATE
        $this->mapelModel->save([
            'id_mapel'           => $id,
            'kode_mapel'         => strtoupper($this->request->getVar('kode_mapel')),
            'nama_mapel'         => $this->request->getVar('nama_mapel'),
            'jenis'              => $this->request->getVar('jenis'),
            'kelompok'           => $this->request->getVar('kelompok'),   // PENTING: Update Kelompok
            'max_jam_per_minggu' => $this->request->getVar('max_jam_per_minggu'), // PENTING: Update Jam
        ]);

        session()->setFlashdata('success', 'Mata Pelajaran berhasil diperbarui.');
        return redirect()->to('/mapel');
    }

    // --- HAPUS DATA ---
    public function delete($id)
    {
        if ($this->jadwalModel->countAllResults() > 0) {
            session()->setFlashdata('error', '<b>Gagal Hapus!</b><br>Jadwal sudah digenerate.');
            return redirect()->to('/mapel');
        }

        $terpakai = $this->pengampuModel->where('id_mapel', $id)->countAllResults();
        if ($terpakai > 0) {
            session()->setFlashdata('error', '<b>Ditolak!</b><br>Mapel sedang diajarkan oleh Guru.');
            return redirect()->to('/mapel');
        }

        $this->mapelModel->delete($id);
        session()->setFlashdata('success', 'Mata Pelajaran berhasil dihapus.');
        return redirect()->to('/mapel');
    }
}