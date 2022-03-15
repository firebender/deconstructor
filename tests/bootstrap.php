<?php

declare(strict_types=1);

require realpath(__DIR__.'/../vendor/autoload.php');

use Illuminate\Filesystem\Filesystem;

/**
 * @firebender: We need to increase the memory limit because php will throw errors trying to do
 * code coverage at low memory settings
 */
$limit = ini_get('memory_limit');
$limit = return_bytes($limit);
if ($limit < 1073741824) {
    ini_set('memory_limit', '-1');
}

/**
 * We create a phpunit directory in the package root if it doesn't exist.
 */
$dir = getcwd().'/phpunit';

$files = new Filesystem();
if ($files->exists($dir)) {
    $files->deleteDirectory($dir);
}
$files->makeDirectory($dir);

/**
 * Helper function to return numeric values of bytes from php ini alue.
 *
 * @param string|false $size
 *
 * @return int|string
 */
function return_bytes($size)
{
    if ($size === false) {
        return 1;
    }

    switch (substr($size, -1)) {
        case 'M': case 'm': return (int) $size * 1048576;
        case 'K': case 'k': return (int) $size * 1024;
        case 'G': case 'g': return (int) $size * 1073741824;
        default: return $size;
    }
}
