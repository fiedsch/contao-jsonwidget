<?php

namespace Fiedsch\JsonWidgetBundle\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Fiedsch\JsonWidgetBundle\Helper\Helper;

class HelperTest extends TestCase
{
    public function testCleanupString(): void
    {
        $testcases = [
            '&#35;&#60;&#62;&#40;&#41;&#92;&#61;&#34;&#39;'            => '#<>()\\="\'',
            '&#35; &#60; &#62; &#40; &#41; &#92; &#61; &#34; &#39;'    => '# < > ( ) \\ = " \'',
            '#&#35;<&#60;>&#62;(&#40;)&#41;\\&#92;=&#61;"&#34;\'&#39;' => '##<<>>(())\\\\==""\'\'',
        ];
        foreach ($testcases as $input => $expected) {
            self::assertSame($expected, Helper::cleanUpString($input));
        }
    }

}
