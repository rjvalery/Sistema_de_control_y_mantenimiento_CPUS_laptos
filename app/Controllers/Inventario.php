<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\InventarioGeneralModel;
use CodeIgniter\HTTP\ResponseInterface;

class Inventario extends BaseController
{
    protected InventarioGeneralModel $inventarioModel;

    public function __construct()
    {
        $this->inventarioModel = new InventarioGeneralModel();
    }

    /**
     * Endpoint API para consultar y sincronizar datos de un equipo en tiempo real al tipear la placa o serial.
     */
    public function buscarEquipo(): ResponseInterface
    {
        $query = trim((string) ($this->request->getGet('query') ?? $this->request->getGet('termino')));

        if ($query === '' || strlen($query) < 2) {
            return $this->response->setJSON([
                'encontrado' => false,
                'mensaje'    => 'Término de búsqueda muy corto.',
            ]);
        }

        $equipo = $this->inventarioModel->buscarPorTermino($query);

        if (!$equipo) {
            return $this->response->setJSON([
                'encontrado' => false,
                'mensaje'    => 'Equipo no encontrado en inventario masivo.',
            ]);
        }

        return $this->response->setJSON([
            'encontrado' => true,
            'equipo'     => [
                'id'                  => (int) $equipo['id'],
                'placa_id'            => $equipo['placa_id'],
                'serial'              => $equipo['serial'],
                'tipo_equipo'         => $equipo['tipo_equipo'],
                'marca'               => $equipo['marca'],
                'modelo'              => $equipo['modelo'],
                'ubicacion'           => $equipo['ubicacion'],
                'estado'              => $equipo['estado'],
                'intervenido'         => (int) ($equipo['intervenido'] ?? 0) === 1,
                'fecha_intervencion'  => $equipo['fecha_intervencion'],
                'modulo_intervencion' => $equipo['modulo_intervencion'],
                'analista_intervencion' => $equipo['analista_intervencion'],
            ],
        ]);
    }
}
