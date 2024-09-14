<?php

function logError($message) {
    $logFile = __DIR__ . '/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    logError("Erreur $errno : $errstr dans $errfile à la ligne $errline");
});

set_exception_handler(function($exception) {
    logError("Exception non attrapée : " . $exception->getMessage());
});