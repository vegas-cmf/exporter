<?php
/**
 * This file is part of Vegas Exporter package.
 *
 * @author KRzysztof Kaplon <krzysztof@kaplon.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. * 
 */

namespace Vegas\Exporter;

use \ReflectionException;

class Exporter
{
    /**
     * @var ExporterInterface 
     */
    private $adapter;
    
    /**
     * 
     * @param \Vegas\Exporter\Adapter\AdapterInterface $adapter
     */
    public function __construct(Adapter\AdapterInterface $adapter = null)
    {
        if (!empty($adapter)) {
            $this->setAdapter($adapter);
        }
    }
    
    public function __call($name, $arguments)
    {
        try {
        
            $reflectionMethod = new \ReflectionMethod(get_class($this->adapter), $name);
            $reflectionMethod->invokeArgs($this->adapter, $arguments);
            
        } catch (ReflectionException $exception) {
            throw new Exception\InvalidMethodException();
        }
    }
    
    /**
     * 
     * @param \Vegas\Exporter\Adapter\AdapterInterface $adapter
     */
    public function setAdapter(Adapter\AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }
    
    /**
     * Executes export on adapter.
     */
    public function run()
    {
        $this->adapter->export();
    }
}

