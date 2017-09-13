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
            // 记录日志——不能获取目标ip地址
            $logArr = array(
                'ERROR' => '无法获取目标IP地址！'
            );
            $logTemplate = LogClass::logTemplate($logArr);
            LogClass::log($logTemplate);
            exit;
        }

        self::$IPINFOS = $this->getIpInfo();
    }

    /**
     * 解析请求，获取客户端的相关信息
     * @return array 返回解析后的IP请求信息
     */
    public function getIpInfo()
    {
        return array(
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],                           // 访问地址
            'REMOTE_PORT' => $_SERVER['REMOTE_PORT'],                           // 访问者源端口
            'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],                   // 访问者的UA
            'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],                     // 请求方法
            'REQUEST_URI' => $_SERVER['REQUEST_URI'],                           // 请求的URI
            'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'],                           // 请求的文件名称
            'REQUEST_URL' => $_SERVER['SERVER_ADDR'] . $_SERVER['REQUEST_URI'], // 访问的URL
            'REQUEST_TIME' => time(),                                           // 访问的时间
            'REQUEST_SCHEME' => $_SERVER['REQUEST_SCHEME'],                     // 请求协议
            'SERVER_PROTOCOL' => $_SERVER['SERVER_PROTOCOL'],                   // 服务器响应协议
            'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],                           // 服务器地址
            'SERVER_PORT' => $_SERVER['SERVER_PORT'],                           // 服务器端口
            'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'],                   // 服务器信息
        );
    }

    /**
     * 引入HTML文件，该方法输出HTML文件后就退出程序，后续不再有输出和执行了，
     * 因此在控制器中需要把数据渲染放在逻辑的最后执行。
     * @param string $html 需要引入的HTML文件名，无需后缀，大小写敏感
     * @param array $htmlData 需要渲染的数据
     */
    public function display($html = '', $htmlData = array())
    {
        $dataRender = new DataRenderClass();
        $dataRender->display($html, $htmlData);
        exit;
    }
}