<?php

namespace App\Test\TestCase\View\Helper;

use App\View\Helper\PurpleHelper;
use Cake\View\View;
use PHPUnit\Framework\TestCase;

class PurpleHelperTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $View = new View();
        $this->Purple = new PurpleHelper($View);
    }

    public function testReadableFileSize()
    {
        $result = $this->Purple->readableFileSize(1024);
        $this->assertContains('1 KB', $result);

        $result = $this->Purple->readableFileSize(1048576);
        $this->assertContains('1 MB', $result);
    }

    public function testShortenNumber()
    {
        $result = $this->Purple->shortenNumber(1000000);
        $this->assertContains('1.00M', $result);

        $result = $this->Purple->shortenNumber(500000);
        $this->assertContains('500.00K', $result);
    }

    public function testNotificationCounter()
    {
        $result = $this->Purple->notificationCounter(12);
        $this->assertContains('10+', $result);

        $result = $this->Purple->notificationCounter(1);
        $this->assertContains('1', $result);
    }

    public function testPlural()
    {
        $result = $this->Purple->plural(1, 'post');
        $this->assertContains('1 post', $result);

        $result = $this->Purple->plural(2, 'post');
        $this->assertContains('2 posts', $result);

        $result = $this->Purple->plural(1, 'glass', 'es');
        $this->assertContains('1 glass', $result);

        $result = $this->Purple->plural(2, 'glass', 'es');
        $this->assertContains('2 glasses', $result);

        $result = $this->Purple->plural(1, 'post', 's', true);
        $this->assertContains('1 post', $result);

        $result = $this->Purple->plural(2, 'post', 's', true);
        $this->assertContains('2 posts', $result);
    }

    public function testGetAllFuncInHtml()
    {
        $result = $this->Purple->getAllFuncInHtml("<section id='fdb-{bind.id}' class='fdb-block purple-theme-block-section remove-padding-in-real' data-fdb-id='{bind.id}'><div class='purple-theme-block'><div class='purple-theme-dynamic-block'>{{function|myFunction}}</div></div></section>");
        $this->assertEquals(1, count($result));

        $result = $this->Purple->getAllFuncInHtml("<p>Test</p>");
        $this->assertEquals(false, $result);
    }
}
