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

    $configs1 = include dirname(__DIR__) . '/config/sysconfig.php';
    $configs2 = include dirname(__DIR__) . '/config/usrconfig.php';
    $configs = array_merge($configs1, $configs2);
    if (key_exists($configName, $configs)) {
        return $configs[$configName];
    } else {
        return '';
    }
}

/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo = true, $label = null, $strict = true)
{
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    } else
        return $output;
}

/**
 * 获取系统设置的访问时间单位的时间戳表示
 * @return int
 */
function getTimeUnitAsTimestamp()
{
    $timeUnit = getConf('TimeUnit');
    switch (strtolower($timeUnit)) {
        case 'm' : {
            $timeUnit = 60;
        }
            break;
        case 'h' : {
            $timeUnit = 3600;
        }
            break;
        case 'd' : {
            $timeUnit = 86400;
        }
            break;
        default : {
            $timeUnit = 60;
        }
    }
    return $timeUnit;
}

/**
 * SESSION操作函数，用于获取、设置和删除SESSION
 * @param string $key 键名
 * @param string $value 键值，键值为空字符串时获取SESSION，键值为null时删除SESSION，键值非空时设置SESSION
 * @return string
 */
function session($key, $value = '')
{
    if (empty($key)) {
        return '';
    }

    // 开启session
    session_start();

    if ('' === $value) {                    // 获取session
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return '';
        }
    } elseif (is_null($value)) {            // 删除session
        unset($_SESSION[$key]);
    } else {                                // 设置session
        $_SESSION[$key] = $value;
    }
}