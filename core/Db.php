<?php

namespace core;

class Db
{
    /**
     * @var \mysqli|null
     */
    private $connection;
    
    /**
     * @var 
    
    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        extract($config);
        
        try {
            $this->connection = new \mysqli($host, $username, $password, $dbname, $port);
        } catch (\Error $e) {
            throw new \Error('Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('Error: ' . $e->getMessage());
        }

        if ($this->connection->connect_errno) {
            throw new \Exception('Error: ' . $this->connection->connect_error . '<br />Error No: ' . $this->connection->connect_errno);
        }

        try {
            $this->connection->set_charset($charset);
        } catch (\Error $e) {
            throw new \Error('Error: ' . $e->getMessage());
        }
        
        $this->connection->query("SET SQL_MODE = ''");
    }
    
    public function __destruct()
    {
        $this->connection->close();
    }

    /**
     * @param string $sql
     * 
     * @return mixed
     */
    public function query($sql)
    {
        $query = $this->connection->query($sql);
        
        if ($this->connection->errno) {
            throw new \Exception('Error: ' . $this->connection->error  . '<br />Error No: ' . $this->connection->errno . '<br />' . $sql);
        }
        
        if (! $query instanceof \mysqli_result) {
            return true;
        }
        
        $data = [];

        while ($row = $query->fetch_assoc()) {
            $data[] = $row;
        }

        $result = new \stdClass();
        $result->num_rows = $query->num_rows;
        $result->row = isset($data[0]) ? $data[0] : [];
        $result->rows = $data;

        $query->close();

        return $result;
    }

    /**
     * @param string $value
     * 
     * @return string
     */
    public function escape($value): string
    {
        return $this->connection->real_escape_string($value);
    }

    /**
     * @return int
     */
    public function getLastId(): int
    {
        return $this->connection->insert_id;
    }
    
    /**
     * @return bool
     */
    public function connected(): bool
    {
        return $this->connection->ping();
    }
}
