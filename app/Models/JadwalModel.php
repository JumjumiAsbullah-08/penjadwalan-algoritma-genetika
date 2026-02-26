<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalModel extends Model
{
    protected $table            = 'jadwal';
    protected $primaryKey       = 'id_jadwal';
    protected $useAutoIncrement = true;
    
    // PERBAIKAN: Tambahkan 'tahun_ajaran' dan 'semester' ke sini
    protected $allowedFields    = [
        'id_pengampu', 
        'hari', 
        'jam_ke', 
        'id_ruangan',
        'tahun_ajaran', // WAJIB ADA
        'semester'      // WAJIB ADA
    ];

    // Ambil Data Jadwal Lengkap (Relasi)
    public function getJadwalLengkap()
    {
        return $this->db->table('jadwal')
            ->join('pengampu', 'pengampu.id_pengampu = jadwal.id_pengampu')
            ->join('guru', 'guru.id_guru = pengampu.id_guru')
            ->join('mapel', 'mapel.id_mapel = pengampu.id_mapel')
            ->join('kelas', 'kelas.id_kelas = pengampu.id_kelas')
            ->join('ruangan', 'ruangan.id_ruangan = jadwal.id_ruangan')
            ->orderBy('jadwal.hari', 'DESC')
            ->orderBy('jadwal.jam_ke', 'ASC')
            ->get()->getResultArray();
    }

    // Fitur Reset Jadwal
    public function resetJadwal()
    {
        return $this->truncate();
    }
}