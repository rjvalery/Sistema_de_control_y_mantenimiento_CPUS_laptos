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
