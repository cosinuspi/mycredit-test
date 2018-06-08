<?php

namespace controllers;

use core\Controller;
use models\Koatuu;

class Geo extends Controller
{
    public function actionCities()
    {
        $area_id = $this->app->request->get('area_id');
        
        if (!$area_id) {
            $data['status'] = $this->app->response::JSON_STATUS_ERROR;
            $data['error'] = 'Отсутствует обязательный параметр area_id';
            
            $this->app->response->setOutput($this->json($data));
            return;
        }
        
        $html_data['name'] = 'city_id';
        $html_data['id'] = 'cities';
        $html_data['action'] = 'index.php?route=geo/cityDistricts';
        $html_data['target'] = '#city-district-wrapper';
        $html_data['label'] = 'Список городов';
        $html_data['options'] = Koatuu::getCities($area_id);
        $html_data['field_id'] = '';
        
        $data['status'] = $this->app->response::JSON_STATUS_OK;
        $data['html'] = '';
        
        if ($html_data['options']) {
            $data['html'] = $this->html_part('_select', $html_data);
        }
        
        $this->app->response->setOutput($this->json($data));
    }
    
    public function actionCityDistricts()
    {
        $city_id = $this->app->request->get('city_id');
        
        if (!$city_id) {
            $data['status'] = $this->app->response::JSON_STATUS_ERROR;
            $data['error'] = 'Отсутствует обязательный параметр city_id';
            
            $this->app->response->setOutput($this->json($data));
            return;
        }
        
        $html_data['name'] = 'city_district_id';
        $html_data['id'] = 'city_districts';
        $html_data['action'] = '';
        $html_data['label'] = 'Список районов';
        $html_data['options'] = Koatuu::getCityDistricts($city_id);
        $html_data['field_id'] = '';
        
        $data['status'] = $this->app->response::JSON_STATUS_OK;
        $data['html'] = '';
        
        if ($html_data['options']) {
            $data['html'] = $this->html_part('_select', $html_data);
        }
        
        $this->app->response->setOutput($this->json($data));
    }
}
