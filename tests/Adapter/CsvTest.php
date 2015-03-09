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

use Vegas\Exporter\Adapter\Csv;
use Vegas\Test\TestCase;

class CsvTest extends TestCase
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

        $this->adapter = new Csv;
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

    public function testDefaultOutput()
    {
        $buffer = $this->adapter->output();

        $sampleCsv = <<<CSV
foo,bar
zażółć gęślą,jaźń
CSV;

        $this->assertSame($sampleCsv, $buffer);
    }

    public function testQuotedOutput()
    {
        $this->config->setExtraSettings([
            'quoteFields' => true
        ]);

        $buffer = $this->adapter->output();

        $sampleCsv = <<<CSV
"foo","bar"
"zażółć gęślą","jaźń"
CSV;

        $this->assertSame($sampleCsv, $buffer);
    }

    public function testOutputWithCustomFieldSeparator()
    {
        $this->config->setExtraSettings([
            'separator' => ';'
        ]);

        $buffer = $this->adapter->output();

        $sampleCsv = <<<CSV
foo;bar
zażółć gęślą;jaźń
CSV;

        $this->assertSame($sampleCsv, $buffer);
    }

    public function testOutputWithCustomLineSeparator()
    {
        $this->config->setExtraSettings([
            'lineSeparator' => ';'
        ]);

        $buffer = $this->adapter->output();

        $sampleCsv = 'foo,bar;zażółć gęślą,jaźń';

        $this->assertSame($sampleCsv, $buffer);
    }

    public function testOutputWithoutHeaders()
    {
        $this->config->setExtraSettings([
            'skipHeaders' => true
        ]);

        $buffer = $this->adapter->output();

        $sampleCsv = 'zażółć gęślą,jaźń';

        $this->assertSame($sampleCsv, $buffer);
    }
}
