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

namespace Vegas\Tests\Exporter;

use Vegas\Test\TestCase;

class ExporterTest extends TestCase
{
    /**
     * @var \Vegas\Exporter\Exporter
     */
    private $exporter;

    /**
     * @param string $ext file extension suffix
     * @return string
     */
    private function getTestFilePath($ext)
    {
        return '/tmp/test_export_file' . $ext;
    }

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
            ->setFilename('test_export_file')
            ->setOutputDir('/tmp')
            ->setTitle('Sample file export') // optional
            ->setHeaders($headers)
            ->setData($exportData);
    }

    private function setUpDI()
    {
        $di = $this->getDI();

        $di->set('view', function() use ($di) {
            $view = new \Vegas\Mvc\View($di->get('config')->application->view->toArray());
            $path = $di->get('config')->application->moduleDir . 'Test/views';
            file_exists($path) && $view->setViewsDir($path);
            return $view;
        }, true);

        $di->set('exporter', function() use ($di) {
            $exporter = new \Vegas\Exporter\Exporter;
            return $exporter->setDI($di);
        }, true);
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpDI();

        $this->exporter = $this->getDI()->get('exporter');

        foreach (['.csv', '.pdf', '.xls', '.xml'] as $ext) {
            $filePath = $this->getTestFilePath($ext);
            file_exists($filePath) && unlink($filePath);
        }
    }

    public function tearDown()
    {
        $this->exporter = null;
    }

    protected function assertPreconditions()
    {
        $this->assertFileNotExists($this->getTestFilePath('.csv'));
        $this->assertFileNotExists($this->getTestFilePath('.pdf'));
        $this->assertFileNotExists($this->getTestFilePath('.xls'));
        $this->assertFileNotExists($this->getTestFilePath('.xml'));
    }

    public function testGetSetConfig()
    {
        $config = $this->createExportConfig();

        $this->assertSame(null, $this->exporter->getConfig());

        $this->exporter->setConfig($config);

        $this->assertSame($config, $this->exporter->getConfig());
    }

    public function testNotConfiguredActionAttempts()
    {
        try {
            $this->exporter->printCsv();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Exception\NotConfiguredException', $e);
        }

        try {
            $this->exporter->downloadXls();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Exception\NotConfiguredException', $e);
        }

        try {
            $this->exporter->savePdf();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Exception\NotConfiguredException', $e);
        }
    }

    public function testPrintAndDownloadCsv()
    {
        $config = $this->createExportConfig();

        $this->exporter->setConfig($config);

        $printBuffer = $this->exporter->printCsv();
        ob_start();
        $this->exporter->downloadCsv();
        $downloadBuffer = ob_get_clean();

        $this->assertNotEmpty($printBuffer);
        $this->assertNotEmpty($downloadBuffer);
        $this->assertSame($printBuffer, $downloadBuffer);
    }

    public function testPrintAndDownloadPdf()
    {
        $config = $this->createExportConfig();
        $config->setTemplate('export_sample');

        $this->exporter->setConfig($config);

        $printBuffer = $this->exporter->printPdf();
        ob_start();
        $this->exporter->downloadPdf();
        $downloadBuffer = ob_get_clean();

        $this->assertNotEmpty($printBuffer);
        $this->assertNotEmpty($downloadBuffer);
    }

    public function testPrintAndDownloadXls()
    {
        $config = $this->createExportConfig();

        $this->exporter->setConfig($config);

        $printBuffer = $this->exporter->printXls();
        ob_start();
        $this->exporter->downloadXls();
        $downloadBuffer = ob_get_clean();

        $this->assertNotEmpty($printBuffer);
        $this->assertNotEmpty($downloadBuffer);
    }

    public function testPrintAndDownloadTemplatedXls()
    {
        $config = $this->createExportConfig();
        $config->setTemplate('export_sample');

        $this->exporter->setConfig($config);

        $printBuffer = $this->exporter->printXls();
        ob_start();
        $this->exporter->downloadXls();
        $downloadBuffer = ob_get_clean();

        $this->assertNotEmpty($printBuffer);
        $this->assertNotEmpty($downloadBuffer);
        $this->assertEquals($printBuffer, $downloadBuffer);
    }

    public function testPrintAndDownloadXml()
    {
        $config = $this->createExportConfig();

        $this->exporter->setConfig($config);

        $printBuffer = $this->exporter->printXml();
        ob_start();
        $this->exporter->downloadXml();
        $downloadBuffer = ob_get_clean();

        $this->assertNotEmpty($printBuffer);
        $this->assertNotEmpty($downloadBuffer);
        $this->assertSame($printBuffer, $downloadBuffer);
    }

    public function testPrintAndDownloadInvalidAdapter()
    {
        $config = $this->createExportConfig();

        $this->exporter->setConfig($config);

        try {
            $this->exporter->printNothing();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Exception\InvalidExporterException', $e);
        }
        try {
            $this->exporter->downloadNothing();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Exception\InvalidExporterException', $e);
        }
    }

    public function testAvailableSaveToFileExports()
    {
        $config = $this->createExportConfig();

        $this->exporter->setConfig($config);

        $this->exporter->saveCsv();
        $this->assertFileExists($this->getTestFilePath('.csv'));

        $config->setTemplate('export_sample');
        $this->exporter->saveXls();
        $this->assertFileExists($this->getTestFilePath('.xls'));

        $this->exporter->saveXml();
        $this->assertFileExists($this->getTestFilePath('.xml'));

        $config->setTemplate('export_sample');
        $this->exporter->savePdf();
        $this->assertFileExists($this->getTestFilePath('.pdf'));

        try {
            $this->exporter->saveNothing();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Exception\InvalidExporterException', $e);
        }
    }

    public function testInvalidProxyMethodException()
    {
        try {
            $this->exporter->doSomethingWithPdf();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Exception\InvalidMethodException', $e);
        }
    }
}
