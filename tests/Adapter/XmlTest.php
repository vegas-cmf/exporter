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

use Vegas\Exporter\Adapter\Xml as Xml;

class XmlTest extends \PHPUnit_Framework_TestCase {

    private $obj;

    protected function setUp()
    {
        $xml = new Xml();
        $this->obj = new \Vegas\Exporter\Exporter($xml);
    }

    protected function tearDown()
    {
        $this->obj = null;

        if (file_exists('test.xml')){
            unlink('test.xml');
        }
    }
    
    /**
     * @param string $name
     * @param string $lastname
     * @param string $age
     */
    public function testXmlExport()
    {
        $exportData = array(array("John", "Smith", "19"));
        $fileName = 'test.xml';
        $outputPath = '/tmp/';
        
        $this->obj->setHeaders(array('name', 'lastname', 'age'));
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        $this->obj->init($exportData);
        $this->obj->run();

        $this->assertSame(file_get_contents($outputPath . $fileName), $this->xmlCreate($exportData, array('name', 'lastname', 'age')));
    }
    
    /**
     * @dataProvider xmlExportWithoutHeadersProvider
     * @param string $exportData
     */
    public function testXmlExportWithoutHeaders($exportData)
    {
        $fileName = 'test.xml';
        $outputPath = '/tmp/';
        $this->obj->init($exportData, true);
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        $this->obj->run($fileName);

        $this->assertSame(file_get_contents($outputPath . $fileName), $this->xmlCreate($exportData));

    } 
    
    public function xmlExportWithoutHeadersProvider()
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
    
    public function testXmlInitWithoutData()
    {
        $this->setExpectedException('Vegas\Exporter\Adapter\Exception\DataNotFoundException');
        $this->obj->init(array());
        $this->obj->init(array(array()));
        $this->obj->init(array(null));
    } 

    
    public function testXmlHtmlTagsDataGiven()
    {
        $this->setExpectedException('Vegas\Exporter\Adapter\Exception\HtmlTagsFoundException');
        
        $exportData = array(array("<a>John</a>", "Smith", "19"));
        $fileName = 'test.xml';
        $outputPath = '/tmp/';
        
        $this->obj->setHeaders(array('name', 'lastname', 'age'));
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        $this->obj->init($exportData);
    } 
    
    public function testXmlHtmlTagsHeaderGiven()
    {
        $this->setExpectedException('Vegas\Exporter\Adapter\Exception\HtmlTagsFoundException');
        
        $exportData = array(array("John", "Smith", "19"));
        $fileName = 'test.xml';
        $outputPath = '/tmp/';
        
        $this->obj->setHeaders(array('<b>name</b>', 'lastname', 'age'));
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        $this->obj->init($exportData);
    } 
    
    public function testXmlInvalidDataGiven()
    {
        $this->setExpectedException('Vegas\Exporter\Adapter\Exception\InvalidArgumentTypeException');
        
        $exportData = array(array(array(), "Smith", "19"));
        $fileName = 'test.xml';
        $outputPath = '/tmp/';
        
        $this->obj->setHeaders(array('name', 'lastname', 'age'));
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        $this->obj->init($exportData);
    } 
    
    /**
     * @dataProvider xmlExportWithHeadersProvider
     * @param string $exportData
     */
    public function testXmlExportWithHeaders($exportData)
    {
        $fileName = 'test.xml';
        $outputPath = '/tmp/';
        $this->obj->setHeaders(array('name', 'lastname', 'age'));  
        $this->obj->setOutputPath($outputPath);
        $this->obj->setFileName($fileName);
        $this->obj->init($exportData, false);    
        $this->obj->run($fileName);

        $this->assertSame(file_get_contents($outputPath . $fileName), $this->xmlCreate($exportData, array('name', 'lastname', 'age')));

    }
       
    public function xmlExportWithHeadersProvider()
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

    private function xmlCreate($exportData, $keys = null)
    {
        $this->obj = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<root>";

        foreach ($exportData as $items) {
            $this->obj .='<item>';
            foreach ($items as $key => $value) {
                
                if(!is_array($keys)){
                    $this->obj .= '<' . $key . '>' . $value . '</' . $key . '>';
                } else {
                    $this->obj .= '<' . $keys[$key] . '>' . $value . '</' . $keys[$key] . '>';
                }
            }
            $this->obj .='</item>';
        }

        $this->obj .= "</root>\n";
        return $this->obj;
    }
}
