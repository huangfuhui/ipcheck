<?php
/**
 * ipcheck工具
 */

// 根目录
define('ROOT_PATH', dirname(__FILE__));

// 注册类自动加载器
require ROOT_PATH . '/library/tool/AutoloadClass.php';
spl_autoload_register('Ipcheck\Tool\AutoloadClass::autoload');

// 加载全局函数
require ROOT_PATH . '/library/common/function.php';

// 执行IP监测过滤
(new Ipcheck\IpcheckClass())->check();

// 注销类自动加载器
spl_autoload_unregister('Ipcheck\Tool\AutoloadClass::autoload');
