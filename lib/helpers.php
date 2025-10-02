<?php

declare(strict_types=1);

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function input(array $source, string $key, $default = null)
{
    return $source[$key] ?? $default;
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function success_message(?string $message): string
{
    if (!$message) {
        return '';
    }
    return '<div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">' . sanitize($message) . '</div>';
}

function error_message(?string $message): string
{
    if (!$message) {
        return '';
    }
    return '<div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">' . sanitize($message) . '</div>';
}
