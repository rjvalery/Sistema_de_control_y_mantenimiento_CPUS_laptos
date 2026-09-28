<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\EquipoModel;
use App\Models\PortatilModel;
use App\Models\InventarioGeneralModel;
use App\Models\SopladoModel;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Model;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $usuarioModel    = new UsuarioModel();
        $totalEquipos    = $this->contarRegistros(new EquipoModel()) ?? 0;
        $totalSoplado    = $this->contarRegistros(new SopladoModel()) ?? 0;
        $totalPortatiles = $this->contarRegistros(new PortatilModel()) ?? 0;
        $totalAnalistas  = $usuarioModel->where('rol', 'analista')->countAllResults();
        $totalIntervenciones = $totalEquipos + $totalSoplado + $totalPortatiles;

        $inventarioModel = new InventarioGeneralModel();
        $statsInventario = $inventarioModel->obtenerEstadisticasInventario();

        $data = [
            'statsInventario'     => $statsInventario,
            'totalIntervenciones' => $totalIntervenciones,
            'totalEquipos'        => $totalEquipos,
            'totalSoplado'        => $totalSoplado,
            'totalPortatiles'     => $totalPortatiles,
            'totalAnalistas'      => $totalAnalistas,
            'usuarios'            => (new UsuarioModel())->orderBy('id', 'ASC')->findAll(),
        ];

        if (session('usuario_rol') === 'analista') {
            return view('dashboard/analista', $data);
        }

        return view('dashboard/index', $data);
    }

    /**
     * Exporta la base de datos completa diagnostico_cpus en formato SQL.
     */
    public function exportarSql(): ResponseInterface
    {
        if (session('usuario_rol') !== 'admin') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Acceso denegado. Función reservada para administradores.');
        }

        $db = \Config\Database::connect();
        $dbName = 'diagnostico_cpus';
        $tables = $db->listTables();

        $sql = "-- ============================================================\n";
        $sql .= "-- BACKUP EXPORT COMPLETO: {$dbName}\n";
        $sql .= "-- FECHA DE EXPORTACIÓN: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- GENERADO PARA DESPLIEGUE EN SERVIDOR\n";
        $sql .= "-- ============================================================\n\n";

        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $sql .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "START TRANSACTION;\n";
        $sql .= "SET time_zone = '+00:00';\n";
        $sql .= "SET NAMES utf8mb4;\n\n";

        $sql .= "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\n";
        $sql .= "USE `{$dbName}`;\n\n";

        foreach ($tables as $table) {
            $sql .= "-- ------------------------------------------------------------\n";
            $sql .= "-- Estructura de tabla para `{$table}`\n";
            $sql .= "-- ------------------------------------------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

            $createRes = $db->query("SHOW CREATE TABLE `{$table}`")->getRowArray();
            if ($createRes && isset($createRes['Create Table'])) {
                $sql .= $createRes['Create Table'] . ";\n\n";
            }

            $rows = $db->table($table)->get()->getResultArray();
            if (!empty($rows)) {
                $total = count($rows);
                $sql .= "-- Volcado de datos para la tabla `{$table}` ({$total} registros)\n";
                $fields = array_keys($rows[0]);
                $fieldsList = '`' . implode('`, `', $fields) . '`';

                $buffer = [];
                foreach ($rows as $r) {
                    $vals = [];
                    foreach ($r as $val) {
                        if ($val === null) {
                            $vals[] = 'NULL';
                        } else {
                            $vals[] = $db->escape($val);
                        }
                    }
                    $buffer[] = '(' . implode(', ', $vals) . ')';

                    if (count($buffer) >= 50) {
                        $sql .= "INSERT INTO `{$table}` ({$fieldsList}) VALUES\n" . implode(",\n", $buffer) . ";\n";
                        $buffer = [];
                    }
                }

                if (!empty($buffer)) {
                    $sql .= "INSERT INTO `{$table}` ({$fieldsList}) VALUES\n" . implode(",\n", $buffer) . ";\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        $sql .= "COMMIT;\n";

        // Guardar copia local en la raíz del proyecto para conveniencia
        @file_put_contents(ROOTPATH . 'diagnostico_cpus.sql', $sql);

        return $this->response
            ->setHeader('Content-Type', 'application/sql; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="diagnostico_cpus.sql"')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody($sql);
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

