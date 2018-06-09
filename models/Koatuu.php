<?php

namespace models;

use core\Model;

class Koatuu extends Model
{
    const AREA_TYPE = 0;
    const CITY_TYPE = 1;
    const CITY_DISTRICT_TYPE = 3;
    
    const SPECIAL_AREAS = [
        '8000000000',
        '8500000000'
    ];
    
    /**
     * @var string
     */
    public $ter_id;
    
    /**
     * @var string
     */
    public $ter_pid;
    
    /**
     * @var string
     */
    public $ter_name;
    
    /**
     * @var string
     */
    public $ter_address;
    
    /**
     * @var int
     */
    public $ter_type_id;
    
    /**
     * @var int
     */
    public $ter_level;
    
    /**
     * @var int
     */
    public $ter_mask;
    
    /**
     * @var string
     */
    public $reg_id;
    
    /**
     * @var string
     */
    protected static $table = 't_koatuu_tree';
    
    /**
     * @return array
     */
    public static function getAreas(): array
    {
        $areas = static::find(['ter_type_id' => self::AREA_TYPE]);
        
        foreach ($areas as &$area) {
            if (static::isSpecialArea($area['ter_id'])) {
                $area['special'] = true;
            } else {
                $area['special'] = false;
            }
        }
        unset($area);
        
        return $areas;
    }
    
    /**
     * @param string $area_id
     *
     * @return array
     */
    public static function getCities(string $area_id): array
    {
        return static::find([
            'ter_pid' => $area_id,
            'ter_type_id' => self::CITY_TYPE
        ]);
    }
    
    /**
     * @param string $city_id
     *
     * @return array
     */
    public static function getCityDistricts(string $city_id): array
    {
        return static::find([
            'ter_pid' => $city_id,
            'ter_type_id' => self::CITY_DISTRICT_TYPE
        ]);
    }
    
    /**
     * @param Koatuu $model
     *
     * @return Koatuu
     */
    public static function getParent(Koatuu $model)
    {
        return static::findOne([
            'ter_id' => $model->ter_pid
        ]);
    }
    
    /**
     * Saving is forbidden
     *
     * {@inheritdoc}
     */
    public function save()
    {
        return;
    }
    
    /**
     * @param int $area_id
     *
     * @return bool
     */
    public static function isSpecialArea(int $area_id): bool
    {
        return in_array($area_id, self::SPECIAL_AREAS);
    }
}
