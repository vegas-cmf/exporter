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

namespace Vegas\Exporter\Exception;

use \Vegas\Exception as VegasException;

/**
 * Class InvalidMethodException
 * @package Vegas\Exporter\Exception
 */
class InvalidMethodException extends VegasException
{
    /**
     * @var string 
     */
    protected $message = 'Exporter method exception: ';
    
    /**
     * @param string $param
     */
    public function __construct($param) {
        parent::__construct($this->message . $param);
    }
}
