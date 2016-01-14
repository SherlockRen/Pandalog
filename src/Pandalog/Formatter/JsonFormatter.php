<?php

namespace Pandalog\Formatter;

use Pandalog\Formatter\FormatterInterface;

/**
 * JsonFormatter
 *
 * @package Pandalog
 * @subpackage Formatter
 * @author Sherlock Ren <sherlock_ren@icloud.com>
 */
class JsonFormatter implements FormatterInterface
{

    /**
     * __construct
     *
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function __construct()
    {
        // do nothing
    }

    /**
     * format function
     *
     * @param  void
     * @return void
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function format(array $data)
    {
        return json_encode($data);   
    }
}
