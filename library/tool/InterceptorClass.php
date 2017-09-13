<?php

/**
 * 拦截器
 */

namespace Ipcheck\Tool;

class InterceptorClass
{
    public $ipInfo = array();                   // 请求IP的信息
    public $DBHandler = null;                   // 数据库操作对象

    public $useInterceptor = true;              // 是否启用拦截器
    public $autoAddToBlackList = false;         // 是否自动加入黑名单
    public $ruleChain = array(                  // 过滤规则链
        'RuleA',
        'RuleB',
        'RuleC',
    );

    public function __construct($ipInfo, $DBHandler)
    {
        $this->ipInfo = $ipInfo;
        $this->DBHandler = $DBHandler;

        $this->useInterceptor = getConf('UseInterceptor');
        $this->autoAddToBlackList = getConf('AutoAddToBlackList');
        $this->ruleChain = getConf('RuleChain');
    }

    /**
     * 拦截不符合规则的IP，拦截成功则返回true
     * @return bool
     */
    public function Filter()
    {
        if (empty($ipInfo) || !$this->useInterceptor) {
            return false;
        }

        $res = false;
        foreach ($this->ruleChain as $rule) {
            $res = $this->DBHandler->interceptor($rule);
            if ($res) {
                // TODO:记录拦截信息
                break;
            }
        }
        return $res;
    }
}