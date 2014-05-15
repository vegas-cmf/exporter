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

use Vegas\Exporter\Adapter\Xls as Xls;


class XlsTest extends \PHPUnit_Framework_TestCase
{
    private $obj;
    
    /**
     * @dataProvider saveWithHeaderDataProvider
     * @param string $exportData
     */
    public function testSaveWithHeader($exportData)
    {
        $filename = 'test.xls';
        $headers = array('name', 'lastname', 'age');
        $this->obj->setHeaders($headers);
        $this->obj->setOutputPath("/tmp/");
        $this->obj->init($exportData, false);
        $this->obj->export($filename);

        $reader = new \PHPExcel_Reader_Excel2007();
        
        $this->assertInstanceOf('PHPExcel', $this->obj->getContent());
        $this->assertInstanceOf('PHPExcel', $reader->load("/tmp/" . $filename));

    }
    
    public function saveWithHeaderDataProvider() 
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


    /**
     * @dataProvider saveWithoutHeaderDataProvider
     * @param string $exportData
     */
    public function testSaveWithoutHeader($exportData)
    {
        $filename = 'test.xls';
        $this->obj->setOutputPath("/tmp/");
        $this->obj->init($exportData, false);
        $this->obj->export($filename);

        $reader = new \PHPExcel_Reader_Excel2007();
        
        $this->assertInstanceOf('PHPExcel', $this->obj->getContent());
        $this->assertInstanceOf('PHPExcel', $reader->load("/tmp/" . $filename));

    }

    public function testXlsNoDataGiven() 
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\DataNotFoundException');
        $this->obj->init(array(), false);        

    }

    /**
     * @dataProvider saveWithHeaderDataProvider
     * @param string $exportData
     */
    public function testInvalidKeys($exportData)
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\InvalidKeysException');
        $this->obj->init($exportData, true);        

    }
    
    public function saveWithoutHeaderDataProvider()
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

    protected function setUp()
    {
        date_default_timezone_set('Europe/Warsaw');
        $this->obj = new Xls();

    }
    
    protected function tearDown()
    {
        $files = array('test.xls', 'tmp.xls');
        
        foreach($files as $value){
            if (file_exists($value)){
                unlink($value);
            }

        }

    }


}
