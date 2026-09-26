<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Intervención de Portátiles<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fs-6"><i class="fa-solid fa-laptop me-2"></i>Garantías e Intervención de Portátiles</h5>
            </div>
            <div class="card-body p-4">

                <?php if (session()->getFlashdata('msg')): ?>
                    <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('msg') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>

                <form action="<?= base_url('portatiles/guardar') ?>" method="POST">
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
                            <input type="text" name="numero_traslado" class="form-control" placeholder="Ej: 123456" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Placa ID del equipo *</label>
                            <input type="text" name="placa_id_equipo" class="form-control" placeholder="Ej: B123456" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tipo de gestión *</label>
                            <select name="tipo_gestion" class="form-select" required>
                                <option value="" disabled selected>-- Seleccione --</option>
                                <option value="Diagnostico">Diagnóstico</option>
                                <option value="Mantenimiento">Mantenimiento</option>
                                <option value="Baja">Baja</option>
                            </select>
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
                            <label class="form-label fw-bold">¿Realizó test Lenovo? *</label>
                            <select name="realizo_test_lenovo" class="form-select" required>
                                <option value="" disabled selected>-- Seleccione --</option>
                                <option value="Si">Si</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estado actual del equipo *</label>
                            <select name="estado_actual_equipo" class="form-select" required>
                                <option value="" disabled selected>-- Seleccione --</option>
                                <option value="Garantia">Garantía</option>
                                <option value="Novedad">Novedad</option>
                                <option value="Funcional">Funcional</option>
                                <option value="Pendiente respuesta">Pendiente respuesta</option>
                                <option value="Baja">Baja</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Diagnóstico del laptop intervenido</label>
                            <textarea name="diagnostico_laptop_intervenido" class="form-control" rows="2" placeholder="Describa el diagnóstico..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Garantía *</label>
                            <select name="garantia" class="form-select" required>
                                <option value="" disabled selected>-- Seleccione --</option>
                                <option value="Aplica">Aplica</option>
                                <option value="No Aplica">No Aplica</option>
                                <option value="En tramite">En trámite</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Número de ticket</label>
                            <input type="text" name="numero_ticket" class="form-control" placeholder="Ej: TCK-9988">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">¿Por qué solicita garantía?</label>
                            <textarea name="porque_solicita_garantia" class="form-control" rows="2" placeholder="Motivo de la solicitud..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estado final del equipo</label>
                            <input type="text" name="estado_final_equipo" class="form-control" placeholder="Ej: Entregado">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Indique la pieza</label>
                            <input type="text" name="indique_pieza" class="form-control" placeholder="Ej: Pantalla">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Indique el FRU</label>
                            <input type="text" name="indique_fru" class="form-control" placeholder="Ej: 5B20V12345">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pieza intervenida</label>
                            <input type="text" name="pieza_intervenida" class="form-control" placeholder="Ej: Systemboard">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Origen de la pieza</label>
                            <input type="text" name="origen_pieza" class="form-control" placeholder="Ej: Stock local">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Serial del disco</label>
                            <input type="text" name="serial_disco" class="form-control" placeholder="Ej: S3Z1NX0T123456">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Motivo de baja</label>
                            <input type="text" name="motivo_baja" class="form-control" placeholder="Ej: Daño irrecuperable">
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary w-100 py-2 fs-6 fw-bold">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Registro de Portátil
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>