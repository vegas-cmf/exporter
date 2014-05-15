Vegas CMF Exporter
======================

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