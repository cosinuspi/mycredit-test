<?php

namespace controllers;

use core\Controller;
use models\Koatuu;
use models\User as UserModel;

class User extends Controller
{
    public function actionView()
    {
        if (!(int)$this->app->request->get('id')) {
            $this->app->response->redirect('index.php?route=error');
        }
        
        $data['user'] = UserModel::findOne(['id' => $this->app->request->get('id')]);
        
        if (!$data['user']) {
            $this->app->response->redirect('index.php?route=error');
        }
        
        $data['city'] = $data['user']->city;
        $data['area'] = Koatuu::getParent($data['city']);
        $data['title'] = $data['user']->name;
        
        $this->app->response->setOutput($this->html('view', $data));
    }
}
 
