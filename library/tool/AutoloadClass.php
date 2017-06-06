<?php

/**
 * 类自动载入工具
 */

namespace Ipcheck\Tool;

class AutoloadClass
{
    public static function autoLoad($class)
    {
        $classInfo = explode(DIRECTORY_SEPARATOR, $class);
        if (in_array('Tool', $classInfo)) { // 载入 '/library/tool' 文件夹下的工具类
            require __DIR__ . DIRECTORY_SEPARATOR . $classInfo[2] . '.php';
        } else {    // 载入 '/library' 目录下的类
            require dirname(__DIR__) . DIRECTORY_SEPARATOR . $classInfo[1] . '.php';
        }
    }
}