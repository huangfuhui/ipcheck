<?php

/**
 * 使用Redis实现IP过滤和监测操作
 */

namespace Ipcheck;

class RedisCheckOptionClass implements CheckOption
{
    private $redis = '';            // 数据库对象
    private $ipInfo = '';           // 访问者信息

    public function __construct($redis, $ipInfo)
    {
        $this->redis = $redis;
        $this->ipInfo = $ipInfo;
    }

    /**
     * 记录或更新当前访问者的信息
     * 数据库：0
     * 键：IP_[访问时间戳]
     * 值:json_encode(
     *      array(
     *       'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],                           // 访问地址
     *       'REMOTE_PORT' => $_SERVER['REMOTE_PORT'],                           // 访问者源端口
     *       'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],                   // 访问者的UA
     *       'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],                     // 请求方法
     *       'REQUEST_URI' => $_SERVER['REQUEST_URI'],                           // 请求的URI
     *       'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'],                           // 请求的文件名称
     *       'REQUEST_URL' => $_SERVER['SERVER_ADDR'] . $_SERVER['REQUEST_URI'], // 访问的URL
     *       'REQUEST_TIME' => time(),                                           // 访问的时间
     *       'REQUEST_SCHEME' => $_SERVER['REQUEST_SCHEME'],                     // 请求协议
     *       'SERVER_PROTOCOL' => $_SERVER['SERVER_PROTOCOL'],                   // 服务器响应协议
     *       'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],                           // 服务器地址
     *       'SERVER_PORT' => $_SERVER['SERVER_PORT'],                           // 服务器端口
     *       'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'],                   // 服务器信息
     *      );
     * )
     */
    public function recordAccessInfo()
    {
    }

    /**
     * 关闭Redis数据库连接
     */
    public function closeConnection()
    {
        RedisConnectionSingle::closeRedis();
    }
}