<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) php-team@yaochufa <php-team@yaochufa.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

ini_set('display_errors', 'stderr');

require_once APP_PATH . '/vendor/autoload.php';



$relay = new Spiral\Goridge\StreamRelay(STDIN, STDOUT);
$psr7  = new Spiral\RoadRunner\PSR7Client(new Spiral\RoadRunner\Worker($relay));

$dumper = new Spiral\Debug\Dumper();
$dumper->setRenderer(Spiral\Debug\Dumper::ERROR_LOG, new Spiral\Debug\Renderer\ConsoleRenderer());

$application = new \Phalcon\Mvc\Application($di);

while ($req = $psr7->acceptRequest()) {
    $uriObject=$req->getUri();
    //私有属性只能通过Closure方式获取
    $getPathClosure = function () {
        return $this->path;
    };
    $uri=$getPathClosure->call($uriObject);
    $dumper->dump('uri: ' . $uri, Spiral\Debug\Dumper::ERROR_LOG);
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
        //程序最后要刷出日志
        $application->logger->flush();
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
