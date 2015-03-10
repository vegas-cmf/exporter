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

use Vegas\Exception as VegasException;

/**
 * Class InvalidExporterException
 * @package Vegas\Exporter\Exception
 */
class InvalidExporterException extends VegasException
{
    /**
     * @var string
     */
    protected $message = 'Exporter is invalid: ';

    /**
     * @param string $param
     */
    public function __construct($param)
    {
        parent::__construct($this->message . $param);
    }
}
