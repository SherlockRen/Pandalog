<?php

namespace Pandalog\Handler;

use Pandalog\Formatter\JsonFormatter;
use Pandalog\Handler\HandlerInterface;
use Pandalog\Formatter\FormatterInterface;

/**
 * Filehandler
 *
 * @package Pandalog
 * @subpackage Handler
 * @author Sherlock Ren <sherlock_ren@icloud.com>
 */
class FileHandler implements HandlerInterface
{
    /**
     * file link
     *
     * @var string
     */
    protected $link = '';

    /**
     * file lock
     *
     * @var boolean
     */
    protected $lock = true;

    /**
     * file stream
     *
     * @var resource
     */
    protected $stream = null;

    /**
     * formatter
     *
     * @var resource
     */
    protected $formatter;

    /**
     * dir created
     *
     * @var boolean
     */
    protected $dirCreated = false;

    /**
     * __construct
     *
     * @param  string $link 
     * @return void
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function __construct($link = '', FormatterInterface $formatter = null)
    {
        if (is_resource($formatter)) {
            $this->formatter = $formatter;
        }
        $this->link = $link;
    }

    /**
     * set formatter
     *
     * @param  resource $formatter
     * @return $this
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function setFormattter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * set link
     *
     * @param  string $link 
     * @return $this
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * set lock
     *
     * @param  boolean $lock
     * @return $this
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function setLock($lock)
    {
        $this->lock = $lock;
        return $this;
    }

    /**
     * use daily
     *
     * @param  string $link
     * @return $this
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function useDaily($link)
    {
        $dirname  = dirname($link);
        $basename = basename($link);
        if ( ! $basename) {
            throw new \InvalidArgumentException('invalid link: not find basename');
        }
        $dateString = date('Y-m-d');
        $basename   = strpos($basename, '.') === false ? $basename . $dateString : str_replace('.', '-' . $dateString . '.', $basename);
        $this->link = $dirname . '/' . $basename;
        return $this;
    }

    /**
     * handle
     *
     * @param  array $data log msg
     * @return boolean
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function handle(array $data)
    {
        if ( ! is_resource($this->stream)) {
            if ( ! $this->link) {
                throw new \LogicException('Missing stream link, the stream can not be opened. This may be caused by a premature call to close().');
            }
            $res = $this->createDir();
            if ( ! $res) {
                return false;
            }
            $this->stream = fopen($this->link, 'a');
            @chmod($this->link, 0644);
        }
        if ($this->lock) {
            flock($this->stream, LOCK_EX);
        }
        if ( ! is_resource($this->formatter)) {
            $this->formatter = new JsonFormatter();
        }
        $data = $this->formatter->format($data);
        $res  = fwrite($this->stream, $data . PHP_EOL);
        if ($this->lock) {
            flock($this->stream, LOCK_UN);
        }
        return (boolean) $res;
    }

    /**
     * __destruct
     *
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function __destruct()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
        $this->stream = null;
    }

    /**
     * create dir
     *
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    private function createDir()
    {
        if ( ! $this->dirCreated) {
            $dirname = dirname($this->link);
            if (is_dir($dirname)) {
                return true;
            }
            if ( ! $dirname) {
                return false;
            }
            $res = mkdir($dirname, 0777, true);
            if ( ! $res) {
                return false;
            }
        }
        $this->dirCreated = true;
        return true;
    }
}
