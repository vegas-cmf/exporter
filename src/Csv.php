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
     * Separator for csv values
     * @var char 
     */
    private $value_separator;
    
    /**
     * Separator for new lines in csv
     * @var char 
     */
    private $nl_separator;
    
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
     * Sets fundamental properties of class (data, separators, headers)
     * @param array $data
     * @param boolean $keysAsHeaders
     * @param char $value_separator
     * @param char $nl_separator
     * @throws ExporterException
     */
    public function init(array $data, $keysAsHeaders = true, $value_separator = ',', $nl_separator = ';') 
    {
        if(is_null($data)){
            throw new ExporterException('Data cannot be empty');
        }
      
        $this->keysAsHeaders = $keysAsHeaders;
        
        $this->data = $data;
        
        $this->value_separator = $value_separator;
        $this->nl_separator = $nl_separator;
                        
        $this->properties = new \stdClass();
        $this->properties->contentSize = null;
        $this->properties->contentType = 'text/csv';
        $this->properties->filename = null;
                
    }
    
    /**
     * Returns content of generated values
     * @return PHPExcel
     */
    public function getContent() 
    {
        return $this->content;
    }
    
    /**
     * Returns state of headers
     * @return boolean
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
    public function exportData($filename = "export_file.csv") 
    {        
        if($this->keysAsHeaders === false && is_null($this->headers)){
            throw new ExporterException("Headers cannot be empty if var keysAsHeaders was given in init function");
        }
        
        $this->properties->filename = $filename;
        
        $this->initCsvValues();
        $this->saveCsv($filename);
        
    }
    
    /**
     * Generates values for csv file. Also it sets contentSize variable to 
     * possibility downloading file when it needs.
     */
    public function initCsvValues()
    {      
        $csv = "";
        
        if($this->keysAsHeaders === true){
            foreach($this->headers as $key){
                $csv .= $key . $this->value_separator;
            }
            
            $csv = substr($csv, 0, -1) . $this->nl_separator;
        }
        
        foreach($this->data as $item){
            
            foreach($item as $value){
                $csv .= $value . $this->value_separator;
            }
            
            $csv = substr($csv, 0, -1) . $this->nl_separator;
            
        }
        
        $this->content = $csv;
        
        $this->properties->contentSize = strlen($this->content);
                
    }
    
    /**
     * Saving values to .csv file as name given in filename variable.
     * @param string $filename
     */
    public function saveCsv($filename)
    {        
        file_put_contents($this->outputPath . $filename, $this->content);        
    }
    

}