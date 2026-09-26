<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1">Panel de control</h1>
        <p class="text-muted mb-0">Bienvenido, <?= esc(session('usuario_nombre')) ?>.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Diagnósticos CPU</div>
                <div class="fs-3 fw-semibold"><?= $totalEquipos === null ? '—' : $totalEquipos ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Soplado</div>
                <div class="fs-3 fw-semibold"><?= $totalSoplado === null ? '—' : $totalSoplado ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Portátiles</div>
                <div class="fs-3 fw-semibold"><?= $totalPortatiles === null ? '—' : $totalPortatiles ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Analistas</div>
                <div class="fs-3 fw-semibold"><?= $totalAnalistas === null ? '—' : $totalAnalistas ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6 col-xl-3">
        <a href="<?= base_url('equipos/formulario') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <i class="fa-solid fa-desktop text-primary mb-2"></i>
                    <h2 class="h6 mb-1 text-dark">Diagnóstico CPU</h2>
                    <p class="small text-muted mb-0">Nuevo registro de equipos de escritorio.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href="<?= base_url('soplado/formulario') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <i class="fa-solid fa-wind text-info mb-2"></i>
                    <h2 class="h6 mb-1 text-dark">Soplado</h2>
                    <p class="small text-muted mb-0">Mantenimiento preventivo y evidencias.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href="<?= base_url('portatiles/formulario') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <i class="fa-solid fa-laptop text-success mb-2"></i>
                    <h2 class="h6 mb-1 text-dark">Portátiles</h2>
                    <p class="small text-muted mb-0">Diagnóstico y garantías Lenovo.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href="<?= base_url('analistas') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <i class="fa-solid fa-users text-warning mb-2"></i>
                    <h2 class="h6 mb-1 text-dark">Analistas</h2>
                    <p class="small text-muted mb-0">Catálogo de personal del taller.</p>
                </div>
            </div>
        </a>
    </div>
</div>
<?= $this->endSection() ?>
