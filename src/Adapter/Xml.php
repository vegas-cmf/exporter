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

use Vegas\Exporter\Adapter\Exception\XmlException as XmlException;

class Xml extends ExporterAbstract {
    
    /**
     * Variable use to store SimpleXMLElement, which are set up in init() function.
     * 
     * @var SimpleXMLElement 
     */
    private $obj;
            
    /**
     * Constructor. Initialize $config variable as instance of stdClass. First 
     * of all, contentSize and fileName are set as null and contentType is set to 
     * 'application/xml' so it points that exported file should be xml type
     */
    public function __construct()
    {
        $this->config = new \stdClass();
        $this->config->contentSize = null;
        $this->config->contentType = 'application/xml';
        $this->config->fileName = null;
    }
    
    /**
     * Sets data and create config object (stdClass). If keysAsHeaders are set to true, 
     * your data variable has to have header values in keys of each element. In 
     * other case, when keysAsHeaders are set to false you have to give a header 
     * value through setHeaders 
     * 
     * @param array $data
     * @param boolean $keysAsHeaders
     * @throws XmlException
     */
    public function init(array $data, $keysAsHeaders = true)
    {
        if($data == array()){
            throw new XmlException("Data cannot be empty");
        }

        $this->obj = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><root></root>');
        
        foreach($data as $items){
            
            $parent = $this->obj->addChild('item');
            
            foreach($items as $key => $item){
                
                if($keysAsHeaders){            
                    $parent->addChild($key, $item);            
                } else {                    
                    $parent->addChild($this->headers[$key], $item);                    
                }
            
            }
            
        }
        
        $this->config->contentSize = strlen($this->obj->asXML());
        
    }
    
    /**
     * Returns generated XML as string
     * 
     * @return string
     */
    public function getContent()
    {
        return $this->obj->asXML();
    }
    
    /**
     * Override abstract function from parent. It sets filename to previously 
     * set up instance of stdClass, and use private method saveXml()
     * 
     * @param string $fileName
     * @throws ExporterException
     */
    protected function exportFile($fileName = 'export_file.xml')
    {
        $this->config->fileName = $fileName;
        
        $this->obj->asXML($this->outputPath . $fileName);
        
    }
    
    

}