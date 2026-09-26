<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Bitácora de Equipos<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fs-6"><i class="fa-solid fa-list me-2"></i>Bitácora de Equipos Registrados</h5>
        <div>
            <a href="<?= base_url('equipos/formulario') ?>" class="btn btn-primary btn-sm me-2"><i class="fa-solid fa-plus me-1"></i> Nuevo</a>
            <a href="<?= base_url('equipos/exportar?buscar=' . urlencode($busqueda)) ?>" class="btn btn-success btn-sm"><i class="fa-solid fa-file-excel me-1"></i> Excel</a>
        </div>
    </div>
    <div class="card-body p-4">

        <form method="GET" action="<?= base_url('equipos/bitacora') ?>" class="row g-2 mb-4">
            <div class="col-md-4">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar por Placa, Traslado o Analista..." value="<?= esc($busqueda) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
            </div>
            <?php if (!empty($busqueda)): ?>
                <div class="col-md-2">
                    <a href="<?= base_url('equipos/bitacora') ?>" class="btn btn-outline-danger w-100">Limpiar</a>
                </div>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle text-nowrap">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Fecha/Hora</th>
                        <th>Analista</th>
                        <th>Traslado</th>
                        <th>Placa ID</th>
                        <th>Gestión</th>
                        <th>Energiza</th>
                        <th>Video</th>
                        <th>Estado</th>
                        <th>Intervención</th>
                        <th>Origen</th>
                        <th>Novedad</th>
                        <th>Motivo Baja</th>
                        <th>Serial Disco</th>
                        <th>Destino</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($registros)): ?>
                        <?php foreach ($registros as $row): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['fecha_creacion'] ?></td>
                                <td><?= esc($row['nombre_analista']) ?></td>
                                <td><?= esc($row['num_traslado']) ?></td>
                                <td><strong><?= esc($row['placa_id']) ?></strong></td>
                                <td><span class="badge bg-primary"><?= esc($row['tipo_gestion']) ?></span></td>
                                <td><?= esc($row['energiza'] ?? '-') ?></td>
                                <td><?= esc($row['da_video'] ?? '-') ?></td>
                                <td><?= esc($row['estado_actual'] ?? '-') ?></td>
                                <td><?= esc($row['que_va_intervenir'] ?? '-') ?></td>
                                <td><?= esc($row['origen_pieza'] ?? '-') ?></td>
                                <td><?= esc($row['descripcion_novedad'] ?? '-') ?></td>
                                <td><?= esc($row['motivo_baja'] ?? '-') ?></td>
                                <td><?= esc($row['serial_disco'] ?? '-') ?></td>
                                <td><?= esc($row['ubicacion_destino'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="15" class="text-center text-muted py-3">No hay registros aún.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
<?= $this->endSection() ?>