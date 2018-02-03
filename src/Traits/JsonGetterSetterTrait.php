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

trait JsonGetterSetterTrait {

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
            if (!is_null($this->arrData[static::$strJsonColumn])) {
                $jsonString = $this->arrData[static::$strJsonColumn];
                if (!empty($jsonString)) {
                    $jsonData = json_decode($jsonString, true);
                    $value = isset($jsonData[$strKey]) ? $jsonData[$strKey] : null;
                }
            }
        }
        return $value;
    }

    /**
     * Set a value
     *
     * @param $strKey the property key (the name of the column/dca field)
     * @param mixed $varValue the property value
     */
    public function __set($strKey, $varValue) {
        $tableColumns = Database::getInstance()->getFieldNames(static::$strTable);
        if ($strKey === static::$strJsonColumn) {
            throw new \RuntimeException("you can not access this column directly");
        }
        if (in_array($strKey, $tableColumns)) {
            parent::__set($strKey, $varValue);
        } else {
            $jsonString = $this->arrData[static::$strJsonColumn];
            $jsonData = null;
            if (!empty($jsonString)) {
                $jsonData = json_decode($jsonString, true);
            }
            if (is_null($jsonData)) { $jsonData = []; }
            $jsonData[$strKey] = $varValue;
            $jsonStr = json_encode($jsonData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            parent::__set(static::$strJsonColumn, $jsonStr);
        }
    }

}
