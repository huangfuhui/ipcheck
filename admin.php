<?php
/**
 * ipcheck后台管理入口
 */

// 注册类自动加载器
require './library/tool/AutoloadClass.php';
spl_autoload_register('Ipcheck\Tool\AutoloadClass::autoload');

// 加载全局函数
require './library/common/function.php';

// 准备进入系统管理首页
(new \Ipcheck\Admin\IndexController())->index();