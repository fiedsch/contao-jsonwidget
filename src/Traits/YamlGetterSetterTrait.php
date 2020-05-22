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
use RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

trait YamlGetterSetterTrait
{

    /**
     * Return an object property
     *
     * @param string $strKey the property key (e.g. the name of the column/dca field)
     * @return mixed|null the property value or null if the property does not exist/is not set
     */
    public function __get($strKey)
    {
        $tableColumns = Database::getInstance()->getFieldNames(static::$strTable);
        if (in_array($strKey, $tableColumns)) {
            $value = parent::__get($strKey);
        } else {
            $yamlData = $this->getYamlData();
            $value = isset($yamlData[$strKey]) ? $yamlData[$strKey] : null;
        }

        return $value;
    }

    /**
     * Set a value
     *
     * @param string $strKey the property key (the name of the column/dca field)
     * @param mixed $varValue the property value
     */
    public function __set($strKey, $varValue)
    {
        $tableColumns = Database::getInstance()->getFieldNames(static::$strTable);
        if ($strKey === static::$strYamlColumn) {
            throw new RuntimeException("you can not access this column directly. Use setYamlColumnData() instead.");
        }
        if (in_array($strKey, $tableColumns)) {
            parent::__set($strKey, $varValue);
        } else {
            $yamlData = $this->getYamlData();
            $yamlData[$strKey] = $varValue;
            $this->setYamlColumnData($yamlData);
        }
    }

    /**
     * @param $strKey
     */
    public function __unset($strKey): void
    {
        $tableColumns = Database::getInstance()->getFieldNames(static::$strTable);
        if (in_array($strKey, $tableColumns)) {
            throw new RuntimeException("unset can only be used for data in the YAML-column");
        }
        $data = $this->getYamlData();
        unset($data[$strKey]);
        $this->setYamlColumnData($data);
    }

    /**
     * Return the data in static::$strYamlColumn as an array
     *
     * @return array
     */
    protected function getYamlData(): array
    {
        try {
            $yamlData = Yaml::parse($this->arrData[static::$strYamlColumn]);
        } catch (ParseException $e) {
            // ignored
            $yamlData = [];
        }
        if (null === $yamlData) {
            $yamlData = [];
        }

        return $yamlData;
    }

    /**
     * Set data in static::$strJsonColumn (overwriting previously set values)
     *
     * @param array $data
     */
    public function setYamlColumnData(array $data): void
    {
        $yamlStr = Yaml::dump($data, 10); // Note: we lose comments in our YAML here :-(
        parent::__set(static::$strYamlColumn, $yamlStr);
    }

}
