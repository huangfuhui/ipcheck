<?php

/**
 * 数据库操作对象工厂，自动返回当前系统配置的数据库操作对象
 */

namespace Ipcheck;

use Ipcheck\Tool\InitializeClass;

class DBHandlerFactory
{
    private $DBType = '';
    private $ipInfo = array();

    public function __construct()
    {
        // 获取数据库类型，默认是Redis
        $this->DBType = getConf('DBType');
        empty($this->DBType) && $this->DBType = 'Redis';

        $this->ipInfo = (new InitializeClass())->getIpInfo();
    }

    /**
     * 获取数据库操作对象
     * @return mixed 返回数据库操作对象
     */
    public function getDBHandler()
    {
        $DBHandler = null;
        switch (strtolower($this->DBType)) {
            case 'redis' : {    // 初始化Redis连接，并返回Redis的操作对象
                $redisConnect = RedisConnectionSingle::getRedis();
                $DBHandler = new RedisCheckOptionClass($redisConnect, $this->ipInfo);
            }
                break;
        }
        return $DBHandler;
    }
}