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
    public static function cleanUpString(string $value): string
    {
        $mapping = [
            '&#92;&#34;' => '\"',
            '&#35;' => '#',
            '&#60;' => '<',
            '&#62;' => '>',
            '&#40;' => '(',
            '&#41;' => ')',
            '&#92;'  => '\\',
            '&#61;' => '=',
            '&#34;' => '"',
            '&#39;' => "'",
        ];

        return html_entity_decode(str_replace(array_keys($mapping), array_values($mapping), $value), ENT_QUOTES);
    }

    /**
     * A helper function (a hack) that is needed whenthe (raw) JSON data was saved and contained &quot; or &#34; entities.
     * We need to change these to \" in order not to run into JSON-errors when applying self::cleanUpSting() (see comment there/above).
     */
    public static function quoteHack(?string $value): string
    {
        return str_replace(['&quot;', '&#34;'], ['\"', '\"'], $value);
    }
}
