<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Soplado de CPUs<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fs-6"><i class="fa-solid fa-wind me-2"></i>Soplado de CPUs - Registro de Mantenimiento</h5>
            </div>
            <div class="card-body p-4">

                <?php if (session()->getFlashdata('msg')): ?>
                    <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('msg') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>

                <form action="<?= base_url('soplado/guardar') ?>" method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre del analista *</label>
                            <select name="nombre_analista" class="form-select" required>
                                <option value="" disabled selected>-- Seleccione --</option>
                                <?php foreach ($analistas as $a): ?>
                                    <option value="<?= esc($a['nombre']) ?>"><?= esc($a['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Número de Traslado *</label>
                            <input type="text" name="num_traslado" class="form-control" placeholder="Ej: 123456" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Placa ID del equipo *</label>
                            <input type="text" name="placa_id" class="form-control" placeholder="Ej: B123456 o Serial" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">¿Energiza? *</label>
                            <select name="energiza" class="form-select" required>
                                <option value="" disabled selected>-- Seleccione --</option>
                                <option value="Si">Si</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">¿Da video? *</label>
                            <select name="da_video" class="form-select" required>
                                <option value="" disabled selected>-- Seleccione --</option>
                                <option value="Si">Si</option>
                                <option value="No">No</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">¿Detecta disco? *</label>
                            <select name="detecta_disco" class="form-select" required>
                                <option value="" disabled selected>-- Seleccione --</option>
                                <option value="Si">Si</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">¿Ingresó a la BIOS? *</label>
                            <select name="ingreso_bios" class="form-select" required>
                                <option value="" disabled selected>-- Seleccione --</option>
                                <option value="Si">Si</option>
                                <option value="No">No</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">¿Se aplicó pasta térmica? *</label>
                            <select name="pasta_termica" class="form-select" required>
                                <option value="" disabled selected>-- Seleccione --</option>
                                <option value="Si">Si</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">¿Se aplicó gel para cucarachas? *</label>
                            <select name="gel_cucarachas" class="form-select" required>
                                <option value="" disabled selected>-- Seleccione --</option>
                                <option value="Si">Si</option>
                                <option value="No">No</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">La máquina contenía: *</label>
                            <select name="maquina_contenia" class="form-select" required>
                                <option value="" disabled selected>-- Seleccione --</option>
                                <option value="Cucaracha">Cucaracha</option>
                                <option value="Polvo">Polvo</option>
                                <option value="Papeles de comida">Papeles de comida</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold"><i class="fa-solid fa-camera me-1"></i> Evidencia Fotográfica *</label>
                            <input type="file" name="foto_equipo" class="form-control" accept="image/*" capture="environment" required>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary w-100 py-2 fs-6 fw-bold">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Registro de Soplado
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>