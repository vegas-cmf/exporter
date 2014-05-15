<?php

/**
 * This file is part of Vegas Exporter package.
 *
 * @author Mateusz Aniołek <matty201@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. * 
 */

namespace Vegas\Exporter\Adapter;

use Vegas\Exporter\Exception as ExporterException;

class AdapterException extends ExporterException
{
    protected $message = 'Vegas Exporter Adapter Exception';
}
