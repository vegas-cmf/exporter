<?php

class Xml extends ExporterAbstract {
    
    private $data;
    
    private $content;
    
    private $headers;
    
    public function init(array $data, $keysAsHeaders = false) 
    {
        $this->data = $data;
        
        
        /*
         * 
         */
        
        
        $this->properties = new stdClass();
        $this->properties->contentSize;
        
    }
    
    public function export($filename) 
    {
        
    }

    public function getContent() 
    {
        
    }

    public function setHeaders(array $headers) 
    {
        $this->headers = $headers;
    }

}