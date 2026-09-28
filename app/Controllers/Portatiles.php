<?php

namespace App\Controllers;

use App\Models\PortatilModel;
use App\Models\UsuarioModel;
use App\Models\InventarioGeneralModel;
use App\Services\UploadService;
use CodeIgniter\HTTP\ResponseInterface;

class Portatiles extends BaseController
{
    protected $portatilModel;
    protected $usuarioModel;
    protected $uploadService;
    protected $inventarioModel;

    public function __construct()
    {
        $this->portatilModel   = new PortatilModel();
        $this->usuarioModel    = new UsuarioModel();
        $this->uploadService   = new UploadService();
        $this->inventarioModel = new InventarioGeneralModel();
    }

    public function formulario()
    {
        $data['analistas'] = $this->usuarioModel->where('rol', 'analista')->where('activo', 1)->orderBy('nombre', 'ASC')->findAll();
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
            // Sincronizar y descontar de pendientes en inventario general
            $this->inventarioModel->marcarIntervenido($placaId, 'portatil', $nombreAnalista);

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

    public function exportar(): ResponseInterface
    {
        $busqueda = trim($this->request->getGet('buscar') ?? '');
        $builder  = $this->portatilModel->builder();

        if ($busqueda !== '') {
            $builder->like('placa_id_equipo', $busqueda)
                    ->orLike('numero_ticket', $busqueda)
                    ->orLike('nombre_analista', $busqueda);
        }

        $registros = $builder->orderBy('id', 'DESC')->get()->getResultArray();
        $filename  = "Reporte_Portatiles_Garantias_" . date('Ymd_His') . ".csv";
        $delimitador = ';';

        $headers = [
            'ID', 'Fecha', 'Analista', 'N° Traslado', 'Placa ID',
            'Gestión', 'Energiza', 'Da Video', 'Test Lenovo', 'Estado Actual',
            'Diagnóstico', 'Garantía', 'N° Ticket', 'Razón Garantía',
            'Estado Final', 'Pieza', 'FRU', 'Pieza Intervenida',
            'Origen Pieza', 'Serial Disco', 'Motivo Baja'
        ];

        $output = "\xEF\xBB\xBF"; // UTF-8 BOM para apertura directa en Excel
        $output .= implode($delimitador, $headers) . "\r\n";

        foreach ($registros as $row) {
            $diagnostico = str_replace(["\r\n", "\r", "\n", '"'], [' ', ' ', ' ', '""'], (string)($row['diagnostico_laptop_intervenido'] ?? '-'));
            $razon = str_replace(["\r\n", "\r", "\n", '"'], [' ', ' ', ' ', '""'], (string)($row['porque_solicita_garantia'] ?? '-'));
            $motivoBaja = str_replace(["\r\n", "\r", "\n", '"'], [' ', ' ', ' ', '""'], (string)($row['motivo_baja'] ?? '-'));

            $line = [
                $row['id'],
                $row['created_at'] ?? '',
                '"' . str_replace('"', '""', (string)($row['nombre_analista'] ?? '')) . '"',
                '"' . str_replace('"', '""', (string)($row['numero_traslado'] ?? '')) . '"',
                '"' . str_replace('"', '""', (string)($row['placa_id_equipo'] ?? '')) . '"',
                '"' . str_replace('"', '""', (string)($row['tipo_gestion'] ?? '')) . '"',
                '"' . str_replace('"', '""', (string)($row['energiza'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string)($row['da_video'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string)($row['realizo_test_lenovo'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string)($row['estado_actual_equipo'] ?? '-')) . '"',
                '"' . $diagnostico . '"',
                '"' . str_replace('"', '""', (string)($row['garantia'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string)($row['numero_ticket'] ?? '-')) . '"',
                '"' . $razon . '"',
                '"' . str_replace('"', '""', (string)($row['estado_final_equipo'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string)($row['indique_pieza'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string)($row['indique_fru'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string)($row['pieza_intervenida'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string)($row['origen_pieza'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string)($row['serial_disco'] ?? '-')) . '"',
                '"' . $motivoBaja . '"',
            ];

            $output .= implode($delimitador, $line) . "\r\n";
        }

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody($output);
    }
}