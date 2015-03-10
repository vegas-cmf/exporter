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

use Vegas\Exporter\Adapter\Xls;
use Vegas\Test\TestCase;

class XlsTest extends TestCase
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
            ['foo' => 1, 'bar' => 2],
            ['bar' => 11, 'foo' => 22],
            ['foo' => 111, 'bar' => 222],
            ['foo' => 'zażółć gęślą', 'bar' => 'jaźń']
        ];

        return (new \Vegas\Exporter\ExportSettings)
            ->setTitle('Sample XLS export') // optional
            ->setHeaders($headers)
            ->setData($exportData);
    }

    public function setUp()
    {
        parent::setUp();

        $this->config = $this->createExportConfig();

        $this->adapter = new Xls;
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
}
