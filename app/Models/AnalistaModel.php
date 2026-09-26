<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalistaModel extends Model
{
    protected $table            = 'analistas';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nombre'];
    protected $useTimestamps    = false;
}