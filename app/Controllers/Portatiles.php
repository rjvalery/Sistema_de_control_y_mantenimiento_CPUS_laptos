<?php

namespace App\Controllers;

use App\Models\PortatilModel;
use App\Models\AnalistaModel;
use App\Services\UploadService;

class Portatiles extends BaseController
{
    protected $portatilModel;
    protected $analistaModel;
    protected $uploadService;

    public function __construct()
    {
        $this->portatilModel = new PortatilModel();
        $this->analistaModel = new AnalistaModel();
        $this->uploadService = new UploadService();
    }

    public function formulario()
    {
        $data['analistas'] = $this->analistaModel->orderBy('nombre', 'ASC')->findAll();
        return view('portatiles/formulario', $data);
    }

    public function guardar()
    {
        $placaId  = (string) $this->request->getPost('placa_id_equipo');
        $file     = $this->request->getFile('foto_equipo');
        $fotoRuta = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fotoRuta = $this->uploadService->guardarEvidencia($file, $placaId, 'portatil');
        }

        $isAjax = $this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';

        if (!$fotoRuta) {
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'La foto de evidencia es obligatoria o el archivo no es válido.']);
            }
            return redirect()->back()->withInput()->with('error', 'La foto de evidencia es obligatoria.');
        }

        $nombreAnalista = (session('usuario_rol') === 'analista')
            ? (string) session('usuario_nombre')
            : (string) ($this->request->getPost('nombre_analista') ?: session('usuario_nombre'));

        $data = [
            'nombre_analista'                => $nombreAnalista,
            'numero_traslado'                => $this->request->getPost('numero_traslado'),
            'placa_id_equipo'                => $placaId,
            'tipo_gestion'                   => $this->request->getPost('tipo_gestion'),
            'energiza'                       => $this->request->getPost('energiza'),
            'da_video'                       => $this->request->getPost('da_video'),
            'realizo_test_lenovo'            => $this->request->getPost('realizo_test_lenovo'),
            'estado_actual_equipo'           => $this->request->getPost('estado_actual_equipo'),
            'diagnostico_laptop_intervenido' => $this->request->getPost('diagnostico_laptop_intervenido'),
            'garantia'                       => $this->request->getPost('garantia'),
            'porque_solicita_garantia'       => $this->request->getPost('porque_solicita_garantia'),
            'numero_ticket'                  => $this->request->getPost('numero_ticket'),
            'estado_final_equipo'            => $this->request->getPost('estado_final_equipo'),
            'indique_pieza'                  => $this->request->getPost('indique_pieza'),
            'indique_fru'                    => $this->request->getPost('indique_fru'),
            'pieza_intervenida'              => $this->request->getPost('pieza_intervenida'),
            'origen_pieza'                   => $this->request->getPost('origen_pieza'),
            'motivo_baja'                    => $this->request->getPost('motivo_baja'),
            'serial_disco'                   => $this->request->getPost('serial_disco'),
            'created_at'                     => date('Y-m-d H:i:s')
        ];

        if ($fotoRuta && $this->portatilModel->db->fieldExists('foto_ruta', 'garantias_portatiles')) {
            $data['foto_ruta'] = $fotoRuta;
        }

        if ($this->portatilModel->insert($data)) {
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Registro de portátiles y evidencia guardados correctamente.']);
            }
            return redirect()->to(base_url('portatiles/formulario'))->with('msg', 'Registro de portátiles guardado correctamente.');
        }

        if ($isAjax) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error al guardar en la base de datos.']);
        }

        return redirect()->back()->withInput()->with('error', 'Error al guardar en base de datos.');
    }

    public function bitacora()
    {
        $busqueda = trim($this->request->getGet('buscar') ?? '');
        $builder  = $this->portatilModel->builder();

        if ($busqueda !== '') {
            $builder->like('placa_id_equipo', $busqueda)
                    ->orLike('numero_ticket', $busqueda)
                    ->orLike('nombre_analista', $busqueda);
        }

        $data['registros'] = $builder->orderBy('id', 'DESC')->get()->getResultArray();
        $data['busqueda']  = $busqueda;

        return view('portatiles/bitacora', $data);
    }

    public function exportar()
    {
        $busqueda = trim($this->request->getGet('buscar') ?? '');
        $builder  = $this->portatilModel->builder();

        if ($busqueda !== '') {
            $builder->like('placa_id_equipo', $busqueda)
                    ->orLike('numero_ticket', $busqueda)
                    ->orLike('nombre_analista', $busqueda);
        }

        $registros = $builder->orderBy('id', 'DESC')->get()->getResultArray();
        $filename  = "bitacora_garantias_portatiles_" . date('Y-m-d_H-i') . ".xls";

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<meta charset='UTF-8'>";
        echo "<table border='1'>";
        echo "<tr style='background-color: #212529; color: #ffffff;'>
                <th>ID</th><th>Fecha</th><th>Analista</th><th>N° Traslado</th><th>Placa ID</th>
                <th>Gestión</th><th>Energiza</th><th>Video</th><th>Test Lenovo</th><th>Estado Actual</th>
                <th>Diagnóstico</th><th>Garantía</th><th>N° Ticket</th><th>Razón Garantía</th>
                <th>Estado Final</th><th>Pieza</th><th>FRU</th><th>Pieza Intervenida</th>
                <th>Origen Pieza</th><th>Serial Disco</th><th>Motivo Baja</th>
              </tr>";
        foreach ($registros as $row) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . esc($row['created_at']) . "</td>";
            echo "<td>" . esc($row['nombre_analista']) . "</td>";
            echo "<td>" . esc($row['numero_traslado']) . "</td>";
            echo "<td>" . esc($row['placa_id_equipo']) . "</td>";
            echo "<td>" . esc($row['tipo_gestion']) . "</td>";
            echo "<td>" . esc($row['energiza']) . "</td>";
            echo "<td>" . esc($row['da_video']) . "</td>";
            echo "<td>" . esc($row['realizo_test_lenovo']) . "</td>";
            echo "<td>" . esc($row['estado_actual_equipo']) . "</td>";
            echo "<td>" . esc($row['diagnostico_laptop_intervenido']) . "</td>";
            echo "<td>" . esc($row['garantia']) . "</td>";
            echo "<td>" . esc($row['numero_ticket']) . "</td>";
            echo "<td>" . esc($row['porque_solicita_garantia']) . "</td>";
            echo "<td>" . esc($row['estado_final_equipo']) . "</td>";
            echo "<td>" . esc($row['indique_pieza']) . "</td>";
            echo "<td>" . esc($row['indique_fru']) . "</td>";
            echo "<td>" . esc($row['pieza_intervenida']) . "</td>";
            echo "<td>" . esc($row['origen_pieza']) . "</td>";
            echo "<td>" . esc($row['serial_disco']) . "</td>";
            echo "<td>" . esc($row['motivo_baja']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }
}