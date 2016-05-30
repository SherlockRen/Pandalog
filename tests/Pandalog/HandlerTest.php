<?php 

namespace Pandalog;

use Pandalog\Handler\FileHandler;
use Pandalog\Formatter\JsonFormatter;
use Pandalog\Handler\HandlerInterface;
use Pandalog\Formatter\FormatterInterface;

/**
 * HandlerTest
 *
 * @package Pandalog
 * @subpackage Test
 * @author Sherlock Ren <sherlock_ren@icloud.com>
 */
class HandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * global test
     * 
     * @test
     * @return void
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function globalTest()
    {
        $hander = new FileHandler('/tmp/test.log');
        $this->assertTrue($hander instanceof $hander);
        $this->assertEquals('/tmp/test.log', $hander->link);
        $hander->setFormatter(new JsonFormatter);
        $this->assertTrue($hander->formatter instanceof FormatterInterface);
        $hander->setLink('/tmp/test.error');
        $this->assertEquals('/tmp/test.error', $hander->link);
        $hander->setLock(false);
        $this->assertFalse($hander->lock);
        $this->assertTrue($hander->handle(['test']));
    }

} // END class HandlerTest extends \PHPUnit_Framework_TestCase
