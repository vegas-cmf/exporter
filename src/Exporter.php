<?php
/**
 * This file is part of Vegas Exporter package.
 *
 * @author Radosław Fąfara <radek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://github.com/vegas-cmf/exporter
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Exporter;

use Phalcon\DI\InjectionAwareInterface;
use Vegas\DI\InjectionAwareTrait;
use Vegas\Exporter\Adapter\AdapterInterface;

class Exporter implements InjectionAwareInterface
{
    use InjectionAwareTrait;

    /**
     * Default namespace which contains available exporters
     */
    const DEFAULT_ADAPTER_NAMESPACE = '\\Vegas\\Exporter\\Adapter\\';

    /**
     * @var ExportSettings
     */
    protected $config;

    /**
     * @return ExportSettings
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param ExportSettings $config
     * @return $this
     */
    public function setConfig(ExportSettings $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Proxy to adapter - allows user to download, get raw text or store to file
     * @param $name
     * @param $arguments
     * @return string
     * @throws Exception\InvalidExporterException
     * @throws Exception\InvalidMethodException
     * @throws Exception\NotConfiguredException
     */
    public function __call($name, $arguments)
    {
        $adapterName = preg_replace('/^(save|download|print)/', '', $name, 1);
        $action = str_replace($adapterName, '', $name);

        if (empty($action)) {
            throw new Exception\InvalidMethodException($name);
        }

        $adapter = $this->createAdapter($adapterName);

        if ($action === 'print') {
            return $adapter->output($arguments);
        } else {
            return $this->{$action}($adapter);
        }
    }

    /**
     * Sets appropriate headers for downloading & renders response.
     * Proxied via __call() method
     * @param AdapterInterface $adapter
     */
    protected function download(AdapterInterface $adapter)
    {
        /** @var \Phalcon\Http\Response $response */
        $response = $this->di->get('response');
        $output = $adapter->output();

        $filename = $this->config->getFilename() . $adapter->getExtension();

        $response->setContentType($adapter->getContentType());
        $response->setRawHeader(sprintf('Content-Disposition: attachment; filename="%s"', $filename));
        $response->setRawHeader('Cache-Control: public, must-revalidate, max-age=0');
        $response->setRawHeader('Pragma: public');
        $response->setRawHeader('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        $response->setContent($output);

        $response->send();
    }

    /**
     * Saves export output under specified filepath without downloading.
     * Proxied via __call() method
     * @param AdapterInterface $adapter
     */
    protected function save(AdapterInterface $adapter)
    {
        $output = $adapter->output();

        $filename = $this->config->getFilename() . $adapter->getExtension();
        $filepath = implode(DIRECTORY_SEPARATOR, [$this->config->getOutputDir(), $filename]);

        $file = fopen($filepath, 'wb');
        fwrite($file, $output);
        fclose($file);
    }

    /**
     * Creates a new instance of export adapter
     * @param string $name
     * @param string $namespace namespace to be used
     * @return \Vegas\Exporter\Adapter\AdapterInterface
     */
    protected function createAdapter($name, $namespace = self::DEFAULT_ADAPTER_NAMESPACE)
    {
        $adapterClassPath = $namespace . $name;

        if (empty($name) || !class_exists($adapterClassPath)) {
            throw new Exception\InvalidExporterException($adapterClassPath);
        }

        $adapter = new $adapterClassPath;

        if (!isset($this->config)) {
            throw new Exception\NotConfiguredException;
        }

        $adapter->setConfig($this->config);
        $adapter->validateOutput();

        return $adapter;
    }
}

