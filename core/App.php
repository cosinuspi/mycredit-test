<?php

namespace core;

class App
{
    const REQUEST_ERROR_URL = '/error';
    
    const DEFAULT_ROUTE = 'main';
    
    const CONTROLLER_NAMESPACE = 'controllers';
    
    /**
     * @var array
     */
    public $config;
    
    /**
     * @var Controller
     */
    public $controller;
    
    /**
     * @var string
     */
    public $charset = 'UTF-8';
    
    /**
     * @var App
     */
    private static $instance;
    
    /**
     * @var Request
     */
    private $request;
    
    /**
     * @var Response
     */
    private $response;
    
    /**
     * @var Db
     */
    private $db;
    
    /**
     * @param array $config
     */
    private function __construct(array $config)
    {
        $this->config = $config;
        
        if (empty($config['name'])) {
            $this->config['name'] = 'Application';
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
     * @param array $config
     *
     * @return App
     */
    public static function getInstance(array $config = []): App
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
            self::$instance->init();
        }
        
        return self::$instance;
    }
    
    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
    
    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
    
    /**
     * @return Db
     */
    public function getDb(): Db
    {
        return $this->db;
    }
    
    private function init()
    {
        $this->request = new Request();
        $this->response = new Response();
        
        try {
            $this->db = new Db($this->config['db']);
        } catch (\Error $e) {
            throw new \Error('Error: ' . $e->getMessage());
        }
    }
    
    public function run()
    {
        $route = $this->request->get('route');
        
        if (!$route) {
            $route = 'main';
        }
        
        try {
            $parts = $this->createController($route);
            
            if (!is_array($parts)) {
                throw new \Exception('Unable to resolve the request "' . $route . '".');
            }
        } catch (\Exception $e) {
            $this->response->redirect('error', 404);
        }
        
        list($controller, $actionID) = $parts;
        $this->controller = $controller;
        
        $controller->runAction($actionID);
        
        $this->response->output();
    }
    
    /**
     * @param string $route
     *
     * @return array|bool
     */
    private function createController($route)
    {
        if ($route === '') {
            $route = self::DEFAULT_ROUTE;
        }
        
        $route = trim($route, '/');
        
        if (strpos($route, '//') !== false) {
            return false;
        }

        if (strpos($route, '/') !== false) {
            list($id, $route) = explode('/', $route, 2);
        } else {
            $id = $route;
            $route = '';
        }
        
        $class_name = '\\' . self::CONTROLLER_NAMESPACE . '\\' . ucfirst($id);
        
        if (!class_exists($class_name)) {
            return false;
        }
        
        $controller = new $class_name($id);
        
        return [$controller, $route];
    }
}
