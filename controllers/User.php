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
        
        $data['title'] = $data['user']->name;
        $data['area'] = $data['user']->area;
        $data['city'] = $data['user']->city;
        $data['cityDistrict'] = $data['user']->cityDistrict;
        
        $this->app->response->setOutput($this->html('view', $data));
    }
}
 
