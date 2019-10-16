<?php

namespace MyApp\Controllers\Admin;

use MyApp\Controllers\Admin\ControllerBase;

class UsersController extends ControllerBase
{

    public function indexAction()
    {
        echo '[' . __METHOD__ . ']';
        //$this->view->disable();

        //echo $this->view->render('admin/users/index');
    }
}
