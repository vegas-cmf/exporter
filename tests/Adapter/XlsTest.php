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
    /**
     * @var Xls
     */
    private $obj;
    
    protected function setUp()
    {
        date_default_timezone_set('Europe/Warsaw');
        
        $xls = new Xls();
        $this->obj = new \Vegas\Exporter\Exporter($xls);
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
    
    public function testSaveWithHeader()
    {
        $fileName = 'test.xls';
        $outputPath = '/tmp/';
        $headers = array('name', 'lastname', 'age');
        
        $exportData = array(
            array("John", "Smith", "19"),
            array("Sam", "Wozniacki", "36"),
            array("Adam", "Ferrero", "14"),
        );
        
        $this->obj->setHeaders($headers);
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        $this->obj->init($exportData);
        $this->obj->run();

        $this->assertFileExists($outputPath . $fileName);
    }

    /**
     * @param string $exportData
     */
    public function testSaveWithoutHeader()
    {
        $exportData = array(
            array('name' => "John", 'lastname' => "Smith", 'age' => "19"),
            array('name' => "Paul", 'lastname' => "Smith2", 'age' => "36"),
            array('name' => "Adam", 'lastname' => "Smit3", 'age' => "14"),
        );
        
        $fileName = 'test.xls';
        $outputPath = '/tmp/';
        
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        
        $this->obj->init($exportData, true);
        $this->obj->run();

        $this->assertFileExists($outputPath . $fileName);
    }

    public function testXlsNoDataGiven() 
    {
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\DataNotFoundException');
        $this->obj->init(array());
    }

    public function testXlsNoHeadersGiven()
    {
        $exportData = array(
            array(
                array("John", "Smith", "19"),
                array("Paul", "Smith2", "36"),
                array("Adam", "Smit3", "14"),
            )
        );
                
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\InvalidHeadersDataException');
        $this->obj->init($exportData, true);
    }

    public function testXlsInvalidDataGiven()
    {
        $exportData = array(
            array(
                array(array(), "Smith", "19"),
                array("Paul", "Smith2", "36"),
                array("Adam", "Smit3", "14"),
            )
        );
                
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\InvalidArgumentTypeException');
        $this->obj->init($exportData);
    }

    public function testXlsNullDataGiven()
    {
        $exportData = array(
            array(
                array("John", "Smith", "19"),
                array(null, "Smith2", "36"),
                array("Adam", "Smit3", "14"),
            )
        );
                
        $this->setExpectedException('\Vegas\Exporter\Adapter\Exception\InvalidArgumentTypeException');
        $this->obj->init($exportData);
    }
}
