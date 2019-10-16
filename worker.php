<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) php-team@yaochufa <php-team@yaochufa.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

ini_set('display_errors', 'stderr');
define('APP_PATH', __DIR__);

require_once  'vendor/autoload.php';



$relay = new Spiral\Goridge\StreamRelay(STDIN, STDOUT);
$psr7  = new Spiral\RoadRunner\PSR7Client(new Spiral\RoadRunner\Worker($relay));

// $dumper = new Spiral\Debug\Dumper();
// $dumper->setRenderer(Spiral\Debug\Dumper::ERROR_LOG, new Spiral\Debug\Renderer\ConsoleRenderer());

/**
 * Read the configuration
 */
$config = include APP_PATH . "/app/config/config.php";

/**
 * Read auto-loader
 */
include APP_PATH . "/app/config/loader.php";

/**
 * Read services
 */
include APP_PATH . "/app/config/main.php";

$application = new \Phalcon\Mvc\Application($di);

while ($req = $psr7->acceptRequest()) {
    $uriObject=$req->getUri();
    //To get private attributes, have to use closure.
    $getPathClosure = function () {
        return $this->path;
    };
    $uri=$getPathClosure->call($uriObject);
    // $dumper->dump('uri: ' . $uri, Spiral\Debug\Dumper::ERROR_LOG);
    $_GET['_url']=$uri;
    //set $_GET $_POST $_REQUEST
    foreach ($req->getQueryParams() as $key => $value) {
        $_GET[$key]     = $value;
        $_POST[$key]    = $value;
        $_REQUEST[$key] = $value;
    }
    try {
        $resp   = new \Zend\Diactoros\Response();

        $content=$application->handle()->getContent();

        $resp->getBody()->write($content);
        $psr7->respond($resp);
    } catch (\Throwable $e) {
        // Executed only in PHP 7, will not match in PHP 5.x
        $e->getMessage() . PHP_EOL;
    } catch (\Exception $e) {
        // Run only in PHP 5.x, will not  happen  in PHP 7
        $e->getMessage() . PHP_EOL;
    }
}
