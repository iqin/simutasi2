<?php

spl_autoload_register(function ($class) {
    $prefixes = [
        'PhpOffice\\PhpSpreadsheet\\' => APPPATH . 'Libraries/PhpSpreadsheet/',
        'Psr\\SimpleCache\\' => APPPATH . 'Libraries/SimpleCache/',
        'ZipStream\\' => APPPATH . 'Libraries/ZipStream/', // Tambahkan ZipStream di sini
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (strpos($class, $prefix) === 0) {
            $relativeClass = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
});
