<?php

namespace App\Services;

use CodeIgniter\HTTP\Files\UploadedFile;

class UploadService
{
    /**
     * Sube y organiza evidencias en D:\[categoria]\[AÑO]\[MES_TEXTO]\[DIA]\
     */
    public function guardarEvidencia(?UploadedFile $file, string $placaId, string $categoria = 'diagnostico'): ?string
    {
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $baseDir = env("uploads.{$categoria}", 'D:/uploads');

        $meses = [
            '01' => 'Enero',      '02' => 'Febrero',   '03' => 'Marzo',
            '04' => 'Abril',      '05' => 'Mayo',      '06' => 'Junio',
            '07' => 'Julio',      '08' => 'Agosto',    '09' => 'Septiembre',
            '10' => 'Octubre',    '11' => 'Noviembre', '12' => 'Diciembre'
        ];

        $anio      = date('Y');
        $nombreMes = $meses[date('m')];
        $dia       = date('d');

        $targetDir = "{$baseDir}/{$anio}/{$nombreMes}/{$dia}/";

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $ext = $file->getClientExtension() ?: 'jpg';
        $placaLimpia = preg_replace('/[^a-zA-Z0-9_\-]/', '_', trim($placaId));
        if (empty($placaLimpia)) {
            $placaLimpia = 'evidencia_' . date('His');
        }

        $fileName = $placaLimpia . '.' . $ext;

        if (file_exists($targetDir . $fileName)) {
            $fileName = $placaLimpia . '_' . date('His') . '.' . $ext;
        }

        $file->move($targetDir, $fileName);

        return $targetDir . $fileName;
    }
}