<?php

namespace App\Controllers;

use App\Models\AnalistaModel;

class Analistas extends BaseController
{
    protected $analistaModel;

    public function __construct()
    {
        $this->analistaModel = new AnalistaModel();
    }

    public function index()
    {
        $data['analistas'] = $this->analistaModel->orderBy('nombre', 'ASC')->findAll();
        return view('analistas/index', $data);
    }

    public function agregar()
    {
        $nombre = trim($this->request->getPost('nombre_analista') ?? '');
        if (!empty($nombre)) {
            $this->analistaModel->insert(['nombre' => $nombre]);
            return redirect()->to(base_url('analistas'))->with('msg', 'Analista agregado correctamente.');
        }
        return redirect()->to(base_url('analistas'))->with('error', 'El nombre no puede estar vacío.');
    }

    public function eliminar($id = null)
    {
        if ($id) {
            $this->analistaModel->delete($id);
            return redirect()->to(base_url('analistas'))->with('msg', 'Analista eliminado correctamente.');
        }
        return redirect()->to(base_url('analistas'));
    }
}