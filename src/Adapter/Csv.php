<?php
/**
 * This file is part of Vegas Exporter package.
 *
 * @author Mateusz Aniolek <matty201@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. * 
 */

namespace Vegas\Exporter\Adapter;

class Csv extends AdapterAbstract
{    
    /**
     * @var string
     */
    private $csv;
        
     /**
     * Constructor. Initialize $config variable as instance of stdClass. First
     * of all, contentSize and fileName are set as null and contentType is set to
     * 'text/csv' so it points that exported file should be string type. Also 
     * sets default separators. For new line - ';', and for next value - ','.
     */
    public function __construct()
    {
        $this->contentType = 'text/csv';
        $this->fileName = "tests/fixtures/export_file.csv";
        
        $this->lineSeparator = PHP_EOL;
        $this->valueSeparator = ",";
    }

    /**
     * Sets data and create config object (stdClass). If keysAsHeaders are set to true,
     * your data variable has to have header values in keys of each element. In 
     * other case, when keysAsHeaders are set to false you have to give a header 
     * value through setHeaders.
     * 
     * @param array $data raw data, given in array
     * @param boolean $useKeysAsHeaders headers in file state
     * @param char $value_separator separator between values
     * @param char $nl_separator new line separator 
     * @throws ExporterException
     */
    public function init(array $data, $useKeysAsHeaders = false)
    {
        if($data == array()){
            throw new Exception\DataNotFoundException();
        }

        if($useKeysAsHeaders){
            $this->setHeaders(array_keys($data[0]));
        } 
        
        if($this->headers != array()){
            foreach($this->headers as $key){
                $key = str_replace(array("\r", "\n"), "", strip_tags($key));
                $this->csv .= $key . $this->valueSeparator;
            }
            
            $separatorLength = (int)strlen($this->valueSeparator);
            $this->csv = substr($this->csv, 0, -$separatorLength) . $this->lineSeparator;
        }
        
        foreach($data as $item){
            
            foreach($item as $value){
                $value = str_replace(array("\r", "\n"), "", strip_tags($value));
                $this->csv .= $value . $this->valueSeparator;
            }
            $separatorLength = (int)strlen($this->valueSeparator);
            $this->csv = substr($this->csv, 0, -$separatorLength) . $this->lineSeparator;
            
        }
        
        $this->contentSize = strlen($this->csv);
    }
    
    /**
     * Sets separator for new line/row
     * 
     * @param char $separator
     */
    public function setNewLineSeparator($separator)
    {
        $this->lineSeparator = $separator;
    }
    
    /**
     * Sets separator for value/new column
     * @param char $separator
     */
    public function setValueSeparator($separator)
    {
        $this->valueSeparator = $separator;
    }
    

    /**
     * Override abstract function from parent. It sets fileName in previously
     * set up instance of stdClass, and do file_put_contents to save csv file
     * 
     * @throws ExporterException
     */
    protected function exportFile()
    {
        file_put_contents($this->outputPath . $this->fileName, $this->csv);
    }
    
    /**
     * Sends generated CSV into to the browser.
     */
    public function download()
    {
        $this->setDownloadHttpHeaders();
        
        echo $this->csv;
    }
}