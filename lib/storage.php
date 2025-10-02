<?php

declare(strict_types=1);

use RuntimeException;

const DATA_DIR = __DIR__ . '/../data';
const JSON_OPTIONS = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;

/**
 * Build the absolute path to a data file.
 */
function data_path(string $file): string
{
    return DATA_DIR . '/' . ltrim($file, '/');
}

/**
 * Ensure the data directory exists before interacting with the filesystem.
 */
function ensure_data_directory(): void
{
    if (!is_dir(DATA_DIR) && !mkdir(DATA_DIR, 0775, true) && !is_dir(DATA_DIR)) {
        throw new RuntimeException('Unable to create data directory at "' . DATA_DIR . '"');
    }
}

/**
 * Load a JSON file from the data directory.
 *
 * @param string $file    File name relative to the data directory.
 * @param array  $default Value returned when the file cannot be read or decoded.
 */
function load_data(string $file, array $default = []): array
{
    $path = data_path($file);
    if (!file_exists($path)) {
        return $default;
    }

    $contents = file_get_contents($path);
    if ($contents === false || trim($contents) === '') {
        return $default;
    }

    $decoded = json_decode($contents, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
        return $default;
    }

    return $decoded;
}

/**
 * Persist data array back to JSON file in a human-readable format.
 */
function save_data(string $file, array $data): void
{
    ensure_data_directory();

    $path = data_path($file);
    $json = json_encode($data, JSON_OPTIONS);
    if ($json === false) {
        throw new RuntimeException('Failed to encode data for "' . $file . '"');
    }

    if (file_put_contents($path, $json . PHP_EOL) === false) {
        throw new RuntimeException('Unable to write data file "' . $path . '"');
    }
}

/**
 * Generate a numeric ID by incrementing the highest existing value.
 */
function generate_id(array $items): int
{
    $max = 0;
    foreach ($items as $item) {
        if (isset($item['id']) && (int)$item['id'] > $max) {
            $max = (int)$item['id'];
        }
    }

    return $max + 1;
}

/**
 * Find a record by ID.
 */
function find_by_id(array $items, int $id): ?array
{
    foreach ($items as $item) {
        if ((int)($item['id'] ?? 0) === $id) {
            return $item;
        }
    }

    return null;
}

/**
 * Replace a record by ID with new data.
 */
function replace_record(array $items, int $id, array $replacement): array
{
    foreach ($items as $index => $item) {
        if ((int)($item['id'] ?? 0) === $id) {
            $items[$index] = $replacement;
            break;
        }
    }

    return $items;
}

/**
 * Update a record by ID with provided fields.
 */
function update_record(array $items, int $id, array $fields): array
{
    foreach ($items as $index => $item) {
        if ((int)($item['id'] ?? 0) === $id) {
            $items[$index] = array_merge($item, $fields);
            break;
        }
    }

    return $items;
}
