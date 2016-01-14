<?php 

namespace Pandalog\Handler;

/**
 * Hander Interface
 *
 * @package Pandalog
 * @subpackage Interface
 * @author Sherlock Ren <sherlock_ren@icloud.com>
 */
interface HandlerInterface         
{
    /**
     * handel function
     *
     * @param  array $data
     * @return array
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function handle(array $data);   
}
