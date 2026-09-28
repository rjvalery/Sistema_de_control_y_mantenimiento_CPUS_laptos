<?php

namespace App\Controllers;

use App\Models\SopladoModel;
use App\Models\UsuarioModel;
use App\Models\InventarioGeneralModel;
use App\Services\UploadService;
use CodeIgniter\HTTP\ResponseInterface;

class Soplado extends BaseController
{
    protected $sopladoModel;
    protected $usuarioModel;
    protected $uploadService;
    protected $inventarioModel;

    public function __construct()
    {
        $this->sopladoModel    = new SopladoModel();
        $this->usuarioModel    = new UsuarioModel();
        $this->uploadService   = new UploadService();
        $this->inventarioModel = new InventarioGeneralModel();
    }

    public function formulario()
    {
        $data['analistas'] = $this->usuarioModel->where('rol', 'analista')->where('activo', 1)->orderBy('nombre', 'ASC')->findAll();
        return view('soplado/formulario', $data);
    }

    public function guardar()
    {
        $placaId  = $this->request->getPost('placa_id');
        $file     = $this->request->getFile('foto_equipo');
        $fotoRuta = $this->uploadService->guardarEvidencia($file, $placaId, 'soplado');

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
            'nombre_analista'  => $nombreAnalista,
            'num_traslado'     => $this->request->getPost('num_traslado'),
            'placa_id'         => $placaId,
            'energiza'         => $this->request->getPost('energiza'),
            'da_video'         => $this->request->getPost('da_video'),
            'detecta_disco'    => $this->request->getPost('detecta_disco'),
            'ingreso_bios'     => $this->request->getPost('ingreso_bios'),
            'pasta_termica'    => $this->request->getPost('pasta_termica'),
            'maquina_contenia' => $this->request->getPost('maquina_contenia'),
            'gel_cucarachas'   => $this->request->getPost('gel_cucarachas'),
            'foto_ruta'        => $fotoRuta
        ];

        if ($this->sopladoModel->insert($data)) {
            // Sincronizar y descontar de pendientes en inventario general
            $this->inventarioModel->marcarIntervenido((string)$placaId, 'soplado', $nombreAnalista);

            if ($isAjax) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Registro y evidencia guardados correctamente.']);
            }
            return redirect()->to(base_url('soplado/formulario'))->with('msg', 'Registro y evidencia guardados correctamente.');
        }

        if ($isAjax) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error al guardar en la base de datos.']);
        }

        return redirect()->back()->withInput()->with('error', 'Error al guardar en base de datos.');
    }

    public function bitacora()
    {
        $busqueda = trim($this->request->getGet('buscar') ?? '');
        $builder  = $this->sopladoModel->builder();

        if ($busqueda !== '') {
            $builder->like('placa_id', $busqueda)
                    ->orLike('num_traslado', $busqueda)
                    ->orLike('nombre_analista', $busqueda);
        }

        $data['registros'] = $builder->orderBy('id', 'DESC')->get()->getResultArray();
        $data['busqueda']  = $busqueda;

        return view('soplado/bitacora', $data);
    }

    public function exportar(): ResponseInterface
    {
        $busqueda = trim($this->request->getGet('buscar') ?? '');
        $builder  = $this->sopladoModel->builder();

        if ($busqueda !== '') {
            $builder->like('placa_id', $busqueda)
                    ->orLike('num_traslado', $busqueda)
                    ->orLike('nombre_analista', $busqueda);
        }

        $registros = $builder->orderBy('id', 'DESC')->get()->getResultArray();
        $filename  = "Reporte_Soplado_CPUs_" . date('Ymd_His') . ".csv";
        $delimitador = ';';

        $headers = [
            'ID', 'Fecha/Hora', 'Analista', 'N° Traslado',
            'Placa ID', 'Energiza', 'Da Video', 'Detecta Disco',
            'Ingresó BIOS', 'Pasta Térmica', 'Gel Cucarachas', 'Contenido Máquina'
        ];

        $output = "\xEF\xBB\xBF"; // UTF-8 BOM para apertura directa en Excel
        $output .= implode($delimitador, $headers) . "\r\n";

        foreach ($registros as $row) {
            $contenido = str_replace(["\r\n", "\r", "\n", '"'], [' ', ' ', ' ', '""'], (string)($row['maquina_contenia'] ?? '-'));

            $line = [
                $row['id'],
                $row['fecha_creacion'] ?? ($row['created_at'] ?? ''),
                '"' . str_replace('"', '""', (string)($row['nombre_analista'] ?? '')) . '"',
                '"' . str_replace('"', '""', (string)($row['num_traslado'] ?? '')) . '"',
                '"' . str_replace('"', '""', (string)($row['placa_id'] ?? '')) . '"',
                '"' . str_replace('"', '""', (string)($row['energiza'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string)($row['da_video'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string)($row['detecta_disco'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string)($row['ingreso_bios'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string)($row['pasta_termica'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string)($row['gel_cucarachas'] ?? '-')) . '"',
                '"' . $contenido . '"',
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