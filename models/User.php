<?php

namespace models;

use core\Model;
use models\Koatuu;

class User extends Model
{
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $email;
    
    /**
     * @var string
     */
    protected $area_id;
    
    /**
     * @var string
     */
    protected $city_id;
    
    /**
     * @var string
     */
    protected $city_district_id;
    
    /**
     * @var string
     */
    protected static $table = 'users';
    
    protected $fields = [
        'name' => 'string',
        'email' => 'string',
        'area_id' => 'string',
        'city_id' => 'string',
        'city_district_id' => 'string'
    ];
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
    
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }
    
    /**
     * @return string
     */
    public function getAreaId()
    {
        return $this->area_id;
    }
    
    /**
     * @param string $area_id
     */
    public function setAreaId(string $area_id)
    {
        $this->area_id = $area_id;
    }
    
    /**
     * @return string
     */
    public function getCityId()
    {
        return $this->city_id;
    }
    
    /**
     * @param string $city_id
     */
    public function setCityId(string $city_id)
    {
        $this->city_id = $city_id;
    }
    
    /**
     * @return string
     */
    public function getCityDistrictId()
    {
        return $this->city_district_id;
    }
    
    /**
     * @param string $city_district_id
     */
    public function setCityDistrictId(string $city_district_id)
    {
        $this->city_district_id = $city_district_id;
    }
    
    /**
     * @return Koatuu
     */
    public function getArea()
    {
        return Koatuu::findOne(['ter_id' => $this->area_id]);
    }
    
    /**
     * @return Koatuu
     */
    public function getCity()
    {
        return Koatuu::findOne(['ter_id' => $this->city_id]);
    }
    
    /**
     * @return Koatuu
     */
    public function getCityDistrict()
    {
        return Koatuu::findOne(['ter_id' => $this->city_district_id]);
    }
}
