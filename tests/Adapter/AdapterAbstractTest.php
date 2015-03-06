<?php

namespace Vegas\Tests\Exporter\Adapter;

use \Vegas\Exporter\Adapter\AdapterAbstract;

class AdapterAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Exporter
     */
    private $obj;
    
    public function setUp()
    {
        $this->fail('Not implemented - version break');
        $this->obj = $this->getMockForAbstractClass('\Vegas\Exporter\Adapter\AdapterAbstract');
    }
    
    public function tearDown()
    {
        $this->obj = null;
    }
    
    public function testSetHeadersException()
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\EmptyHeadersException');
        $this->obj->setHeaders(array());
    }
    
    public function testSetHeaders()
    {
        $expectedHeaders = array('some', 'headers', 'test');
        
        $this->obj->setHeaders($expectedHeaders);
        $headers = $this->getPrivatePropertyValue($this->obj, 'headers');
        
        $this->assertSame($expectedHeaders, $headers);
    }
    
    public function testSetOutputPathException()
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\OutputPathNotWritableException');
        $this->obj->setOutputPath('NotExistingPath');
    }
    
    public function testSetOutputPath()
    {
        $expectedValue = '/tmp/';
        
        $this->obj->setOutputPath($expectedValue);
        $outputPath = $this->getPrivatePropertyValue($this->obj, 'outputPath');
        
        $this->assertSame($expectedValue, $outputPath);
    }
    
    public function testSetFileNameException()
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\InvalidArgumentTypeException');
        $this->obj->setFileName(123);
    }
    
    public function testSetFileName()
    {
        $expectedValue = 'foo_bar.name';
        
        $this->obj->setFileName($expectedValue);
        $fileName = $this->getPrivatePropertyValue($this->obj, 'fileName');
        
        $this->assertSame($expectedValue, $fileName);
    }
    
    /**
     * @param type $classObj
     * @param type $propertyValue
     * @return mixed
     */
    private function getPrivatePropertyValue($classObj, $propertyValue)
    {
        $reflection = new \ReflectionClass($this->obj);
        $reflection_method = $reflection->getProperty($propertyValue);
        $reflection_method->setAccessible('public');
        
        return $reflection_method->getValue($this->obj);
    }
}
