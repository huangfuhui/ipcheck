<?php

/**
 * 监测请求IP的合法性，记录访问日志，同时对不合法的请求进行处理。
 */

namespace Ipcheck;

use Ipcheck\Tool\InitializeClass;
use Ipcheck\Tool\DataRenderClass;

class IpcheckClass extends InitializeClass
{
    private $DBHandler = null;            // 数据库操作对象

    public function __construct()
    {
        // 获取数据库操作对象
        $this->DBHandler = (new DBHandlerFactory())->getDBHandler();

        parent::__construct();
    }

    public function check()
    {
        // 记录或更新当前访问者的IP信息
        $this->DBHandler->recordAccessInfo();

        // 记录或更新当前访问者的访问次数
        $this->DBHandler->recordAccessTimes();

        // 检测当前IP的访问频率，如果大于用户设置的访问间隔那就采取对应的措施，同时记录访问的有效性
        $res = $this->DBHandler->checkFrequency();
        if ($res) {
            // 记录访问的有效性
            $this->DBHandler->recordAccessValidity(false);

            // 返回禁止访问页面
            $dataRenderClass = new DataRenderClass();
            $dataRenderClass->display('ACCESS_DENIED');
        } else {
            // 记录访问的有效性
            $this->DBHandler->recordAccessValidity(true);
        }

        // 关闭当前数据库连接
        $this->DBHandler->closeConnection();
    }
}