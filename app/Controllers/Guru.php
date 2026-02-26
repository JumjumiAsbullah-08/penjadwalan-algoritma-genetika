<?php

namespace App\Controllers;

use App\Models\GuruModel;
use App\Models\JadwalModel;
use App\Models\PengampuModel;

class Guru extends BaseController
{
    protected $guruModel;
    protected $jadwalModel;
    protected $pengampuModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->jadwalModel = new JadwalModel();
        $this->pengampuModel = new PengampuModel();
    }

    public function index()
    {
        // 1. CEK GLOBAL LOCK
        $is_schedule_exist = $this->jadwalModel->countAllResults() > 0;

        // 2. AMBIL DATA GURU + JUMLAH BEBAN
        $daftarGuru = $this->guruModel
            ->select('guru.*')
            ->select('(SELECT COUNT(*) FROM pengampu WHERE pengampu.id_guru = guru.id_guru) as jumlah_beban')
            ->orderBy('nama_guru', 'ASC')
            ->findAll();

        $data = [
            'daftar_guru' => $daftarGuru,
            'is_locked'   => $is_schedule_exist
        ];
        return view('guru/index', $data);
    }

    // --- LOGIKA SIMPAN (ADD) - REVISI NIP OPSIONAL ---
    public function store()
    {
        // 1. Cek apakah jadwal sudah ada (Global Lock)
        if ($this->jadwalModel->countAllResults() > 0) {
            session()->setFlashdata('error', '<b>Akses Ditolak!</b><br>Jadwal Pelajaran sudah digenerate. Hapus jadwal dulu untuk menambah guru.');
            return redirect()->to('/guru');
        }

        // 2. Ambil Input NIP
        $nipInput = $this->request->getVar('nip');
        
        // 3. Logika Kondisional NIP
        // Jika NIP kosong atau diisi tanda strip (-), kita anggap NULL
        if (empty($nipInput) || trim($nipInput) === '-') {
            $nipFinal = null; 
            // Validasi: Cuma cek Nama (NIP tidak perlu dicek unique karena null)
            $rules = [
                'nama_guru' => 'required'
            ];
        } else {
            $nipFinal = $nipInput;
            // Validasi: NIP harus unik dan wajib ada isinya
            $rules = [
                'nip'       => 'required|is_unique[guru.nip]',
                'nama_guru' => 'required'
            ];
        }

        // 4. Jalankan Validasi
        if (!$this->validate($rules)) {
            session()->setFlashdata('error', '<b>Gagal Simpan!</b><br>NIP mungkin sudah terdaftar atau Nama Guru kosong.');
            return redirect()->to('/guru')->withInput();
        }

        // 5. Validasi Hari Libur (Max 2 Hari)
        $hariRequest = $this->request->getVar('hari_tidak_bersedia');
        if ($hariRequest && count($hariRequest) > 2) {
            session()->setFlashdata('error', '<b>Ditolak!</b><br>Guru hanya boleh merequest libur maksimal 2 hari.');
            return redirect()->to('/guru')->withInput();
        }

        $hariString = $hariRequest ? implode(',', $hariRequest) : null;

        // 6. Simpan ke Database
        $this->guruModel->save([
            'nip'                 => $nipFinal, // Akan menyimpan NULL jika inputnya (-)
            'nama_guru'           => $this->request->getVar('nama_guru'),
            'tugas_tambahan'      => $this->request->getVar('tugas_tambahan'),
            'hari_tidak_bersedia' => $hariString,
        ]);

        session()->setFlashdata('success', 'Data Guru berhasil ditambahkan.');
        return redirect()->to('/guru');
    }

    // --- LOGIKA UPDATE (EDIT) - REVISI NIP OPSIONAL ---
    public function update()
    {
        $id = $this->request->getVar('id_guru');

        // 1. Cek Global Lock
        if ($this->jadwalModel->countAllResults() > 0) {
            session()->setFlashdata('error', '<b>Akses Ditolak!</b><br>Data terkunci karena Jadwal sudah ada.');
            return redirect()->to('/guru');
        }

        // (Opsional) Cek Beban Mengajar - hanya peringatan, tidak memblokir update profil
        // $beban = $this->pengampuModel->where('id_guru', $id)->countAllResults();

        // 2. Ambil Input NIP
        $nipInput = $this->request->getVar('nip');

        // 3. Logika Kondisional NIP untuk Edit
        if (empty($nipInput) || trim($nipInput) === '-') {
            $nipFinal = null;
            // Abaikan unique check
            $rules = [
                'nama_guru' => 'required'
            ];
        } else {
            $nipFinal = $nipInput;
            // Cek unique, TAPI kecualikan ID guru ini sendiri
            $rules = [
                'nip'       => "required|is_unique[guru.nip,id_guru,{$id}]",
                'nama_guru' => 'required'
            ];
        }

        // 4. Jalankan Validasi
        if (!$this->validate($rules)) {
             session()->setFlashdata('error', '<b>Gagal Update!</b><br>NIP duplikat atau Nama kosong.');
             return redirect()->to('/guru');
        }

        // 5. Validasi Hari Libur
        $hariRequest = $this->request->getVar('hari_tidak_bersedia');
        if ($hariRequest && count($hariRequest) > 2) {
            session()->setFlashdata('error', '<b>Gagal Update!</b><br>Maksimal request libur hanya 2 hari.');
            return redirect()->to('/guru');
        }

        $hariString = $hariRequest ? implode(',', $hariRequest) : null;

        // 6. Simpan Perubahan
        $this->guruModel->save([
            'id_guru'             => $id,
            'nip'                 => $nipFinal,
            'nama_guru'           => $this->request->getVar('nama_guru'),
            'tugas_tambahan'      => $this->request->getVar('tugas_tambahan'),
            'hari_tidak_bersedia' => $hariString,
        ]);

        session()->setFlashdata('success', 'Data Guru berhasil diperbarui.');
        return redirect()->to('/guru');
    }

    public function delete($id)
    {
        if ($this->jadwalModel->countAllResults() > 0) {
            session()->setFlashdata('error', '<b>Gagal Hapus!</b><br>Jadwal terkunci.');
            return redirect()->to('/guru');
        }

        $bebanMengajar = $this->pengampuModel->where('id_guru', $id)->countAllResults();
        if ($bebanMengajar > 0) {
            session()->setFlashdata('error', '<b>Gagal!</b><br>Guru ini masih memiliki jam mengajar. Hapus dulu bebannya.');
            return redirect()->to('/guru');
        }

        $this->guruModel->delete($id);
        session()->setFlashdata('success', 'Data Guru berhasil dihapus.');
        return redirect()->to('/guru');
    }
}