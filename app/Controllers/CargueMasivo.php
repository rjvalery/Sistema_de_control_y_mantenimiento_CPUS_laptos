<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\InventarioGeneralModel;
use CodeIgniter\HTTP\ResponseInterface;

class CargueMasivo extends BaseController
{
    protected InventarioGeneralModel $inventarioModel;

    public function __construct()
    {
        $this->inventarioModel = new InventarioGeneralModel();
    }

    /**
     * Muestra la interfaz de cargue masivo directo a la base de datos (inventario_general).
     */
    public function index(): string|ResponseInterface
    {
        if (session('usuario_rol') !== 'admin') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Acceso denegado. Función reservada para administradores.');
        }

        $this->inventarioModel->asegurarTabla();

        $busqueda = trim((string) $this->request->getGet('buscar'));
        $builder = $this->inventarioModel->builder();

        if ($busqueda !== '') {
            $builder->groupStart()
                ->like('placa_id', $busqueda)
                ->orLike('serial', $busqueda)
                ->orLike('marca', $busqueda)
                ->orLike('modelo', $busqueda)
                ->orLike('ubicacion', $busqueda)
                ->groupEnd();
        }

        $registros = $builder->orderBy('id', 'DESC')->limit(100)->get()->getResultArray();
        $totalRegistros = $this->inventarioModel->countAllResults();

        $data = [
            'registros'      => $registros,
            'totalRegistros' => $totalRegistros,
            'busqueda'       => $busqueda,
        ];

