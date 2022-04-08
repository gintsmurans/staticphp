<?php

namespace Tests\Modules\Core\Helpers;

use PHPUnit\Framework\TestCase;
use System\Modules\Core\Models\Load;

// Load helper here, because it cannot be loaded more than once
Load::helper(['Html'], 'Core', 'System');

class HtmlTest extends TestCase
{
    public function testCss()
    {
        html_css('test.css');
        html_css('test2.css');

        $this->expectOutputString('<link rel="stylesheet" type="text/css" href="test.css" />' . "\n" . '<link rel="stylesheet" type="text/css" href="test2.css" />' . "\n");
        html_css();
    }


    public function testJs()
    {
        html_js('test.js');
        html_js('test2.js');

        $this->expectOutputString('<script type="text/javascript" src="test.js"></script>' . "\n" . '<script type="text/javascript" src="test2.js"></script>' . "\n");
        html_js();
    }


    public function testDropdown()
    {
        $dropdown = html_dropdown(['1' => 'One', '2' => 'Two'], $selected = 2, $addons = ['#' => 'class="test"', '1' => 'data-param="xx"'], []);

        $this->assertContains('<select', $dropdown);
        $this->assertContains('class="test"', $dropdown);
        $this->assertContains('data-param', $dropdown);
        $this->assertContains('selected', $dropdown);
        $this->assertContains('Two', $dropdown);
    }

    public function testInputValue()
    {
        $test = html_escape_input('dfsgf"sdfas"sfsf"');
        $this->assertEquals('dfsgf&quot;sdfas&quot;sfsf&quot;', $test);
    }

    public function testTextareaValue()
    {
        $test = html_escape_textarea('dfsgf"sdfas"sfsf"> < />');
        $this->assertEquals('dfsgf"sdfas"sfsf"&gt; &lt; /&gt;', $test);
    }

    public function testSelected()
    {
        $current = 1;
        $test = html_set_selected($current, 1);
        $this->assertContains('selected', $test);

        $current = 2;
        $test = html_set_selected($current, 1);
        $this->assertNull($test);
    }

    public function testChecked()
    {
        $current = 1;
        $test = html_set_checked($current, 1);
        $this->assertContains('checked', $test);

        $current = 2;
        $test = html_set_selected($current, 1);
        $this->assertNull($test);
    }
}
