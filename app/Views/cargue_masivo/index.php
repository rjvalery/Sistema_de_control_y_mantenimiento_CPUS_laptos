<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Cargue Masivo a Base de Datos<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .upload-box {
        border: 2px dashed #0d6efd;
        border-radius: 12px;
        background-color: #f8faff;
        transition: all 0.2s ease-in-out;
        padding: 2.5rem 1.5rem;
        text-align: center;
        cursor: pointer;
    }
    .upload-box:hover {
        background-color: #eef4ff;
        border-color: #0b5ed7;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-12 col-xl-11">

        <!-- ENCABEZADO -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="fa-solid fa-database text-primary me-2"></i>Cargue Masivo Directo a Base de Datos
                </h1>
                <p class="text-muted mb-0">
                    Importación masiva de datos hacia la tabla <code>inventario_general</code> mediante hojas de cálculo Excel / CSV.
                </p>
            </div>
            <div class="mt-2 mt-md-0 d-flex gap-2">
                <a href="<?= base_url('cargue-masivo/plantilla') ?>" class="btn btn-success btn-sm fw-semibold">
                    <i class="fa-solid fa-file-excel me-1"></i> Descargar Plantilla Excel/CSV
                </a>
                <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Volver al Dashboard
                </a>
            </div>
        </div>

        <!-- ALERTAS FLASH -->
        <?php if (session()->getFlashdata('msg')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('msg') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            
            <!-- FORMULARIO DE CARGUE DIRECTO -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-cloud-arrow-up text-primary me-2"></i>Subir Archivo para Inserción en Base de Datos
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <form action="<?= base_url('cargue-masivo/procesar') ?>" method="POST" enctype="multipart/form-data" id="formCargueMasivo">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Archivo CSV delimitado (.csv) *</label>
                                <div class="upload-box" onclick="document.getElementById('archivo_csv').click();">
                                    <i class="fa-solid fa-file-csv display-4 text-primary mb-3"></i>
                                    <h6 class="fw-bold text-dark mb-1" id="file-label">Haz clic para seleccionar tu archivo CSV</h6>
                                    <p class="small text-muted mb-0">Admite archivos exportados desde Excel o texto delimitado (, o ;).</p>
                                    <input type="file" name="archivo_csv" id="archivo_csv" class="d-none" accept=".csv, text/csv, text/plain" required onchange="mostrarNombreArchivo(this)">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <small class="text-muted">
                                    <i class="fa-solid fa-info-circle me-1"></i>Tabla de destino: <code>inventario_general</code>
                                </small>
                                <button type="submit" class="btn btn-primary px-4 py-2 fw-bold" id="btnProcesar">
                                    <i class="fa-solid fa-upload me-2"></i>Importar a Base de Datos
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <!-- INSTRUCCIONES Y GUÍA -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-list-check text-success me-2"></i>Estructura de Columnas
                        </h5>
                        <a href="<?= base_url('cargue-masivo/plantilla') ?>" class="btn btn-sm btn-outline-success">
                            <i class="fa-solid fa-download me-1"></i>Plantilla
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <p class="small text-muted mb-3">
                            Estructura de 8 columnas adaptada a la plantilla oficial <strong>Formato en Cubic</strong>:
                        </p>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered small mb-3">
                                <thead class="table-light">
                                    <tr>
                                        <th>Columna (Excel/CSV)</th>
                                        <th>Tipo</th>
                                        <th>Ejemplo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>Identificador 1</code></td>
                                        <td><span class="badge bg-danger-subtle text-danger">Requerido*</span></td>
                                        <td>ACT-10021</td>
                                    </tr>
                                    <tr>
                                        <td><code>Identificador 2</code></td>
                                        <td><span class="badge bg-danger-subtle text-danger">Requerido*</span></td>
                                        <td>SN-MBP99201</td>
                                    </tr>
                                    <tr>
                                        <td><code>Ref. Principal</code></td>
                                        <td><span class="badge bg-secondary-subtle text-secondary">Opcional</span></td>
                                        <td>MacBook Pro 16 M1</td>
                                    </tr>
                                    <tr>
                                        <td><code>Descripción</code></td>
                                        <td><span class="badge bg-secondary-subtle text-secondary">Opcional</span></td>
                                        <td>Laptop Apple Corporativo</td>
                                    </tr>
                                    <tr>
                                        <td><code>Zona Origen</code></td>
                                        <td><span class="badge bg-secondary-subtle text-secondary">Opcional</span></td>
                                        <td>Sede Central</td>
                                    </tr>
                                    <tr>
                                        <td><code>Ubicación Origen</code></td>
                                        <td><span class="badge bg-secondary-subtle text-secondary">Opcional</span></td>
                                        <td>Piso 3 - Puesto 302</td>
                                    </tr>
                                    <tr>
                                        <td><code>Verificado</code></td>
                                        <td><span class="badge bg-secondary-subtle text-secondary">Opcional</span></td>
                                        <td>Verificado / Pendiente</td>
                                    </tr>
                                    <tr>
                                        <td><code>Observaciones</code></td>
                                        <td><span class="badge bg-secondary-subtle text-secondary">Opcional</span></td>
                                        <td>Equipo en buen estado</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted d-block"><em>* Se requiere al menos Identificador 1, Identificador 2 o Ref. Principal para procesar la fila.</em></small>
                    </div>
                </div>
            </div>

        </div>

        <!-- TABLA DE REGISTROS ALMACENADOS EN inventario_general -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-table text-primary me-2"></i>Registros en Base de Datos (Estructura Cubic)
                        <span class="badge bg-primary ms-1"><?= $totalRegistros ?> total</span>
                    </h5>
                    <small class="text-muted">Visualizando los registros almacenados en la tabla <code>inventario_general</code> de la base de datos <code>diagnostico_cpus</code>.</small>
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <form method="GET" action="<?= base_url('cargue-masivo') ?>" class="d-flex gap-2">
                        <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Buscar ID 1, ID 2, Ref, Zona..." value="<?= esc($busqueda) ?>">
                        <button type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-magnifying-glass"></i></button>
                        <?php if (!empty($busqueda)): ?>
                            <a href="<?= base_url('cargue-masivo') ?>" class="btn btn-outline-danger btn-sm">Limpiar</a>
                        <?php endif; ?>
                    </form>

                    <?php if ($totalRegistros > 0): ?>
                        <form action="<?= base_url('cargue-masivo/vaciar') ?>" method="POST" onsubmit="return confirm('¿Seguro que deseas vaciar todos los registros del inventario masivo? Esta acción no se puede deshacer.');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Vaciar tabla">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0 text-nowrap">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Identificador 1</th>
                                <th>Identificador 2</th>
                                <th>Ref. Principal</th>
                                <th>Descripción</th>
                                <th>Zona Origen</th>
                                <th>Ubicación Origen</th>
                                <th>Verificado</th>
                                <th>Observaciones</th>
                                <th>Intervenido</th>
                                <th class="text-end pe-3">Fecha Cargue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($registros)): ?>
                                <?php foreach ($registros as $r): ?>
                                    <tr>
                                        <td class="ps-3 text-muted"><?= $r['id'] ?></td>
                                        <td><strong><?= esc($r['identificador_1'] ?: ($r['placa_id'] ?: '—')) ?></strong></td>
                                        <td><code><?= esc($r['identificador_2'] ?: ($r['serial'] ?: '—')) ?></code></td>
                                        <td><?= esc($r['ref_principal'] ?: ($r['modelo'] ?: '—')) ?></td>
                                        <td><span class="badge bg-secondary"><?= esc($r['descripcion'] ?: ($r['tipo_equipo'] ?: '—')) ?></span></td>
                                        <td><?= esc($r['zona_origen'] ?: '—') ?></td>
                                        <td><?= esc($r['ubicacion_origen'] ?: ($r['ubicacion'] ?: '—')) ?></td>
                                        <td>
                                            <span class="badge bg-info-subtle text-dark border"><?= esc($r['verificado'] ?: ($r['estado'] ?: 'Cargado')) ?></span>
                                        </td>
                                        <td class="small text-muted" style="max-width: 180px; overflow: hidden; text-overflow: ellipsis;">
                                            <?= esc($r['observaciones'] ?: '—') ?>
                                        </td>
                                        <td>
                                            <?php if ((int)($r['intervenido'] ?? 0) === 1): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                    <i class="fa-solid fa-check me-1"></i><?= esc($r['modulo_intervencion'] ?: 'Intervenido') ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                    Pendiente
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-3 text-muted small">
                                            <?= !empty($r['created_at']) ? date('d/m/Y H:i', strtotime($r['created_at'])) : '—' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">
                                        No hay registros en la base de datos de inventario. Descarga la plantilla de Formato en Cubic o sube tu archivo CSV para comenzar.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function mostrarNombreArchivo(input) {
    const label = document.getElementById('file-label');
    if (input.files && input.files[0]) {
        label.innerHTML = `<i class="fa-solid fa-check text-success me-1"></i> Archivo seleccionado: <strong>${input.files[0].name}</strong> (${(input.files[0].size / 1024).toFixed(1)} KB)`;
    } else {
        label.innerText = 'Haz clic para seleccionar tu archivo CSV';
    }
}

document.getElementById('formCargueMasivo').addEventListener('submit', function() {
    const btn = document.getElementById('btnProcesar');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Importando a la base de datos...';
});
</script>
<?= $this->endSection() ?>
