<?php

namespace Tests\Modules\Core\Helpers;

use PHPUnit\Framework\TestCase;
use System\Modules\Core\Models\Load;

// Load helper here, because it cannot be loaded more than once
Load::helper(['Other'], 'Core', 'System');

class OtherTest extends TestCase
{
    public function testFixFloat()
    {
        $number = fixFloat('10,30');
        $this->assertEquals(10.3, $number);

        $number = fixFloat('10,31345', 2);
        $this->assertEquals(10.31, $number);
    }


    public function testTrimChars()
    {
        $test = "\r\nAAA\t\n\r\0\x0B";
        trimChars($test);
        $this->assertEquals('AAA', $test);
    }


    public function testUuid4()
    {
        $test = uuid4();
        $this->assertEquals(36, strlen($test));
    }


    public function testParseQueryString()
    {
        $test = parseQueryString('aa=bb&cc=dd');
        $this->assertEquals(['aa' => 'bb', 'cc' => 'dd'], $test);

        $test = parseQueryString('aa=bb#cc=dd', '#');
        $this->assertEquals(['aa' => 'bb', 'cc' => 'dd'], $test);
    }


    public function testWeekRange()
    {
        $year = date('Y');
        $weeks = getIsoWeeksInYear($year);
        for ($i = 1; $i <= $weeks; ++$i) {
            $test = weekRange($i, $year);
            $this->assertTrue(count($test) == 2 && !empty($test[0]) && !empty($test[1]));
        }
    }


    public function testMonthRangeDateTime()
    {
        $test = monthRangeDateTime();
        $this->assertTrue(count($test) == 2 && !empty($test[0]) && !empty($test[1]));
    }


    public function testExtractArrayByKeys()
    {
        $testArr = ['post' => 'data', 'is' => 'awesome'];

        $test = extractArrayByKeys($testArr, ['post', 'is']);
        $this->assertEquals($testArr, $test);

        $test = extractArrayByKeys($testArr, ['post', 'is', 'as'], false, 'get');
        $this->assertEquals($testArr + ['as' => 'get'], $test);

        $test = extractArrayByKeys($testArr, ['post', 'is', 'as'], true);
        $this->assertFalse($test);
    }


    public function testAnyEmpty()
    {
        $this->assertFalse(anyEmpty(['a', 'b', 'c']));
        $this->assertTrue(anyEmpty(['a', 'b', '']));
    }


    public function testAllEmpty()
    {
        $this->assertFalse(allEmpty(['', '', 'c']));
        $this->assertTrue(allEmpty(['', '', '']));
    }


    public function testTmpFilename()
    {
        $test = tmpFilename('test_', '_test');
        $this->assertContains('test_', $test);
        $this->assertContains('_test', $test);
    }


    // @TODO
    public function testGroupArray()
    {
        // groupArray($array, $keys = [], $unique = false)
    }


    public function testValidISODate()
    {
        $this->assertFalse(validISODate('9.5.2017'));
        $this->assertTrue(validISODate('2017-05-09'));
    }


    public function testValidISODateTime()
    {
        $this->assertFalse(validISODateTime('9.5.2017 3:3'));
        $this->assertTrue(validISODateTime('2017-05-09T03:03:10+02:00'));
    }
}
