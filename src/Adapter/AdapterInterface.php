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

interface AdapterInterface
{
    /**
     * @param \Vegas\Exporter\ExportSettings $config
     * @return $this
     */
    public function setConfig(\Vegas\Exporter\ExportSettings $config);

    /**
     * Gets Content-Type header for browser output
     * @return string
     */
    public function getContentType();

    /**
     * Gets file extension including dot (e.x. .xls)
     * @return string
     */
    public function getExtension();

    /**
     * Gets rendered export file data
     * @return string
     */
    public function output();
}
