<?php

namespace controllers;

use core\Controller;
use core\Validator;
use models\Koatuu;
use models\User;

class Registration extends Controller
{
    /**
     * @var array
     */
    private $errors = [];
    
    public function actionIndex()
    {
        $data['title'] = 'Регистрация';
        $data['h1'] = 'Регистрация';
        
        $form_data[$this->app->request::CSRF_NAME] = $this->app->request->post($this->app->request::CSRF_NAME);
        $form_data['name'] = $this->app->request->post('name');
        $form_data['email'] = $this->app->request->post('email');
        $form_data['area_id'] = $this->app->request->post('area_id');
        $form_data['city_id'] = $this->app->request->post('city_id');
        $form_data['city_district_id'] = $this->app->request->post('city_district_id');
        $special_area_id = null;
        $city_districts_action = 'index.php?route=geo/cityDistricts';
        
        if ($this->app->request->post()) {
            $this->validate($form_data);
            
            if ($this->errors) {
                if ($form_data['area_id']) {
                    if (Koatuu::isSpecialArea($form_data['area_id'])) {
                        $special_area_id = $form_data['area_id'];
                    }
                    
                    if ($special_area_id !== null) {
                        $cities_data['name'] = 'city_id';
                        $cities_data['id'] = 'cities';
                        $cities_data['action'] = $city_districts_action;
                        $cities_data['target'] = '#city-districts-wrapper';
                        $cities_data['label'] = 'Список городов';
                        
                        $cities_data['options'] = Koatuu::getCities($form_data['area_id']);
                        
                        $cities_data['field_id'] = $form_data['city_id'];
                        
                        $form_data['cities'] = $this->html_part('_select', $cities_data, 'geo');
                    }
                }
                
                if ($special_area_id !== null) {
                    $city_id = $special_area_id;
                } else {
                    $city_id = $form_data['city_id'];
                }
                
                if ($city_id) {
                    $city_districts_data['name'] = 'city_district_id';
                    $city_districts_data['id'] = 'city_districts';
                    $city_districts_data['action'] = '';
                    $city_districts_data['label'] = 'Список районов';
                    $city_districts_data['options'] = Koatuu::getCityDistricts($city_id);
                    $city_districts_data['field_id'] = $form_data['city_district_id'];
                    
                    $form_data['city_districts'] = $this->html_part('_select', $city_districts_data, 'geo');
                }
                
            } else {
                $user = new User();
                $user->load($this->app->request->post());
                $user->save();
                
                $data['success'] = 'Пользователь ' . $user->email . ' успешно зарегистрирован.';
            }
        }
        
        $form_data['cities_action'] = 'index.php?route=geo/cities';
        $form_data['city_districts_action'] = $city_districts_action;
        $form_data['csrf_name'] = $this->app->request::CSRF_NAME;
        $form_data['csrf_token'] = $this->app->request->getCsrfToken();
        
        $form_data['errors'] = $this->errors;
        
        $form_data['areas'] = Koatuu::getAreas();
        
        $data['form'] = $this->html_part('_form', $form_data);
        
        $this->app->response->setOutput($this->html('index', $data));
    }
    
    /**
     * @param array $data
     */
    private function validate(array $data)
    {
        extract($data);
        
        if ($this->app->request->unmaskToken(${$this->app->request::CSRF_NAME}) !== $this->app->request->unmaskToken($this->app->request->getCsrfToken())) {
            $this->errors[$this->app->request::CSRF_NAME] = 'Неправильный CSRF-токен';
        }
        
        if ($message = Validator::required($name)) {
            $this->errors['name'][] = $message;
        } elseif ($message = Validator::string($name, ['max' => 255])) {
            $this->errors['name'][] = $message;
        }
        
        if ($message = Validator::required($email)) {
            $this->errors['email'][] = $message;
        } elseif ($message = Validator::email($email)) {
            $this->errors['email'][] = $message;
        } elseif ($user = User::findOne(['email' => $email])) {
            $this->app->response->redirect('index.php?route=user/view&id=' . $user->id);
        }
        
        if ($message = Validator::required($area_id)) {
            $this->errors['city_id'][] = 'Выберите область!!';
        } elseif (Koatuu::count([
            'ter_id' => $area_id,
            'ter_type_id' => Koatuu::AREA_TYPE
        ]) == 0) {
            $this->errors['area_id'][] = 'Нет такой области!';
        }
        
        $special_area = false;
        
        if (Koatuu::isSpecialArea($area_id)) {
            $city_id = $area_id;
            $special_area = true;
        }
        
        if (!$special_area) {
            if ($message = Validator::required($city_id)) {
                $this->errors['city_id'][] = 'Выберите город!!';
            } elseif (Koatuu::count([
                'ter_id' => $city_id,
                'ter_type_id' => Koatuu::CITY_TYPE
            ]) == 0) {
                $this->errors['city_id'][] = 'Нет такого города!';
            }
        }
        
        if (Koatuu::count([
            'ter_pid' => $city_id,
            'ter_type_id' => Koatuu::CITY_DISTRICT_TYPE
        ]) > 0) {
            if ($message = Validator::required($city_district_id)) {
                $this->errors['city_district_id'][] = $message;
            } elseif (Koatuu::count([
                'ter_id' => $city_district_id,
                'ter_type_id' => Koatuu::CITY_DISTRICT_TYPE
            ]) == 0) {
                $this->errors['city_district_id'][] = 'Нет такого района!';
            }
        }
    }
}
