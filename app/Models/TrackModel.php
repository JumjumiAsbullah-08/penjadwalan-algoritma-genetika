<?php

namespace App\Models;

use CodeIgniter\Model;

class TrackModel extends Model
{
    protected $table            = 'track_algoritma';
    protected $primaryKey       = 'id_track';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Field yang boleh diisi/diupdate
    protected $allowedFields    = [
        'tahun_ajaran', 
        'semester', 
        'history_log', 
        'best_kromosom', 
        'durasi_proses',
        'created_at'
    ];

    // Mengaktifkan fitur timestamps otomatis (Opsional, tapi bagus untuk tracking)
    protected $useTimestamps = false; 

    /**
     * Fungsi Khusus: Ambil Data Track Terakhir berdasarkan Tahun & Semester
     * Digunakan agar saat Admin login ulang, data yang diambil sesuai periode aktif.
     */
    public function getTrack($tahun, $semester)
    {
        return $this->where('tahun_ajaran', $tahun)
                    ->where('semester', $semester)
                    ->orderBy('id_track', 'DESC') // Ambil yang paling baru (terakhir digenerate)
                    ->first();
    }
}