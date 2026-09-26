<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Panel del Analista<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .module-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px;
        overflow: hidden;
    }
    .module-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
    }
    .icon-wrapper {
        width: 72px;
        height: 72px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-bottom: 1rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="fa-solid fa-clipboard-check text-primary me-2"></i>Panel del Analista
                </h1>
                <p class="text-muted mb-0">
                    Bienvenido, <strong><?= esc(session('usuario_nombre')) ?></strong>. Selecciona la tarea que deseas registrar:
                </p>
            </div>
            <div class="mt-2 mt-md-0">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-user-gear me-1"></i> Rol: Analista
                </span>
            </div>
        </div>

        <?php if (!empty($statsInventario)): ?>
        <!-- WIDGET CONTROL DE INVENTARIO Y STOCK -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark fs-6">
                            <i class="fa-solid fa-boxes-stacked text-primary me-2"></i>Estado del Inventario General
                        </h5>
                        <span class="text-muted small">Los equipos registrados en tus formularios se sincronizan y descuentan del stock pendiente.</span>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill mt-2 mt-md-0 fw-semibold">
                        <i class="fa-solid fa-chart-pie me-1"></i> <?= esc((string)$statsInventario['porcentaje']) ?>% Procesado
                    </span>
                </div>
                
                <div class="row g-2 g-md-3 text-center mb-3">
                    <div class="col-4">
                        <div class="p-2 rounded bg-light border">
                            <small class="text-muted d-block small">Total en Sistema</small>
                            <span class="fs-5 fw-bold text-dark"><?= esc((string)$statsInventario['totalCargados']) ?></span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded bg-success-subtle border border-success-subtle">
                            <small class="text-success d-block fw-semibold small">Intervenidos</small>
                            <span class="fs-5 fw-bold text-success"><?= esc((string)$statsInventario['intervenidos']) ?></span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded bg-warning-subtle border border-warning-subtle">
                            <small class="text-warning-emphasis d-block fw-semibold small">Pendientes (Stock)</small>
                            <span class="fs-5 fw-bold text-warning-emphasis"><?= esc((string)$statsInventario['pendientes']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="progress" style="height: 10px; border-radius: 5px;">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= (float)$statsInventario['porcentaje'] ?>%;" aria-valuenow="<?= (float)$statsInventario['porcentaje'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row g-4 justify-content-center">
            
            <!-- 1. NUEVO REGISTRO DE DIAGNÓSTICO -->
            <div class="col-md-4">
                <div class="card module-card border-0 shadow-sm h-100 text-center p-3">
                    <div class="card-body d-flex flex-column align-items-center">
                        <div class="icon-wrapper bg-primary-subtle text-primary">
                            <i class="fa-solid fa-desktop fs-2"></i>
                        </div>
                        <h2 class="h5 fw-bold text-dark mb-2">Diagnóstico CPU</h2>
                        <p class="small text-muted mb-4 flex-grow-1">
                            Registro de diagnósticos, garantías, intervenciones de piezas y novedades de CPUs.
                        </p>
                        <a href="<?= base_url('equipos/formulario') ?>" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="fa-solid fa-plus-circle me-1"></i> Nuevo Registro
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. NUEVO REGISTRO DE SOPLADO -->
            <div class="col-md-4">
                <div class="card module-card border-0 shadow-sm h-100 text-center p-3">
                    <div class="card-body d-flex flex-column align-items-center">
                        <div class="icon-wrapper bg-info-subtle text-info">
                            <i class="fa-solid fa-wind fs-2"></i>
                        </div>
                        <h2 class="h5 fw-bold text-dark mb-2">Soplado de CPUs</h2>
                        <p class="small text-muted mb-4 flex-grow-1">
                            Mantenimiento preventivo, limpieza interna, cambio de pasta térmica y gel de plagas.
                        </p>
                        <a href="<?= base_url('soplado/formulario') ?>" class="btn btn-info text-white w-100 py-2 fw-semibold">
                            <i class="fa-solid fa-plus-circle me-1"></i> Nuevo Registro
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. NUEVO REGISTRO DE PORTÁTIL -->
            <div class="col-md-4">
                <div class="card module-card border-0 shadow-sm h-100 text-center p-3">
                    <div class="card-body d-flex flex-column align-items-center">
                        <div class="icon-wrapper bg-success-subtle text-success">
                            <i class="fa-solid fa-laptop fs-2"></i>
                        </div>
                        <h2 class="h5 fw-bold text-dark mb-2">Portátiles</h2>
                        <p class="small text-muted mb-4 flex-grow-1">
                            Garantías e intervención de portátiles Lenovo, reporte de FRU y cambio de piezas.
                        </p>
                        <a href="<?= base_url('portatiles/formulario') ?>" class="btn btn-success w-100 py-2 fw-semibold">
                            <i class="fa-solid fa-plus-circle me-1"></i> Nuevo Registro
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
<?= $this->endSection() ?>
