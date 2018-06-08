<?php

namespace core;

class Request {
    const CSRF_NAME = '_csrf';
    const CSRF_HEADER = 'X-CSRF-Token';
    const CSRF_STRING_LENGTH = 32;
    
    private $get = [];
    private $post = [];
    private $request = [];
    private $cookie = [];
    private $files = [];
    private $server = [];
    private $_csrfToken;

    public function __construct() {
        $this->get = $this->clean($_GET);
        $this->post = $this->clean($_POST);
        $this->request = $this->clean($_REQUEST);
        $this->cookie = $this->clean($_COOKIE);
        $this->files = $this->clean($_FILES);
        $this->server = $this->clean($_SERVER);
    }
    
    /**
     * @param string $name
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function get(string $name, $defaultValue = null)
    {
        return isset($this->get[$name]) ? $this->get[$name] : $defaultValue;
    }
    
    /**
     * @param string $name
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function post(string $name = '', $defaultValue = null)
    {
        if ($name == '') {
            return $this->post;
        }
        
        return $this->post[$name] ?? $defaultValue;
    }
    
    /**
     * @param string $name
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function request(string $name, $defaultValue = null)
    {
        return $this->request[$name] ?? $defaultValue;
    }
    
    /**
     * @param string $name
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function cookie(string $name, $defaultValue = null)
    {
        return $this->cookie[$name] ?? $defaultValue;
    }
    
    /**
     * @param string $name
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function files(string $name, $defaultValue = null)
    {
        return $this->files[$name] ?? $defaultValue;
    }
    
    /**
     * @param string $name
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function server(string $name, $defaultValue = null)
    {
        return $this->server[$name] ?? $defaultValue;
    }
    
    /**
     * @return string
     */
    public function getCsrfToken(): string
    {
        if ($this->_csrfToken === null) {
            if (isset($_SESSION[self::CSRF_NAME])) {
                $token = $_SESSION[self::CSRF_NAME];
            }
            
            if (empty($token)) {
                $token = $this->generateCsrfToken();
            }
            
            $this->_csrfToken = $this->maskToken($token);
        }

        return $this->_csrfToken;
    }

    /**
     * @return string
     */
    public function getCsrfTokenFromHeader(): string
    {
        return $this->server('HTTP_' . self::CSRF_HEADER);
    }
    
    /**
     * @param mixed $data
     *
     * @return mixed
     */
    private function clean($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);

                $data[$this->clean($key)] = $this->clean($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
        }

        return $data;
    }
    
    /**
     * @return string
     */
    private function generateCsrfToken(): string
    {
        $bytes = random_bytes(self::CSRF_STRING_LENGTH);
        
        $token = substr(strtr(base64_encode($bytes), '+/', '-_'), 0, self::CSRF_STRING_LENGTH);
        
        $_SESSION[self::CSRF_NAME] = $token;

        return $token;
    }
    
    /**
     * @param $token
     *
     * @return string
     */
    private function maskToken($token): string
    {
        $mask = random_bytes(mb_strlen($token, '8bit'));
        
        return strtr(base64_encode($mask . ($mask ^ $token)), '+/', '-_');
    }
    
    /**
     * @param $token
     *
     * @return string
     */
    public function unmaskToken($token): string
    {
        $decoded = base64_decode(strtr($token, '-_', '+/'));
        $length = mb_strlen($decoded, '8bit');
        
        if (!is_int($length)) {
            return '';
        }
        
        return mb_substr($decoded, $length, $length === null ? mb_strlen($decoded, '8bit') : $length, '8bit') ^ mb_substr($decoded, 0, $length === null ? mb_strlen($decoded, '8bit') : $length, '8bit');
    }
}
