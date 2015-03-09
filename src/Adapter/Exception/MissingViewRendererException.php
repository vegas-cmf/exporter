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

namespace Vegas\Exporter\Exception;

use Vegas\Exporter\Adapter\Exception as ExporterException;

/**
 * Class MissingViewRendererException
 * @package Vegas\Exporter\Adapter\Exception
 */
class MissingViewRendererException extends ExporterException
{
    /**
     * @var string
     */
    protected $message = 'View renderer configuration is missing in DI container.';
}
