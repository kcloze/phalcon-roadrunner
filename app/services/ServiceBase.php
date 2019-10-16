<?php

namespace MyApp\Services;

use \Phalcon\DI;
use \Phalcon\DI\Injectable;

class ServiceBase extends Injectable
{

    public function __construct()
    {
        $di = DI::getDefault();
        $this->setDI($di);
    }
}
