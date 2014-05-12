<?php

namespace Vegas\Exporter;

use Vegas\Exporter\Exception as ExporterException;

class Csv extends ExporterAbstract {
    
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
        $this->properties->contentType = 'application/vnd.ms-excel';
        $this->properties->filename = null;
                
    }
    
    /**
     * Returns instance of PHPExcel
     * @return PHPExcel
     */
    public function getContent() 
    {
        return $this->content;
    }
    
    /**
     * Returns state of headers
     * @return PHPExcel
     */
    public function getKeysAsHeaders() 
    {
        return $this->keysAsHeaders;
    }

    /**
     * Implementation of exportData function
     * @param string $filename
     * @throws ExporterException
     */
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
    
    /**
     * Generates worksheet for xls file. In addition, it saves temporary our 
     * file to get content length. It's necessary to download file.
     */
    public function initExcelValues()
    {        
        $i = 1;
        $sheet = $this->content->getActiveSheet();

        if(!$this->keysAsHeaders){
            array_unshift($this->data, $this->headers);
        }
        
        foreach($this->data as $item){
            
            $column = 'A';
            foreach($item as $value){
                $sheet->getCell($column.$i)->setValue($value);
                $column++;
            }
            
            $i++;
            
        }
        $temporary_file = $this->outputPath . "test.xls";
        $writer = new \PHPExcel_Writer_Excel2007($this->content);
        $writer->save($temporary_file);
        
        $this->properties->contentSize = filesize($temporary_file);
        unlink($temporary_file);
                
    }
    
    /**
     * Saving excel file
     * @param string $filename
     */
    public function saveExcel($filename)
    {        
        $writer = new \PHPExcel_Writer_Excel2007($this->content);
        $writer->save($this->outputPath . $filename);
        
    }
    

}