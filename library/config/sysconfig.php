<?php
/**
 * 系统配置文件
 */

return array(
    // 设置系统时区，默认'Asia/Shanghai'
    'DefaultTimezone' => 'Asia/Shanghai',

    // 配置数据库服务器
    'DBType' => 'Redis',                            // 数据库类型，目前仅支持Redis
    'DBAddress' => 'localhost',                     // 数据库地址
    'DBPort' => '6379',                             // 数据库端口
    'DBUserName' => '',                             // 数据库用户名
    'DBPassword' => '',                             // 数据库密码
    'SaltKey' => 'Ipcheck',                         // 数据库加密盐值

    // 系统日志配置
    'LogPath' => './',                              // 日志路径
    'LogName' => 'ipcheck.log',                     // 日志名称
);