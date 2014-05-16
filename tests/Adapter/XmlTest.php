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
        $this->obj = new Xml();
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
        $data = array(array("John", "Smith", "19"));
        $filename = 'test.xml';
        
        $this->obj->init($data);
        $this->obj->setHeaders(array('name', 'lastname', 'age'));
        $this->obj->setOutputPath("/tmp/");
        $this->obj->export($filename);

        $this->assertSame(file_get_contents('/tmp/' . $filename), file_get_contents('/tmp/test.xml'));
    }
    
    /**
     * @dataProvider xmlCreationWithoutHeadersProvider
     * @param string $exportData
     */
    public function testXmlCreationWithoutHeaders($exportData)
    {
        $filename = 'test.xml';
        $this->obj->init($exportData, true);
        $this->obj->setOutputPath("/tmp/");
        $this->obj->export($filename);

        $this->assertSame(file_get_contents("/tmp/" . $filename), $this->primitiveXmlCreate($exportData));

    } 
    
    public function xmlCreationWithoutHeadersProvider()
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

    } 
    
    public function testXmlInitObjSet()
    {
        
        $this->obj->init(array(array("John", "Smith", "19")));        
        
        $class = new \ReflectionClass("Vegas\Exporter\Adapter\Xml");
        $property = $class->getProperty("xml");
        $property->setAccessible(true);
        
        $exportData = array(array("John", "Smith", "19"));

        $temp = new Xml();
        $temp->init($exportData);
        $this->assertInstanceOf('SimpleXMLElement', $property->getValue($temp));

    } 

    /**
     * @dataProvider xmlCreationWithHeadersProvider
     * @param string $exportData
     */
    public function testXmlCreationWithHeaders($exportData)
    {
        $filename = 'test.xml';
        $this->obj->setHeaders(array('name', 'lastname', 'age'));  
        $this->obj->init($exportData, false);      
        $this->obj->setOutputPath("/tmp/");
        $this->obj->export($filename);

        $this->assertSame(file_get_contents("/tmp/" . $filename), $this->primitiveXmlCreate($exportData, array('name', 'lastname', 'age')));

    }
       
    public function xmlCreationWithHeadersProvider()
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

    private function primitiveXmlCreate($exportData, $keys = null)
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
