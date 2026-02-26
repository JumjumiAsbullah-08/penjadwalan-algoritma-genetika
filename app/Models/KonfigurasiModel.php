<?php

namespace App\Models;

use CodeIgniter\Model;

class KonfigurasiModel extends Model
{
    protected $table            = 'konfigurasi';
    protected $primaryKey       = 'id_konfig';
    
    // UPDATE 1: Tambahkan 'catatan_revisi' agar bisa disimpan ke database
    protected $allowedFields    = ['nama_key', 'nilai', 'tahun_ajaran', 'semester', 'catatan_revisi'];

    // Helper function untuk mengambil nilai status dengan cepat
    public function getStatus()
    {
        $query = $this->where('nama_key', 'status_jadwal')->first();
        return $query ? $query['nilai'] : 'draft';
    }

    // Helper function untuk update status
    public function setStatus($status)
    {
        $exist = $this->where('nama_key', 'status_jadwal')->first();
        
        if ($exist) {
            return $this->where('nama_key', 'status_jadwal')->set(['nilai' => $status])->update();
        } else {
            return $this->insert(['nama_key' => 'status_jadwal', 'nilai' => $status]);
        }
    }

    // --- BAGIAN INFO TAHUN & SEMESTER ---

    // UPDATE 2: Sertakan 'catatan_revisi' saat mengambil info aktif
    public function getInfoAktif()
    {
        $data = $this->where('nama_key', 'status_jadwal')->first();
        
        if (!$data) {
            $data = $this->first();
        }

        return [
            'tahun'          => $data['tahun_ajaran'] ?? '2025/2026',
            'semester'       => $data['semester']     ?? 'Ganjil',
            // Tambahan: Ambil catatan revisi juga (jika ada)
            'catatan_revisi' => $data['catatan_revisi'] ?? null 
        ];
    }

    public function updateInfoAktif($tahun, $semester)
    {
        return $this->where('nama_key', 'status_jadwal')
                     ->set([
                         'tahun_ajaran' => $tahun,
                         'semester'     => $semester
                     ])
                     ->update();
    }

    // --- BAGIAN BARU: MANAJEMEN CATATAN REVISI ---

    // 3. Simpan Catatan Revisi (Dipakai saat Kepsek Menolak)
    public function setCatatan($pesan)
    {
        return $this->where('nama_key', 'status_jadwal')
                    ->set(['catatan_revisi' => $pesan])
                    ->update();
    }

    // 4. Hapus Catatan Revisi (Dipakai saat Admin Mengajukan Ulang)
    public function clearCatatan()
    {
        return $this->where('nama_key', 'status_jadwal')
                    ->set(['catatan_revisi' => null])
                    ->update();
    }
}