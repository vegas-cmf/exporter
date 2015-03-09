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
 * Class Xml
 *
 * Available extra settings to be provided:
 * - rootName: name of node tree, defaults to 'root'
 * - nodeName: name of node of each item, defaults to 'item'
 *
 * TODO add nesting possibility
 * @package Vegas\Exporter\Adapter
 */
class Xml extends AdapterAbstract
{
    const DEFAULT_ROOT_NAME = 'root';

    const DEFAULT_NODE_NAME = 'item';

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return 'application/xml';
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return '.xml';
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

        $headers = $this->config->getHeaderParams();
        foreach ($data as $item) {
            $item = $this->getRawItem($item);

            $itemNode = $dom->createElement($node);
            foreach ($headers as $i => $param) {
                $itemNode->appendChild($dom->createElement($param, $item[$i]));
            }
            $documentTree->appendChild($itemNode);
        }
        $dom->appendChild($documentTree);

        return $dom->saveXML(null, LIBXML_NOEMPTYTAG);
    }
}