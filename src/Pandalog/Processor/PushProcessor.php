<?php 

namespace Pandalog\Processor;

/**
 * PushProcessor
 *
 * @package Pandalog
 * @subpackage Processor
 * @author Sherlock Ren <sherlock_ren@icloud.com>
 */
class PushProcessor
{
    /**
     * push keys
     *
     * @var array
     */
    protected $keys = [];

    /**
     * push value
     *
     * @var array
     */
    protected $values = [];

    /**
     * __construct
     *
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function __construct(array $keys, array $values)
    {
        $this->keys   = $keys;  
        $this->values = $values;  
    }

    /**
     * __invoke function
     *
     * @param  array $data
     * @return array
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function __invoke(array $data)
    {
        if (count($this->keys) != count($this->values)) {
            throw new \InvalidArgumentException('The scope of the number of keys and values are not equal');
        }

        foreach ($this->keys as $k => $v) {
            $data[$v] = $this->values[$k];
        }

        return $data;
    }

}
