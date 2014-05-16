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

class Xml extends ExporterAbstract {
    
    /**
     * Variable use to store SimpleXMLElement, which are set up in init() function.
     * 
     * @var SimpleXMLElement 
     */
    private $xml;
    
    /**
     * Constructor. Initialize $config variable as instance of stdClass. First
     * of all, contentSize and fileName are set as null and contentType is set to
     * 'application/xml' so it points that exported file should be xml type
     */
    public function __construct()
    {
        $this->contentType = 'application/xml';
        $this->fileName = 'export_file.xml';
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
            throw new Exception\DataNotFoundException();
        }

        if($keysAsHeaders === true){
            $keys = array_keys($data);
            if(!is_array($keys)){
                throw new Exception\EmptyKeysException();
            }
            $this->setHeaders(array_keys($data));
        }
        
        $this->xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><root></root>');
        
        foreach($data as $items){
            
            $parent = $this->xml->addChild('item');
            
            foreach($items as $key => $item){
                
                if($keysAsHeaders){
                    $parent->addChild($key, $item);
                } else {      
                    if(!$this->headers){
                        throw new Exception\EmptyHeadersException();
                    }
                    $parent->addChild($this->headers[$key], $item);
                }
            
            }
            
        }
        
        $this->contentSize = strlen($this->xml->asXML());
        
    }
    
    /**
     * Override abstract function from parent. It sets filename to previously
     * set up instance of stdClass, and use private method saveXml()
     * 
     * @param string $fileName
     * @throws ExporterException
     */
    protected function exportFile()
    {
        $this->xml->asXML($this->outputPath . $this->fileName);
        
    }
    
    /**
     * Sends generated XML into to a browser.
     */
    public function download()
    {
        $this->setDownloadHttpHeaders();
        
        echo $this->xml->asXML();
    }
}