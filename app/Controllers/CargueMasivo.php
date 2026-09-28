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
                ->like('identificador_1', $busqueda)
                ->orLike('identificador_2', $busqueda)
                ->orLike('ref_principal', $busqueda)
                ->orLike('descripcion', $busqueda)
                ->orLike('zona_origen', $busqueda)
                ->orLike('ubicacion_origen', $busqueda)
                ->orLike('verificado', $busqueda)
                ->orLike('observaciones', $busqueda)
                ->orLike('placa_id', $busqueda)
                ->orLike('serial', $busqueda)
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
     * Descarga la plantilla CSV oficial con codificación UTF-8 basada en Formato en Cubic (8 columnas).
     */
    public function plantilla(): ResponseInterface
    {
        if (session('usuario_rol') !== 'admin') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Acceso denegado.');
        }

        $delimitador = ';';
        $filename    = 'plantilla_cargue_formato_cubic.csv';

        $headers = [
            'Identificador 1',
            'Identificador 2',
            'Ref. Principal',
            'Descripción',
            'Zona Origen',
            'Ubicación Origen',
            'Verificado',
            'Observaciones'
        ];

        $ejemplos = [
            ['ACT-10021', 'SN-MBP99201', 'MacBook Pro 16 M1', 'Portátil corporativo Apple', 'Sede Central', 'Piso 3 - Operaciones', 'Verificado', 'Equipo en buen estado físico'],
            ['ACT-10022', 'SN-TC883011', 'ThinkCentre M70q', 'CPU de escritorio Lenovo', 'Sede Norte', 'Bodega 1 - Estante B', 'Pendiente', 'Requiere mantenimiento y soplado'],
        ];

        $output = "\xEF\xBB\xBF"; // UTF-8 BOM para compatibilidad total con Excel
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
     * Procesa la importación masiva adaptada a las 8 columnas del Formato en Cubic.
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
            return redirect()->to(base_url('cargue-masivo'))->with('error', 'El archivo debe tener extensión .csv o .txt.');
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

        rewind($handle);
        if ($bom === "\xEF\xBB\xBF") {
            fread($handle, 3);
        }

        $rawHeaders = fgetcsv($handle, 0, $delimitador);
        if (!$rawHeaders) {
            fclose($handle);
            return redirect()->to(base_url('cargue-masivo'))->with('error', 'No se encontraron encabezados válidos.');
        }

        // Normalizar encabezados (quitar tildes, minúsculas y caracteres extraños)
        $headers = array_map(function ($h) {
            $str = mb_strtolower(trim((string)$h), 'UTF-8');
            $str = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $str);
            $str = preg_replace('/[^a-z0-9_]/', '_', $str);
            return trim(preg_replace('/_+/', '_', $str), '_');
        }, $rawHeaders);

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

            // Mapeo flexible de las 8 columnas del Formato en Cubic
            $id1             = $mapped['identificador_1'] ?? $mapped['identificador1'] ?? $mapped['placa_id'] ?? $mapped['placa'] ?? $mapped['activo'] ?? null;
            $id2             = $mapped['identificador_2'] ?? $mapped['identificador2'] ?? $mapped['serial'] ?? $mapped['serie'] ?? $mapped['sn'] ?? null;
            $refPrincipal    = $mapped['ref_principal'] ?? $mapped['refprincipal'] ?? $mapped['referencia'] ?? $mapped['modelo'] ?? null;
            $descripcion     = $mapped['descripcion'] ?? $mapped['tipo_equipo'] ?? $mapped['equipo'] ?? $mapped['detalle'] ?? null;
            $zonaOrigen      = $mapped['zona_origen'] ?? $mapped['zona'] ?? $mapped['sede'] ?? $mapped['bodega'] ?? null;
            $ubicacionOrigen = $mapped['ubicacion_origen'] ?? $mapped['ubicacion'] ?? $mapped['puesto'] ?? null;
            $verificado      = $mapped['verificado'] ?? $mapped['estado'] ?? $mapped['estatus'] ?? null;
            $observaciones   = $mapped['observaciones'] ?? $mapped['notas'] ?? $mapped['comentario'] ?? null;

            // Si la fila no contiene ningún dato identificador clave, se omite
            if (empty($id1) && empty($id2) && empty($refPrincipal)) {
                $errores++;
                continue;
            }

            // Datos derivados para compatibilidad total con el resto del sistema (formularios/dashboard)
            $placaDerivada = !empty($id1) ? $id1 : $id2;
            $serialDerivado = !empty($id2) ? $id2 : $id1;
            $tipoDerivado = !empty($descripcion) ? $descripcion : 'General';
            $modeloDerivado = !empty($refPrincipal) ? $refPrincipal : $descripcion;
            $ubicacionDerivada = trim(($zonaOrigen ?? '') . ($ubicacionOrigen ? ' - ' . $ubicacionOrigen : ''), ' -');
            $estadoDerivado = !empty($verificado) ? $verificado : 'Cargado';

            $insertData = [
                'identificador_1'   => $id1,
                'identificador_2'   => $id2,
                'ref_principal'     => $refPrincipal,
                'descripcion'       => $descripcion,
                'zona_origen'       => $zonaOrigen,
                'ubicacion_origen'  => $ubicacionOrigen,
                'verificado'        => $verificado,
                'observaciones'     => $observaciones,
                'placa_id'          => $placaDerivada,
                'serial'            => $serialDerivado,
                'tipo_equipo'       => $tipoDerivado,
                'marca'             => null,
                'modelo'            => $modeloDerivado,
                'ubicacion'         => $ubicacionDerivada,
                'estado'            => $estadoDerivado,
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
            $msg .= " Hubo {$errores} filas omitidas por no tener identificadores válidos.";
        }

        return redirect()->to(base_url('cargue-masivo'))->with('msg', $msg);
    }

    /**
     * Permite vaciar la tabla inventario_general.
     */
    public function vaciar(): ResponseInterface
    {
        if (session('usuario_rol') !== 'admin') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Acceso denegado.');
        }

        $this->inventarioModel->asegurarTabla();
        $this->inventarioModel->truncate();

        return redirect()->to(base_url('cargue-masivo'))->with('msg', 'Los registros de inventario_general han sido vaciados correctamente.');
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
