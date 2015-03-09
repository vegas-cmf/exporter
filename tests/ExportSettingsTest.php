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

use Vegas\Exporter\ExportSettings;
use Vegas\Test\TestCase;

class FakeExportSettings extends ExportSettings
{
    public $notMassParam = 'whatever';
    protected $hiddenField = 'not empty';
    private $privateField = 'nothing';

    public function getPrivateField()
    {
        return $this->privateField;
    }
}

class ExportSettingsTest extends TestCase
{
    public function testMagicSetterOnlyForExplicitSetters()
    {
        $config = new FakeExportSettings;

        $this->assertSame('nothing', $config->getPrivateField());
        $this->assertNull($config->getTitle());

        $config->privateField = 'test';
        $config->title = 'test';

        $this->assertSame('nothing', $config->getPrivateField());
        $this->assertSame('test', $config->getTitle());
    }

    public function testMagicGetterOnlyForExplicitGetters()
    {
        $config = new FakeExportSettings;

        $this->assertNull($config->hiddenField);
        $this->assertFalse(is_callable([$config, 'getHiddenField']));

        $this->assertSame('nothing', $config->privateField);
        $this->assertTrue(is_callable([$config, 'getPrivateField']));
    }

    public function testMassAssignment()
    {
        $config = new ExportSettings;

        $headers = ['foo', 'bar'];
        $data = [
            ['bar' => 1, 'foo' => '123']
        ];
        $templateName = 'path/to/template';

        $this->assertNull($config->getHeaders());
        $this->assertNull($config->getData());
        $this->assertNull($config->getTemplate());

        $config->bind([
            'headers'   => $headers,
            'data'      => $data,
            'template'  => $templateName,
        ]);

        $this->assertSame($headers, $config->getHeaders());
        $this->assertSame($data, $config->getData());
        $this->assertSame($templateName, $config->getTemplate());
    }

    public function testMassAssignmentIsOnlyForProtectedFieldsWithExplicitSetters()
    {
        $config = new FakeExportSettings;

        $this->assertSame('whatever', $config->notMassParam);
        $this->assertSame('nothing', $config->getPrivateField());
        $this->assertNull($config->getTitle());

        $config->bind([
            'notMassParam'  => 'foo',
            'privateField'  => 'bar',
            'title'         => 'new value'
        ]);

        $this->assertSame('whatever', $config->notMassParam);
        $this->assertSame('nothing', $config->getPrivateField());
        $this->assertSame('new value', $config->getTitle());
    }

    public function testGetSetData()
    {
        $config = new ExportSettings;

        $this->assertNull($config->getData());

        $config->setData(['foo' => 6, 'bar' => '321']);

        $this->assertSame(['foo' => 6, 'bar' => '321'], $config->getData());
    }

    public function testGetSetHeaders()
    {
        $config = new ExportSettings;

        $this->assertNull($config->getHeaders());

        $config->setHeaders(['foo', 'bar']);

        $this->assertSame(['foo', 'bar'], $config->getHeaders());
    }

    public function testGetSetHeaderParams()
    {
        $config = new ExportSettings;
        $this->assertFalse($config->isAssociativeHeaders());

        $this->assertNull($config->getHeaders());

        $config->setHeaders(['foo', 'bar']);

        $this->assertSame(['foo', 'bar'], $config->getHeaderParams());
        $this->assertSame($config->getHeaders(), $config->getHeaderParams());

        $config->setAssociativeHeaders(true);
        $this->assertTrue($config->isAssociativeHeaders());

        $this->assertSame([0, 1], $config->getHeaderParams());
        $this->assertSame(['foo', 'bar'], $config->getHeaders());


        $config->setHeaders(['this' => 'is', 'associative' => 'array']);
        $this->assertTrue($config->isAssociativeHeaders());

        $this->assertSame(['this', 'associative'], $config->getHeaderParams());
        $this->assertSame(['this' => 'is', 'associative' => 'array'], $config->getHeaders());
    }

    public function testGetSetTitle()
    {
        $config = new ExportSettings;

        $this->assertNull($config->getTitle());

        $config->setTitle('sample title');

        $this->assertSame('sample title', $config->getTitle());
    }

    public function testGetSetOutputDirWithDefaultValue()
    {
        $config = new ExportSettings;

        $newOutputDir = '/path/to/directory';

        $this->assertNotNull($config->getOutputDir());
        $this->assertNotSame($newOutputDir, $config->getOutputDir());

        $config->setOutputDir($newOutputDir);

        $this->assertSame($newOutputDir, $config->getOutputDir());
    }

    public function testGetSetFilenameWithDefaultValue()
    {
        $config = new ExportSettings;

        $newFilename = 'new_output_filename';

        $this->assertNotNull($config->getFilename());
        $this->assertNotSame($newFilename, $config->getFilename());

        $config->setFilename($newFilename);

        $this->assertSame($newFilename, $config->getFilename());
    }
}
