<?php

namespace Fiedsch\JsonWidgetBundle\Helper;

class Helper
{
    public static function cleanUpString($value): string
    {
        $value = str_replace('&#34;', '"', $value);

        return $value;
    }
}
