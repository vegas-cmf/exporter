<?php 

abstract class ExporterAbstract {

    /**
     *
     * @var StdClass
     *  
     */
    protected $properties;
    
    protected $outputPath = null;
    
    abstract function init(array $data, bool $mongo = false);
    
    abstract function setHeaders(array $headers);
    
    abstract function export($filename);
    
    abstract function getContent();
    
    function download()
    {
        header('Content-Type: '. $this->properties->contentType);
        header('Content-Disposition: attachment; filename='.$this->properties->filename);
        header('Content-Length: ' . $this->properties->contentSize);
        
        echo $this->getContent();
        exit;        
    }
    
    function setOutputPath($path)
    {
        $this->outputPath = $path;
    }

}


