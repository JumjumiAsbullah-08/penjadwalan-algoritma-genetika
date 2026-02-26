<?php

namespace App\Controllers;

use App\Models\KelasModel;
use App\Models\JadwalModel;
use App\Models\PengampuModel;
use App\Models\GuruModel;
use App\Models\RuanganModel; // TAMBAHKAN INI

class Kelas extends BaseController
{
    protected $kelasModel;
    protected $jadwalModel;
    protected $pengampuModel;
    protected $guruModel;
    protected $ruanganModel; // PROPERTY BARU

    public function __construct()
    {
        $this->kelasModel    = new KelasModel();
        $this->jadwalModel   = new JadwalModel();
        $this->pengampuModel = new PengampuModel();
        $this->guruModel     = new GuruModel();
        $this->ruanganModel  = new RuanganModel(); // INISIALISASI
    }

    public function index()
    {
        $is_schedule_exist = $this->jadwalModel->countAllResults() > 0;

        $daftarKelas = $this->kelasModel
            ->select('kelas.*, guru.nama_guru as nama_wali, ruangan.nama_ruangan as nama_homebase') // Select Homebase
            ->select('(SELECT COUNT(*) FROM pengampu WHERE pengampu.id_kelas = kelas.id_kelas) as terpakai')
            ->join('guru', 'guru.id_guru = kelas.id_guru_wali', 'left')
            ->join('ruangan', 'ruangan.id_ruangan = kelas.id_ruang_homebase', 'left') // Join Ruangan
            ->orderBy('nama_kelas', 'ASC')
            ->findAll();

        $data = [
            'daftar_kelas'  => $daftarKelas,
            'daftar_guru'   => $this->guruModel->orderBy('nama_guru', 'ASC')->findAll(),
            'daftar_ruangan'=> $this->ruanganModel->where('jenis', 'Teori')->orderBy('nama_ruangan', 'ASC')->findAll(), // Ambil Ruang Teori Saja
            'is_locked'     => $is_schedule_exist
        ];
        return view('kelas/index', $data);
    }

    public function store()
    {
        if ($this->jadwalModel->countAllResults() > 0) {
            session()->setFlashdata('error', '<b>Akses Ditolak!</b><br>Jadwal sudah digenerate.');
            return redirect()->to('/kelas');
        }

        // 1. Validasi Input Dasar
        if (!$this->validate([
            'nama_kelas' => 'required|is_unique[kelas.nama_kelas]',
            'jumlah_siswa' => 'required|integer',
            'id_ruang_homebase' => 'required' // Wajib Pilih Homebase
        ])) {
            session()->setFlashdata('error', 'Gagal Validasi: Data tidak lengkap atau Nama Kelas duplikat.');
            return redirect()->to('/kelas');
        }

        // 2. Validasi Homebase Unik (1 Kelas = 1 Homebase)
        $homebase = $this->request->getVar('id_ruang_homebase');
        $cekRuang = $this->kelasModel->where('id_ruang_homebase', $homebase)->first();
        if ($cekRuang) {
            session()->setFlashdata('error', '<b>Gagal Simpan!</b><br>Ruangan tersebut sudah menjadi homebase kelas <b>'.$cekRuang['nama_kelas'].'</b>.');
            return redirect()->to('/kelas');
        }

        // 3. Validasi Wali Kelas (Anti Double)
        $wali = $this->request->getVar('id_guru_wali');
        if($wali == "") $wali = null;

        if ($wali) {
            $cekWali = $this->kelasModel->where('id_guru_wali', $wali)->first();
            if ($cekWali) {
                session()->setFlashdata('error', '<b>Gagal Simpan!</b><br>Guru tersebut sudah menjadi Wali Kelas di <b>'.$cekWali['nama_kelas'].'</b>.');
                return redirect()->to('/kelas');
            }
        }

        $this->kelasModel->save([
            'nama_kelas'        => strtoupper($this->request->getVar('nama_kelas')),
            'jumlah_siswa'      => $this->request->getVar('jumlah_siswa'),
            'id_guru_wali'      => $wali,
            'id_ruang_homebase' => $homebase // SIMPAN HOMEBASE
        ]);

        session()->setFlashdata('success', 'Data Kelas berhasil ditambahkan.');
        return redirect()->to('/kelas');
    }

