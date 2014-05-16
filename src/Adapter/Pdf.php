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

namespace Vegas\Exporter\Adapter;

use \Vegas\Exporter\Adapter\Exception\PdfException as PdfException;

class Pdf extends AdapterAbstract
{
    /**
     * @var string
     */
    const EXPORT_TYPE_FILE = 'F';
    
    /**
     * @var string
     */
    const EXPORT_TYPE_DOWNLOAD = 'D';
    
    /**
     * @var string
     */
    const HEADER = 'header';
    
    /**
     * @var string
     */
    const CONTENT = 'content';
    
    /**
     * @var string
     */
    const PAGE_SIZE_A3 = 'A3';
    
    /**
     * @var string
     */
    const PAGE_SIZE_A4 = 'A4';
    
    /**
     * @var string
     */
    const PAGE_SIZE_A5 = 'A5';
    
    /**
     * @var string
     */
    const PAGE_SIZE_LETTER = 'LETTER';
    
    /**
     * @var string
     */
    const PAGE_SIZE_LEGAL = 'LEGAL';
    
    /**
     * @var string
     */
    const PAGE_ORIENTATION_PORTRAIT = 'Portrait';
            
    /**
     * @var string
     */
    const PAGE_ORIENTATION_LANDSCAPE = 'Landscape';
    
    /**
     * @var string
     */
    const FONT_FAMILY_COURIER = 'Courier';
    
    /**
     * @var string
     */
    const FONT_FAMILY_HELVETICA = 'Helvetica';
    
    /**
     * @var string
     */
    const FONT_FAMILY_ARIAL = 'Arial';
    
    /**
     * @var string
     */
    const FONT_FAMILY_TIMES = 'Times';
    
    /**
     * @var string
     */
    const FONT_STYLE_REGULAR = '';
    
    /**
     * @var string
     */
    const FONT_STYLE_BOLD = 'B';
    
    /**
     * @var string
     */
    const FONT_STYLE_ITALIC = 'I';
    
    /**
     * @var string
     */
    const FONT_STYLE_UNDERLINE = 'U';
    
    /**
     * @var StdClass
     */
    private $config;
    
    /**
     * @var FPDF
     */
    private $pdf;
    
    public function __construct()
    {
        $this->config = new \stdClass();
        
        $this->setConfig('contentType', 'application/pdf');
        $this->setFileName('export_file.pdf');
        
        $this->setPageOrientation(self::PAGE_ORIENTATION_PORTRAIT);
        $this->setPageSize(self::PAGE_SIZE_A4);
        
        $this->setCellWidth(40);
        $this->setCellHeight(10);
        
        $this->setFontSize(16);
        $this->setFontFamily(self::FONT_FAMILY_ARIAL);
        $this->setFontStyle(self::FONT_STYLE_REGULAR);
        
        // Separate table header config
        $this->config->header = new \stdClass();
        $this->setHeaderFontStyle(self::FONT_STYLE_BOLD);
        
        // Separate table content config
        $this->config->content = new \stdClass();
    }
    
    
    /**
     * Initializes PDF object and preps data to export.
     * 
     * @param array $data
     * @param type $useKeysAsHeaders
     * @throws Exception\ExportDataEmptyException
     */
    public function init(array $data, $useKeysAsHeaders = false)
    {
        if (empty($data[0]) || !is_array($data[0]))
        {
            throw new Exception\ExportDataEmptyException();
        }
        
        if ($useKeysAsHeaders){
            $this->setHeaders(array_keys($data[0]));
        }
        
        $this->pdf = new \FPDF(
            $this->getConfig('pageOrientation'),
            'mm',
            $this->getConfig('pageSize')
        );
        
        $this->pdf->AddPage();
        
        $this->pdf->SetFont(
            $this->getConfig('fontFamily'),
            $this->getConfig('fontStyle'),
            $this->getConfig('fontSize')
        );
        
        // Export data headers
        if (count($this->headers) > 0){
            
            foreach ($this->headers as $text){
                $this->pdf->Cell(
                    $this->getConfig('cellWidth', self::HEADER),
                    $this->getConfig('cellHeight', self::HEADER),
                    $text,
                    1,
                    0,
                    'L'
                );
            }
            
            $this->pdf->Ln();
        }
        
        // Export data conent
        foreach ($data as $row) {
            
            foreach ($row as $text) {
                
                $this->pdf->Cell(
                    $this->getConfig('cellWidth', self::CONTENT),
                    $this->getConfig('cellHeight', self::CONTENT),
                    $text,
                    1,
                    0,
                    'L'
                ); 
            }
            
            $this->pdf->Ln();
        }
    }
    
