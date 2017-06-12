<?php
/**
 * 路由器工具
 */

namespace Ipcheck\Tool;

use Ipcheck\Admin\IndexController;

class RouterClass
{
    private $action = null;

    public function __construct()
    {
        // 获取需要操作的action，默认index
        empty($_GET['a']) ? $this->action = 'index' : $this->action = $_GET['a'];
    }

    /**
     * 分发请求
     */
    public function dispatch()
    {
        $controller = new IndexController();
        method_exists($controller, $this->action) ? $callMethod = 'index' : null;
        call_user_func_array(array($controller, $this->action), array());
    }
}