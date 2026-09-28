<?php

namespace App\Controllers;

use App\Models\EquipoModel;
use App\Models\UsuarioModel;
use App\Models\InventarioGeneralModel;
use App\Services\UploadService;

class Equipos extends BaseController
{
    protected $equipoModel;
    protected $usuarioModel;
    protected $uploadService;
    protected $inventarioModel;

    public function __construct()
    {
        $this->equipoModel     = new EquipoModel();
        $this->usuarioModel    = new UsuarioModel();
        $this->uploadService   = new UploadService();
        $this->inventarioModel = new InventarioGeneralModel();
    }

    public function formulario()
    {
        $data['analistas'] = $this->usuarioModel->where('rol', 'analista')->where('activo', 1)->orderBy('nombre', 'ASC')->findAll();
        return view('equipos/formulario', $data);
    }

    public function guardar()
    {
        $placaId  = $this->request->getPost('placa_id');
        $file     = $this->request->getFile('foto_equipo');
        
        // Guarda en D:\uploads
        $fotoRuta = $this->uploadService->guardarEvidencia($file, $placaId, 'diagnostico');

        $nombreAnalista = (session('usuario_rol') === 'analista')
            ? (string) session('usuario_nombre')
            : (string) ($this->request->getPost('nombre_analista') ?: session('usuario_nombre'));

        $data = [
            'nombre_analista'     => $nombreAnalista,
            'num_traslado'        => $this->request->getPost('num_traslado'),
            'placa_id'            => $placaId,
            'tipo_gestion'        => $this->request->getPost('tipo_gestion'),
            'energiza'            => $this->request->getPost('energiza'),
            'da_video'            => $this->request->getPost('da_video'),
            'estado_actual'       => $this->request->getPost('estado_actual'),
            'que_va_intervenir'   => $this->request->getPost('que_va_intervenir'),
            'origen_pieza'        => $this->request->getPost('origen_pieza'),
            'serial_disco'        => $this->request->getPost('serial_disco'),
            'descripcion_novedad' => $this->request->getPost('descripcion_novedad'),
            'motivo_baja'         => $this->request->getPost('motivo_baja'),
            'ubicacion_destino'   => $this->request->getPost('ubicacion_destino'),
            'foto_equipo'         => $fotoRuta,
            'fecha_creacion'      => date('Y-m-d H:i:s')
        ];

        if ($this->equipoModel->insert($data)) {
            // Sincronizar y descontar de pendientes en inventario general
            $this->inventarioModel->marcarIntervenido((string)$placaId, 'diagnostico', $nombreAnalista);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Guardado correctamente']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Error al guardar en base de datos']);
    }

    public function bitacora()
    {
        $busqueda = trim($this->request->getGet('buscar') ?? '');
        $builder  = $this->equipoModel->builder();

        if ($busqueda !== '') {
            $builder->like('placa_id', $busqueda)
                    ->orLike('num_traslado', $busqueda)
                    ->orLike('nombre_analista', $busqueda);
        }

        $data['registros'] = $builder->orderBy('id', 'DESC')->get()->getResultArray();
        $data['busqueda']  = $busqueda;

        return view('equipos/bitacora', $data);
    }

    public function exportar()
    {
        $busqueda = trim($this->request->getGet('buscar') ?? '');
        $builder  = $this->equipoModel->builder();

        if ($busqueda !== '') {
            $builder->like('placa_id', $busqueda)
                    ->orLike('num_traslado', $busqueda)
                    ->orLike('nombre_analista', $busqueda);
        }

        $registros = $builder->orderBy('id', 'DESC')->get()->getResultArray();
        $filename  = "Reporte_Equipos_" . date('Ymd_His') . ".xls";

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
        echo "<table border='1'>
                <thead>
                    <tr style='background-color: #343a40; color: #ffffff;'>
                        <th>ID</th><th>Fecha/Hora</th><th>Analista</th><th>Traslado</th>
                        <th>Placa ID</th><th>Gestión</th><th>Energiza</th><th>Video</th>
                        <th>Estado</th><th>Intervención</th><th>Origen</th><th>Novedad</th>
                        <th>Motivo Baja</th><th>Serial Disco</th><th>Ubicación Destino</th>
                    </tr>
                </thead>
                <tbody>";
        foreach ($registros as $row) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['fecha_creacion']}</td>
                    <td>" . esc($row['nombre_analista']) . "</td>
                    <td>" . esc($row['num_traslado']) . "</td>
                    <td>" . esc($row['placa_id']) . "</td>
                    <td>" . esc($row['tipo_gestion']) . "</td>
                    <td>" . esc($row['energiza'] ?? '-') . "</td>
                    <td>" . esc($row['da_video'] ?? '-') . "</td>
                    <td>" . esc($row['estado_actual'] ?? '-') . "</td>
                    <td>" . esc($row['que_va_intervenir'] ?? '-') . "</td>
                    <td>" . esc($row['origen_pieza'] ?? '-') . "</td>
                    <td>" . esc($row['descripcion_novedad'] ?? '-') . "</td>
                    <td>" . esc($row['motivo_baja'] ?? '-') . "</td>
                    <td>" . esc($row['serial_disco'] ?? '-') . "</td>
                    <td>" . esc($row['ubicacion_destino'] ?? '-') . "</td>
                  </tr>";
        }
        echo "</tbody></table>";
        exit;
    }
}