    /**
     * Exports PDF file.
     */
    protected function exportFile()
    {
        $outputFilePath = $this->outputPath . $this->fileName;
        
        $this->pdf->Output($outputFilePath, self::EXPORT_TYPE_FILE);
    }
    
    /**
     * Sends PDF file into the browser.
     */
    public function download()
    {
        $this->pdf->Output(
            $this->fileName,
            self::EXPORT_TYPE_DOWNLOAD
        );
    }
    
    /**
     * Sets PDF pages size.
     * Can be chained with other config set* methods.
     * 
     * Available options:
     * - PAGE_SIZE_A3
     * - PAGE_SIZE_A4
     * - PAGE_SIZE_A5
     * - PAGE_SIZE_LETTER
     * - PAGE_SIZE_LEGAL
     * 
     * @param string $size
     * @return obj
     * @throws \Exception\InvalidPageSizeException
     */
    public function setPageSize($size)
    {
        $allowed = array(
            self::PAGE_SIZE_A3,
            self::PAGE_SIZE_A4,
            self::PAGE_SIZE_A5,
            self::PAGE_SIZE_LETTER,
            self::PAGE_SIZE_LEGAL,
        );
        
        if (!in_array($size, $allowed))
        {
            throw new Exception\InvalidPageSizeException();
        }
        
        $this->config->pageSize = $size;
        
        return $this;
    }

    /**
     * Sets PDF pages orientation.
     * Can be chained with other config set* methods.
     * 
     * Available options:
     * - PAGE_ORIENTATION_PORTRAIT
     * - PAGE_ORIENTATION_LANDSCAPE
     * 
     * @param type $orientation
     * @return \Vegas\Exporter\Adapter\Pdf
     * @throws Exception\InvalidCellWidthException
     */
    public function setPageOrientation($orientation)
    {
        $allowed = array(
            self::PAGE_ORIENTATION_PORTRAIT,
            self::PAGE_ORIENTATION_LANDSCAPE,
        );
        
        if (!in_array($orientation, $allowed))
        {
            throw new Exception\InvalidPageOrientationException();
        }
        
        $this->config->pageOrientation = $orientation;
        
        return $this;
    }
    
    /**
     * Sets fixed width for all teble cells in PDF.
     * Can be chained with other config set* methods.
     * 
     * @param integer $width
     * @return \Vegas\Exporter\Adapter\Pdf
     * @throws Exception\InvalidCellWidthException
     */
    public function setCellWidth($width)
    {
        if (!is_int($width) || $width < 1)
        {
            throw new Exception\InvalidCellWidthException();
        }
        
        $this->config->cellWidth = $width;
        
        return $this;
    }
    
    /**
     * Sets fixed height for all teble cells in PDF.
     * Can be chained with other config set* methods.
     * 
     * @param integer $height
     * @return \Vegas\Exporter\Adapter\Pdf
     * @throws Exception\InvalidCellHeightException
     */
    public function setCellHeight($height)
    {
        if (!is_int($height) || $height < 1)
        {
            throw new Exception\InvalidCellHeightException();
        }
        
        $this->config->cellHeight = $height;
        
        return $this;
    }

    /**
     * Sets font size for table headers and content.
     * Can be chained with other config set* methods.
     *  
     * @param integer $size default FONT_FAMILY_ARIAL.
     * @return \Vegas\Exporter\Adapter\Pdf
     */
    public function setFontSize($size)
    {
        return $this->setFontSizeConfig($size);
    }
    
    /**
     * Sets font size for table headers.
     * Won't function if table headers aren't specified.
     * Can be chained with other config set* methods.
     *  
     * @param integer $size default FONT_FAMILY_ARIAL.
     * @return \Vegas\Exporter\Adapter\Pdf
     */
    public function setHeaderFontSize($size)
    {
        $this->setFontSizeConfig($size, self::HEADER);
    }
    
