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

abstract class AdapterAbstract implements AdapterInterface
{
    /**
     * @var string
     */
    protected $contentType;
    
    /**
     * @var string
     */
    protected $contentSize;
    
    /**
     * @var string
     */
    protected $outputPath;
    
    /**
     * @var string
     */
    protected $fileName;
    
    /**
     * Data headers to use in export.
     * 
     * @var array
     */
    protected $headers = array();
    
    /**
     * @param array $data
     * @param boolean $useKeysAsHeaders
     */
    abstract public function init(array $data, $useKeysAsHeaders = false);

    /**
     * @return mixed
     */
    abstract protected function download();

    /**
     * @return mixed
     */
    abstract protected function exportFile();
    
    /**
     * Exports data into file if output path was set.
     * Forces file download otherwise.
     */
    public function export()
    {
        if (empty($this->outputPath)) {
            $this->download();
        } else {
            $this->exportFile();
        }
    }

    /**
     * Sets header rows for output data.
     * It must be used before init in order to work.
     *
     * @param array $headers data with header
     * @throws Exception\EmptyHeadersException
     */
    public function setHeaders(array $headers)
    {
        if (empty($headers)) {
            throw new Exception\EmptyHeadersException();
        }
        
        $this->headers = array_values($headers);
    }

    /**
     * @param String $path
     * @throws Exception\OutputPathNotWritableException
     */
    public function setOutputPath($path)
    {
        if (!is_writable($path)) {
             throw new Exception\OutputPathNotWritableException();
        }
        
        $this->outputPath = $path;
    }
    
    /**
     * Sets exported file name.
     * 
     * @param string $name
     * @return \Vegas\Exporter\Adapter\Pdf
     * @throws Exception\InvalidArgumentTypeException
     */
    public function setFileName($name)
    {
        if (!is_string($name)) {
            throw new Exception\InvalidArgumentTypeException();
        }
        
        return $this->fileName = $name;
    }
    
    /**
     * Sets HTTP headers for file download.
     */
    protected function setDownloadHttpHeaders()
    {
        if (!is_string($this->contentType) || !is_string($this->fileName)) {
            throw new Exception\InvalidArgumentException();
        }
        
        header('Content-Type: ' . $this->contentType);
        header('Content-Disposition: attachment; filename=' . $this->fileName);
        
        if (!empty($this->contentSize)) {
            header('Content-Length: ' . $this->contentSize);
        }
    }
}

