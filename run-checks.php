<?php

echo "=== Running PHPStan ===\n";
passthru('php85 vendor/phpstan/phpstan/phpstan analyse --error-format=table --no-progress 2>&1', $phpstanExit);
echo "\n";

echo "=== Running PHPCS ===\n";
passthru('php85 vendor/squizlabs/php_codesniffer/bin/phpcs --report=full 2>&1', $phpcsExit);
echo "\n";

echo "=== Running PHPUnit ===\n";
passthru('php85 vendor/bin/phpunit 2>&1', $phpunitExit);
echo "\n";

$totalExit = $phpstanExit + $phpcsExit + $phpunitExit;
exit($totalExit > 0 ? 1 : 0);
