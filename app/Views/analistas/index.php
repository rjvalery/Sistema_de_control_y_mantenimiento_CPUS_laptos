<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Administrar Analistas<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fs-6"><i class="fa-solid fa-users me-2"></i>Gestión de Analistas</h5>
            </div>
            <div class="card-body p-4">
                
                <?php if (session()->getFlashdata('msg')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('msg') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('analistas/agregar') ?>" method="POST" class="mb-4">
                    <label for="nombre_analista" class="form-label fw-bold">Nuevo Analista</label>
                    <div class="input-group">
                        <input type="text" name="nombre_analista" id="nombre_analista" class="form-control" placeholder="Ej. Juan Pérez" required>
                        <button class="btn btn-primary" type="submit">
                            <i class="fa-solid fa-plus me-1"></i> Agregar
                        </button>
                    </div>
                </form>

                <h6 class="fw-bold mb-3">Analistas Activos</h6>
                <ul class="list-group">
                    <?php if (!empty($analistas)): ?>
                        <?php foreach ($analistas as $a): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <span><?= esc($a['nombre']) ?></span>
                                <a href="<?= base_url('analistas/eliminar/' . $a['id']) ?>" 
                                   class="btn btn-outline-danger btn-sm border-0"
                                   onclick="return confirm('¿Seguro que deseas eliminar a <?= esc($a['nombre']) ?>?');">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-muted text-center py-3">No hay analistas registrados.</li>
                    <?php endif; ?>
                </ul>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>