<?php
/**
 * ipcheck后台管理入口
 */

// 注册类自动加载器
require './library/tool/AutoloadClass.php';
spl_autoload_register('Ipcheck\Tool\AutoloadClass::autoloadForAdmin');

// 加载全局函数
require './library/common/function.php';

// 执行系统初始化安装
if (!file_exists(__DIR__ . '/library/install/install.lock')) {
    $installResult = (new \Ipcheck\Install\InstallOptionClass())->install();
    if (!$installResult) {
        echo '数据库安装失败！';
        exit;
    }
}

// 准备进入后台，使用路由工具对请求进行分发
(new \Ipcheck\Tool\RouterClass())->dispatch();