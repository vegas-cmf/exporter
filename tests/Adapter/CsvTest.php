<?php

/**
 * This file is part of Vegas Exporter package
 *
 * @author Mateusz Aniołek <matty201@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Tests\Exporter\Adapter;

use Vegas\Exporter\Adapter\Csv as Csv;

class CsvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $csv;
    
    private $defaultNewLineSeparator = PHP_EOL;
    
    protected function setUp()
    {
        $this->fail('Not implemented - version break');
        $csv = new Csv();
        $this->defaultNewLineSeparator = ';';
        $csv->setNewLineSeparator(';');
        $this->obj = new \Vegas\Exporter\Exporter($csv);
    }
    
    protected function tearDown()
    {
        $this->obj = null;

        if (file_exists('test.csv')){
            unlink('test.csv');
        }
    }

    public function testCsvExport()
    {
        $exportData = array(
            array("John", "Smith", "19"),
            array("Paul", "Smith2", "36"),
            array("Adam", "Smit3", "14"),
        );

        $fileName = 'test.csv';
        $outputPath = '/tmp/';
        $headers = array('name','lastname','age');
        
        $this->obj->setHeaders($headers);
        $this->obj->setFileName($fileName);
        $this->obj->setOutputPath($outputPath);
        
        $this->obj->init($exportData);
        $this->obj->run();
        
        $csv_string = 'name,lastname,age;John,Smith,19;Paul,Smith2,36;Adam,Smit3,14;';
        
        $this->assertSame($csv_string, file_get_contents($outputPath . $fileName));
    }

    public function testCsvExportWithoutHeaders()
    {
        $exportData = array(
            array("John", "Smith", "19"),
            array("Paul", "Smith2", "36"),
            array("Adam", "Smit3", "14"),
        );
                
        $fileName = 'test.csv';
        $outputPath = '/tmp/';
        
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        
        $this->obj->init($exportData, false);
        $this->obj->run($fileName);

        $csv_string = 'John,Smith,19;Paul,Smith2,36;Adam,Smit3,14;';
        
        $this->assertSame($csv_string, file_get_contents($outputPath . $fileName));
    }
    
    public function testCsvExportWithHeadersInData()
    {
        $exportData = array(
            array('name' => "John", 'lastname' => "Smith", 'age' => 19),
            array('name' => "Paul", 'lastname' => "Smith2", 'age' => 36),
            array('name' => "Adam", 'lastname' => "Smit3", 'age' => 14),
        );
                
        $fileName = 'test.csv';
        $outputPath = '/tmp/';
        
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        
        $this->obj->init($exportData, true);
        $this->obj->run($fileName);

        $csv_string = 'name,lastname,age;John,Smith,19;Paul,Smith2,36;Adam,Smit3,14;';
        
        $this->assertSame($csv_string, file_get_contents($outputPath . $fileName));
    }
    
    
    public function testCsvEmptyDataInit()
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\DataNotFoundException');
        $this->obj->init(array(), false);        
        $this->obj->init(null, false);        
        $this->obj->init(array(array()), false);        
        $this->obj->init(array(array(null)), false);        
    }
    
    
    public function testCsvObjVariable()
    {
        $class = new \ReflectionClass("Vegas\Exporter\Adapter\Csv");
        $property = $class->getProperty("csv");
        $property->setAccessible(true);

        $exportData = array(array("John", "Smith", "19"));

        $temp = new Csv();
        $temp->init($exportData, false);
        $this->assertTrue(is_string($property->getValue($temp)));
    }
    
    public function testCsvLongSeparator(){
        
        $fileName = 'test.csv';
        $outputPath = '/tmp/';
        
        $exportData = array(
            array("John", "Smith", "19"),
            array("Paul", "Smith2", "36"),
            array("Adam", "Smit3", "14"),
        );
        $sep = '@$-=*';
        
        
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        
        $this->obj->setValueSeparator($sep);   
        
        $this->obj->init($exportData, false);
        $this->obj->run($fileName);

        $csv_string = 'John'.$sep.'Smith'.$sep.'19;Paul'.$sep.'Smith2'.$sep.'36;Adam'.$sep.'Smit3'.$sep.'14;';
        
        $this->assertSame($csv_string, file_get_contents($outputPath . $fileName));
        
    }
    
    public function testCsvNewLine(){
        
        $fileName = 'test.csv';
        $outputPath = '/tmp/';
        
        $exportData = array(
            array("John\n in new line", "Smith", "19"),
            array("Paul", "Smith2", "36"),
            array("Adam", "Smit3", "14"),
        );
        
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        
        $this->obj->init($exportData, false);
        $this->obj->run($fileName);

        $csv_string = 'John in new line,Smith,19;Paul,Smith2,36;Adam,Smit3,14;';
        
        $this->assertSame($csv_string, file_get_contents($outputPath . $fileName));
        
    }
    
    public function testCsvHtmlTags(){
        
        $fileName = 'test.csv';
        $outputPath = '/tmp/';
        
        $exportData = array(
            array("<b>John</b>", "Smith", "19"),
            array("Paul", "Smith2", "36"),
            array("Adam", "Smit3", "14"),
        );
        
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        
        $this->obj->init($exportData, false);
        $this->obj->run($fileName);

        $csv_string = 'John,Smith,19;Paul,Smith2,36;Adam,Smit3,14;';
        
        $this->assertSame($csv_string, file_get_contents($outputPath . $fileName));
        
    }
}
