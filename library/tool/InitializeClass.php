<?php
/**
 * 初始化系统，解析请求
 */

namespace Ipcheck\Tool;

class InitializeClass
{
    public static $IPINFOS = array();

    public function __construct()
    {
        // 设置系统默认时区
        $defaultTimezone = getConf('DefaultTimezone');
        if (empty($defaultTimezone)) {
            date_default_timezone_set('Asia/Shanghai');
        } else {
            date_default_timezone_set($defaultTimezone);
        }

        // 测试能否获取访问者信息
        if (empty($_SERVER['REMOTE_ADDR'])) {
            // TODO:记录日志——不能获取目标ip地址
            exit;
        }

        self::$IPINFOS = $this->getIpInfo();
    }

    /**
     * 解析请求，获取客户端的相关信息
     * @return array 返回解析后的IP请求信息
     */
    private function getIpInfo()
    {
        return array(
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],                           // 访问地址
            'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],                   // 访问者的UA
            'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],                     // 请求方法
            'REQUEST_URI' => $_SERVER['REQUEST_URI'],                           // 请求的URI
            'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'],                           // 请求的文件名称
            'REQUEST_URL' => $_SERVER['SERVER_ADDR'] . $_SERVER['REQUEST_URI'], // 访问的URL
            'REQUEST_TIME' => time(),                                           // 访问的时间
        );
    }
}