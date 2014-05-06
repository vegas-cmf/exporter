<?php 

abstract class ExporterAbstract 
{

    /**
     * Contans std object with content properties:
     *  - contentType
     *  - contentSize
     *  - filename
     * @var StdClass
     *  
     */
    protected $properties;
    
    /**
     * Contains file path 
     * @var String
     *  
     */
    protected $outputPath = null;
    
    /**
     * Abstract init, inside should be created stdClass object 
     * @param array $data set of data to export
     * @param type $keysAsHeaders if we are going to use mongo db set this var to true
     */
    abstract function init(array $data, $keysAsHeaders = false);
    
    /**
     * It sets header row for output data
     * @param array $headers data with header
     */
    abstract function setHeaders(array $headers);
    
    abstract function export($filename);
    
    abstract function getContent();
    
    function download()
    {
        header('Content-Type: ' . $this->properties->contentType);
        header('Content-Disposition: attachment; filename=' . $this->properties->filename);
        header('Content-Length: ' . $this->properties->contentSize);
        
        echo $this->getContent();
        exit;        
    }
    
    function setOutputPath($path)
    {
        $this->outputPath = $path;
    }

}

    /**
     * Setter
     * @param String $path
     */
