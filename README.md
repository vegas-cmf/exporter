Vegas CMF Exporter
======================

[![Build Status](https://travis-ci.org/vegas-cmf/exporter.png?branch=master)](https://travis-ci.org/vegas-cmf/exporter)
[![Coverage Status](https://coveralls.io/repos/vegas-cmf/exporter/badge.png?branch=master)](https://coveralls.io/r/vegas-cmf/exporter?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/vegas-cmf/exporter.svg)](https://packagist.org/packages/vegas-cmf/exporter)
[![Total Downloads](https://img.shields.io/packagist/dt/vegas-cmf/exporter.svg)](https://packagist.org/packages/vegas-cmf/exporter)

Exporter works in two modes: saving file onto server and downloading. By default, the first one was set. If there is a need to export you have to set output path of file. 

**Saving usage:**
```
#!php

$export_data = array(
    array("John", "Malkovic", "52"),
    array("Kenny", "Smith", "36"),
    array("Sam", "Stevenson", "18"),
);

$columns = array("Firstname", "Lastname", "age");

$exporter = new \Vegas\Exporter\Xls();
$exporter->init($export_data);
$exporter->setHeaders($columns);
$exporter->setOutputPath("public/export/");
$exporter->exportData();

```


**Downloading usage:**
```
#!php

$export_data = array(
    array("John", "Malkovic", "52"),
    array("Kenny", "Smith", "36"),
    array("Sam", "Stevenson", "18"),
);

$columns = array("Firstname", "Lastname", "age");

$exporter = new \Vegas\Exporter\Xls();
$exporter->init($export_data);
$exporter->setHeaders($columns);
$exporter->exportData();

```