<?php
/**
 * This file is part of Vegas Exporter package.
 *
 * @author Krzysztof Kaplon <krzysztof@kaplon.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. * 
 */

namespace Vegas\Exporter\Adapter;

interface AdapterInterface
{
    /**
     * Initializes data object for export.
     * It must set up properties object as well.
     * 
     * @param array $data set of data to export
     * @param type $useKeysAsHeaders uses array keys as headers for export data
     */
    public function init(array $data, $useKeysAsHeaders = false);
    
    /**
     * Exports data into file if output path was set.
     * Forces file download otherwise.
     */
    public function export();
    
    /**
     * Sets header rows for output data.
     * It must be used before init in order to work.
     * 
     * @param array $headers
     */
    public function setHeaders(array $headers);
    
    /**
     * @param string
     */
    public function setOutputPath($path);
    
    /**
     * @param string $name
     */
    public function setFileName($name);
}
