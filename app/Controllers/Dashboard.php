<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\AnalistaModel;
use App\Models\EquipoModel;
use App\Models\PortatilModel;
use App\Models\SopladoModel;
use App\Models\UsuarioModel;
use CodeIgniter\Model;

class Dashboard extends BaseController
{
    public function index(): string
    {
        if (session('usuario_rol') === 'analista') {
            return view('dashboard/analista');
        }

        $data = [
            'totalEquipos'    => $this->contarRegistros(new EquipoModel()),
            'totalSoplado'    => $this->contarRegistros(new SopladoModel()),
            'totalPortatiles' => $this->contarRegistros(new PortatilModel()),
            'totalAnalistas'  => $this->contarRegistros(new AnalistaModel()),
            'usuarios'        => (new UsuarioModel())->orderBy('id', 'ASC')->findAll(),
        ];

        return view('dashboard/index', $data);
    }

    private function contarRegistros(Model $model): ?int
    {
        try {
            return $model->countAllResults();
        } catch (\Throwable) {
            return null;
        }
    }
}

