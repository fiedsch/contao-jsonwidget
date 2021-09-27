<?php

namespace Fiedsch\JsonWidgetBundle\Helper;

class Helper
{
    /**
     * Make sure, we have no leftovers from \Contao\Input::encodeSpecialChars()
     *
     * Note: this will obviously fail with data like
     * <code>
     * {
     *   "will_fail": "the entity for a double quote is &#34;"
     * }
     * </code>
     */
    public static function cleanUpString($value): string
    {
        $mapping = [
            '&#35;' => '#',
            '&#60;' => '<',
            '&#62;' => '>',
            '&#40;' => '(',
            '&#41;' => ')',
            '&#92;'  => '\\',
            '&#61;' => '=',
            '&#34;' => '"', // would also be handled by using flag ENT_QUOTES
            '&#39;' => "'", // dito
        ];

        return html_entity_decode(str_replace(array_keys($mapping), array_values($mapping), $value), ENT_QUOTES);
    }
}
