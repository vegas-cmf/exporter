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

namespace Vegas\Tests\Exporter\Adapter;

use Vegas\Test\TestCase;

class AdapterAbstractTest extends TestCase
{
    /**
     * @return \Vegas\Exporter\ExportSettings
     */
    private function createExportConfig()
    {
        $headers = ['foo', 'bar'];

        $exportData = [
            ['foo' => 1, 'bar' => 2],
            ['bar' => 11, 'foo' => 22],
            ['foo' => 111, 'bar' => 222],
            ['foo' => 'zażółć gęślą', 'bar' => 'jaźń']
        ];

        return (new \Vegas\Exporter\ExportSettings)
            ->setTemplate('export_sample')
            ->setTitle('Sample export') // optional
            ->setHeaders($headers)
            ->setData($exportData);
    }

    /**
     * @var \Vegas\Exporter\Adapter\AdapterAbstract
     */
    private $adapter;
    
    public function setUp()
    {
        parent::setUp();
        $this->adapter = $this->getMockForAbstractClass('\Vegas\Exporter\Adapter\AdapterAbstract');
    }

    public function tearDown()
    {
        $this->adapter = null;
    }
    
    public function testSetConfig()
    {
        $config = $this->createExportConfig();
        
        $this->adapter->setConfig($config);
        $internalConfig = $this->getPrivatePropertyValue($this->adapter, 'config');
        
        $this->assertSame($config, $internalConfig);
    }

    public function testEmptyHeadersAreNotAllowed()
    {
        $config = $this->createExportConfig();

        $config->setHeaders([]);

        $this->adapter->setConfig($config);

        try {
            $this->adapter->validateOutput();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Adapter\Exception\EmptyHeadersException', $e);
        }
    }

    public function testOutputPathMustBeWritable()
    {
        $config = $this->createExportConfig();

        $this->adapter->setConfig($config);

        $void = $this->adapter->validateOutput();
        $this->assertEmpty($void);

        $config->setOutputDir('/this/path/doesnt/exist');

        try {
            $this->adapter->validateOutput();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Adapter\Exception\OutputPathNotWritableException', $e);
        }
    }

    public function testFilenameMustBeString()
    {
        $config = $this->createExportConfig();

        $config->setFilename(null);

        $this->adapter->setConfig($config);

        try {
            $this->adapter->validateOutput();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Adapter\Exception\InvalidArgumentTypeException', $e);
        }
    }
    
    /**
     * @param object $object
     * @param string $propertyValue
     * @return mixed
     */
    private function getPrivatePropertyValue($object, $propertyValue)
    {
        $reflection = new \ReflectionClass($object);
        $reflectionMethod = $reflection->getProperty($propertyValue);
        $reflectionMethod->setAccessible('public');
        
        return $reflectionMethod->getValue($object);
    }
}
