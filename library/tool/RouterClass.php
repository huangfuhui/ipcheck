<?php
/**
 * 路由器工具
 */

namespace Ipcheck\Tool;

class RouterClass
{
    private $action = '';                           // 用户请求的动作
    private $controller = '';                       // 用户请求的控制器

    public function __construct()
    {
        // 获取需要操作的控制器，默认index
        empty($_GET['c']) ? $this->controller = 'Index' : $this->controller = ucfirst(strtolower($_GET['c']));

        // 获取需要操作的action，默认index
        empty($_GET['a']) ? $this->action = 'index' : $this->action = strtolower($_GET['a']);
    }

    /**
     * 分发请求
     */
    public function dispatch()
    {
        // 判断是API请求还是控制器请求
        if (substr_count($this->controller, 'api')) {
            $controllerClass = 'Ipcheck\Admin\API\\' . $this->controller;
        } else {
            $controllerClass = 'Ipcheck\Admin\Controller\\' . $this->controller . 'Controller';
        }

        // 判断类是否存在
        if (!class_exists($controllerClass)) {
            $controllerClass = 'Ipcheck\Admin\Controller\IndexController';
        }

        // 使用反射实例化控制器或API类，调用目标action
        try {
            $reflection = new \ReflectionClass($controllerClass);
            $controllerInstance = $reflection->newInstance();
            if ($reflection->hasMethod($this->action)) {
                $action = $reflection->getMethod($this->action);
                $action->invoke($controllerInstance);
            } else {
                // TODO：action 不存在
                echo 'Method not found';
            }
        } catch (\ReflectionException $e) {
            // TODO:异常处理
            echo $e->getMessage();
        }
    }
}