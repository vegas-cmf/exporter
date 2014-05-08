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
    private $keysAsHeaders;
        
    public function init(array $data, $keysAsHeaders = true) 
    {
        if(is_null($data)){
            throw new ExporterException('Data cannot be empty');
        }
      
        $this->keysAsHeaders = $keysAsHeaders;
        
        $this->data = $data;
                
        $this->properties = new \stdClass();
        $this->properties->contentSize = null;
        $this->properties->contentType = 'application/vnd.ms-excel';
        $this->properties->filename = null;
                
    }
    

    public function getContent() 
    {
        return $this->content;
    }

    public function getKeysAsHeaders() 
    {
        return $this->keysAsHeaders;
    }

    public function exportData($filename = "export_file.xls") 
    {
        
        if($this->keysAsHeaders === false && is_null($this->headers)){
            throw new ExporterException("Headers cannot be empty if var keysAsHeaders was given in init function");
        }
        
        $this->properties->filename = $filename;
        
        
        $this->content = new \PHPExcel();
        $this->content->setActiveSheetIndex(0);
        
        $this->initExcelValues();
        $this->saveExcel($filename);
        
    }
    
    public function initExcelValues(){
        
        $i = 1;
        $sheet = $this->content->getActiveSheet();

        if(!$this->keysAsHeaders){
            array_unshift($this->data, $this->headers);
        }
        
        foreach($this->data as $item){
            
            $column = 'A';
            foreach($item as $key => $value){
                $sheet->getCell($column.$i)->setValue($value);
                $column++;
            }
            
            $i++;
            
        }
        $temporary_file = $this->outputPath."xls";
        $writer = new \PHPExcel_Writer_Excel2007($this->content);
        $writer->save($temporary_file);
        
        $this->properties->contentSize = filesize($temporary_file);
        unlink($temporary_file);
                
    }
    
    public function saveExcel($filename){
        
        $writer = new \PHPExcel_Writer_Excel2007($this->content);
        $writer->save($this->outputPath.$filename);
        
    }
    

}