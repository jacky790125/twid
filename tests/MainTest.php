<?php

namespace Meditate\IdentityCard;

use PHPUnit\Framework\TestCase;

class MainTest extends TestCase
{
    public function testTwId()
    {
        $main = new TaiwanIdentityCard();

        $this->assertTrue($main->check('A123456789'));
        $this->assertFalse($main->check('A223456789'));
    }

    public function testIdNum()
    {
        $main = new TaiwanIdentityCard();

        $this->assertTrue($main->check('FA12345689'));
        $this->assertFalse($main->check('HE23456789'));
    }

    public function testNewIdNum()
    {
        $main = new TaiwanIdentityCard();

        $this->assertTrue($main->check('A800000014'));
        $this->assertFalse($main->check('A690000013'));
    }
}
