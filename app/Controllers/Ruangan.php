<?php

namespace App\Controllers;

use App\Models\RuanganModel;
use App\Models\JadwalModel;
use App\Models\KelasModel; // TAMBAHAN: Untuk cek Homebase

class Ruangan extends BaseController
{
    protected $ruanganModel;
    protected $jadwalModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->ruanganModel = new RuanganModel();
        $this->jadwalModel  = new JadwalModel();
        $this->kelasModel   = new KelasModel(); // Load Model Kelas
    }

    public function index()
    {
        // CEK GLOBAL LOCK
        $is_locked = $this->jadwalModel->countAllResults() > 0;

        $data = [
            'daftar_ruangan' => $this->ruanganModel->orderBy('jenis', 'ASC')->orderBy('nama_ruangan', 'ASC')->findAll(),
            'is_locked'      => $is_locked
        ];
        return view('ruangan/index', $data);
    }

    public function store()
    {
        if ($this->jadwalModel->countAllResults() > 0) {
            session()->setFlashdata('error', '<b>Akses Ditolak!</b><br>Jadwal sudah digenerate.');
            return redirect()->to('/ruangan');
        }

        // Validasi
        if (!$this->validate([
            'nama_ruangan' => 'required|is_unique[ruangan.nama_ruangan]',
            'kapasitas'    => 'required|integer',
            'jenis'        => 'required' // Wajib pilih jenis
        ])) {
            session()->setFlashdata('error', 'Gagal Validasi: Data tidak lengkap atau Nama Ruangan sudah ada.');
            return redirect()->to('/ruangan');
        }

        $this->ruanganModel->save([
            'nama_ruangan' => strtoupper($this->request->getVar('nama_ruangan')),
            'jenis'        => $this->request->getVar('jenis'), // PENTING: Untuk Algoritma
            'kapasitas'    => $this->request->getVar('kapasitas'),
        ]);

        session()->setFlashdata('success', 'Data Ruangan berhasil ditambahkan.');
        return redirect()->to('/ruangan');
    }

    public function update()
    {
        if ($this->jadwalModel->countAllResults() > 0) {
            session()->setFlashdata('error', '<b>Akses Ditolak!</b><br>Data terkunci.');
            return redirect()->to('/ruangan');
        }

        $id = $this->request->getVar('id_ruangan');

        // Cek apakah Ruangan ini dipakai sebagai Homebase Kelas?
        $isHomebase = $this->kelasModel->where('id_ruang_homebase', $id)->first();
        // Jika dipakai Homebase, kita peringatkan jika kapasitas diubah jadi kecil (Opsional logic)
        
        if (!$this->validate([
            'nama_ruangan' => 'required|is_unique[ruangan.nama_ruangan,id_ruangan,'.$id.']',
            'kapasitas'    => 'required|integer',
            'jenis'        => 'required'
        ])) {
            session()->setFlashdata('error', 'Gagal Validasi: Nama Ruangan duplikat.');
            return redirect()->to('/ruangan');
        }

        $this->ruanganModel->save([
            'id_ruangan'   => $id,
            'nama_ruangan' => strtoupper($this->request->getVar('nama_ruangan')),
            'jenis'        => $this->request->getVar('jenis'),
            'kapasitas'    => $this->request->getVar('kapasitas'),
        ]);

        session()->setFlashdata('success', 'Data Ruangan berhasil diperbarui.');
        return redirect()->to('/ruangan');
    }

    public function delete($id)
    {
        if ($this->jadwalModel->countAllResults() > 0) {
            session()->setFlashdata('error', '<b>Gagal Hapus!</b><br>Jadwal sudah digenerate.');
            return redirect()->to('/ruangan');
        }

        // VALIDASI 1: Cek apakah dipakai sebagai Homebase Kelas?
        $terpakaiKelas = $this->kelasModel->where('id_ruang_homebase', $id)->first();
        if ($terpakaiKelas) {
            session()->setFlashdata('error', '<b>Ditolak!</b><br>Ruangan ini adalah Homebase untuk kelas <b>' . $terpakaiKelas['nama_kelas'] . '</b>.<br>Ganti dulu homebase kelas tersebut sebelum menghapus.');
            return redirect()->to('/ruangan');
        }

        // VALIDASI 2: Cek apakah dipakai di Jadwal (Double check logic)
        // (Biasanya sudah tercover oleh Global Lock di atas, tapi aman untuk jaga-jaga)

        $this->ruanganModel->delete($id);
        session()->setFlashdata('success', 'Data Ruangan berhasil dihapus.');
        return redirect()->to('/ruangan');
    }
}