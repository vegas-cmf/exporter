<?php
/**
 * This file is part of Vegas Exporter package.
 *
 * @author Radosław Fąfara <radek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://github.com/vegas-cmf/exporter
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. *
 */

namespace Vegas\Exporter\Adapter;

abstract class AdapterAbstract implements AdapterInterface
{
    /**
     * @var \Vegas\Exporter\ExportSettings
     */
    protected $config;

    /**
     * @param \Vegas\Exporter\ExportSettings $config
     * @return $this
     */
    public function setConfig(\Vegas\Exporter\ExportSettings $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @throws Exception\EmptyHeadersException
     * @throws Exception\InvalidArgumentTypeException
     * @throws Exception\OutputPathNotWritableException
     */
    public function validateOutput()
    {
        $headers = $this->config->getHeaders();
        if (empty($headers)) {
            throw new Exception\EmptyHeadersException;
        }

        if (!is_writable($this->config->getOutputDir())) {
            throw new Exception\OutputPathNotWritableException;
        }

        if (!is_string($this->config->getFilename())) {
            throw new Exception\InvalidArgumentTypeException;
        }

        //$data = $this->config->getData();
        //FIXME do we need this?
        //if (empty($data)) {
        //  throw new Exception\DataNotFoundException();
        //}
    }

    /**
     * Retrieves raw set of data based on used headers.
     * @param mixed $item
     * @return array
     */
    protected function getRawItem($item)
    {
        $values = [];
        foreach ($this->config->getHeaderParams() as $header) {
            if (is_object($item)) {
                $values[] = $item->{$header};
            } else if (isset($item[$header])) {
                $values[] = $item[$header];
            } else {
                $values[] = null;
            }
        }
        return $values;
    }
}

