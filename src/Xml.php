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
        
    /**
     * Sets data and create properties object
     * @param array $data
     * @param boolean $keysAsHeaders
     * @throws ExporterException
     */
    public function init(array $data, $keysAsHeaders = true) 
    {
        if(is_null($data)){
            throw new ExporterException('Data cannot be empty');
        }
      
        $this->keysAsHeaders = $keysAsHeaders;
                
        $this->data = $data;
                
        $this->properties = new \stdClass();
        $this->properties->contentSize = null;
        $this->properties->contentType = 'application/xml';
        $this->properties->filename = null;
                
    }
    
    /**
     * Returns generated XML as string
     * @return string
     */
    public function getContent() 
    {
        return $this->content->asXML();
    }
    
    /**
     * Implementation of exportData function
     * @param string $filename
     * @throws ExporterException
     */
    public function exportData($filename = 'export_file.xml') 
    {
        if($this->keysAsHeaders === false && is_null($this->headers)){
            throw new ExporterException('Headers cannot be empty if var keysAsHeaders was given in init function');
        }
        
        $this->properties->filename = $filename;
        
        $this->initXml();
        $this->saveXml($this->outputPath . $filename);
        
    }
    
    /**
     * Generates SimpleXMLElement object from data which was set in init()
     * @param string $filename
     */
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
    
    /**
     * Saving xml file 
     * @param string $filename
     */
    public function saveXml($filename)
    {
        return $this->content->asXML($filename);
    }

}