    /**
     * Sets font size for table content.
     * Can be chained with other config set* methods.
     *  
     * @param integer $size default FONT_FAMILY_ARIAL.
     * @return \Vegas\Exporter\Adapter\Pdf
     */
    public function setContentFontSize($size)
    {
        $this->setFontSizeConfig($size, self::CONTENT);
    }
    
    /**
     * Sets font family for table headers and content.
     * Can be chained with other config set* methods.
     * 
     * Available options:
     * - FONT_FAMILY_COURIER
     * - FONT_FAMILY_HELVETICA
     * - FONT_FAMILY_ARIAL
     * - FONT_FAMILY_TIMES
     *  
     * @param string $name default FONT_FAMILY_ARIAL.
     * @return \Vegas\Exporter\Adapter\Pdf
     */
    public function setFontFamily($name = self::FONT_FAMILY_ARIAL)
    {
        return $this->setFontFamilyConfig($name);
    }
    
    /**
     * Sets font family for table headers.
     * Won't function if table headers aren't specified.
     * Can be chained with other config set* methods.
     * 
     * Available options:
     * - FONT_FAMILY_COURIER
     * - FONT_FAMILY_HELVETICA
     * - FONT_FAMILY_ARIAL
     * - FONT_FAMILY_TIMES
     *  
     * @param string $name defaultFONT_FAMILY_ARIAL.
     * @return \Vegas\Exporter\Adapter\Pdf
     */
    public function setHeaderFontFamily($name = self::FONT_FAMILY_ARIAL)
    {
        return $this->setFontFamilyConfig($name, self::HEADER);
    }
    
    /**
     * Sets font family for table content.
     * Can be chained with other config set* methods.
     * 
     * Available options:
     * - FONT_FAMILY_COURIER
     * - FONT_FAMILY_HELVETICA
     * - FONT_FAMILY_ARIAL
     * - FONT_FAMILY_TIMES
     *  
     * @param string $name default FONT_FAMILY_ARIAL.
     * @return \Vegas\Exporter\Adapter\Pdf
     */
    public function setContentFontFamily($name = self::FONT_FAMILY_ARIAL)
    {
        return $this->setFontFamilyConfig($name, self::CONTENT);
    }
    
    /**
     * Sets font style for table headers and content.
     * Can be chained with other config set* methods.
     * 
     * Available name options:
     * - FONT_STYLE_REGULAR
     * - FONT_STYLE_BOLD
     * - FONT_STYLE_ITALIC
     * - FONT_STYLE_UNDERLINE
     *  
     * @param string $name default FONT_STYLE_REGULAR
     * @param string|null $target default null
     * @return \Vegas\Exporter\Adapter\Pdf
     */
    public function setFontStyle($name = self::FONT_STYLE_REGULAR)
    {
        return $this->setFontStyleConfig($name);
    }
    
    /**
     * Sets font style for table headers.
     * Won't function if table headers aren't specified.
     * Can be chained with other config set* methods.
     * 
     * Available options:
     * - FONT_STYLE_REGULAR
     * - FONT_STYLE_BOLD
     * - FONT_STYLE_ITALIC
     * - FONT_STYLE_UNDERLINE
     *  
     * @param string $name default FONT_STYLE_REGULAR.
     * @return \Vegas\Exporter\Adapter\Pdf
     */
    public function setHeaderFontStyle($name = self::FONT_STYLE_REGULAR)
    {
        return $this->setFontStyleConfig($name, self::HEADER);
    }
    
    /**
     * Sets font style for table content.
     * Can be chained with other config set* methods.
     * 
     * Available options:
     * - FONT_STYLE_REGULAR
     * - FONT_STYLE_BOLD
     * - FONT_STYLE_ITALIC
     * - FONT_STYLE_UNDERLINE
     *  
     * @param string $name default FONT_STYLE_REGULAR.
     * @return \Vegas\Exporter\Adapter\Pdf
     */
    public function setContentFontStyle($name = self::FONT_STYLE_REGULAR)
    {
        return $this->setFontStyleConfig($name, self::CONTENT);
    }
    
