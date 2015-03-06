<?php

namespace Vegas\Tests\Exporter\Adapter;

use \Vegas\Exporter\Adapter\Pdf;

class PdfTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Pdf
     */
    private $obj;

    /**
     * @var string
     */
    private $testFile = 'test.pdf';

    /**
     * @return string
     */
    private function getTestPath()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    private function getTestFilePath()
    {
        return $this->getTestPath() . $this->testFile;
    }
    
    public function setUp()
    {
        $filePath = $this->getTestPath() . $this->testFile;
        file_exists($filePath) && unlink($filePath);

        $pdf = new Pdf();
        $this->obj = new \Vegas\Exporter\Exporter($pdf);
    }
    
    public function tearDown()
    {
        $this->obj = null;
    }
    
    /**
     * @dataProvider initDataEmptyExceptionProvider
     */
    public function testInitDataEmptyException($exportData)
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\ExportDataEmptyException');
        $this->obj->init($exportData);
    }
    
    public function initDataEmptyExceptionProvider()
    {
        return [
            // #0
            [[1]],
            // #1
            [[null]],
            // #2
            [[new \stdClass()]],
            // #3
            [[]],
        ];
    }
    
    public function testInitSuccess()
    {
        $this->assertInstanceOf('\Vegas\Exporter\Exporter', $this->obj);
        
        $exportData = [
            [1, 2],
            [11, 22],
            [111, 222],
        ];
        
        $this->obj->init($exportData);
    }
    
    public function testExportFileWithoutHeaders()
    {
        $exportData = [
            [1, 2],
            [11, 22],
            [111, 222],
        ];
        
        $this->obj->init($exportData);
        
        $this->obj->setOutputPath($this->getTestPath());
        $this->obj->setFileName($this->testFile);
        $this->obj->run();
        
        $this->assertFileExists($this->getTestFilePath());
    }
    
    public function testExportFileWithtHeadersSetFromMethod()
    {
        $headers = ['foo', 'bar'];
        $outputFilePath = $this->getTestFilePath();
        
        $exportData = [
            [1, 2],
            [11, 22],
            [111, 222],
            ['zażółć gęślą', 'jaźń']
        ];
        
        $this->obj->setHeaders($headers);
        $this->obj->setOutputPath($this->getTestPath());
        $this->obj->setFileName($this->testFile);
        
        $this->obj->init($exportData);
        $this->obj->run();
        
        $this->assertFileExists($outputFilePath);

        unset($outputFilePath);
    }
    
    public function testExportFileWithtHeadersSetFromArgument()
    {
        $outputFilePath = $this->getTestFilePath();
        
        $exportData = [
            ['foo', 'bar'],
            [1, 2],
            [11, 22],
            [111, 222],
            ['zażółć gęślą', 'jaźń']
        ];
        
        $this->obj->setOutputPath($this->getTestPath());
        $this->obj->setFileName($this->testFile);
        
        $this->obj->init($exportData);
        $this->obj->run();
        
        $this->assertFileExists($outputFilePath);
        
        unset($outputFilePath);
    }
    
    public function testSetPageSizeException()
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\InvalidPageSizeException');
        $this->obj->setPageSize('foo');
    }
    
    public function testSetPageOrientation()
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\InvalidPageOrientationException');
        $this->obj->setPageOrientation('bar');
    }
    
    public function testSetCellWidthException()
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\InvalidCellWidthException');
        $this->obj->setCellWidth('baz');
    }
    
    public function testSetCellHeightException()
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\InvalidCellHeightException');
        $this->obj->setCellHeight('fooo');
    }
    
    public function testSetFontSize()
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\InvalidFontSizeException');
        $this->obj->setFontSize('baar');
    }
    
    public function testSetFontFamily()
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\InvalidFontFamilyException');
        $this->obj->setFontFamily('baaz');
    }
    
    public function testSetFontStyle()
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\InvalidFontStyleException');
        $this->obj->setFontStyle('baaz');
    }
}
