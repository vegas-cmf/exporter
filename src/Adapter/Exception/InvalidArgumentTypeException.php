<?php
/**
 * This file is part of Vegas package
 *
 * @author Krzysztof Kaplon <krzysztof@kaplon.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://github.com/vegas-cmf
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Exporter\Adapter\Exception;

use \Vegas\Exporter\Adapter\Exception as ExporterException;

/**
 * Class InvalidArgumentTypeException
 * @package Vegas\Exporter\Adapter\Exception
 */
class InvalidArgumentTypeException extends ExporterException
{
    /**
     * @var string 
     */
    protected $message = 'Invalid argument type.';
}
