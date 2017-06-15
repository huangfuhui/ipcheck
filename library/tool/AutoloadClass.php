<?php

/**
 * 类自动载入工具
 */

namespace Ipcheck\Tool;

class AutoloadClass
{
    /**
     * Ipcheck工具类自动加载
     * @param $class
     */
    public static function autoLoad($class)
    {
        $classInfo = explode('\\', $class);
        if (in_array('Tool', $classInfo)) {                         // 载入 '/library/tool' 文件夹下的工具类
            require __DIR__ . DIRECTORY_SEPARATOR . $classInfo[2] . '.php';
        } else {                                                    // 载入 '/library' 目录下的类
            require dirname(__DIR__) . DIRECTORY_SEPARATOR . $classInfo[1] . '.php';
        }
        // TODO:文件 NOT_FOUND 异常处理
    }

    /**
     * 后台类自动载入
     * @param $class
     */
    public static function autoloadForAdmin($class)
    {
//        echo $class . '<br/>';
        // TODO:类路径匹配待优化
        $classInfo = explode('\\', $class);
        if (in_array('Tool', $classInfo)) {                         // 载入 '/library/tool' 文件夹下的工具类
            require __DIR__ . DIRECTORY_SEPARATOR . $classInfo[2] . '.php';
        } elseif (substr_count($class, 'Ipcheck\Install\\')) {      // 载入 '/library/install' 文件夹下的安装器
            require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . $classInfo[2] . '.php';
        } elseif (substr_count($class, 'api') ||
            substr_count($class, 'API')
        ) {                                                         // 载入 '/library/admin/API' 文件夹下的API
            require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR .
                'API' . DIRECTORY_SEPARATOR . substr($classInfo[3], 0, -3) . 'API.php';
        } elseif (count($classInfo) == 2) {                         // 载入 '/library' 目录下的类
            require dirname(__DIR__) . DIRECTORY_SEPARATOR . $classInfo[1] . '.php';
        } else {                                                    // 载入 '/library/admin' 文件夹下的控制器
            if (in_array('Admin', $classInfo)) {
                require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . $classInfo[3] . '.php';
            } else {
                require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . ucfirst($class) . '.php';
            }
        }
        // TODO:文件 NOT_FOUND 异常处理
    }
}