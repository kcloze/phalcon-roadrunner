<?php
use Phalcon\Cli\Console as ConsoleApp;

error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');
define('APP_PATH', __DIR__ . '/..');

/**
 * Read the configuration
 */
$config = include __DIR__ . '/config/config.php';
/**
 * Read auto-loader
 */
include __DIR__ . '/config/loader.php';

/**
 * Read the services
 */

include __DIR__ . '/config/mainCli.php';

/**
 * Create a console application
 */
$console = new ConsoleApp($di);

/**
 * Process the console arguments
 */
$arguments = [];

foreach ($argv as $k => $arg) {
    if ($k == 1) {
        $arguments['task'] = $arg;
    } elseif ($k == 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

try {

    /**
     * Handle
     */
    $console->handle($arguments);

    /**
     * If configs is set to true, then we print a new line at the end of each execution
     *
     * If we dont print a new line,
     * then the next command prompt will be placed directly on the left of the output
     * and it is less readable.
     *
     * You can disable this behaviour if the output of your application needs to don't have a new line at end
     */
    if (isset($config["printNewLine"]) && $config["printNewLine"]) {
        echo PHP_EOL;
    }

} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
    exit(255);
}
