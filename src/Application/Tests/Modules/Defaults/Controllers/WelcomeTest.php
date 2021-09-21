<?php

namespace Tests\Modules\Defaults\Controllers;


use PHPUnit\Framework\TestCase;
use Core\Controllers\Controller;
use Core\Models\Load;
use Core\Models\Router;
use Core\Models\Request;

class WelcomeTest extends TestCase
{
    public function testDefaultController()
    {
        $response = Request::internal('');
        $this->assertNotEmpty($response);
        $this->assertFalse(Request::httpErrorInData($response));
        $this->assertContains('Welcome', $response);
    }


    public function testUrl()
    {
        $response = Request::internal('defaults/welcome/index');
        $this->assertNotEmpty($response);
        $this->assertFalse(Request::httpErrorInData($response));
        $this->assertContains('Welcome', $response);
    }


    public function testMissingUrl()
    {
        $response = Request::internal('/non/existant/url');
        $this->assertNotEmpty($response);
        $this->assertTrue(Request::httpErrorInData($response));
    }
}
