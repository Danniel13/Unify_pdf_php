<?php
require 'vendor/autoload.php';
use setasign\Fpdi\Fpdi;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivos'])) {
    $archivosSubidos = $_FILES['archivos'];
    $rutaTemporal = 'tmp/';
    $archivos = [];

    // Crear directorio temporal si no existe
    if (!file_exists($rutaTemporal)) {
        mkdir($rutaTemporal, 0777, true);
    }

    // Mover archivos subidos al directorio temporal
    for ($i = 0; $i < count($archivosSubidos['name']); $i++) {
        $nombreArchivo = $archivosSubidos['name'][$i];
        $rutaArchivo = $rutaTemporal . basename($nombreArchivo);

        if (move_uploaded_file($archivosSubidos['tmp_name'][$i], $rutaArchivo)) {
            $archivos[] = $rutaArchivo;
        } else 
        {
            echo "<script>";
            echo "alert('Error al subir el archivo: ')";
            echo "</script>";
            exit;
            index.html;
            
            // echo "Error al subir el archivo: " . $nombreArchivo;
           
        }
    }

    // Nombre del archivo PDF resultante
    $archivoSalida = 'unificado.pdf';

    // Unir los PDFs
    unirPDFs($archivos, $archivoSalida);

    // Descargar el archivo unificado
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($archivoSalida) . '"');
    readfile($archivoSalida);

    // Eliminar archivos temporales
    foreach ($archivos as $archivo) {
        unlink($archivo);
    }
    unlink($archivoSalida);
} else {
    echo "No se han subido archivos.";
}

function unirPDFs($archivos, $archivoSalida) {
    $pdf = new FPDI();

    foreach ($archivos as $archivo) {
        $paginas = $pdf->setSourceFile($archivo);
        for ($i = 1; $i <= $paginas; $i++) {
            $tplIdx = $pdf->importPage($i);
            $specs = $pdf->getTemplateSize($tplIdx);

            // Determinar orientación y tamaño
            $orientation = ($specs['width'] > $specs['height']) ? 'L' : 'P';
            $pdf->AddPage($orientation, [$specs['width'], $specs['height']]);
            $pdf->useTemplate($tplIdx, 0, 0, $specs['width'], $specs['height']);
        }
    }

    $pdf->Output('F', $archivoSalida);
}
?>
