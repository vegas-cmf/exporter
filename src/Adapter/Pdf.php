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

/**
 * Class Pdf
 *
 * Available extra settings to be provided:
 * - viewParam: parameter which keeps ExportSettings object of the exporter to be used inside .volt template, defaults to 'exporter'
 * - pageOrientation: Portrait or Landscape, defaults to 'Portrait'
 * - pageSize: selected page size of output, defaults to 'A4'
 * - fontFamily: default font for for output, defaults to '' which is mPDF's default
 * - fontSize: default font size for output, defaults to 0 which is mPDF's default
 *
 * @package Vegas\Exporter\Adapter
 */
class Pdf extends AdapterAbstract
{
    /**
     * Default parameter name which keeps ExportSettings object inside template
     */
    const VIEW_PARAM_NAME = 'exporter';

    const PAGE_SIZE_A3 = 'A3';
    const PAGE_SIZE_A4 = 'A4';
    const PAGE_SIZE_A5 = 'A5';

    const PAGE_ORIENTATION_PORTRAIT = 'Portrait';
    const PAGE_ORIENTATION_LANDSCAPE = 'Landscape';

    const FONT_FAMILY_DEFAULT = '';
    const FONT_FAMILY_COURIER = 'Courier';
    const FONT_FAMILY_HELVETICA = 'Helvetica';
    const FONT_FAMILY_ARIAL = 'Arial';
    const FONT_FAMILY_TIMES = 'Times';
    
    const FONT_SIZE_DEFAULT = 0;

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return 'application/pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return '.pdf';
    }

    /**
     * @return string
     */
    private function getViewParamName()
    {
        $extraSettings = $this->config->getExtraSettings();
        return isset($extraSettings['viewParam']) ? $extraSettings['viewParam'] : self::VIEW_PARAM_NAME;
    }

    /**
     * @throws Exception\EmptyHeadersException
     * @throws Exception\InvalidArgumentTypeException
     * @throws Exception\InvalidFontFamilyException
     * @throws Exception\InvalidPageOrientationException
     * @throws Exception\InvalidPageSizeException
     * @throws Exception\OutputPathNotWritableException
     * @throws Exception\TemplateNotSetException
     */
    public function validateOutput()
    {
        parent::validateOutput();

        $template = $this->config->getTemplate();
        if (empty($template)) {
            throw new Exception\TemplateNotSetException;
        }

        $extraSettings = $this->config->getExtraSettings();
        if (isset($extraSettings['pageOrientation']) && !in_array($extraSettings['pageOrientation'], $this->getAvailablePageOrientation())) {
            throw new Exception\InvalidPageOrientationException;
        }

        if (isset($extraSettings['pageSize']) && !in_array($extraSettings['pageSize'], $this->getAvailablePageSize())) {
            throw new Exception\InvalidPageSizeException;
        }

        if (isset($extraSettings['fontFamily']) && !in_array($extraSettings['fontFamily'], $this->getAvailableFontFamily())) {
            throw new Exception\InvalidFontFamilyException;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function output()
    {
        $extraSettings = $this->config->getExtraSettings();

        $pageOrientation = isset($extraSettings['pageOrientation']) ? $extraSettings['pageOrientation'] : self::PAGE_ORIENTATION_PORTRAIT;
        $pageSize = isset($extraSettings['pageSize']) ? $extraSettings['pageSize'] : self::PAGE_SIZE_A4;
        $fontSize = isset($extraSettings['fontSize']) ? $extraSettings['fontSize'] : self::FONT_SIZE_DEFAULT;
        $fontFamily = isset($extraSettings['fontFamily']) ? $extraSettings['fontFamily'] : self::FONT_FAMILY_DEFAULT;

        $mpdf = new \mPDF('utf-8', $pageSize, $fontSize, $fontFamily, 15, 15, 16, 16, 9, 9, $pageOrientation);

        $title = $this->config->getTitle();
        is_string($title) && $mpdf->SetTitle($title);

        $mpdf->WriteHTML($this->getRenderedView());

        return $this->getBuffer($mpdf);
    }

    /**
     * Triggers the rendering process and gets result content as string
     * @return string
     * @throws \Vegas\Mvc\Exception
     */
    private function getRenderedView()
    {
        try {
            $view = \Phalcon\DI::getDefault()->get('view');
        } catch (\Phalcon\DI\Exception $e) {
            throw new \Vegas\Mvc\Exception;
        }

        $view->setVar($this->getViewParamName(), $this->config);
        $view->start();
        $view->render($this->config->getTemplate(), null);
        $view->finish();

        return $view->getContent();
    }

    /**
     * Dumps PDF file to memory & retrieves content
     * @param \mPDF $mpdf
     * @return string
     */
    private function getBuffer(\mPDF $mpdf)
    {
        ob_start();
        $mpdf->Output('php://output');
        return ob_get_clean();
    }

    /**
     * @return array
     */
    private function getAvailablePageOrientation()
    {
        return [
            self::PAGE_ORIENTATION_LANDSCAPE,
            self::PAGE_ORIENTATION_PORTRAIT
        ];
    }

    /**
     * @return array
     */
    private function getAvailablePageSize()
    {
        return [
            self::PAGE_SIZE_A3,
            self::PAGE_SIZE_A4,
            self::PAGE_SIZE_A5
        ];
    }

    /**
     * @return array
     */
    private function getAvailableFontFamily()
    {
        return [
            self::FONT_FAMILY_DEFAULT,
            self::FONT_FAMILY_ARIAL,
            self::FONT_FAMILY_COURIER,
            self::FONT_FAMILY_HELVETICA,
            self::FONT_FAMILY_TIMES
        ];
    }
}
