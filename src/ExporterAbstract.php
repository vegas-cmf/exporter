<?php 

namespace Vegas\Exporter;

use \Vegas\Exporter\Exception as ExporterException;

abstract class ExporterAbstract 
{
    /**
     * Object with following properties:
     *  - contentType
     *  - contentSize
     *  - filename
     * 
     * @var StdClass
     */
    protected $properties;
    
    /**
     * Output file path.
     * 
     * @var String
     */
    protected $outputPath = null;
    
    /**
     * Data headers to use in export.
     * 
     * @var array
     */
    protected $headers = array();
    
    /**
     * Sets header rows for output data.
     * It must be used before init in order to work.
     * 
     * @param array $headers data with header
     */
    public function setHeaders(array $headers)
    {
        if (empty($headers))
        {
            throw new ExporterException('Data headers cannot be empty');
        }
        
        $this->headers = array_values($headers);
    }
    
    /**
     * Initializes data object for export.
     * It must set up properties object as well.
     * 
     * @param array $data set of data to export
     * @param type $keysAsHeaders uses array keys as headers for export data
     */
    abstract function init(array $data, $keysAsHeaders = false);
    
    /**
     * Returns exported output file content.
     */
    abstract function getContent();
    
    /**
     * Executes data export.
     * 
     * @param string $filename Download file name
     */
    abstract function exportData($filename);
    
    /**
     * @param String $path
     */
    function setOutputPath($path)
    {
        $this->outputPath = $path;
    }
        
    /**
     * Exports data into file if output path was set.
     * Forces file download otherwise.
     * 
     * @param string $filename Download file name
     */
    public function export($filename)
    {
        if (empty($this->outputPath))
            $this->download();
        else
            $this->exportData($filename);
    }
    
    /**
     * Exports file download.
     */
    function download()
    {
        header('Content-Type: ' . $this->properties->contentType);
        header('Content-Disposition: attachment; filename=' . $this->properties->filename);
        header('Content-Length: ' . $this->properties->contentSize);
        
        echo $this->getContent();
        exit;        
    }
}

