<?php
declare(strict_types=1);

use Symfony\Component\Filesystem\Filesystem;

require dirname(__DIR__) . '/vendor/autoload.php';

$filesystem = new Filesystem();

if ($filesystem->exists(__DIR__ . '/../src/_generated')) {
    echo "Removing generated test files...\n";
    $filesystem->remove(__DIR__ . '/../src/_generated');
}
