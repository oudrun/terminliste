<?php
declare(strict_types=1);

if (!function_exists('h')) {
    function h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('flash')) {
    function flash(string $message, string $type = 'success'): void
    {
        $_SESSION['flash'] = [
            'message' => $message,
            'type' => $type,
        ];
    }
}

if (!function_exists('getFlash')) {
    function getFlash(): ?array
    {
        if (!isset($_SESSION['flash'])) {
            return null;
        }

        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);

        return $flash;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}

if (!function_exists('formatDate')) {
    function formatDate(?string $date): string
    {
        if ($date === null || $date === '') {
            return '';
        }

        return date('d.m.Y', strtotime($date));
    }
}

if (!function_exists('formatTime')) {
    function formatTime(?string $time): string
    {
        if ($time === null || $time === '') {
            return 'Ikke satt';
        }

        return substr($time, 0, 5);
    }
}

if (!function_exists('renderView')) {
    function renderView(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require APP_BASE_PATH . '/resources/views/' . $view . '.php';
    }
}
