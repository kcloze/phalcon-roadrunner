<?php
/**
 * Services are globally registered in this file
 *
 * @var \Phalcon\Config $config
 */

use Phalcon\Cache\Backend\Redis as RedisCache;
use Phalcon\Cache\Frontend\Data as FrontData;
use Phalcon\Di\FactoryDefault;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

$di->set('dispatcher', function () {
    $dispatcher = new Dispatcher();
    $dispatcher->setDefaultNamespace('MyApp\Controllers');
    return $dispatcher;
});

$di->set('router', function () {
    return require __DIR__ . '/routes.php';
}, true);
/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt'  => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions([
                'compiledPath'      => $config->application->cacheDir,
                'compiledSeparator' => '_',
            ]);

            return $volt;
        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php',
    ]);

    return $view;
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
