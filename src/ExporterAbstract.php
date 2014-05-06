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
     * @param type $mongo if we are going to use mongo db set this var to true
     */
    abstract function init(array $data, $mongo = false);
    
    /**
     * It sets header row for output data
     * @param array $headers data with header
     */
    abstract function setHeaders(array $headers);
    
    /**
     * @param string $filename Name of file, override with default set value
     */
    abstract function export($filename);
    
    /**
     * It returns content that should be exported
     */
    abstract function getContent();
    
    /**
     * Download function. Sets a few header, and print content
     */
    function download()
    {
        header('Content-Type: ' . $this->properties->contentType);
        header('Content-Disposition: attachment; filename=' . $this->properties->filename);
        header('Content-Length: ' . $this->properties->contentSize);
        
        echo $this->getContent();
        exit;        
    }
    
    /**
     * Setter
     * @param String $path
     */
    function setOutputPath($path)
    {
        $this->outputPath = $path;
    }

}

