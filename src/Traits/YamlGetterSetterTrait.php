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

namespace Fiedsch\JsonWidgetBundle\Traits;

use Contao\Database;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

trait YamlGetterSetterTrait {

    /**
     * Return an object property
     *
     * @param string $strKey the property key (e.g. the name of the column/dca field)
     * @return mixed|null the property value or null if the property does not exist/is not set
     */
    public function __get($strKey)
    {
        // FIXME: is \Model::getUniqueFields() cheaper than directly querying the database?
        $tableColumns = Database::getInstance()->getFieldNames(static::$strTable);
        if (in_array($strKey, $tableColumns)) {
            $value = parent::__get($strKey);
        } else {
            $value = null;
            if (!is_null($this->arrData[static::$strYamlColumn])) {
                $yamlString = $this->arrData[static::$strYamlColumn];
                if (!empty($jsonString)) {
                    try {
                        $yamlData = Yaml::parse($yamlString);
                        $value = isset($yamlData[$strKey]) ? $yamlData[$strKey] : null;
                    } catch (ParseException $e) {
                        // ignored
                    }
                }
            }
        }
        return $value;
    }

    /**
     * Set a value
     *
     * @param string $strKey the property key (the name of the column/dca field)
     * @param mixed $varValue the property value
     */
    public function __set($strKey, $varValue) {
        $tableColumns = Database::getInstance()->getFieldNames(static::$strTable);
        if ($strKey === static::$strYamlColumn) {
            throw new \RuntimeException("you can not access this column directly");
        }
        if (in_array($strKey, $tableColumns)) {
            parent::__set($strKey, $varValue);
        } else {
            $yamlString = $this->arrData[static::$strYamlColumn];
            $yamlData = null;
            if (!empty($yamlString)) {
                try {
                    $yamlData = Yaml::parse($yamlString);
                } catch (ParseException $e) {
                    // ignored
                }
            }
            if (is_null($yamlData)) { $yamlData = []; }
            $yamlData[$strKey] = $varValue;
            $yamlStr = Yaml::dump($yamlData, 10); // Note: we lose comments in our YAML here :-(
            parent::__set(static::$strYamlColumn, $yamlStr);
        }
    }

}
