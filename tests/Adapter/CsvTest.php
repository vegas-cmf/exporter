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
     * @dataProvider saveWithoutHeadersDataProvider
     * @param string $exportData
     */
    public function testCsvExport($exportData)
    {
        $this->obj->setHeaders(array('name','lastname','age'));
        $this->obj->init($exportData, false);
        $this->obj->setOutputPath("/tmp/");
        $this->obj->export('test.csv');

        $this->csv_string = "name,lastname,age;John,Smith,19;Paul,Smith2,36;Adam,Smit3,14;";
        
        $this->assertSame($this->csv_string, $this->obj->getContent());
        
    }

    /**
     * @dataProvider saveWithoutHeadersDataProvider
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
    public function saveWithoutHeadersDataProvider()
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
    
    public function testCsvInit()
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\DataNotFoundException');
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
     * @param type $exportData
     */
    public function testCsvNoKeys()
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\InvalidKeysException');
        $this->obj->init(array(array()), true);        

    }
    
    public function noHeadersDataProvider()
    {
        return array(
            
        );
        
//        return array(
//            array(
//                array(
//                    array('name' => "John", 'lastname' => "Smith", 'age' => "19"),
//                    array('name' => "Paul", 'lastname' => "Smith2", 'age' => "36"),
//                    array('name' => "Adam", 'lastname' => "Smit3", 'age' => "14"),
//                ) 
//            )
//        );
    }

    protected function setUp()
    {
        $this->obj = new Csv();
    }
    
    protected function tearDown()
    {
        $this->obj = null;

        if (file_exists('test.csv')){
            unlink('test.csv');
        }

    }

}
