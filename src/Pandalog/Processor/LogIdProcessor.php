<?php 

namespace Pandalog\Processor;

/**
 * LogIdProcessor
 *
 * @package Pandalog
 * @subpackage Processor
 * @author Sherlock Ren <sherlock_ren@icloud.com>
 */
class LogIdProcessor
{
    /**
     * __invoke function
     *
     * @param  array $data
     * @return array
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function __invoke(array $data)
    {
        $data['logid'] = $this->createLogId();
        return $data;
    }

    /**
     * create log id
     *
     * @return string
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    protected function createLogId()
    {
        $array  = gettimeofday();
        if (isset($_SERVER['REMOTE_ADDR'])) {
            mt_srand(ip2long($_SERVER['REMOTE_ADDR']));
        }
        $logId  = sprintf('%04d', mt_rand(0, 999));
        $logId .= sprintf('%03d', rand(0, 999));
        $logId .= sprintf('%04d', $array['usec'] % 10000);
        $logId .= sprintf('%04d', $array['sec'] % 3600);
        return $logId;
    }
} // END class LogIdProcessor
