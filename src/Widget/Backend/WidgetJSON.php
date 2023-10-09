<?php
/**
 * bundle for Contao Open Source CMS
 *
 * Copyright (c) 2016-2018 Andreas Fieger
 *
 * @package fiedsch/contao-jsonwidget
 * @author  Andreas Fieger
 * @license MIT
 */

namespace Fiedsch\JsonWidgetBundle\Widget\Backend;

use Fiedsch\JsonWidgetBundle\Helper\Helper;
use Contao\TextArea;
use Contao\Input;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * Class WidgetJSON
 * Provide a widget for JSON data.
 */
class WidgetJSON extends TextArea
{

    /**
     * Initialize the object
     * @param array $arrAttributes
     */
    public function __construct($arrAttributes=null)
    {
        parent::__construct($arrAttributes);
        $this->rte = ''; // RTE (tinyMCE) does not make sense here
    }

    /**
     * Validate input and set value
     * @return void
     */
    public function validate()
    {
        $varValue = $this->getPost($this->strName);
        if (empty($varValue)) {
            // the empty string is not a valid JSON string. So we set it to
            // the string representation of an empty JSON object.
            $varValue = '{}';
        }
        $varValue = Helper::cleanUpString($varValue);
        Input::setPost($this->strName, $varValue);
        parent::validate();
    }

    /**
     * @param mixed $varInput
     * @return mixed
     */
    public function validator($varInput)
    {
        if ('' === trim($varInput)) {
            return parent::validator($varInput);
        }
        $varInput = Helper::cleanUpString($varInput);
        if (null === json_decode($varInput)) {
            $this->addError($GLOBALS['TL_LANG']['MSC']['json_widget_invalid_json']);
        } else {
            // revert the effect of prettyPrintJson() in generate()
            $varInput = $this->minifyJson($varInput);
        }
        return parent::validator($varInput);
    }

    /**
     * Generate the widget and return it as string
     *
     * @return string
     */
    public function generate()
    {
        $this->varValue = $this->prettyPrintJson($this->varValue);
        return parent::generate();
    }

    /**
     * Pretty print a JSON string
     *
     * @return string
     */
    protected function prettyPrintJson(?string $jsonString)
    {
        if (null === $jsonString) {
            return '{}';
        }
        $fixedJsonString = Helper::quoteHack($jsonString);
        $decoded = json_decode($fixedJsonString);
        if (null === $decoded) {
            return $jsonString;
        }
        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Make a compact version of a (potentially) pretty printed JSON string
     *
     * @return string
     */
    protected function minifyJson(?string $jsonString)
    {
        if (null === $jsonString) {
            return '{}';
        }
        $decoded = json_decode($jsonString);
        if (null === $decoded) {
            return $jsonString;
        }
        return json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

}
