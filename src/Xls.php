<?php

namespace Vegas\Exporter;

use Vegas\Exporter\Exception as ExporterException;

class Xls extends ExporterAbstract {
    
    /**
     * Holds data for exportData function
     * @var array 
     */
    private $data;
    
    /**
     * Variable contains PHPExcel object 
     * @var PHPExcel 
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
        
        
    }
    

}