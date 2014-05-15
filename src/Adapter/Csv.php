<?php

/**
 * This file is part of Vegas Exporter package.
 *
 * @author Mateusz Aniołek <matty201@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. * 
 */

namespace Vegas\Exporter\Adapter;

use Vegas\Exporter\Adapter\Exception\DataNotFoundException as DataNotFoundException;
use Vegas\Exporter\Adapter\Exception\InvalidKeysException as InvalidKeysException;

class Csv extends ExporterAbstract {
    
    /**
     * Separator for csv values in line
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
     * @var string 
     */
    private $obj;
        
     /**
     * Constructor. Initialize $config variable as instance of stdClass. First 
     * of all, contentSize and fileName are set as null and contentType is set to 
     * 'text/csv' so it points that exported file should be string type. Also 
      * sets default separators. For new line - ';', and for next value - ','.
     */
    public function __construct()
    {                        
        $this->config = new \stdClass();
        $this->config->contentSize = null;
        $this->config->contentType = 'text/csv';
        $this->config->filename = null;
        
        $this->nl_separator = ";";
        $this->value_separator = ",";
        
    }

    /**
     * Sets data and create config object (stdClass). If keysAsHeaders are set to true, 
     * your data variable has to have header values in keys of each element. In 
     * other case, when keysAsHeaders are set to false you have to give a header 
     * value through setHeaders.
     * 
     * @param array $data raw data, given in array
     * @param boolean $keysAsHeaders headers in file state
     * @param char $value_separator separator between values
     * @param char $nl_separator new line separator 
     * @throws ExporterException
     */
    public function init(array $data, $keysAsHeaders = true)
    {
        if($data == array()){
            throw new DataNotFoundException();
        }

        if($keysAsHeaders === true){
            $keys = array_keys($data);
            if(is_numeric($keys[0])){
                throw new InvalidKeysException();
            }
            $this->setHeaders($keys);
            
        } 
        
        if($this->headers != array()){
            foreach($this->headers as $key){
                 $this->obj .= $key . $this->value_separator;
            }
            
            $this->obj = substr($this->obj, 0, -1) . $this->nl_separator;
        }
        
        foreach($data as $item){
            
            foreach($item as $value){
                 $this->obj .= $value . $this->value_separator;
            }
            
            $this->obj = substr($this->obj, 0, -1) . $this->nl_separator;
            
        }
        
        $this->config->contentSize = strlen($this->obj);
                
    }
    
    /**
     * Sets separator for new line/row
     * 
     * @param char $separator
     */
    public function setNewLineSeparator($separator)
    {
        $this->nl_separator = $separator;
    }
    
    /**
     * Sets separator for value/new column
     * @param char $separator
     */
    public function setValueSeparator($separator)
    {
        $this->value_separator = $separator;
    }
    
    /**
     * Returns generated values as string
     * 
     * @return string
     */
    public function getContent()
    {
        return $this->obj;
    }
    

    /**
     * Override abstract function from parent. It sets filename in previously 
     * set up instance of stdClass, and do file_put_contents to save csv file
     * 
     * @param string $fileName name of file
     * @throws ExporterException
     */
    protected function exportFile($fileName = "export_file.csv")
    {        
        $this->config->filename = $fileName;
        
        file_put_contents($this->outputPath . $fileName, $this->obj); 
        
    }
    

}