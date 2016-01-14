<?php 

namespace Pandalog;

use Pandalog\Logger;
use Pandalog\Handler\FileHandler;

/**
 * LoggerTest
 *
 * @package Pandalog
 * @subpackage Test
 * @author Sherlock Ren <sherlock_ren@icloud.com>
 */
class LoggerTest extends \PHPUnit_Framework_TestCase
{
    public function testLog()
    {
        $logger = new Logger('test');
        $hander = new FileHandler();
        $hander->useDaily('/Users/SherlockRen/Data/logs/sites/a/b/a.log');
        $logger->setHandler($hander);
        $this->assertTrue($logger->warning('test'));
    }   
} // END class LoggerTest extends \PHPUnit_Framework_TestCase
