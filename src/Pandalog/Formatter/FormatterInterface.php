<?php 

namespace Pandalog\Formatter;

/**
 * Interface of format data
 *
 * @package Pandalog
 * @subpackage Interface
 * @author Sherlock Ren <sherlock_ren@icloud.com>
 */
interface FormatterInterface         
{
    /**
     * formar function
     *
     * @param  array $data
     * @return array
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function format(array $data);   
}
