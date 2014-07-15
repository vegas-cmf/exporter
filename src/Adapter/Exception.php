<?php

namespace Vegas\Exporter\Adapter;

use Vegas\Exception as VegasException;

/**
 * Class Exception
 * @package Vegas\Exporter\Adapter
 */
class Exception extends VegasException
{
    /**
     * @var string
     */
    protected $message = 'Vegas Exporter Exception';
}
