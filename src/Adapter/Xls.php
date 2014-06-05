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

class Xls extends AdapterAbstract
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
    private $xls;
    
    /**
     * Constructor. Initialize $config variable as instance of stdClass. First 
     * of all, contentSize and fileName are set as null and contentType is set to 
     * 'application/vnd.ms-excel' so it points that exported file should be xls type.
     * Initialize $xls variable as instance of PHPExcel() and sets active sheet 
     * index to 0
     */
    public function __construct()
    {
        $this->fileName = 'export_file.xls';
        $this->contentType = 'application/vnd.ms-excel';
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
     * @param boolean $useKeysAsHeaders
     * @throws Exception\DataNotFoundException
     * @throws Exception\InvalidArgumentTypeException
     */
    public function init(array $data, $useKeysAsHeaders = false)
    {
        if($data == array()){
            throw new Exception\DataNotFoundException();
        }
        
        $this->xls = new \PHPExcel();
        $this->xls->setActiveSheetIndex(0);
        
        $i = 1;
        $column = 'A';
        
        $sheet = $this->xls->getActiveSheet();
        
        if ($useKeysAsHeaders) {
            $this->setHeaders(array_keys($data[0]));
        }
        
        foreach($this->headers as $key){
            if (!is_string($key)){
                throw new Exception\InvalidHeadersDataException();
            }
            $sheet->getCell($column . $i)->setValue($key);
            ++$column;
        }
        $i++;

        foreach($data as $item){
            $column = 'A';
            foreach($item as $value){
                if (!is_string($value)){
                    throw new Exception\InvalidArgumentTypeException();
                }
                $sheet->getCell($column . $i)->setValue($value);
                ++$column;
            }
            $i++;
        }

        $temporary_file = '/tmp/'.uniqid().'.xls';
        if(!empty($this->outputPath)) {
            $temporary_file = $this->outputPath .uniqid() . '.xls';
        }

        $writer = new \PHPExcel_Writer_Excel2007($this->xls);
        $writer->save($temporary_file);
        
        $this->contentSize = filesize($temporary_file);
        unlink($temporary_file);
    }
    
    /**
     * It sets fileName to previously set up instance of stdClass
     * and use private method saveExcel()
     */
    public function exportFile()
    {
        $writer = new \PHPExcel_Writer_Excel2007($this->xls);
        $writer->save($this->outputPath . $this->fileName);
    }
    
    /**
     * Sends generated XLS into to the browser.
     */
    public function download()
    {
        $this->setDownloadHttpHeaders();
        
        echo $this->obj();
    }
}