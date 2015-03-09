<?php

namespace Vegas\Tests\Exporter\Adapter;

use Vegas\Exporter\Adapter\Pdf;
use Vegas\Mvc\View;
use Vegas\Test\TestCase;

class PdfTest extends TestCase
{
    /**
     * @var \Vegas\Exporter\Exporter
     */
    private $exporter;

    /**
     * @var string
     */
    private $testFile = 'test';

    /**
     * @return string
     */
    private function getTestPath()
    {
        return sys_get_temp_dir();
    }

    /**
     * @return string
     */
    private function getTestFilePath()
    {
        return $this->getTestPath() . DIRECTORY_SEPARATOR . $this->testFile . (new \Vegas\Exporter\Adapter\Pdf)->getExtension();
    }

    /**
     * Enables view service in DI container
     */
    private function setUpView()
    {
        $di = $this->getDI();
        $di->set('view', function() use ($di) {
            $view = new View($di->get('config')->application->view->toArray());
            $path = $di->get('config')->application->moduleDir . 'Test/views';
            file_exists($path) && $view->setViewsDir($path);
            return $view;
        });
    }

    /**
     * @return \Vegas\Exporter\ExportSettings
     */
    private function createExportConfig()
    {
        $headers = ['foo', 'bar'];

        $exportData = [
            [1, 2],
            [11, 22],
            [111, 222],
            ['zażółć gęślą', 'jaźń']
        ];

        return (new \Vegas\Exporter\ExportSettings)
            ->setTemplate('export_sample')
            ->setFilename($this->testFile)
            ->setOutputDir($this->getTestPath())
            ->setTitle('Sample PDF export') // optional
            ->setHeaders($headers)
            ->setData($exportData);
    }
    
    public function setUp()
    {
        parent::setUp();
        $this->setUpView();

        $filePath = $this->getTestFilePath();
        file_exists($filePath) && unlink($filePath);

        $this->exporter = new \Vegas\Exporter\Exporter;
        $this->exporter->setDI($this->getDI());
    }
    
    public function tearDown()
    {
        $this->exporter = null;
    }
    
    public function testSaveFile()
    {
        $outputFilePath = $this->getTestFilePath();
        $config = $this->createExportConfig();

        $this->exporter->setConfig($config);

        $this->assertFileNotExists($outputFilePath);

        $this->exporter->savePdf();
        
        $this->assertFileExists($outputFilePath);
    }

    public function testDownloadAndPrintFile()
    {
        $outputFilePath = $this->getTestFilePath();
        $config = $this->createExportConfig();

        $this->exporter->setConfig($config);

        $this->assertFileNotExists($outputFilePath);

        ob_start();
        $this->exporter->downloadPdf();
        $downloadBuffer = ob_get_clean();

        $this->assertNotEmpty($downloadBuffer);
        $this->assertFileNotExists($outputFilePath);

        $printBuffer = $this->exporter->printPdf();

        $this->assertNotEmpty($printBuffer);

        $this->assertEquals($printBuffer, $downloadBuffer);

        $this->assertFileNotExists($outputFilePath);
    }

    public function testPrintFile()
    {
        $outputFilePath = $this->getTestFilePath();
        $config = $this->createExportConfig();

        $this->exporter->setConfig($config);

        $this->assertFileNotExists($outputFilePath);

        $downloadBuffer = $this->exporter->printPdf();

        $this->assertNotEmpty($downloadBuffer);
        $this->assertFileNotExists($outputFilePath);
    }

    public function testValidCustomExtraSettings()
    {
        $outputFilePath = $this->getTestFilePath();
        $config = $this->createExportConfig();

        $this->exporter->setConfig($config);

        $config->setExtraSettings([
            'fontFamily' => 'Non existing font'
        ]);

        $this->assertFileNotExists($outputFilePath);

        $config->setExtraSettings([
            'fontSize'          => 10,
            'fontFamily'        => Pdf::FONT_FAMILY_HELVETICA,
            'pageOrientation'   => Pdf::PAGE_ORIENTATION_LANDSCAPE,
            'pageSize'          => Pdf::PAGE_SIZE_A5
        ]);

        $this->exporter->savePdf();

        $this->assertFileExists($outputFilePath);
    }

    public function testInvalidCustomExtraSettings()
    {
        $outputFilePath = $this->getTestFilePath();
        $config = $this->createExportConfig();

        $this->exporter->setConfig($config);

        $config->setExtraSettings([
            'fontFamily'        => 'Invalid font name',
        ]);

        $this->assertFileNotExists($outputFilePath);

        try {
            $this->exporter->savePdf();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Adapter\Exception\InvalidFontFamilyException', $e);
        }

        $this->assertFileNotExists($outputFilePath);

        $config->setExtraSettings([
            'pageOrientation'   => 'Invalid page orientation',
        ]);

        try {
            $this->exporter->savePdf();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Adapter\Exception\InvalidPageOrientationException', $e);
        }

        $config->setExtraSettings([
            'pageSize'   => 'A20',
        ]);

        try {
            $this->exporter->savePdf();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Adapter\Exception\InvalidPageSizeException', $e);
        }
    }
}
