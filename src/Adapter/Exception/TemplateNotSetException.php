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

namespace Vegas\Exporter\Adapter\Exception;

use Vegas\Exporter\Adapter\Exception as ExporterException;

/**
 * Class TemplateNotSetException
 * @package Vegas\Exporter\Adapter\Exception
 */
class TemplateNotSetException extends ExporterException
{
    /**
     * @var string
     */
    protected $message = 'No view template specified for exports.';
}
