<?php
/**
 * This file is part of Vegas Exporter package.
 *
 * @author Radosław Fąfara <radek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://github.com/vegas-cmf/exporter
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Tests\Exporter\Adapter;

use Vegas\Exporter\Adapter\Xml;
use Vegas\Test\TestCase;

class XmlTest extends TestCase
{
    /**
     * @var \Vegas\Exporter\ExportSettings
     */
    private $config;

    /**
     * @var \Vegas\Exporter\Adapter\AdapterInterface
     */
    private $adapter;

    /**
     * @return \Vegas\Exporter\ExportSettings
     */
    private function createExportConfig()
    {
        $headers = ['foo', 'bar'];

        $exportData = [
            ['foo' => 'zażółć gęślą', 'bar' => 'jaźń']
        ];

        return (new \Vegas\Exporter\ExportSettings)
            ->setHeaders($headers)
            ->setData($exportData);
    }

    public function setUp()
    {
        parent::setUp();

        $this->config = $this->createExportConfig();

        $this->adapter = new Xml;
        $this->adapter->setConfig($this->config);
    }

    public function tearDown()
    {
        $this->exporter = null;
        $this->config = null;
    }

    public function testOutputGivesNoSideEffects()
    {
        $this->adapter->validateOutput();

        ob_start();
        $buffer = $this->adapter->output();
        $sideEffectsBuffer = ob_get_clean();

        $this->assertNotEmpty($buffer);
        $this->assertEmpty($sideEffectsBuffer);
    }

    public function testOutput()
    {
        $buffer = $this->adapter->output();

        $prettyPrintXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root>
  <item>
    <foo>zażółć gęślą</foo>
    <bar>jaźń</bar>
  </item>
</root>
XML;

        $this->assertSame($prettyPrintXml, rtrim($buffer, PHP_EOL));
    }

    public function testOutputHeadersWithoutValue()
    {
        $this->config->setHeaders([
            'foo', 'bar', 'no_value'
        ]);
        $buffer = $this->adapter->output();

        $prettyPrintXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root>
  <item>
    <foo>zażółć gęślą</foo>
    <bar>jaźń</bar>
    <no_value></no_value>
  </item>
</root>
XML;

        $this->assertSame($prettyPrintXml, rtrim($buffer, PHP_EOL));
    }

    public function testUseObjectDataForOutput()
    {
        $object = new \stdClass;
        $object->bar = 'zażółć gęślą';
        $object->foo = 'jaźń';

        $this->config->setHeaders(['foo', 'bar', 'empty']);
        $this->config->setData([$object]);

        $buffer = $this->adapter->output();

        $prettyPrintXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root>
  <item>
    <foo>jaźń</foo>
    <bar>zażółć gęślą</bar>
    <empty></empty>
  </item>
</root>
XML;

        $this->assertSame($prettyPrintXml, rtrim($buffer, PHP_EOL));
    }
}
