<?php

namespace App\Utils;
namespace App\Utils;

class Logger {
    private $logFile;
    private $logLevel;

    const ERROR = 3;
    const WARNING = 2;
    const INFO = 1;
    const DEBUG = 0;

    public function __construct($logFile = 'app.log', $logLevel = self::INFO) {
        $logDir = __DIR__ . '/../../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $this->logFile = $logDir . '/' . $logFile;
        $this->logLevel = $logLevel;
    }

    public function log($message, $level = self::INFO) {
        if ($level >= $this->logLevel) {
            $levelStr = $this->getLevelString($level);
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = "[$timestamp] [$levelStr] $message" . PHP_EOL;
            file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        }
    }

    private function getLevelString($level) {
        switch ($level) {
            case self::ERROR:
                return 'ERROR';
            case self::WARNING:
                return 'WARNING';
            case self::INFO:
                return 'INFO';
            case self::DEBUG:
                return 'DEBUG';
            default:
                return 'UNKNOWN';
        }
    }

    public function error($message) {
        $this->log($message, self::ERROR);
    }

    public function warning($message) {
        $this->log($message, self::WARNING);
    }

    public function info($message) {
        $this->log($message, self::INFO);
    }

    public function debug($message) {
        $this->log($message, self::DEBUG);
    }
}