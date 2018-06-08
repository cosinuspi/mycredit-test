<?php

namespace controllers;

use core\Controller;

class Error extends Controller
{
    public function actionIndex()
    {
        $this->app->response->setOutput($this->html('error404'));
    }
}
