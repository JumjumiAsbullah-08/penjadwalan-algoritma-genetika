<?php

namespace App\Models;

use CodeIgniter\Model;

class MapelModel extends Model
{
    protected $table            = 'mapel';
    protected $primaryKey       = 'id_mapel';
    // app/Models/MapelModel.php
    protected $allowedFields = ['kode_mapel', 'nama_mapel', 'jenis', 'kelompok', 'max_jam_per_minggu'];
}