<?php
/**
 * Redis数据库连接操作类，采用单例模式设计。
 */

namespace Ipcheck;


class RedisConnectionSingle implements DBAdapter
{
    private static $REDIS_CONNECTION = '';      // 全局单一Redis连接对象

    private $redisURL = '127.0.0.1';            // Redis连接地址
    private $redisPort = '6379';                // Redis端口

    /**
     * 私有化构造函数，完成数据库配置的读取
     */
    private function __construct()
    {
        $DBAddress = getConf('DBAddress');
        $DBPort = getConf('DBPort');
        if (!empty($DBAddress)) {
            $this->redisURL = $DBAddress;
        }
        if (!empty($DBPort)) {
            $this->redisPort = $DBPort;
        }
    }

    /**
     * 获取Redis数据库连接
     * @param string $address 数据库地址
     * @param int $port 数据库端口
     * @param string $userName 数据库用户名
     * @param string $password 数据库密码
     * @return mixed 若成功则返回数据库连接
     */
    public function getConnection($address, $port, $userName, $password)
    {
        try {
            $redis = new Redis();
            $redisConnection = $redis->connect($this->redisURL, $this->redisPort);
        } catch (\Exception $e) {
            // TODO:异常处理和日志记录
        } finally {
            if ($redisConnection) {
                return $redis;
            } else {
                return null;
            }
        }
    }

    /**
     * 关闭Redis数据库连接
     */
    public function close()
    {
        empty(self::$REDIS_CONNECTION) || self::$REDIS_CONNECTION->close();
    }

    /**
     * 获取Redis数据库对象
     * @return Redis 返回数据库连接对象
     */
    public static function getRedis()
    {
        if (!empty(self::$REDIS_CONNECTION)) {
            return self::$REDIS_CONNECTION;
        } else {
            self::$REDIS_CONNECTION = new self();
            return self::$REDIS_CONNECTION;
        }
    }
}