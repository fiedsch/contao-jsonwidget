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

namespace Contao;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class WidgetYAML
 * Provide a widget for JSON data.
 */
class WidgetYAML extends TextArea
{

    /**
     * Initialize the object
     * @param array
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
            Input::setPost($this->strName, '');
        }
        parent::validate();
    }

    /**
     * @param mixed $varInput
     * @return mixed
     */
    public function validator($varInput)
    {
        try {
            if (null === Yaml::parse($varInput)) {
                $this->addError($GLOBALS['TL_LANG']['MSC']['json_widget_invalid_yaml']);
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
        return parent::generate();
    }

}
