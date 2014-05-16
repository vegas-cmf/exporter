<?php

namespace Vegas\Tests\Exporter\Adapter;

use \Vegas\Exporter\Adapter\Pdf;

class PdfTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Pdf
     */
    private $obj;
    
    public function setUp()
    {
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
        return array(
            // #0
            array(array(1)),
            // #1
            array(array(null)),
            // #2
            array(array(new \stdClass())),
            // #3
            array(array()),
        );
    }
    
    public function testInitSuccess()
    {
        $this->assertInstanceOf('\Vegas\Exporter\Exporter', $this->obj);
        
        $exportData = array(
            array(1, 2),
            array(11, 22),
            array(111, 222),
        );
        
        $this->obj->init($exportData);
    }
    
    public function testExportFileWithoutHeaders()
    {
        $outputPath = '/tmp/';
        $fileName = 'file.pdf';
        
        $exportData = array(
            array(1, 2),
            array(11, 22),
            array(111, 222),
        );
        
        $this->obj->init($exportData);
        
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        $this->obj->run();
        
        $this->assertFileExists($outputPath . $fileName);
    }
    
    public function testExportFileWithtHeadersSetFromMethod()
    {
        $headers = array('foo', 'bar');
        $outputPath = '/tmp/';
        $fileName = 'file.pdf';
        $outputFilePath = $outputPath . $fileName;
        
        $exportData = array(
            array(1, 2),
            array(11, 22),
            array(111, 222),
        );
        
        $this->obj->setHeaders($headers);
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        
        $this->obj->init($exportData);
        $this->obj->run();
        
        $this->assertFileExists($outputFilePath);
        
        unset($outputFilePath);
    }
    
    public function testExportFileWithtHeadersSetFromArgument()
    {
        $outputPath = '/tmp/';
        $fileName = 'file.pdf';
        $outputFilePath = $outputPath . $fileName;
        
        $exportData = array(
            array('foo', 'bar'),
            array(1, 2),
            array(11, 22),
            array(111, 222),
        );
        
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        
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
