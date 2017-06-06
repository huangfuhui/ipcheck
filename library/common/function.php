<?php
//---------------------------------------------------------------------------------
// 系统通用函数
//---------------------------------------------------------------------------------

/**
 * 获取系统配置文件，并返回目标配置值
 * @param string $configName 配置名称
 * @return string 配置值
 */
function getConf($configName = '')
{
    if (empty($configName)) {
        return '';
    }

    $configs = include dirname(__DIR__) . '/config/sysconfig.php';
    if (in_array($configName, $configs)) {
        return $configs[$configName];
    } else {
        return '';
    }
}