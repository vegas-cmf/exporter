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
    protected $config;
    
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
    abstract public function init(array $data, $keysAsHeaders = false);
    
    /**
     * Returns exported output file content.
     */
    abstract protected function getContent();
    
    /**
     * Executes data export.
     * 
     * @param string $filename Download file name
     */
    abstract protected function exportFile($filename);
    
    /**
     * @param String $path
     */
    public function setOutputPath($path)
    {
        if(!is_writable($path)){
             throw new ExporterException("Output path: $path is not writable");
        }
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
        if (empty($this->outputPath)) {
            $this->download();
        } else {
            $this->exportFile($filename);
        }
    }
    
    /**
     * Exports file download.
     */
    private function download()
    {
        header('Content-Type: ' . $this->config->contentType);
        header('Content-Disposition: attachment; filename=' . $this->config->filename);
        header('Content-Length: ' . $this->config->contentSize);
        
        echo $this->getContent();
        exit;        
    }
}

