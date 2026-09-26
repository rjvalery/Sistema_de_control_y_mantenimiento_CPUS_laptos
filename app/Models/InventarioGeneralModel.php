<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class InventarioGeneralModel extends Model
{
    protected $table         = 'inventario_general';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
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
            KEY `idx_placa` (`placa_id`),
            KEY `idx_serial` (`serial`),
            KEY `idx_intervenido` (`intervenido`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

        $this->db->query($sql);

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
    }

    /**
     * Busca un equipo en inventario por coincidencia exacta o cercana con placa_id o serial.
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
                ->where('placa_id', $queryLimpia)
                ->orWhere('serial', $queryLimpia)
            ->groupEnd()
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        if ($exacto) {
            return $exacto;
        }

        if (strlen($queryLimpia) >= 4) {
            return $this->builder()
                ->groupStart()
                    ->like('placa_id', $queryLimpia)
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
