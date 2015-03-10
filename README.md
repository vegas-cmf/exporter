Vegas CMF Exporter
======================

[![Build Status](https://travis-ci.org/vegas-cmf/exporter.png?branch=master)](https://travis-ci.org/vegas-cmf/exporter)
[![Coverage Status](https://coveralls.io/repos/vegas-cmf/exporter/badge.png?branch=master)](https://coveralls.io/r/vegas-cmf/exporter?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/vegas-cmf/exporter.svg)](https://packagist.org/packages/vegas-cmf/exporter)
[![Total Downloads](https://img.shields.io/packagist/dt/vegas-cmf/exporter.svg)](https://packagist.org/packages/vegas-cmf/exporter)

Exporter allows user to get table output in one of following ways:
- store into file
- download file in a browser
- raw string buffer

Currently, the library supports following formats:
- CSV
- PDF
- XLS (Excel 2007)
- XML

**Installation**

Set `\Vegas\Exporter\Exporter` class as a service by adding following snippet into your `services` directory:
```php
use Phalcon\DiInterface;
use Vegas\DI\ServiceProviderInterface;

/**
 * Class ExporterServiceProvider
 */
class ExporterServiceProvider implements ServiceProviderInterface
{
    const SERVICE_NAME = 'exporter';

    /**
     * {@inheritdoc}
     */
    public function register(DiInterface $di)
    {
        $di->set(self::SERVICE_NAME, function() use ($di) {
            $exporter = new \Vegas\Exporter\Exporter;
            return $exporter->setDI($di);
        }, true);
    }

    public function getDependencies()
    {
        return ['view'];
    }
}
```

**Saving usage:**
```php
$exportData = [
    ["John", "Malkovic", "52"],
    ["Kenny", "Smith", "36"],
    ["Sam", "Stevenson", "18"],
];

$columns = ["Firstname", "Lastname", "age"];

$extraSettings = [  // CSV-only settings, default values below
    'separator'     => ',',
    'lineSeparator' => PHP_EOL,
    'skipHeaders'   => false,   // skip printing headers in first row?
    'quoteFields'   => false    // enclose output fields in ""
];

$config = (new \Vegas\Exporter\ExportSettings)
            ->setFilename('my_export_file')
            ->setOutputDir('/tmp')
            ->setHeaders($columns)
            ->setHeaderKeysAsParams(true)
            ->setData($exportData)
            ->setExtraSettings($extraSettings);
            
/** @var \Phalcon\DiInterface $di */
$exporter = $di->get('exporter');

$exporter->setConfig($config);
$exporter->saveCsv();
```
This will store our data in CSV format into file `/tmp/my_export_file.csv`. 

**Downloading usage:**
```php
$exportData = [
    ["John", "Malkovic", "52"],
    ["Kenny", "Smith", "36"],
    ["Sam", "Stevenson", "18"],
];

$columns = ["Firstname", "Lastname", "age"];

$extraSettings = [  // PDF-only settings, default values below
    'pageOrientation'       => 'Portrait',
    'pageSize'              => 'A4',
    'fontSize'              => 0,
    'fontFamily'            => ''
];

$config = (new \Vegas\Exporter\ExportSettings)
            ->setTemplate('template_name')
            ->setTitle('My first PDF export')
            ->setFilename('my_export_file')
            ->setHeaders($columns)
            ->setData($exportData)
            ->setExtraSettings($extraSettings);
            
/** @var \Phalcon\DiInterface $di */
$exporter = $di->get('exporter');

$di->get('view')->disable();    // prevent default view rendering
$exporter->setConfig($config);
$exporter->downloadPdf();
```
This will download a PDF file named `my_export_file.pdf` in a browser.

Note that PDF format requires a `template_name` partial in the modules view directory to render output properly.

**Printing usage:**
```php
$john = new \stdClass;
$john->Firstname = 'John';
$john->Lastname = 'Malkovic';
$john->age = 52;

$exportData = [
    $john,
    // ...
];

$columns = ["Firstname", "Lastname", "age"];

$extraSettings = [  // XML-only settings, default values below
    'rootName'  => 'root',  // document tree tag name
    'nodeName'  => 'item'   // each node tag name
];

$config = (new \Vegas\Exporter\ExportSettings)
            ->setHeaders($columns)
            ->setHeaderKeysAsParams(false)
            ->setData($exportData)
            ->setExtraSettings($extraSettings);
            
/** @var \Phalcon\DiInterface $di */
$exporter = $di->get('exporter');
            
$exporter->setConfig($config);
$result = $exporter->printXml();
```
This will assign pretty printed XML string to `$result`. As presented, exporter accepts object array input as well.