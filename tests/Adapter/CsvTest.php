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

class CsvTest extends \PHPUnit_Framework_TestCase {

    private $csv;


    /**
     * @dataProvider getRecordWithoutKeysProvider
     * @param string $exportData
     */
    public function testCsvExport($exportData) 
    {
        $this->obj->setHeaders(array('name','lastname','age'));
        $this->obj->init($exportData, true);
        $this->obj->setOutputPath("/tmp/");
        $this->obj->export('test.csv');

        $this->csv_string = "name,lastname,age;John,Smith,19;Paul,Smith2,36;Adam,Smit3,14;";
        
        $this->assertSame($this->csv_string, $this->obj->getContent());
        
    }

    /**
     * @dataProvider getRecordWithoutKeysProvider
     * @param string $exportData
     */
    public function testCsvSaveWithoutHeaders($exportData) 
    {
        $filename = 'test.csv';
        $this->obj->setOutputPath("/tmp/");
        $this->obj->init($exportData, false);
        $this->obj->export($filename);

        $this->csv_string = "John,Smith,19;Paul,Smith2,36;Adam,Smit3,14;";
        
        $this->assertSame($this->csv_string, file_get_contents("/tmp/" . $filename));

    }
    
    public function testCsvInit() 
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\CsvException');
        $this->obj->init(array(), false);        

    }
    
    public function testCsvObjVar() 
    {
        $class = new \ReflectionClass("Vegas\Exporter\Adapter\Csv");
        $property = $class->getProperty("obj");
        $property->setAccessible(true);

        $exportData = array(array("John", "Smith", "19"));

        $temp = new Csv();
        $temp->init($exportData, false);
        $this->assertTrue(is_string($property->getValue($temp)));
    }
    
    /**
     * @dataProvider getRecordProvider
     * @param type $exportData
     */
    public function testCsvNoHeaders($exportData) 
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\CsvException');
        $this->obj->init($exportData, true);        

    }

    protected function setUp() {
        $this->obj = new Csv();
    }
    
    protected function tearDown() 
    {
        $this->obj = null;
        
//        $files = array('test.csv');
//        
//        foreach($files as $value){
//            if (file_exists($value)){
//                unlink($value);
//            }
//
//        }

    }

    public function getRecordProvider() 
    {
        return array(
            array(
                array(
                    array('name' => "John", 'lastname' => "Smith", 'age' => "19"),
                    array('name' => "Paul", 'lastname' => "Smith2", 'age' => "36"),
                    array('name' => "Adam", 'lastname' => "Smit3", 'age' => "14"),
                ) 
            )
        );
    }
    
    public function getHeader() 
    {
        return array('name', 'lastname', 'age');
    }
    
   

    public function getRecordWithoutKeysProvider() 
    {
        return array(
            array(
                array(
                    array("John", "Smith", "19"),
                    array("Paul", "Smith2", "36"),
                    array("Adam", "Smit3", "14"),
                )
            )
        );
    }

}
