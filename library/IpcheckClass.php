<?php

/**
 * 监测请求IP的合法性，记录访问日志，同时对不合法的请求进行处理。
 */

namespace Ipcheck;

use Ipcheck\Tool\InitializeClass;
use Ipcheck\Tool\DataRenderClass;

class IpcheckClass extends InitializeClass
{
    private $DBHandler = null;          // 数据库操作对象

    private $TimeUsed = 0;              // 记录一次请求所花费的时间，单位为毫秒

    public function __construct()
    {
        $this->TimeUsed = microtime(true);

        // 获取数据库操作对象
        $this->DBHandler = (new DBHandlerFactory($this->getIpInfo()))->getDBHandler();

        parent::__construct();
    }

    public function check()
    {
        // 记录或更新当前访问者的IP信息
        $this->DBHandler->recordAccessInfo();

        // 记录或更新当前访问者的访问次数
        $this->DBHandler->recordAccessTimes();

        // 黑名单过滤
        if ($this->DBHandler->isBanIP()) {
            $baseResult = true;
        } else {
            // 检测当前IP的访问频率，如果大于用户设置的访问间隔那就采取对应的措施，同时记录访问的有效性
            $baseResult = $this->DBHandler->checkFrequency();

            // 判断是否开启拦截器
            if (getConf('UseInterceptor')) {
                $rules = getConf('RuleChain');
                // 对IP进行规则链过滤
                foreach ($rules as $rule) {
                    $interceptorResult = $this->DBHandler->interceptor($rule);
                    if ($interceptorResult) {
                        break;
                    }
                }
            }
        }

        if ($baseResult || $interceptorResult) {
            // 记录访问的无效性
            $this->DBHandler->recordAccessValidity(false);
        } else {
            // 记录访问的有效性
            $this->DBHandler->recordAccessValidity(true);
        }

        // 记录一次请求使用时间
        $this->TimeUsed = round((microtime(true) - $this->TimeUsed) * 1000, 2);
        $this->DBHandler->recordTimeUsed($this->TimeUsed);

        // 关闭当前数据库连接
        $this->DBHandler->closeConnection();

        if ($baseResult || $interceptorResult) {
            // 返回禁止访问页面
            $dataRenderClass = new DataRenderClass();
            $dataRenderClass->display('ACCESS_DENIED');
            die;
        }
    }
}