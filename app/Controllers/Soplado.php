<?php

namespace App\Controllers;

use App\Models\SopladoModel;
use App\Models\UsuarioModel;
use App\Models\InventarioGeneralModel;
use App\Services\UploadService;

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

    public function exportar()
    {
        $busqueda = trim($this->request->getGet('buscar') ?? '');
        $builder  = $this->sopladoModel->builder();

        if ($busqueda !== '') {
            $builder->like('placa_id', $busqueda)
                    ->orLike('num_traslado', $busqueda)
                    ->orLike('nombre_analista', $busqueda);
        }

        $registros = $builder->orderBy('id', 'DESC')->get()->getResultArray();
        $filename  = "bitacora_soplado_" . date('Y-m-d_H-i') . ".xls";

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<?mso-application progid="Excel.Sheet"?>';
        ?>
        <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
          <Styles>
            <Style ss:ID="Header"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#2563EB" ss:Pattern="Solid"/></Style>
          </Styles>
          <Worksheet ss:Name="Bitácora Soplado">
            <Table>
              <Row>
                <Cell ss:StyleID="Header"><Data ss:Type="String">ID</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Analista</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">N° Traslado</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Placa ID</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Energiza</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Da Video</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Detecta Disco</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Ingresó BIOS</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Pasta Térmica</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Gel Cucarachas</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Contenido</Data></Cell>
              </Row>
              <?php foreach ($registros as $row): ?>
              <Row>
                <Cell><Data ss:Type="Number"><?= $row['id'] ?></Data></Cell>
                <Cell><Data ss:Type="String"><?= esc($row['nombre_analista']) ?></Data></Cell>
                <Cell><Data ss:Type="String"><?= esc($row['num_traslado']) ?></Data></Cell>
                <Cell><Data ss:Type="String"><?= esc($row['placa_id']) ?></Data></Cell>
                <Cell><Data ss:Type="String"><?= esc($row['energiza']) ?></Data></Cell>
                <Cell><Data ss:Type="String"><?= esc($row['da_video']) ?></Data></Cell>
                <Cell><Data ss:Type="String"><?= esc($row['detecta_disco']) ?></Data></Cell>
                <Cell><Data ss:Type="String"><?= esc($row['ingreso_bios']) ?></Data></Cell>
                <Cell><Data ss:Type="String"><?= esc($row['pasta_termica']) ?></Data></Cell>
                <Cell><Data ss:Type="String"><?= esc($row['gel_cucarachas']) ?></Data></Cell>
                <Cell><Data ss:Type="String"><?= esc($row['maquina_contenia']) ?></Data></Cell>
              </Row>
              <?php endforeach; ?>
            </Table>
          </Worksheet>
        </Workbook>
        <?php
        exit;
    }
}