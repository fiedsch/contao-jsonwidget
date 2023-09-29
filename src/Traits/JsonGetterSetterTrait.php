<?php declare(strict_types=1);

/**
 * bundle for Contao Open Source CMS
 *
 * Copyright (c) 2016-2020 Andreas Fieger
 *
 * @package fiedsch/contao-jsonwidget
 * @author  Andreas Fieger
 * @license MIT
 */

namespace Fiedsch\JsonWidgetBundle\Traits;

use Contao\Database;
use RuntimeException;
use const JSON_FORCE_OBJECT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;


trait JsonGetterSetterTrait
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
            $jsonData = $this->getJsonData();
            $value = isset($jsonData[$strKey]) ? $jsonData[$strKey] : null;
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
        if ($strKey === static::$strJsonColumn) {
            throw new \RuntimeException("you can not access this column directly. Use setJsonColumnData() instead.");
        }
        if (in_array($strKey, $tableColumns)) {
            parent::__set($strKey, $varValue);
        } else {
            $jsonData = $this->getJsonData();
            $jsonData[$strKey] = $varValue;
            $this->setJsonColumnData($jsonData);
        }
    }

    /**
     * @param $strKey
     */
    public function __unset($strKey): void
    {
        $tableColumns = Database::getInstance()->getFieldNames(static::$strTable);
        if (in_array($strKey, $tableColumns)) {
            throw new RuntimeException("unset can only be used for data in the JSON-column");
        }
        $data = $this->getJsonData();
        unset($data[$strKey]);
        $this->setJsonColumnData($data);
    }

    /**
     * Return the data in static::$strJsonColumn as an array
     *
     * @return array
     */
    protected function getJsonData(): array
    {
        $jsonString = $this->arrData[static::$strJsonColumn] ?? '';

        if (empty($jsonString)) {
            $jsonData = [];
        } else {
            $jsonData = json_decode($jsonString, true);
        }
        if (null === $jsonData) {
            $jsonData = [];
        }

        return $jsonData;
    }


    /**
     * Set data in static::$strJsonColumn (overwriting previously set values)
     *
     * @param array $data
     */
    public function setJsonColumnData(array $data): void
    {
        // Using JSON_FORCE_OBJECT in json_encode() below would make every empty array an object
        // in the JSON data. We only want to achieve this on the top level (i.t. empty $data should
        // yield {} and not []).
        if (empty($data)) { $data = new \stdClass(); }
        $jsonStr = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        parent::__set(static::$strJsonColumn, $jsonStr);
    }

}
