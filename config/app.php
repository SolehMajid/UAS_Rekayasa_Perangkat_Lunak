<?php
// Deteksi protokol secara dinamis (HTTP atau HTTPS)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Deteksi host domain (misal: localhost atau projectrplkelompok-4.fwh.is)
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';

// Deteksi subfolder aplikasi secara dinamis
$project_root = str_replace('\\', '/', dirname(__DIR__));
$doc_root = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) : '';

$subfolder = '';
if (!empty($doc_root) && strpos($project_root, $doc_root) === 0) {
    $subfolder = substr($project_root, strlen($doc_root));
} else {
    // Fallback deteksi manual
    $script_name = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    if (strpos($script_name, '/squashy') === 0) {
        $subfolder = '/squashy';
    }
}

// Bersihkan slash ganda di awal dan akhir subfolder
$subfolder = trim($subfolder, '/');
if ($subfolder !== '') {
    $subfolder = $subfolder . '/';
}

$base_url = $protocol . $host . '/' . $subfolder;
