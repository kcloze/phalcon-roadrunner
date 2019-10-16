<?php

namespace MyApp\Controllers;

use MyApp\Controllers\ControllerBase;

class TestController extends ControllerBase
{

    public function indexAction()
    {

        $this->view->setVar("title", "phalcon template");
        $this->view->render('test/index', 'index');

    }

}
