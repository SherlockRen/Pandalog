<?php 

namespace Pandalog;

use Pandalog\Logger;
use Pandalog\Handler\FileHandler;
use Pandalog\Processor\PushProcessor;
use Pandalog\Processor\LogIdProcessor;

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
        $logger->pushProcessor(new LogIdProcessor());
        $logger->pushProcessor(new PushProcessor(['host_ip', 'product'], ['12312', '123']));
        $this->assertTrue($logger->warning('test'));

        $params = [
            'name' => 'test',
            'path' => '/Users/SherlockRen/Data/logs/sites/test.log',
            'product' => 'supply',
            'module'  => 'order'
        ];
        $this->assertTrue(Logger::quickInit($params)->warning('test'));
    }   
} // END class LoggerTest extends \PHPUnit_Framework_TestCase
