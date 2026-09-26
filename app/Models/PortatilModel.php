<?php

namespace App\Models;

use CodeIgniter\Model;

class PortatilModel extends Model
{
    protected $table            = 'garantias_portatiles';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'nombre_analista', 'numero_traslado', 'placa_id_equipo', 'tipo_gestion',
        'energiza', 'da_video', 'realizo_test_lenovo', 'estado_actual_equipo',
        'diagnostico_laptop_intervenido', 'garantia', 'porque_solicita_garantia',
        'numero_ticket', 'estado_final_equipo', 'indique_pieza', 'indique_fru',
        'pieza_intervenida', 'origen_pieza', 'motivo_baja', 'serial_disco', 'foto_ruta'
    ];
    protected $useTimestamps    = false;
}