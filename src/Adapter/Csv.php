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
 * Class Csv
 *
 * Available extra settings to be provided:
 * - separator: field separator, defaults to ','
 * - lineSeparator: output line separator, defaults to PHP_EOL
 * - quoteFields: whether to quote each field in "", defaults to false
 * - skipHeaders: whether to skip printing headers, defaults to false
 *
 * @package Vegas\Exporter\Adapter
 */
class Csv extends AdapterAbstract
{
    const DEFAULT_SEPARATOR = ',';

    /**
     * Whether to quote fields in ""
     * @var bool
     */
    protected $quoteFields;

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return 'text/csv';
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return '.csv';
    }

    /**
     * {@inheritdoc}
     */
    public function output()
    {
        $extraSettings = $this->config->getExtraSettings();

        $separator = isset($extraSettings['separator']) ? $extraSettings['separator'] : self::DEFAULT_SEPARATOR;
        $lineSeparator = isset($extraSettings['lineSeparator']) ? $extraSettings['lineSeparator'] : PHP_EOL;
        $this->quoteFields = isset($extraSettings['quoteFields']) && $extraSettings['quoteFields'];

        $output = '';

        if (!isset($extraSettings['skipHeaders'])) {
            $output .= implode($separator, $this->getCsvItem($this->config->getHeaders())) . $lineSeparator;
        }

        $data = $this->config->getData();
//        FIXME unify behavior
//        if (empty($data)) {
//            throw new Exception\DataNotFoundException();
//        }

        foreach ($data as $item) {
            $item = $this->getRawItem($item);
            $output .= implode($separator, $this->getCsvItem($item)) . $lineSeparator;
        }
        return $output;
    }

    /**
     * Quotes CSV line output when needed
     * @param array $fields
     * @return array
     */
    private function getCsvItem(array $fields)
    {
        if (!$this->quoteFields) {
            return $fields;
        }
        return array_map(function($field) {
            '"' . str_replace('"', '""', $field) . '"';
        }, $fields);
    }
}