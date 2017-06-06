<?php

/**
 * 监测请求IP的合法性，记录访问日志，同时对不合法的请求进行处理。
 */

namespace Ipcheck;

use Ipcheck\Tool;

class IpcheckClass extends Tool\InitializeClass
{
    public function __construct()
    {
        parent::__construct();
    }

    public function check()
    {
        $redis = RedisConnectionSingle::getRedis();
        var_dump($redis);
    }
}