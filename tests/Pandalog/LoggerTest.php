<?php 

namespace Pandalog;

use Pandalog\Logger;
use Pandalog\Handler\FileHandler;
use Pandalog\Handler\HandlerInterface;
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
    /**
     * write test
     * 
     * @test
     * @return void
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function write()
    {
        $logger = new Logger('test');
        $hander = new FileHandler();
        $hander->useDaily('/tmp/test.log');
        $logger->setHandler($hander);
        $logger->pushProcessor(new LogIdProcessor());
        $logger->pushProcessor(new PushProcessor(['host_ip', 'product'], ['12312', '123']));
        $this->assertEquals('test', $logger->getName());
        $this->assertTrue($logger->log('info', 'log'));
        $this->assertTrue($logger->debug('debug'));
        $this->assertTrue($logger->info('info'));
        $this->assertTrue($logger->notice('notice'));
        $this->assertTrue($logger->warning('warning'));
        $this->assertTrue($logger->error('error'));
        $this->assertTrue($logger->critical('critical'));
        $this->assertTrue($logger->alert('alert'));
        $this->assertTrue($logger->emergency('emergency'));
    }

    /**
     * quickinit
     *
     * @test
     * @return void
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function quickInit()
    {
        $params = [
            'name' => 'test',
            'path' => '/tmp/test.log',
            'product' => 'supply',
            'module'  => 'order'
            ];
        $this->assertTrue(Logger::quickInit($params)->warning('test'));
    }

    /**
     * processor
     *
     * @test
     * @return void
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function processor()
    {
        $logger = new Logger('test');
        $logger = $logger->pushProcessor(new LogIdProcessor());
        $this->assertTrue($logger instanceof Logger);
        $this->assertTrue(! empty($logger->getProcessors()));
        $this->assertTrue(is_callable($logger->popProcessor()));
    }

    /**
     * hander
     *
     * @test 
     * @return void
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function hander()
    {
        $logger = new Logger('test');
        $logger = $logger->setHandler(new FileHandler());
        $this->assertTrue($logger instanceof Logger);
        $this->assertTrue($logger->getHandler() instanceof HandlerInterface);
    }

    /**
     * format
     *
     * @test 
     * @return void
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function format()
    {
        $logger = new Logger('test');
        $logger->setDateFormat('Y-m-d');
        $this->assertTrue($logger instanceof Logger);
    }

} // END class LoggerTest extends \PHPUnit_Framework_TestCase
