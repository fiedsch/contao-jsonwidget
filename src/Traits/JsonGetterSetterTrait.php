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
            throw new \RuntimeException("you can not access this column directly");
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
     * @param $jsonData
     */
    public function __unset($strKey, $jsonData): void
    {
        $tableColumns = Database::getInstance()->getFieldNames(static::$strTable);
        if (in_array($strKey, $tableColumns)) {
            throw new RuntimeException("unset can only be used for data in the JSON-column");
        }
        $jsonData = $this->getJsonData();
        unset($jsonData[$strKey]);
        $this->setJsonColumnData($jsonData);
    }

    /**
     * Return the data in static::$strJsonColumn as an array
     *
     * @return array
     */
    protected function getJsonData(): array
    {
        $jsonString = $this->arrData[static::$strJsonColumn];

        if (empty($jsonString)) {
            $jsonData = [];
        } else {
            $jsonData = json_decode($jsonString, true);
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
        $jsonStr = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        parent::__set(static::$strJsonColumn, $jsonStr);
    }

}
