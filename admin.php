<?php
/**
 * ipcheck后台管理入口
 */

// 注册类自动加载器
require './library/tool/AutoloadClass.php';
spl_autoload_register('Ipcheck\Tool\AutoloadClass::autoloadForAdmin');

// 加载全局函数
require './library/common/function.php';

// 准备进入后台，使用路由工具对请求进行分发
(new \Ipcheck\Tool\RouterClass())->dispatch();