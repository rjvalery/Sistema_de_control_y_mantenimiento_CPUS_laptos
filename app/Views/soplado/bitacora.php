<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Bitácora de Soplado<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fs-6"><i class="fa-solid fa-list me-2"></i>Bitácora de Soplado</h5>
        <div>
            <a href="<?= base_url('soplado/formulario') ?>" class="btn btn-primary btn-sm me-2"><i class="fa-solid fa-plus me-1"></i> Nuevo</a>
            <a href="<?= base_url('soplado/exportar?buscar=' . urlencode($busqueda)) ?>" class="btn btn-success btn-sm"><i class="fa-solid fa-file-excel me-1"></i> Excel</a>
        </div>
    </div>
    <div class="card-body p-4">
        
        <form method="GET" action="<?= base_url('soplado/bitacora') ?>" class="row g-2 mb-4">
            <div class="col-md-4">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar por Placa, Traslado o Analista..." value="<?= esc($busqueda) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
            </div>
            <?php if (!empty($busqueda)): ?>
                <div class="col-md-2">
                    <a href="<?= base_url('soplado/bitacora') ?>" class="btn btn-outline-danger w-100">Limpiar</a>
                </div>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle text-nowrap">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Analista</th>
                        <th>Traslado</th>
                        <th>Placa ID</th>
                        <th>Energiza</th>
                        <th>Video</th>
                        <th>Disco</th>
                        <th>BIOS</th>
                        <th>Pasta</th>
                        <th>Gel</th>
                        <th>Contenido</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($registros)): ?>
                        <?php foreach ($registros as $r): ?>
                            <tr>
                                <td><?= $r['id'] ?></td>
                                <td><?= esc($r['nombre_analista']) ?></td>
                                <td><?= esc($r['num_traslado']) ?></td>
                                <td><strong><?= esc($r['placa_id']) ?></strong></td>
                                <td><span class="badge bg-<?= $r['energiza'] === 'Si' ? 'success' : 'danger' ?>"><?= $r['energiza'] ?></span></td>
                                <td><span class="badge bg-<?= $r['da_video'] === 'Si' ? 'success' : 'danger' ?>"><?= $r['da_video'] ?></span></td>
                                <td><span class="badge bg-<?= $r['detecta_disco'] === 'Si' ? 'success' : 'danger' ?>"><?= $r['detecta_disco'] ?></span></td>
                                <td><span class="badge bg-<?= $r['ingreso_bios'] === 'Si' ? 'success' : 'danger' ?>"><?= $r['ingreso_bios'] ?></span></td>
                                <td><span class="badge bg-<?= $r['pasta_termica'] === 'Si' ? 'success' : 'danger' ?>"><?= $r['pasta_termica'] ?></span></td>
                                <td><span class="badge bg-<?= $r['gel_cucarachas'] === 'Si' ? 'success' : 'danger' ?>"><?= $r['gel_cucarachas'] ?></span></td>
                                <td><?= esc($r['maquina_contenia']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="11" class="text-center text-muted py-3">No hay registros encontrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
<?= $this->endSection() ?>