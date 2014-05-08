<?php

namespace Vegas\Exporter;

use Vegas\Exporter\Exception as ExporterException;

class Xml extends ExporterAbstract {
    
    /**
     * Holds data for exportData function
     * @var array 
     */
    private $data;
    
    /**
     * Variable contains XML Object 
     * @var SimpleXMLElement 
     */
    private $content;
    
    /**
     * Variable for recognize what type was setted.
     * @var boolean 
     */
    public $keysAsHeaders;
        
    public function init(array $data, $keysAsHeaders = true) 
    {
        if(is_null($data)){
            throw new ExporterException('Data cannot be empty');
        }
      
        $this->keysAsHeaders = $keysAsHeaders;
        
        
        $this->data = $data;
                
        $this->properties = new \stdClass();
        $this->properties->contentSize = null;
        $this->properties->contentType = 'Xml';
        $this->properties->filename = null;
                
    }
    

    public function getContent() 
    {
        return $this->content->asXML();
    }

    public function exportData($filename = 'export_file.xml') 
    {
        
        if($this->keysAsHeaders === false && is_null($this->headers)){
            throw new ExporterException('Headers cannot be empty if var keysAsHeaders was given in init function');
        }
        
        $this->properties->filename = $filename;
        
        $this->initXml();
        $this->saveXml($this->outputPath . $filename);
        
    }
    
    public function initXml()
    {
        $xmlstr = '<?xml version="1.0" encoding="utf-8"?><root></root>';
        $this->content = new \SimpleXMLElement($xmlstr);
        
        foreach($this->data as $items){
            
            $parent = $this->content->addChild('item');
            
            foreach($items as $key => $item){
                
                if($this->keysAsHeaders){
            
                    $parent->addChild($key, $item);
            
                } else {
                    
                    $parent->addChild($this->headers[$key], $item);
                    
                }
            
            }
            
        }
        
        $this->properties->contentSize = strlen($this->content->asXML());
                
    }
    
    public function saveXml($filename)
    {
        return $this->content->asXML($filename);
    }

}