<?php
/**
 * ipcheck后台管理入口
 */

// 根目录
define('ROOT_PATH', dirname(__FILE__));

// 注册类自动加载器
require ROOT_PATH . '/library/tool/AutoloadClass.php';
spl_autoload_register('Ipcheck\Tool\AutoloadClass::autoloadForAdmin');

// 加载全局函数
require ROOT_PATH . '/library/common/function.php';

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

// 注销类自动加载器
spl_autoload_unregister('Ipcheck\Tool\AutoloadClass::autoload');
