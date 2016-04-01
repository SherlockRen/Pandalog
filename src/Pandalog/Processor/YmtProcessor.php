<?php 

namespace Pandalog\Processor;

/**
 * YmtProcessor
 *
 * @package Pandalog
 * @subpackage Processor
 * @author Sherlock Ren <sherlock_ren@icloud.com>
 */
class YmtProcessor
{
    /**
     * server
     *
     * @var array
     */
    protected $server = [];

    /**
     * product name
     *
     * @var string
     */
    protected $product = '';

    /**
     * module name
     *
     * @var string
     */
    protected $module = '';

    /**
     * __construct
     *
     * @param  void
     * @return void
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    public function __construct($product, $module)
    {
        $this->product = $product;
        $this->module  = $module;
        $this->server  = $_SERVER;
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
        $data['logid']     = $this->getLogId();
        $data['caller_ip'] = $this->getClientIp();
        $data['host_ip']   = isset($server['SERVER_ADDR']) ? $server['SERVER_ADDR'] : '0.0.0.0';
        $data['product']   = $this->product;
        $data['module']    = $this->module;
        return $data;
    }

    /**
     * get or create log id
     *
     * @return string
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    protected function getLogId()
    {
        $server  = $this->server;
        $request = $_REQUEST;
        if (
            isset($server['HTTP_X_YMT_LOGID']) 
            && intval(trim($server['HTTP_X_YMT_LOGID'])) !== 0
        ) {
            return trim($server['HTTP_X_YMT_LOGID']);
        }
        if (
            isset($request['logid']) 
            && intval($request['logid']) !== 0
        ) {
            return trim($request['logid']);
        }
        $array  = gettimeofday();
        if (isset($server['REMOTE_ADDR'])) {
            mt_srand(ip2long($server['REMOTE_ADDR']));
        }
        $logId  = sprintf('%04d', mt_rand(0, 999));
        $logId .= sprintf('%03d', rand(0, 999));
        $logId .= sprintf('%04d', $array['usec'] % 10000);
        $logId .= sprintf('%04d', $array['sec'] % 3600);
        return $logId;
    }

    /**
     * get client ip
     *
     * @param  void
     * @return void
     * @author Sherlock Ren <sherlock_ren@icloud.com>
     */
    protected function getClientIp()
    {
        $server   = $this->server;
        $clientIp = array_key_exists('HTTP_X_REAL_IP', $server) 
            ? $server['HTTP_X_REAL_IP'] 
            : (array_key_exists('HTTP_X_FORWARDED_FOR', $server) 
            ? $server['HTTP_X_FORWARDED_FOR'] 
            : (array_key_exists('REMOTE_ADDR', $server) 
            ? $server['REMOTE_ADDR'] : '0.0.0.0'));
        //识别代理
        if (
            isset($server['HTTP_X_FORWARDED_FOR']) 
            && preg_match('/^10\./', $clientIp) 
            && preg_match('/([\d\.]+)(\, 10\.([\d\.]+)){1,}$/', $server['HTTP_X_FORWARDED_FOR'], $res)
        ) {
            $clientIp = $res[1];
        }
        return $clientIp;
    }

} // END class LogIdProcessor
