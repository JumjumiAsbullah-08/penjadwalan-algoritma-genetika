<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    protected $table            = 'guru';
    protected $primaryKey       = 'id_guru';
    // app/Models/GuruModel.php
    protected $allowedFields = ['nip', 'nama_guru', 'hari_tidak_bersedia', 'tugas_tambahan'];
}