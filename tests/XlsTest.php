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

use Vegas\Exporter\Xls as Xls;

class XlsTest extends \PHPUnit_Framework_TestCase {

    protected function setUp() 
    {
        date_default_timezone_set('Europe/Warsaw');
    }

    /**
     * @dataProvider getRecordProvider
     * @param string $export_data
     */
    public function testSaveWithoutHeader($export_data) 
    {
        $filename = 'test.xls';
        $xls = new Xls();
        $xls->init($export_data);
        $xls->exportData($filename);

        $reader = new \PHPExcel_Reader_Excel2007();
        
        $this->assertInstanceOf('PHPExcel', $xls->getContent());
        $this->assertInstanceOf('PHPExcel', $reader->load($filename));

    }


    /**
     * @dataProvider getRecordProvider
     * @param string $export_data
     */
    public function testHeaders($export_data) 
    {
        $xls = new Xls();        
        $xls->init(array($export_data), false);        
        $this->assertFalse($xls->getKeysAsHeaders());

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
    
    

    public function getRecordWithoutKeysProvider() 
    {
        return array(
            array(
                array("John", "Smith", "19"),
                array("Paul", "Smith2", "36"),
                array("Adam", "Smit3", "14"),
            )
        );
    }

    protected function tearDown() 
    {
        parent::tearDown();

        $files = array('test.xls', 'tmp.xls');
        
        foreach($files as $value){
            if (file_exists($value)){
                unlink($value);
            }

        }

    }

}