    public function update()
    {
        if ($this->jadwalModel->countAllResults() > 0) {
            session()->setFlashdata('error', '<b>Akses Ditolak!</b><br>Data terkunci.');
            return redirect()->to('/kelas');
        }

        $id = $this->request->getVar('id_kelas');
        $terpakai = $this->pengampuModel->where('id_kelas', $id)->countAllResults();
        $kelasLama = $this->kelasModel->find($id);
        
        $namaBaru   = strtoupper($this->request->getVar('nama_kelas'));
        $jumlahBaru = $this->request->getVar('jumlah_siswa');
        $homebase   = $this->request->getVar('id_ruang_homebase');

        // Validasi Lock Jika Terpakai (Hanya Kunci Nama & Jumlah, Homebase boleh ganti)
        if ($terpakai > 0) {
            if ($kelasLama['nama_kelas'] != $namaBaru || $kelasLama['jumlah_siswa'] != $jumlahBaru) {
                session()->setFlashdata('error', '<b>Gagal Update!</b><br>Nama Kelas & Jumlah Siswa terkunci karena sedang aktif.');
                return redirect()->to('/kelas');
            }
        }

        // Validasi Duplikat Nama
        if (!$this->validate([
            'nama_kelas' => 'required|is_unique[kelas.nama_kelas,id_kelas,'.$id.']',
            'jumlah_siswa' => 'required|integer',
            'id_ruang_homebase' => 'required'
        ])) {
            session()->setFlashdata('error', 'Gagal Validasi.');
            return redirect()->to('/kelas');
        }

        // Validasi Homebase Unik (Kecuali Punya Sendiri)
        $cekRuang = $this->kelasModel
            ->where('id_ruang_homebase', $homebase)
            ->where('id_kelas !=', $id)
            ->first();
        if ($cekRuang) {
            session()->setFlashdata('error', '<b>Gagal Update!</b><br>Ruangan Homebase sudah dipakai kelas <b>'.$cekRuang['nama_kelas'].'</b>.');
            return redirect()->to('/kelas');
        }

        // Validasi Wali Kelas
        $wali = $this->request->getVar('id_guru_wali');
        if($wali == "") $wali = null;

        if ($wali) {
            $cekWali = $this->kelasModel
                ->where('id_guru_wali', $wali)
                ->where('id_kelas !=', $id)
                ->first();
            
            if ($cekWali) {
                session()->setFlashdata('error', '<b>Gagal Update!</b><br>Guru tersebut sudah menjadi Wali Kelas di <b>'.$cekWali['nama_kelas'].'</b>.');
                return redirect()->to('/kelas');
            }
        }

        $this->kelasModel->save([
            'id_kelas'          => $id,
            'nama_kelas'        => $namaBaru,
            'jumlah_siswa'      => $jumlahBaru,
            'id_guru_wali'      => $wali,
            'id_ruang_homebase' => $homebase // UPDATE HOMEBASE
        ]);

        session()->setFlashdata('success', 'Data Kelas berhasil diperbarui.');
        return redirect()->to('/kelas');
    }

    public function delete($id)
    {
        if ($this->jadwalModel->countAllResults() > 0) {
            session()->setFlashdata('error', '<b>Gagal Hapus!</b><br>Jadwal sudah digenerate.');
            return redirect()->to('/kelas');
        }

        $terpakai = $this->pengampuModel->where('id_kelas', $id)->countAllResults();
        if ($terpakai > 0) {
            session()->setFlashdata('error', '<b>Ditolak!</b><br>Kelas sedang aktif mengajar.');
            return redirect()->to('/kelas');
        }

        $this->kelasModel->delete($id);
        session()->setFlashdata('success', 'Data Kelas berhasil dihapus.');
        return redirect()->to('/kelas');
    }
}