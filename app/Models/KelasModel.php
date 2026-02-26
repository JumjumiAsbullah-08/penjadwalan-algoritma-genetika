<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table            = 'kelas';
    protected $primaryKey       = 'id_kelas';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['nama_kelas', 'jumlah_siswa', 'id_guru_wali', 'id_ruang_homebase'];
}