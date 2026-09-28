<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class InventarioGeneralModel extends Model
{
    protected $table         = 'inventario_general';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'identificador_1',
        'identificador_2',
        'ref_principal',
        'descripcion',
        'zona_origen',
        'ubicacion_origen',
        'verificado',
        'observaciones',
        'placa_id',
        'serial',
        'tipo_equipo',
        'marca',
        'modelo',
        'ubicacion',
        'estado',
        'datos_adicionales',
        'archivo_origen',
        'usuario_cargue',
        'intervenido',
        'fecha_intervencion',
        'modulo_intervencion',
        'analista_intervencion',
        'created_at',
    ];
    protected $useTimestamps = false;
    protected $returnType    = 'array';

    /**
     * Crea la tabla en la base de datos si aún no existe y asegura las columnas requeridas.
     */
    public function asegurarTabla(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `inventario_general` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `identificador_1` VARCHAR(100) NULL,
            `identificador_2` VARCHAR(100) NULL,
            `ref_principal` VARCHAR(150) NULL,
            `descripcion` VARCHAR(255) NULL,
            `zona_origen` VARCHAR(100) NULL,
            `ubicacion_origen` VARCHAR(150) NULL,
            `verificado` VARCHAR(50) NULL,
            `observaciones` TEXT NULL,
            `placa_id` VARCHAR(100) NULL,
            `serial` VARCHAR(100) NULL,
            `tipo_equipo` VARCHAR(80) NULL,
            `marca` VARCHAR(100) NULL,
            `modelo` VARCHAR(150) NULL,
            `ubicacion` VARCHAR(150) NULL,
            `estado` VARCHAR(80) NULL,
            `datos_adicionales` TEXT NULL,
            `archivo_origen` VARCHAR(255) NULL,
            `usuario_cargue` VARCHAR(120) NULL,
            `intervenido` TINYINT(1) DEFAULT 0,
            `fecha_intervencion` DATETIME NULL,
            `modulo_intervencion` VARCHAR(50) NULL,
            `analista_intervencion` VARCHAR(120) NULL,
            `created_at` DATETIME NULL,
            KEY `idx_id1` (`identificador_1`),
            KEY `idx_id2` (`identificador_2`),
            KEY `idx_placa` (`placa_id`),
            KEY `idx_serial` (`serial`),
            KEY `idx_intervenido` (`intervenido`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

        $this->db->query($sql);

        // Asegurar que las columnas del Formato en Cubic existan
        $columnasCubic = [
            'identificador_1'  => "ALTER TABLE `inventario_general` ADD COLUMN `identificador_1` VARCHAR(100) NULL AFTER `id`;",
            'identificador_2'  => "ALTER TABLE `inventario_general` ADD COLUMN `identificador_2` VARCHAR(100) NULL AFTER `identificador_1`;",
            'ref_principal'    => "ALTER TABLE `inventario_general` ADD COLUMN `ref_principal` VARCHAR(150) NULL AFTER `identificador_2`;",
            'descripcion'      => "ALTER TABLE `inventario_general` ADD COLUMN `descripcion` VARCHAR(255) NULL AFTER `ref_principal`;",
            'zona_origen'      => "ALTER TABLE `inventario_general` ADD COLUMN `zona_origen` VARCHAR(100) NULL AFTER `descripcion`;",
            'ubicacion_origen' => "ALTER TABLE `inventario_general` ADD COLUMN `ubicacion_origen` VARCHAR(150) NULL AFTER `zona_origen`;",
            'verificado'       => "ALTER TABLE `inventario_general` ADD COLUMN `verificado` VARCHAR(50) NULL AFTER `ubicacion_origen`;",
            'observaciones'    => "ALTER TABLE `inventario_general` ADD COLUMN `observaciones` TEXT NULL AFTER `verificado`;",
        ];

        foreach ($columnasCubic as $col => $alter) {
            if (!$this->db->fieldExists($col, 'inventario_general')) {
                $this->db->query($alter);
            }
        }

        // Si la tabla fue creada previamente sin las columnas de intervención, agregarlas
        if (!$this->db->fieldExists('intervenido', 'inventario_general')) {
            $this->db->query("ALTER TABLE `inventario_general` ADD COLUMN `intervenido` TINYINT(1) DEFAULT 0 AFTER `usuario_cargue`;");
            $this->db->query("ALTER TABLE `inventario_general` ADD KEY `idx_intervenido` (`intervenido`);");
        }
        if (!$this->db->fieldExists('fecha_intervencion', 'inventario_general')) {
            $this->db->query("ALTER TABLE `inventario_general` ADD COLUMN `fecha_intervencion` DATETIME NULL AFTER `intervenido`;");
        }
        if (!$this->db->fieldExists('modulo_intervencion', 'inventario_general')) {
            $this->db->query("ALTER TABLE `inventario_general` ADD COLUMN `modulo_intervencion` VARCHAR(50) NULL AFTER `fecha_intervencion`;");
        }
        if (!$this->db->fieldExists('analista_intervencion', 'inventario_general')) {
            $this->db->query("ALTER TABLE `inventario_general` ADD COLUMN `analista_intervencion` VARCHAR(120) NULL AFTER `modulo_intervencion`;");
        }

        // Eliminar base de datos externa 'base_de_datos' si aún existe en MySQL
        try {
            $this->db->query("DROP DATABASE IF EXISTS `base_de_datos`;");
        } catch (\Throwable $e) {
            // Ignorar si no existen permisos o no existe la base de datos
        }
    }

    /**
     * Busca un equipo en inventario por coincidencia exacta o cercana con identificadores o seriales.
     */
    public function buscarPorTermino(string $query): ?array
    {
        $this->asegurarTabla();
        $queryLimpia = trim($query);
        if ($queryLimpia === '') {
            return null;
        }

        $exacto = $this->builder()
            ->groupStart()
                ->where('identificador_1', $queryLimpia)
                ->orWhere('identificador_2', $queryLimpia)
                ->orWhere('ref_principal', $queryLimpia)
                ->orWhere('placa_id', $queryLimpia)
                ->orWhere('serial', $queryLimpia)
            ->groupEnd()
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        if ($exacto) {
            return $exacto;
        }

        if (strlen($queryLimpia) >= 3) {
            return $this->builder()
                ->groupStart()
                    ->like('identificador_1', $queryLimpia)
                    ->orLike('identificador_2', $queryLimpia)
                    ->orLike('ref_principal', $queryLimpia)
                    ->orLike('placa_id', $queryLimpia)
                    ->orLike('serial', $queryLimpia)
                ->groupEnd()
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();
        }

        return null;
    }

    /**
     * Marca un equipo como intervenido en el inventario cuando se registra en cualquier formulario.
     */
    public function marcarIntervenido(string $placaOserial, string $modulo, string $analista): bool
    {
        $this->asegurarTabla();
        $termino = trim($placaOserial);
        if ($termino === '') {
            return false;
        }

        $equipo = $this->buscarPorTermino($termino);
        if (!$equipo) {
            return false;
        }

        return $this->update($equipo['id'], [
            'intervenido'           => 1,
            'fecha_intervencion'    => date('Y-m-d H:i:s'),
            'modulo_intervencion'   => $modulo,
            'analista_intervencion' => $analista,
        ]);
    }

    /**
     * Retorna las estadísticas consolidadas del inventario para el dashboard.
     */
    public function obtenerEstadisticasInventario(): array
    {
        $this->asegurarTabla();

        $totalCargados = (int) $this->countAllResults();
        
        $intervenidosEnInventario = (int) $this->where('intervenido', 1)->countAllResults();
        $pendientesEnInventario  = max(0, $totalCargados - $intervenidosEnInventario);

        return [
            'totalCargados'  => $totalCargados,
            'intervenidos'   => $intervenidosEnInventario,
            'pendientes'     => $pendientesEnInventario,
            'porcentaje'     => $totalCargados > 0 ? round(($intervenidosEnInventario / $totalCargados) * 100, 1) : 0,
        ];
    }
}
