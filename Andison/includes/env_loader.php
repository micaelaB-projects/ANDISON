<?php

declare(strict_types=1);

if (!function_exists('andison_load_env_file')) {
    function andison_load_env_file(string $filePath): void
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!is_array($lines)) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim((string)$line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $key = trim($parts[0]);
            $value = trim($parts[1]);

            if ($key === '' || !preg_match('/^[A-Z0-9_]+$/', $key)) {
                continue;
            }

            if ($value !== '' && ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'")))) {
                $value = substr($value, 1, -1);
            }

            if (getenv($key) !== false || isset($_ENV[$key]) || isset($_SERVER[$key])) {
                continue;
            }

            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

if (!function_exists('andison_bootstrap_env')) {
    function andison_bootstrap_env(): void
    {
        static $loaded = false;
        if ($loaded) {
            return;
        }
        $loaded = true;

        $candidates = [
            dirname(__DIR__, 2) . '/.env', // workspace root
            dirname(__DIR__) . '/.env',     // Andison/.env
        ];

        foreach ($candidates as $filePath) {
            andison_load_env_file($filePath);
        }
    }
}

andison_bootstrap_env();
