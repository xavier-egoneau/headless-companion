<?php

namespace App\Utils;

class Logger {
    private $logFile;

    public function __construct($filename = 'app.log') {
        $this->logFile = __DIR__ . '/../../logs/' . $filename;
    }

    public function info($message) {
        $this->log('INFO', $message);
    }

    public function error($message) {
        $this->log('ERROR', $message);
    }

    private function log($level, $message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}