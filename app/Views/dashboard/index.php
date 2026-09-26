<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1">Panel de control</h1>
        <p class="text-muted mb-0">Bienvenido, <?= esc(session('usuario_nombre')) ?>.</p>
    </div>
</div>

<?php if (session()->getFlashdata('msg')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('msg') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

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

<div class="row g-3 mb-4">
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

<!-- SECCIÓN: GESTIÓN DE USUARIOS Y ASIGNACIÓN DE ROLES -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="card-title fw-bold mb-0 text-dark">
                <i class="fa-solid fa-users-gear text-primary me-2"></i>Gestión de Usuarios y Asignación de Roles
            </h5>
            <small class="text-muted">Asigna roles para definir qué dashboard y accesos verá cada usuario al iniciar sesión.</small>
        </div>
        <button type="button" class="btn btn-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
            <i class="fa-solid fa-user-plus me-1"></i> Nuevo Usuario
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Nombre</th>
                        <th>Usuario (Login)</th>
                        <th>Rol Actual</th>
                        <th>Asignar Rol</th>
                        <th>Estado</th>
                        <th class="text-end pe-3">Registrado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td class="ps-3 text-muted"><?= $u['id'] ?></td>
                                <td class="fw-semibold">
                                    <?= esc($u['nombre']) ?>
                                    <?php if ((int)$u['id'] === (int)session('usuario_id')): ?>
                                        <span class="badge bg-secondary-subtle text-secondary border ms-1 small">Tú</span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= esc($u['usuario']) ?></code></td>
                                <td>
                                    <?php if ($u['rol'] === 'admin'): ?>
                                        <span class="badge bg-dark text-white px-2 py-1">
                                            <i class="fa-solid fa-shield-halved me-1"></i>Administrador
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-primary text-white px-2 py-1">
                                            <i class="fa-solid fa-clipboard-user me-1"></i>Analista
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action="<?= base_url('usuarios/cambiar-rol') ?>" method="POST" class="d-inline-block">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <select name="rol" class="form-select form-select-sm" style="width: 145px;" onchange="this.form.submit()">
                                            <option value="admin" <?= $u['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                            <option value="analista" <?= $u['rol'] === 'analista' ? 'selected' : '' ?>>Analista</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                                </td>
                                <td class="text-end pe-3 text-muted small">
                                    <?= !empty($u['created_at']) ? date('d/m/Y', strtotime($u['created_at'])) : '—' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted py-3">No hay usuarios registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL NUEVO USUARIO -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <h5 class="modal-title fs-6 fw-bold mb-0" id="modalNuevoUsuarioLabel">
                    <i class="fa-solid fa-user-plus me-2"></i>Crear Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('usuarios/crear') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="nuevo_nombre" class="form-label fw-bold small">Nombre Completo *</label>
                        <input type="text" name="nombre" id="nuevo_nombre" class="form-control" placeholder="Ej: Carlos Gómez" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuevo_usuario" class="form-label fw-bold small">Nombre de Usuario (Login) *</label>
                        <input type="text" name="usuario" id="nuevo_usuario" class="form-control" placeholder="Ej: cgomez" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuevo_password" class="form-label fw-bold small">Contraseña *</label>
                        <input type="password" name="password" id="nuevo_password" class="form-control" placeholder="Mínimo 6 caracteres" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label for="nuevo_rol" class="form-label fw-bold small">Rol Asignado *</label>
                        <select name="rol" id="nuevo_rol" class="form-select" required>
                            <option value="analista" selected>Analista (Solo acceso a nuevo registro)</option>
                            <option value="admin">Administrador (Acceso total al sistema)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
