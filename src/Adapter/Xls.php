<?php
/**
 * This file is part of Vegas Exporter package.
 *
 * @author Radosław Fąfara <radek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://github.com/vegas-cmf/exporter
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. *
 */

namespace Vegas\Exporter\Adapter;

/**
 * Class Xls
 * @package Vegas\Exporter\Adapter
 */
class Xls extends AdapterAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return 'application/vnd.ms-excel';
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return '.xls';
    }

    /**
     * {@inheritdoc}
     */
    public function output()
    {
        $extraSettings = $this->config->getExtraSettings();

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $root = isset($extraSettings['rootName']) ? $extraSettings['rootName'] : self::DEFAULT_ROOT_NAME;
        $node = isset($extraSettings['nodeName']) ? $extraSettings['nodeName'] : self::DEFAULT_NODE_NAME;
        $documentTree = $dom->createElement($root);

        $data = $this->config->getData();
        //        FIXME unify behavior
        //        if (empty($data)) {
        //            throw new Exception\DataNotFoundException();
        //        }

        $xls = new \PHPExcel;
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();

        $row = 1;
        $column = 'A';
        foreach ($this->config->getHeaders() as $header) {
            $sheet->setCellValue($column . $row, $header);
            ++$column;
        }
        ++$row;

        foreach ($data as $item) {
            $column = 'A';
            foreach ($this->getRawItem($item) as $value) {
                $sheet->setCellValue($column . $row, $value);
                ++$column;
            }
            ++$row;
        }

        return $this->getBuffer($xls);
    }

    /**
     * Dumps XLS file to memory & retrieves content
     * @param \PHPExcel $xls
     * @return string
     */
    private function getBuffer(\PHPExcel $xls)
    {
        ob_start();
        $writer = new \PHPExcel_Writer_Excel2007($xls);
        $writer->save('php://output');
        return ob_get_clean();
    }
}