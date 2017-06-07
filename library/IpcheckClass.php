<?php

/**
 * 监测请求IP的合法性，记录访问日志，同时对不合法的请求进行处理。
 */

namespace Ipcheck;

use Ipcheck\Tool;

class IpcheckClass extends Tool\InitializeClass
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
        // 1.记录或更新当前访问者的IP信息
        $this->DBHandler->recordAccessInfo();


        // 关闭当前数据库连接
        $this->DBHandler->closeConnection();
    }
}