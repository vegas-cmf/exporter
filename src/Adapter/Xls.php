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

use Vegas\Exporter\Exception as ExporterException;

use Vegas\Exporter\Adapter\Exception\DataNotFoundException as DataNotFoundException;
use Vegas\Exporter\Adapter\Exception\InvalidKeysException as InvalidKeysException;

class Xls extends ExporterAbstract
{
    
    /**
     * Holds data for exportData function
     * @var array 
     */
    private $data;
    
    /**
     * Variable contains PHPExcel object 
     * @var PHPExcel 
     */
    private $obj;
    
    /**
     * Constructor. Initialize $config variable as instance of stdClass. First 
     * of all, contentSize and fileName are set as null and contentType is set to 
     * 'application/vnd.ms-excel' so it points that exported file should be xls type.
     * Initialize $obj variable as instance of PHPExcel() and sets active sheet 
     * index to 0
     */
    public function __construct()
    {
        $this->config = new \stdClass();
        $this->config->contentSize = null;
        $this->config->contentType = 'application/vnd.ms-excel';
        $this->config->fileName = null;
        
        $this->obj = new \PHPExcel();
        $this->obj->setActiveSheetIndex(0);
        
    }

    /**
     * Sets data and create config object (stdClass). If keysAsHeaders are set to true, 
     * your data variable has to have header values in keys of each element. In 
     * other case, when keysAsHeaders are set to false you have to give a header 
     * value through setHeaders.
     * 
     * Generates worksheet for xls file. In addition, it saves temporary our 
     * file to get content length. It's necessary to download file.
     * 
     * @param array $data
     * @param boolean $keysAsHeaders
     * @throws ExporterException
     */
    public function init(array $data, $keysAsHeaders = true)
    {
        if($data == array()){
            throw new DataNotFoundException();
        }
        
        $i = 1;
        $sheet = $this->obj->getActiveSheet();
        
        if($keysAsHeaders === true){
            $keys = array_keys($data);
            if(is_numeric($keys[0])){
                throw new InvalidKeysException();
            }
            $this->setHeaders(array_keys($data));
            
            array_unshift($data, $this->headers);
        }
        
        foreach($data as $item){
            
            $column = 'A';
            foreach($item as $value){
                $sheet->getCell($column.$i)->setValue($value);
                $column++;
            }
            
            $i++;
            
        }
        
        $temporary_file = $this->outputPath . "test.xls";
        $writer = new \PHPExcel_Writer_Excel2007($this->obj);
        $writer->save($temporary_file);
        
        $this->config->contentSize = filesize($temporary_file);
        unlink($temporary_file);
                
    }
    
    /**
     * Returns instance of PHPExcel
     * @return PHPExcel
     */
    public function getContent()
    {
        return $this->obj;
    }
    
     /**
     * Override abstract function from parent. It sets filename to previously 
     * set up instance of stdClass, and use private method saveExcel()
     * 
     * @param array $data
     * @param boolean $keysAsHeaders
     * @throws XmlException
     */
    public function exportFile($fileName = "export_file.xls")
    {                
        $this->config->fileName = $fileName;        
        $this->saveExcel($fileName);
        
    }
        
    /**
     * Save generated xls file
     * 
     * @param string $fileName
     * @throws ExporterException
     */
    private function saveExcel($fileName)
    {        
        $writer = new \PHPExcel_Writer_Excel2007($this->obj);
        $writer->save($this->outputPath . $fileName);
        
    }
    

}