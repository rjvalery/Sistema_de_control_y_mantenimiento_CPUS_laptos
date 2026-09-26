<?php

namespace App\Models;

use CodeIgniter\Model;

class SopladoModel extends Model
{
    protected $table            = 'soplado_registros';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'nombre_analista', 'num_traslado', 'placa_id', 'energiza', 
        'da_video', 'detecta_disco', 'ingreso_bios', 'pasta_termica', 
        'maquina_contenia', 'gel_cucarachas', 'foto_ruta'
    ];
    protected $useTimestamps    = false;
}