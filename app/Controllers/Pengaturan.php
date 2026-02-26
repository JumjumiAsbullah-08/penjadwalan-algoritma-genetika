<?php

namespace App\Controllers;

use App\Models\KonfigurasiModel;

class Pengaturan extends BaseController
{
    protected $konfigModel;

    public function __construct()
    {
        $this->konfigModel = new KonfigurasiModel();
    }

    public function index()
    {
        // Ambil info aktif (Tahun & Semester)
        $active = $this->konfigModel->getInfoAktif();
        
        // Ambil status jadwal (draft/pending/approved)
        $statusJadwal = $this->konfigModel->getStatus();

        // Tentukan apakah form dikunci? 
        // Terkunci jika status BUKAN draft
        $isLocked = ($statusJadwal != '-');

        $data = [
            'judul'     => 'Pengaturan Sistem',
            'active'    => $active,
            'status'    => $statusJadwal,
            'is_locked' => $isLocked,
            // List opsi tahun ajaran (Bisa ditambah manual nanti)
            'opsi_tahun'=> ['2024/2025', '2025/2026', '2026/2027', '2027/2028']
        ];

        return view('pengaturan/index', $data);
    }

    public function update()
    {
        // 1. CEK KEAMANAN: Apakah Jadwal Terkunci?
        $statusJadwal = $this->konfigModel->getStatus();
        if ($statusJadwal != '-') {
            return redirect()->to('/pengaturan')->with('error', 'GAGAL! Tahun Ajaran tidak dapat diubah karena Jadwal sudah digenerate/diterbitkan. Silakan Reset Jadwal terlebih dahulu.');
        }

        // 2. VALIDASI INPUT
        if (!$this->validate([
            'tahun_ajaran' => [
                'rules'  => 'required|min_length[9]|max_length[9]',
                'errors' => [
                    'required'   => 'Tahun ajaran wajib diisi.',
                    'min_length' => 'Format tahun harus YYYY/YYYY (Contoh: 2025/2026)',
                ]
            ],
            'semester' => [
                'rules'  => 'required|in_list[Ganjil,Genap]',
                'errors' => [
                    'in_list' => 'Semester harus Ganjil atau Genap.'
                ]
            ]
        ])) {
            return redirect()->to('/pengaturan')->withInput()->with('errors', $this->validator->getErrors());
        }

        // Ambil Data
        $tahun    = $this->request->getPost('tahun_ajaran');
        $semester = $this->request->getPost('semester');

        // 3. CEK LOGIKA (Opsional: Cegah perubahan jika data sama persis)
        $current = $this->konfigModel->getInfoAktif();
        if ($current['tahun'] == $tahun && $current['semester'] == $semester) {
            return redirect()->to('/pengaturan')->with('warning', 'Tidak ada perubahan data yang disimpan.');
        }

        // 4. SIMPAN PERUBAHAN
        $this->konfigModel->updateInfoAktif($tahun, $semester);

        return redirect()->to('/pengaturan')->with('success', 'Pengaturan Berhasil Diperbarui! Sistem sekarang aktif di ' . $tahun . ' - ' . $semester);
    }
}