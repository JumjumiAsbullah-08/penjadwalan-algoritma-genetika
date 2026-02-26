<?php

namespace App\Models;

use CodeIgniter\Model;

class PengampuModel extends Model
{
    protected $table            = 'pengampu';
    protected $primaryKey       = 'id_pengampu';
    protected $useAutoIncrement = true;
    
    // UPDATE PENTING DI SINI:
    // Tambahkan 'tahun_ajaran' dan 'semester' agar bisa disimpan
    protected $allowedFields    = [
        'id_guru', 
        'id_mapel', 
        'id_kelas', 
        'jumlah_jam',
        'tahun_ajaran', // Baru
        'semester'      // Baru
    ];

    // Fungsi Khusus untuk Mengambil Data Lengkap (JOIN)
    // (Opsional: Bisa ditambahkan parameter filter tahun jika mau dipakai di tempat lain)
    public function getLengkap()
    {
        return $this->db->table('pengampu')
            ->join('guru', 'guru.id_guru = pengampu.id_guru')
            ->join('mapel', 'mapel.id_mapel = pengampu.id_mapel')
            ->join('kelas', 'kelas.id_kelas = pengampu.id_kelas')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->orderBy('mapel.nama_mapel', 'ASC')
            ->get()->getResultArray();
    }

    // Hitung Total Jam Per Guru (Untuk Fitur Pintar)
    public function getTotalJamGuru($id_guru)
    {
        $query = $this->where('id_guru', $id_guru)->selectSum('jumlah_jam')->first();
        return $query['jumlah_jam'] ?? 0;
    }
}