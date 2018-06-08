<?php

namespace core;

class View
{
    const DIR_TEMPLATE = '/views/';
    const DIR_LAYOUT = '/views/layouts/';
    
    /**
     * @var App
     */
    protected $app;
    
    /**
     * @var array
     */
    private $data = [];
    
    /**
     * @param array $adta
     */
    public function __construct($data = [])
    {
        $this->app = App::getInstance();
        $this->data = $data;
    }
    
    /**
     * @param string $template
     * @param string|null $layout
     *
     * @return string
     */
    public function render($template, $layout = null): string
    {
        $file = $this->app->config['appDir'] . self::DIR_TEMPLATE . $template . '.phtml';
        
        if (!is_file($file)) {
            throw new \Exception('Error: Could not load template ' . $file . '!');
        }
        
        extract($this->data);
        
        ob_start();
        
        require($file);
        
        $content = ob_get_clean();
        
        $title = $title ?? '';
        
        $title = $this->getTitle($title);

        if ($layout) {
            ob_start();

            require($this->app->config['appDir'] . self::DIR_LAYOUT . $layout . '.phtml');
            
            return ob_get_clean();
        } else {
            return $content;
        }
    }
    
    /**
     * @param string $page_title
     *
     * @return string
     */
    private function getTitle($page_title): string
    {
        $title = $this->app->config['name'];
        
        if ($page_title) {
            $title = $page_title . ' | ' . $this->app->config['name'];
        }
        
        return $title;
    }
}
