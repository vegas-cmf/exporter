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

use Vegas\Exporter\Adapter\Pdf;
use Vegas\Test\TestCase;

class PdfTest extends TestCase
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
            ->setTemplate('export_sample')
            ->setTitle('Sample PDF export') // optional
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

        $this->config = $this->createExportConfig();

        $this->adapter = new Pdf;
        $this->adapter->setConfig($this->config);
        $this->getDI()->get('exporter')->setConfig($this->config);
    }
    
    public function tearDown()
    {
        $this->adapter = null;
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

    public function testAdapterRequiresViewRenderer()
    {
        $this->adapter->validateOutput();

        $this->getDI()->remove('view');

        try {
            $this->adapter->output();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Mvc\Exception', $e);
        }
    }

    public function testValidCustomExtraSettings()
    {
        $this->config->setExtraSettings([
            'fontSize'          => 10,
            'fontFamily'        => Pdf::FONT_FAMILY_HELVETICA,
            'pageOrientation'   => Pdf::PAGE_ORIENTATION_LANDSCAPE,
            'pageSize'          => Pdf::PAGE_SIZE_A5
        ]);

        $void = $this->adapter->validateOutput();
        $this->assertEmpty($void);
    }

    public function testInvalidCustomFontFamily()
    {
        $this->config->setExtraSettings([
            'fontFamily' => 'Invalid font name',
        ]);

        try {
            $this->adapter->validateOutput();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Adapter\Exception\InvalidFontFamilyException', $e);
        }
    }

    public function testInvalidCustomPageOrientation()
    {
        $this->config->setExtraSettings([
            'pageOrientation' => 'Invalid page orientation',
        ]);

        try {
            $this->adapter->validateOutput();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Adapter\Exception\InvalidPageOrientationException', $e);
        }
    }

    public function testInvalidCustomPageSize()
    {
        $this->config->setExtraSettings([
            'pageSize'   => 'A20',
        ]);

        try {
            $this->adapter->validateOutput();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Adapter\Exception\InvalidPageSizeException', $e);
        }
    }

    public function testMissingTemplateSettings()
    {
        $this->config->setTemplate(null);

        try {
            $this->adapter->validateOutput();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\Vegas\Exporter\Adapter\Exception\TemplateNotSetException', $e);
        }
    }
}
