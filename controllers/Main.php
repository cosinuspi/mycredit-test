<?php

namespace controllers;

use core\Controller;

class Main extends Controller
{
    public function actionIndex()
    {
        $this->app->response->setOutput($this->html('index'));
    }
}
