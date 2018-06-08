<?php

namespace core;

abstract class Model
{
    /**
     * @var App
     */
    protected $app;
    
    /**
     * @var bool
     */
    protected $isNewRecord = false;
    
    /**
     * @var string
     */
    protected static $table;
    
    /**
     * @var string|array
     */
    protected static $primaryKey = 'id';
    
    /**
     * @var array
     */
    protected $fields;
    
    /**
     * @param array $fields
     */
    public function __construct($fields = [])
    {
        $this->app = App::getInstance();
        
        foreach ($fields as $field => $value) {
            $this->$field = $value;
        }
        
        if (!$this->getPrimaryKeyValue) {
            $this->isNewRecord = true;
        }
    }
    
    /**
     * @param string $property
     *
     * @return mixed
     */
    public function __get(string $property)
    {
        $method = "get$property";
        
        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }
    
    /**
     * @param string $property
     * @param mixed $value
     *
     */
    public function __set(string $property, $value)
    {
        $method = "set$property";
        
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }
    
    /**
     * @param array
     *
     * @return string
     */
    public static function count(array $condition): string
    {
        $condition = static::makeCondition($condition);
        
        $sql = "SELECT COUNT(*) AS count FROM " . static::$table . $condition;
        
        $query = App::getInstance()->db->query($sql);
        
        return $query->row['count'];
    }
    
    /**
     * @param array $conditions
     *
     * @return array
     */
    public static function find(array $conditions): array
    {
        $condition = static::makeCondition($conditions);
        
        $sql = "SELECT * FROM " . static::$table . $condition;
        
        $query = App::getInstance()->db->query($sql);
        
        return $query->rows;
    }
    
    /**
     * @param array $conditions
     *
     * @return Model|null
     */
    public static function findOne(array $conditions)
    {
        $rows = static::find($conditions);
        
        if (!$rows) {
            return null;
        }
        
        return new static($rows[0]);
    }
    
    /**
     * @param array $fields
     */
    public function load($fields)
    {
        foreach ($this->fields as $field => $type) {
            $this->$field = $fields[$field] ?? null;
        }
    }
    
    public function save()
    {
        $fields_set = $this->makeFieldsSet();
        
        if ($this->isNewRecord) {
            $sql = "INSERT INTO ";
        } else {
            $sql = "UPDATE ";
        }
        
        $sql .= static::$table . " SET " . $fields_set;
        
        if (!$this->isNewRecord) {
            $primaryKeyValue = $this->getPrimaryKeyValue();
            
            if (is_string($primaryKeyValue)) {
                $condition = $this->primaryKey . ' = ' . $primaryKeyValue;
            } elseif (is_array($primaryKeyValue)) {
                $conditions = [];
                
                foreach ($primaryKeyValue as $field => $value) {
                    $conditions[] = $field . ' = ' . $value;
                }
            }
            
            $sql = " WHERE " . implode('AND', $conditions);
        }
        
        $this->app->db->query($sql);
    }
    
    /**
     * @return mixed
     */
    protected function getPrimaryKeyValue()
    {
        if (is_string($this->primaryKey)) {
            return $this->$primaryKey ?? null;
        }
        
        if (is_array($this->primaryKey)) {
            $result = [];
            
            foreach ($this->primaryKey as $field) {
                $value = $this->$field;
                
                if (!$value) {
                    return null;
                }
                
                $result[$field] = $value;
            }
            
            return $result;
        }
        
        return null;
    }
    
    /**
     * @param array $condition
     *
     * @return string
     */
    protected static function makeCondition(array $conditions): string
    {
        $condition = '';
        
        if ($conditions) {
            $condition .= " WHERE ";
        }
        
        foreach ($conditions as $field => $value) {
            $conditions_strings[] = "$field = '" . App::getInstance()->db->escape($value) . "'";
        }
        
        $condition .= implode(' AND ', $conditions_strings);
        
        return $condition;
    }
    
    /**
     * @return string
     */
    protected function makeFieldsSet(): string
    {
        $fields = [];
        
        foreach ($this->fields as $field => $type) {
            $string = $field . " = ";
            
            if ($this->$field == null) {
                $string .= 'NULL';
            } else {
                switch ($type) {
                    case 'string':
                        $string .= "'" . $this->app->db->escape($this->$field) . "'";
                        break;
                    case 'int':
                        $string .= (int)$this->$field;
                        break;
                    case 'float':
                        $string .= (float)$this->$field;
                        break;
                }
            }
            
            $fields[] = $string;
        }
        
        return implode(', ', $fields);
    }
}
