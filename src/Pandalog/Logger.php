<?php

/**
 * Pandalog.
 *
 * (c) SherlockRen <sherlock_ren@icloud.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pandalog;

use Psr\Log\LoggerInterface;
use Psr\Log\InvalidArgumentException;
use Pandalog\Handler\HandlerInterface;

/**
 * Pandalog
 *
 * @author Sherlock Ren <sherlock_ren@icloud.com>
 */
class Logger implements LoggerInterface
{
    /**
     * Detailed debug information
     */
    const DEBUG = 100;

    /**
     * Interesting events
     */
    const INFO = 200;

    /**
     * Uncommon events
     */
    const NOTICE = 250;

    /**
     * Exceptional occurrences that are not errors
     */
    const WARNING = 300;

    /**
     * Runtime errors
     */
    const ERROR = 400;

    /**
     * Critical conditions
     */
    const CRITICAL = 500;

    /**
     * Action must be taken immediately
     */
    const ALERT = 550;

    /**
     * Urgent alert.
     */
    const EMERGENCY = 600;

    /**
     * Pandalog API version
     * @var int
     */
    const VERSION = 1.0;

    /**
     * Logging levels
     *
     * @var array $levels Logging levels
     */
    protected static $levels = array(
        100 => 'DEBUG',
        200 => 'INFO',
        250 => 'NOTICE',
        300 => 'WARNING',
        400 => 'ERROR',
        500 => 'CRITICAL',
        550 => 'ALERT',
        600 => 'EMERGENCY',
    );

    /**
     * @var string
     */
    protected $name;

    /**
     * The handler stack
     */
    protected $handler;

    /**
     * Processors that will process all log records
     *
     * @var callable[]
     */
    protected $processors;

    /**
     * @param string             $name       The logging channel
     * @param HandlerInterface   $handler   Optional stack of handlers, the first one in the array is called first, etc.
     * @param callable[]         $processors Optional array of processors
     */
    public function __construct($name, HandlerInterface $handler = null, $processors = array())
    {
        $this->name    = $name;
        $this->handler = $handler;
        $this->processors = $processors;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set handler
     *
     * @param object $handler
     * @return $this
     */
    public function setHandler(HandlerInterface $handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @return HandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Adds a processor on to the stack.
     *
     * @param callable $callback
     * @return $this
     */
    public function pushProcessor($callback)
    {
        if ( ! is_callable($callback)) {
            throw new \InvalidArgumentException('Processors must be valid callables (callback or object with an __invoke method), ' . var_export($callback, true) . ' given');
        }
        array_unshift($this->processors, $callback);

        return $this;
    }

    /**
     * Removes the processor on top of the stack and returns it.
     *
     * @return callable
     */
    public function popProcessor()
    {
        if ( ! $this->processors) {
            throw new \LogicException('You tried to pop from an empty processor stack.');
        }

        return array_shift($this->processors);
    }

    /**
     * @return callable[]
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * Adds a log record.
     *
     * @param  integer $level   The logging level
     * @param  string  $message The log message
     * @return Boolean Whether the record has been processed
     */
    public function addRecord($level, $message, array $trace = array())
    {
        if ( ! $this->handler) {
            throw new \LogicException('hander error: please set a hander');
            return false;
        }

        $levelName = static::getLevelName($level);
        $timestamp = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
        $record = array(
            'date'  => date('Y-m-d H:i:s', $timestamp),
            'level' => $levelName,
            'msg'   => $message,
            'trace' => $trace,
            'timestamp' => $timestamp
        );

        foreach ($this->processors as $processor) {
            $record = call_user_func($processor, $record);
        }

        return $this->handler->handle($record);
    }

    /**
     * Gets all supported logging levels.
     *
     * @return array
     */
    public static function getLevels()
    {
        return array_flip(static::$levels);
    }

    /**
     * Gets the name of the logging level.
     *
     * @param  integer $level
     * @return string
     */
    public static function getLevelName($level)
    {
        if ( ! isset(static::$levels[$level])) {
            throw new InvalidArgumentException('Level "'.$level.'" is not defined, use one of: '.implode(', ', array_keys(static::$levels)));
        }

        return static::$levels[$level];
    }

    /**
     * Converts PSR-3 levels to Pandalog ones if necessary
     *
     * @param string|int Level number (pandalog) or name (PSR-3)
     * @return int
     */
    public static function toLevel($level)
    {
        if (is_string($level) && defined(__CLASS__.'::'.strtoupper($level))) {
            return constant(__CLASS__.'::'.strtoupper($level));
        }

        return $level;
    }

    /**
     * Adds a log record at an arbitrary level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  mixed   $level   The log level
     * @param  string  $message The log message
     * @param  array   $trace The log trace
     * @return Boolean
     */
    public function log($level, $message, array $trace = array())
    {
        $level = static::toLevel($level);

        return $this->addRecord($level, $message, $trace);
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $trace The log trace
     * @return Boolean Whether the record has been processed
     */
    public function debug($message, array $trace = array())
    {
        return $this->addRecord(static::DEBUG, $message, $trace);
    }

    /**
     * Adds a log record at the INFO level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $trace The log trace
     * @return Boolean Whether the record has been processed
     */
    public function info($message, array $trace = array())
    {
        return $this->addRecord(static::INFO, $message, $trace);
    }

    /**
     * Adds a log record at the NOTICE level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $trace The log trace
     * @return Boolean Whether the record has been processed
     */
    public function notice($message, array $trace = array())
    {
        return $this->addRecord(static::NOTICE, $message, $trace);
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $trace The log trace
     * @return Boolean Whether the record has been processed
     */
    public function warning($message, array $trace = array())
    {
        return $this->addRecord(static::WARNING, $message, $trace);
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $trace The log trace
     * @return Boolean Whether the record has been processed
     */
    public function error($message, array $trace = array())
    {
        return $this->addRecord(static::ERROR, $message, $trace);
    }

    /**
     * Adds a log record at the CRITICAL level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $trace The log trace
     * @return Boolean Whether the record has been processed
     */
    public function critical($message, array $trace = array())
    {
        return $this->addRecord(static::CRITICAL, $message, $trace);
    }

    /**
     * Adds a log record at the ALERT level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $trace The log trace
     * @return Boolean Whether the record has been processed
     */
    public function alert($message, array $trace = array())
    {
        return $this->addRecord(static::ALERT, $message, $trace);
    }

    /**
     * Adds a log record at the EMERGENCY level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $trace The log trace
     * @return Boolean Whether the record has been processed
     */
    public function emergency($message, array $trace = array())
    {
        return $this->addRecord(static::EMERGENCY, $message, $trace);
    }

    /**
     * Quick Initialization
     *
     * @param  array 
     * [
     *    'name' => 'logname',
     *    'path' => '/tmp/test.log',
     *    'product' => 'supply',
     *    'module'  => 'order',
     * ]
     * @return logger
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public static function quickInit($params)
    {
        static $logger = [];
        $md5Key = md5(json_encode($params));
        if ( ! isset($logger[$md5Key])) {
            $params['product'] = isset($params['product']) ? $params['product'] : '';
            $params['module']  = isset($params['module']) ? $params['module'] : '';
            $hander = new \Pandalog\Handler\FileHandler();
            $hander->useDaily($params['path']);
            $processor = new \Pandalog\Processor\YmtProcessor($params['product'], $params['module']);
            $logger[$md5Key] = new Logger($params['name'], $hander, [$processor]);
        }   

        return $logger[$md5Key];
    }

}