        return view('cargue_masivo/index', $data);
    }

    /**
     * Descarga la plantilla CSV oficial con codificación UTF-8 para Excel.
     */
    public function plantilla(): ResponseInterface
    {
        if (session('usuario_rol') !== 'admin') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Acceso denegado.');
        }

        $delimitador = ';';
        $filename    = 'plantilla_cargue_masivo_inventario.csv';

        $headers = [
            'placa_id',
            'serial',
            'tipo_equipo',
            'marca',
            'modelo',
            'ubicacion',
            'estado',
            'observaciones'
        ];

        $ejemplos = [
            ['CPU-001', 'SN-A10099', 'CPU', 'Lenovo', 'ThinkCentre M720', 'Piso 2 - Operaciones', 'Activo', 'Equipo asignado'],
            ['LAP-002', 'SN-L88721', 'Portatil', 'HP', 'ProBook 450 G8', 'Piso 3 - Finanzas', 'En reparacion', 'Revisión técnica'],
        ];

        $output = "\xEF\xBB\xBF"; // UTF-8 BOM
        $output .= implode($delimitador, $headers) . "\r\n";
        foreach ($ejemplos as $row) {
            $output .= implode($delimitador, $row) . "\r\n";
        }

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody($output);
    }

    /**
     * Procesa la importación directa a la tabla inventario_general.
     */
    public function procesar(): ResponseInterface
    {
        if (session('usuario_rol') !== 'admin') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Acceso denegado.');
        }

        $this->inventarioModel->asegurarTabla();

        $file = $this->request->getFile('archivo_csv');
        if (!$file || !$file->isValid()) {
            return redirect()->to(base_url('cargue-masivo'))->with('error', 'Por favor selecciona un archivo CSV válido.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['csv', 'txt'], true)) {
            return redirect()->to(base_url('cargue-masivo'))->with('error', 'El archivo debe tener extensión .csv.');
        }

        $realPath = $file->getTempName();
        $handle = fopen($realPath, 'r');
        if (!$handle) {
            return redirect()->to(base_url('cargue-masivo'))->with('error', 'No se pudo abrir el archivo para lectura.');
        }

        // Detectar y saltar BOM UTF-8
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Detectar delimitador leyendo la primera línea
        $primeraLinea = fgets($handle);
        if (!$primeraLinea) {
            fclose($handle);
            return redirect()->to(base_url('cargue-masivo'))->with('error', 'El archivo cargado está vacío.');
        }

        $delimitador = (substr_count($primeraLinea, ';') >= substr_count($primeraLinea, ',')) ? ';' : ',';

        // Rebobinar para procesar con el delimitador detectado
        rewind($handle);
        if ($bom === "\xEF\xBB\xBF") {
            fread($handle, 3);
        }

        $rawHeaders = fgetcsv($handle, 0, $delimitador);
        if (!$rawHeaders) {
            fclose($handle);
            return redirect()->to(base_url('cargue-masivo'))->with('error', 'No se encontraron encabezados válidos.');
        }

        $headers = array_map(static fn($h) => strtolower(trim((string)$h)), $rawHeaders);
        $nombreArchivo = $file->getClientName();
        $usuarioCargue = (string) session('usuario_nombre');
        $ahora = date('Y-m-d H:i:s');

        $insertados = 0;
        $errores    = 0;

        while (($row = fgetcsv($handle, 0, $delimitador)) !== false) {
            if ($this->filaEstaVacia($row)) {
                continue;
            }

            $mapped = [];
            foreach ($headers as $index => $header) {
                if (isset($row[$index])) {
                    $mapped[$header] = trim((string)$row[$index]);
                }
            }

            // Identificar campos clave con alias comunes
            $placa = $mapped['placa_id'] ?? ($mapped['placa'] ?? ($mapped['activo'] ?? ($mapped['id_equipo'] ?? null)));
            $serial = $mapped['serial'] ?? ($mapped['serie'] ?? ($mapped['numero_serie'] ?? ($mapped['sn'] ?? null)));
            $tipo = $mapped['tipo_equipo'] ?? ($mapped['tipo'] ?? ($mapped['equipo'] ?? ($mapped['clase'] ?? null)));
            $marca = $mapped['marca'] ?? ($mapped['fabricante'] ?? null);
            $modelo = $mapped['modelo'] ?? null;
            $ubicacion = $mapped['ubicacion'] ?? ($mapped['sede'] ?? ($mapped['area'] ?? ($mapped['sitio'] ?? null)));
            $estado = $mapped['estado'] ?? ($mapped['condicion'] ?? ($mapped['estatus'] ?? null));

            // Si no tiene placa ni serial, omitir
            if (empty($placa) && empty($serial)) {
                $errores++;
                continue;
            }

            // Campos sobrantes para datos_adicionales
            $adicionales = array_diff_key($mapped, array_flip([
                'placa_id', 'placa', 'activo', 'id_equipo',
                'serial', 'serie', 'numero_serie', 'sn',
                'tipo_equipo', 'tipo', 'equipo', 'clase',
                'marca', 'fabricante', 'modelo',
                'ubicacion', 'sede', 'area', 'sitio',
                'estado', 'condicion', 'estatus'
            ]));

            $insertData = [
                'placa_id'          => $placa,
                'serial'            => $serial,
                'tipo_equipo'       => $tipo,
                'marca'             => $marca,
                'modelo'            => $modelo,
                'ubicacion'         => $ubicacion,
                'estado'            => $estado,
                'datos_adicionales' => !empty($adicionales) ? json_encode($adicionales, JSON_UNESCAPED_UNICODE) : null,
                'archivo_origen'    => $nombreArchivo,
                'usuario_cargue'    => $usuarioCargue,
                'created_at'        => $ahora,
            ];

            if ($this->inventarioModel->insert($insertData)) {
                $insertados++;
            } else {
                $errores++;
            }
        }

        fclose($handle);

        $msg = "Cargue masivo completado. Se insertaron <strong>{$insertados}</strong> registros en la tabla <code>inventario_general</code>.";
        if ($errores > 0) {
            $msg .= " Hubo {$errores} filas omitidas por falta de placa/serial.";
        }

        return redirect()->to(base_url('cargue-masivo'))->with('msg', $msg);
    }

    /**
     * Permite vaciar la tabla inventario_general si se requiere recargar de cero.
     */
    public function vaciar(): ResponseInterface
    {
        if (session('usuario_rol') !== 'admin') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Acceso denegado.');
        }

        $this->inventarioModel->asegurarTabla();
        $this->inventarioModel->truncate();

        return redirect()->to(base_url('cargue-masivo'))->with('msg', 'La tabla inventario_general ha sido vaciada correctamente.');
    }

    private function filaEstaVacia(array $row): bool
    {
        foreach ($row as $val) {
            if (trim((string)$val) !== '') {
                return false;
            }
        }
        return true;
    }
}