    /**
     * Sets PDF config.
     * 
     * Available target options:
     * - TABLE_HEADER
     * - TABLE_CONTENT
     * - null (both above)
     * 
     * @param string $name
     * @param mixed $value
     * @param string|null $target
     */
    private function setConfig($name, $value, $target = null)
    {
        switch($target){
            
            case self::HEADER:
                $this->config->header->{$name} = $value;
                break;
            
            case self::CONTENT:
                $this->config->content->{$name} = $value;
                break;
            
            default:
                $this->config->{$name} = $value;
                break;
        }
    }
    
    /**
     * Gets value from class config.
     * 
     * Available target options:
     * - TABLE_HEADER ($this->config->header)
     * - TABLE_CONTENT ($this->config->content)
     * - null ($this->config)
     * 
     * If target is provided, a property value from its config will be returned if was set.
     * If property value was not set, the property value from main config will be returned.
     * If a property value doesn't exist in main config PdfException will be thrown.
     * 
     * @param string $name Config value property name
     * @param string|null $target Taget object to get property value from
     * @throws Exception\InvalidConfigTypeException
     * @throws Exception\InvalidConfigPropertyException
     */
    private function getConfig($name, $target = null)
    {
        if (!is_string($name)) {
            throw new Exception\InvalidConfigTypeException();
        }
        
        if (isset($this->config->{$target}->{$name})) {
            
            return $this->config->{$target}->{$name};
            
        } elseif (isset($this->config->{$name})) {
            
            return $this->config->{$name};
            
        } else {
            throw new Exception\InvalidConfigPropertyException($name);
        }
    }
    
    /**
     * Sets PDF font style.
     * Can be chained with other config set* methods.
     * 
     * Available name options:
     * - FONT_STYLE_REGULAR
     * - FONT_STYLE_BOLD
     * - FONT_STYLE_ITALIC
     * - FONT_STYLE_UNDERLINE
     * 
     * Available target options:
     * - TABLE_HEADER
     * - TABLE_CONTENT
     * - null (both above)
     *  
     * @param string $name default FONT_STYLE_REGULAR
     * @param string|null $target default null
     * @return \Vegas\Exporter\Adapter\Pdf
     * @throws Exception\InvalidFontStyleException
     */
    private function setFontStyleConfig($name = self::FONT_STYLE_REGULAR, $target = null)
    {
        $allowed = array(
            self::FONT_STYLE_BOLD,
            self::FONT_STYLE_ITALIC,
            self::FONT_STYLE_REGULAR,
            self::FONT_STYLE_UNDERLINE,
        );
        
        if (!in_array($name, $allowed))
        {
            throw new Exception\InvalidFontStyleException();
        }
        
        $this->setConfig('fontStyle', $name, $target);
        
        return $this;
    }
    
    /**
     * Sets PDF font family.
     * Can be chained with other config set* methods.
     * 
     * Available name options:
     * - FONT_FAMILY_COURIER
     * - FONT_FAMILY_HELVETICA
     * - FONT_FAMILY_ARIAL
     * - FONT_FAMILY_TIMES
     * 
     * Available target options:
     * - TABLE_HEADER
     * - TABLE_CONTENT
     * - null (both above)
     *  
     * @param string $name default FONT_FAMILY_ARIAL.
     * @return \Vegas\Exporter\Adapter\Pdf
     * @throws Exception\InvalidFontFamilyException
     */
    private function setFontFamilyConfig($name = self::FONT_FAMILY_ARIAL, $target = null)
    {
        $allowed = array(
            self::FONT_FAMILY_COURIER,
            self::FONT_FAMILY_HELVETICA,
            self::FONT_FAMILY_ARIAL,
            self::FONT_FAMILY_TIMES,
        );
        
        if (!in_array($name, $allowed))
        {
            throw new Exception\InvalidFontFamilyException();
        }
        
        $this->setConfig('fontFamily', $name, $target);
        
        return $this;
    }
    
    /**
     * Sets PDF font size.
     * Can be chained with other config set* methods.
     * 
     * Available target options:
     * - TABLE_HEADER
     * - TABLE_CONTENT
     * - null (both above)
     *  
     * @param integer $size
     * @return \Vegas\Exporter\Adapter\Pdf
     * @throws Exception\InvalidFontSizeException
     */
    private function setFontSizeConfig($size, $target = null)
    {
        if (!is_int($size) || $size < 1)
        {
            throw new Exception\InvalidFontSizeException();
        }
        
        $this->setConfig('fontSize', $size, $target);
        
        return $this;
    }
}
