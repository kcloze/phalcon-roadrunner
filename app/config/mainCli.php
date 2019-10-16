<?php
/**
 * Services are globally registered in this file
 *
 * @var \Phalcon\Config $config
 */

use Phalcon\Cache\Backend\Redis as RedisCache;
use Phalcon\Cache\Frontend\Data as FrontData;
use Phalcon\Di\FactoryDefault\Cli as CliDi;
use Phalcon\Logger\Adapter\File as FileAdapter;

$di = new CliDi();

$di->setShared('config', function () use ($config) {
    return $config;
});

$di->setShared('logger', function () use ($config) {

    return new FileAdapter($config->logger->application);
});
/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () use ($config) {
    $dbConfig = $config->database->toArray();
    $adapter  = $dbConfig['adapter'];
    unset($dbConfig['adapter']);

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;

    return new $class($dbConfig);
});

$di->setShared('cache', function () use ($config) {
    // Cache data for one hour
    $frontCache = new FrontData(
        array(
            "lifetime" => $config->redis->lifetime,
        )
    );
    // Create the component that will cache "Data" to a "RedisCache" backend
    // RedisCache connection settings
    $cache = new RedisCache(
        $frontCache,
        array(
            "servers" => array(
                array(
                    "host" => $config->redis->host,
                    "port" => $config->redis->port,
                    "auth" => $config->redis->auth,
                ),
            ),
        )
    );
    return $cache;
});

$di->get('dispatcher')->setDefaultNamespace('MyApp\Tasks');
$di->get('dispatcher')->setNamespaceName('MyApp\Tasks');
