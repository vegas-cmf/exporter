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

use Vegas\Exporter\Xml as Xml;

class XmlTest extends \PHPUnit_Framework_TestCase {

    protected function setUp() 
    {
        
    }

    /**
     * @dataProvider getRecordProvider
     * @param string $name
     * @param string $lastname
     * @param string $age
     */
    public function testEncodingXml($name, $lastname, $age) 
    {

        $xml = new Xml();
        $xml->init(array($name, $lastname, $age));
        $xml->exportData('test.xml');

        $this->assertSame(file_get_contents('test.xml'), $xml->getContent());
    }

    /**
     * @dataProvider getRecordProvider
     * @param string $export_data
     */
    public function testXmlCreateWithKeysHeaders($export_data) 
    {
        $filename = 'test.xml';
        $xml = new Xml();
        $xml->init(array($export_data));
        $xml->exportData($filename);

        $this->assertSame($this->primitiveXmlCreate(array($export_data)), file_get_contents($filename));

    }

    /**
     * @dataProvider getRecordWithoutkeysProvider
     * @param string $export_data
     */
    public function testXmlCreateWithoutKeysHeaders($export_data) 
    {
        $filename = 'test.xml';
        $xml = new Xml();
        $xml->init(array($export_data), false);
        $xml->setHeaders(array('name', 'lastname', 'age'));
        $xml->exportData($filename);

        $this->assertSame($this->primitiveXmlCreate(array($export_data), array('name', 'lastname', 'age')), file_get_contents($filename));

    }

    /**
     * @dataProvider getRecordProvider
     * @param string $export_data
     */
    public function testXmlCreateEmptyHeader($export_data) 
    {
        $xml = new Xml();
        $xml->init(array($export_data), false);
        $this->assertFalse($xml->keysAsHeaders);

    }

    
    private function primitiveXmlCreate($export_data, $keys = null) 
    {
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<root>";

        foreach ($export_data as $items) {
            $xml .='<item>';
            foreach ($items as $key => $value) {
                if(is_null($keys)){
                    $xml .= '<' . $key . '>' . $value . '</' . $key . '>';
                } else {
                    $xml .= '<' . $keys[$key] . '>' . $value . '</' . $keys[$key] . '>';
                }
            }
            $xml .='</item>';
        }

        $xml .= "</root>\n";
        return $xml;
    }
    
    

    public function getRecordProvider() 
    {
        return array(
            array(
                array('name' => "John", 'lastname' => "Smith", 'age' => "19"),
                array('name' => "Paul", 'lastname' => "Smith2", 'age' => "36"),
                array('name' => "Adam", 'lastname' => "Smit3", 'age' => "14"),
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

        if (file_exists('test.xml')){
            unlink('test.xml');
        }
    }

}
