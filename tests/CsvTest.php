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

namespace Vegas\Exporter\Tests;

use Vegas\Exporter\Csv as Csv;

class CsvTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider getRecordWithoutKeysProvider
     * @param string $export_data
     */
    public function testCsvCreate($export_data) 
    {
        $filename = 'test.csv';
        $csv = new Csv();
        $csv->init($export_data, false);
        $csv->exportData($filename);

        $csv_string = "John,Smith,19;Paul,Smith2,36;Adam,Smit3,14;";
        
        $this->assertSame($csv_string, $csv->getContent());
        
    }

    /**
     * @dataProvider getRecordWithoutKeysProvider
     * @param string $export_data
     */
    public function testCsvSaveWithHeaders($export_data) 
    {
        $filename = 'test.csv';
        $csv = new Csv();
        $csv->init($export_data, true);
        $csv->setHeaders($this->getHeader());
        $csv->exportData($filename);

        $csv_string = "name,lastname,age;John,Smith,19;Paul,Smith2,36;Adam,Smit3,14;";
        
        $this->assertSame($csv_string, file_get_contents($filename));

    }
    
    /**
     * @dataProvider getRecordWithoutKeysProvider
     * @param string $export_data
     */
    public function testHeaders($export_data) 
    {
        $csv = new Csv();        
        $csv->init($export_data, false);        
        $this->assertFalse($csv->getKeysAsHeaders());

    }

    protected function tearDown() 
    {
        parent::tearDown();

        $files = array('test.csv');
        
        foreach($files as $value){
            if (file_exists($value)){
                unlink($value);
            }

        }

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
