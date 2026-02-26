<?php

namespace App\Models;

use CodeIgniter\Model;

class JamModel extends Model
{
    protected $table            = 'jam_pelajaran';
    protected $primaryKey       = 'id_jam';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['hari', 'jam_ke', 'waktu_mulai', 'waktu_selesai', 'is_istirahat'];
}