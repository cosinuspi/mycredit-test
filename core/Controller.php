<?php

namespace core;

class Controller
{
    /**
     * @var App
     */
    protected $app;
    
    /**
     * @var string
     */
    protected $defaultAction = 'index';
    
    /**
     * @var string
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $layout = 'main';
    
    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->app = App::getInstance();
        $this->id = $id;
    }
    
    /**
     * @param string $id
     *
     * @return mixed
     */
    public function runAction($id)
    {
        try {
            $method = $this->createAction($id);
        
            if ($method === null) {
                $this->app->response->redirect('error', 404);
            }
            
            return call_user_func_array([$this, $method->getName()], []);
        } catch (\Error $e) {
            $this->app->response->redirect('error', 500);
        } catch (\Exception $e) {
            $this->app->response->redirect('error', 500);
        }
    }
    
    /**
     * @param string $template
     * @param array $data
     * @param strin $controller
     *
     * @return string
     */
    public function html($template, $data = [], $controller = null): string
    {
        $template = preg_replace('/[^a-zA-Z0-9_\/]/', '', $template);
        
        $view = new View($data);
        
        $this->app->response->addHeader('Content-Type: text/html; charset=utf-8');
        
        if (!$controller) {
            $controller = $this->id;
        }
        
        return $view->render($controller . '/' . $template, $this->layout);
    }
    
    /**
     * @param string $template
     * @param array $data
     * @param strin $controller
     *
     * @return string
     */
    public function html_part($template, $data = [], $controller = null): string
    {
        $template = preg_replace('/[^a-zA-Z0-9_\/]/', '', $template);
        
        $view = new View($data);
        
        if (!$controller) {
            $controller = $this->id;
        }
        
        return $view->render($controller . '/' . $template);
    }
    
    /**
     * @param array $data
     *
     * @return string
     */
    public function json(array $data = []): string
    {
        $this->app->response->addHeader('Content-Type: application/json');
        
        return json_encode($data);
    }
    
    /**
     * @param string $id
     *
     * @return \ReflectionMethod|null
     */
    private function createAction($id)
    {
        if ($id === '') {
            $id = $this->defaultAction;
        }
        
        $methodName = 'action' . ucfirst($id);
        
        if (method_exists($this, $methodName)) {
            $method = new \ReflectionMethod($this, $methodName);
            
            if ($method->isPublic() && $method->getName() === $methodName) {
                return $method;
            }
        }
        
        return null;
    }
}
