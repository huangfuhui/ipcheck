<?php

/**
 * 系统日志工具
 */

namespace Ipcheck\Tool;

class LogClass
{

    /**
     * 撰写系统日志
     * @param string $logs
     * @return bool|int
     */
    public static function log($logs = '')
    {
        $logPath = getConf('LogPath');
        '/' == substr($logPath, -1) ? null : $logPath .= '/';
        $logName = getConf('LogName');

        return file_put_contents($logPath . $logName, $logs, FILE_APPEND);
    }

    /**
     * 根据不同的日志模板渲染不同样式的日志
     * @param array $logData
     * @param int $templateType
     * @return string
     */
    public static function logTemplate($logData = array(), $templateType = 1)
    {
        $result = '';
        if (empty($logData)) {
            return $result;
        }

        switch ($templateType) {
            case 1 : {
                $result = self::setLogResult($logData);
            }
                break;
        }
        return $result;
    }

    /**
     * 渲染日志结果
     * @param $dataArr
     * @param string $baseResult
     * @return string
     */
    private static function setLogResult($dataArr, $baseResult = '')
    {
        $baseResult .= "[Time]: " . date('Y-m-d H:i:s', time()) . PHP_EOL;
        foreach ($dataArr as $k => $v) {
            $baseResult .= "[$k]: $v" . PHP_EOL;
        }
        return $baseResult . PHP_EOL . PHP_EOL;
    }
}