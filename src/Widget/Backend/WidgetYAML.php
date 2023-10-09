<?php
/**
 * bundle for Contao Open Source CMS
 *
 * Copyright (c) 2016-2019 Andreas Fieger
 *
 * @package fiedsch/contao-jsonwidget
 * @author  Andreas Fieger
 * @license MIT
 */

namespace Fiedsch\JsonWidgetBundle\Widget\Backend;

use Fiedsch\JsonWidgetBundle\Helper\Helper;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Contao\TextArea;
use Contao\Input;

/**
 * Class WidgetYAML
 * Provide a widget for JSON data.
 */
class WidgetYAML extends TextArea
{

    /**
     * Initialize the object
     * @param array $arrAttributes
     */
    public function __construct($arrAttributes=null)
    {
        parent::__construct($arrAttributes);
    }

    /**
     * Validate input and set value
     */
    public function validate(): void
    {
        $varValue = $this->getPost($this->strName);
        if (null === $varValue) {
            // NULL values can not be pared as YAML. So we set the parameter
            // to an empty string which -- when parsed -- returns a NULL value.
            $varValue = '';
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
        try {
            if (null === Yaml::parse($varInput)) {
                $this->addError($GLOBALS['TL_LANG']['MSC']['json_widget_invalid_yaml']);
            } else {
                // revert the effect of prettyPrintYaml() in generate()
                $varInput = $this->minifyYaml($varInput);
            }
        } catch (ParseException $e) {
            $this->addError($e->getMessage());
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
        $this->varValue = $this->prettyPrintYaml($this->varValue);
        return parent::generate();
    }

    /**
     * @param string $value
     * @return string
     */
    protected function minifyYaml(string $value)
    {
        if ('' === trim($value)) { return ''; }
        try {
            return Yaml::dump(Yaml::parse($value), 0);
        } catch (ParseException $e) {
            $this->addError($e->getMessage());
            return $value;
        }
    }

    /**
     * @param string $value
     * @return string
     */
    protected function prettyPrintYaml(string $value)
    {
        if ('' === trim($value)) { return ''; }
        try {
            return Yaml::dump(Yaml::parse($value), 10);
        } catch (ParseException $e) {
            $this->addError($e->getMessage());
            return $value;
        }
    }
}
