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
    
    protected function setUp()
    {
        $csv = new Csv();
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

        $csv_string = "name,lastname,age;John,Smith,19;Paul,Smith2,36;Adam,Smit3,14;";
        
        $this->assertSame($csv_string, file_get_contents($outputPath . $fileName));
    }

    public function testCsvSaveWithoutHeaders()
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

        $csv_string = "John,Smith,19;Paul,Smith2,36;Adam,Smit3,14;";
        
        $this->assertSame($csv_string, file_get_contents($outputPath . $fileName));
    }
    
    public function testCsvInit()
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\DataNotFoundException');
        $this->obj->init(array(), false);        
    }
    
    public function testCsvObjVar()
    {
        $class = new \ReflectionClass("Vegas\Exporter\Adapter\Csv");
        $property = $class->getProperty("csv");
        $property->setAccessible(true);

        $exportData = array(array("John", "Smith", "19"));

        $temp = new Csv();
        $temp->init($exportData, false);
        $this->assertTrue(is_string($property->getValue($temp)));
    }
}
