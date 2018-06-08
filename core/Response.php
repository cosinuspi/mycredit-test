<?php

namespace core;

class Response
{
    const JSON_STATUS_OK = 'ok';
    const JSON_STATUS_ERROR = 'error';
    
    /**
     * @var array
     */
    private $headers = [];
    
    /**
     * @var string
     */
    private $output;

    /**
     * @param string $header
     */
    public function addHeader(string $header)
    {
        $this->headers[] = $header;
    }
    
    /**
     * @param string $url
     * @param int $status
     */
    public function redirect(string $url, int $status = 302)
    {
        header('Location: ' . str_replace(['&amp;', "\n", "\r"], ['&', '', ''], $url), true, $status);
        exit();
    }
    
    /**
     * @return array
     */
    public function getOutput(): array
    {
        return $this->output;
    }
    
    /**
     * @param string $output
     */	
    public function setOutput(string $output)
    {
        $this->output = $output;
    }
    
    public function output() {
        if (!$this->output) {
            return;
        }
        
        if (!headers_sent()) {
            foreach ($this->headers as $header) {
                header($header, true);
            }
        }
            
        echo $this->output;
    }
}
