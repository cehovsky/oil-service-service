<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

$envFiles = array_filter([
    file_exists(__DIR__ . '/../codenow/config/.env.test')
        ? __DIR__ . '/../codenow/config/.env.test'
        : null,
    file_exists(__DIR__ . '/../codenow/config/.env.test.local')
        ? __DIR__ . '/../codenow/config/.env.test.local'
        : null,
]);

if ($envFiles !== []) {
    (new Dotenv())->load(...$envFiles);
}
