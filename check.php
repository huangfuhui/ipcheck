<?php
/**
 * ipcheck工具
 */

// 注册类自动加载器
require './library/tool/AutoloadClass.php';
spl_autoload_register('Ipcheck\Tool\AutoloadClass::autoload');

// 加载全局函数
require './library/common/function.php';

// 执行IP监测过滤
require './library/IpcheckClass.php';
(new Ipcheck\IpcheckClass())->check();
