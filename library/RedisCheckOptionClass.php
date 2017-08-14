<?php

/**
 * 使用Redis实现IP过滤和监测操作，该类实现的CheckOption接口主要服务Ipcheck
 * 工具的IP过滤和监测，而实现的CheckOptionAdmin接口主要为系统后台管理提供服务。
 */

namespace Ipcheck;

class RedisCheckOptionClass implements CheckOption, CheckOptionAdmin
{
    public $redis = '';                         // 数据库对象
    public $ipInfo = '';                        // 访问者信息

    private $violateBaseAccessRule = false;     // 当前IP是否违反基本访问规则

    public function __construct($redis, $ipInfo)
    {
        $this->redis = $redis;
        $this->ipInfo = $ipInfo;
    }

//-------------------------------Redis数据库使用记录------------------------------
    /**
     * 数据库编号|   使用备注
     * ---------------------------------------------------------
     * 0        |   专用于记录来访IP的信息  ip:access_record、ip:access_times、ip:effective_access、ip:invalid_access
     * 1        |   专门用于检测IP访问频率
     * 2        |   ip:ban_list
     * 3        |   拦截器专用
     * 4        |
     * 5        |
     * 6        |
     * 7        |
     * 8        |
     * 9        |
     * 10       |
     * 11       |
     * 12       |
     * 13       |   系统监控专用
     * 14       |   后台用户日志库
     * 15       |   后台用户信息库
     */
//-------------------------------Redis数据库使用记录------------------------------

//----------------------------------接口CheckOption实现----------------------------------------

    /**
     * 记录或更新当前访问者的信息，按照用户配置只记录最新的N条访问者信息，默认N=200
     *
     * 数据库：0
     * 键：IP_[访问时间戳]
     * 值:json_encode(
     *      array(
     *       'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],                           // 访问地址
     *       'REMOTE_PORT' => $_SERVER['REMOTE_PORT'],                           // 访问者源端口
     *       'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],                   // 访问者的UA
     *       'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],                     // 请求方法
     *       'REQUEST_URI' => $_SERVER['REQUEST_URI'],                           // 请求的URI
     *       'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'],                           // 请求的文件名称
     *       'REQUEST_URL' => $_SERVER['SERVER_ADDR'] . $_SERVER['REQUEST_URI'], // 访问的URL
     *       'REQUEST_TIME' => time(),                                           // 访问的时间
     *       'REQUEST_SCHEME' => $_SERVER['REQUEST_SCHEME'],                     // 请求协议
     *       'SERVER_PROTOCOL' => $_SERVER['SERVER_PROTOCOL'],                   // 服务器响应协议
     *       'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],                           // 服务器地址
     *       'SERVER_PORT' => $_SERVER['SERVER_PORT'],                           // 服务器端口
     *       'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'],                   // 服务器信息
     *      );
     * )
     * 类型：list
     */
    public function recordAccessInfo()
    {
        $this->redis->select(0);
        // JSON格式化访问信息
        $ipInfo = json_encode($this->ipInfo);
        // 更新访问记录队列，控制保留最新的N条信息
        $this->redis->lPush('ip:access_record', $ipInfo);
        $takeNewRecords = getConf('NewRecords');
        empty($takeNewRecords) ? $takeNewRecords = 199 : --$takeNewRecords;
        $this->redis->lTrim('ip:access_record', 0, $takeNewRecords);
    }

    /**
     * 更新当前IP的访问次数
     *
     * 数据库：0
     * 键：ip:access_times | 属性：IP | 值：+1 | 类型：sort set
     */
    public function recordAccessTimes()
    {
        $this->redis->select(0);
        $this->redis->zIncrBy('ip:access_times', 1, $this->ipInfo['REMOTE_ADDR']);
    }

    /**
     * 检测当前IP的访问频率是否符合用户设置的标准
     * @return bool 是则返回false，否则返回true
     *
     * 数据库：1
     * 键：IP | 值：当前IP请求时间的时间戳 | 类型：list
     */
    public function checkFrequency()
    {
        // 1.选择一号数据库
        $this->redis->select(1);

        // 2.更新当前IP的访问频率
        $this->redis->lPush($this->ipInfo['REMOTE_ADDR'], $this->ipInfo['REQUEST_TIME']);

        // 3.求当前IP总共访问次数
        $times = $this->redis->lLen($this->ipInfo['REMOTE_ADDR']);

        // 4.如果当前IP总访问次数大于系统设置的访问间隔，则进行IP频率检测
        $accessFrequency = getConf('AccessFrequency');      // 获取用户设置的访问频率，默认'10t/m'
        empty($accessFrequency) ? $accessFrequency = 10 : null;
        if ($times > $accessFrequency) {
            // 4.1求当前IP访问间隔内第一次访问的时间
            $last_time = $this->redis->lIndex($this->ipInfo['REMOTE_ADDR'], -1);
            // 4.2保留当前IP访问间隔内的每一次访问时间，方便下一次统计比较
            $this->redis->lTrim($this->ipInfo['REMOTE_ADDR'], 0, $accessFrequency - 1);
            // 4.3如果当前IP在规定的时间内访问频率超出标准，则返回true
            if ($this->ipInfo['REQUEST_TIME'] - $last_time < getTimeUnitAsTimestamp()) {
                // 标记当前IP违反基本访问规则
                $this->violateBaseAccessRule = true;
                return true;
            }
        }

        // 5.如果当前IP的访问频率正常则返回false
        return false;
    }

    /**
     * 判断是否是被禁IP
     * @param string $ip
     * @return bool 是则返回true，否则返回false
     */
    public function isBanIP($ip = '')
    {
        empty($ip) ? $ip = $this->ipInfo['REMOTE_ADDR'] : null;
        if (empty($ip)) {
            return true;
        }

        $this->redis->select(2);
        return $this->redis->sIsMember('ip:ban_list', $ip);
    }

    /**
     * 记录当前IP访问的有效性
     * @param bool $validity true表示有效，false表示无效
     *
     * 数据库：0
     * 键：ip:effective_access | 属性：date('y-m-d', time()) | 值：+1 | 类型：sort set
     * 键：ip:invalid_access   | 属性：date('y-m-d', time()) | 值：+1 | 类型：sort set
     */
    public function recordAccessValidity($validity = true)
    {
        $this->redis->select(0);
        if ($validity) {
            $this->redis->zIncrBy('ip:effective_access', 1, date('y-m-d', time()));
        } else {
            $this->redis->zIncrBy('ip:invalid_access', 1, date('y-m-d', time()));
        }
    }

    /**
     * 记录一次请求Redis操作耗费时间
     * @param int $timeUsed
     *
     * 数据库：13
     * 键：date('Y:m:d H:i', time())  | 值：一次请求Redis耗费时长   | 类型：list
     */
    public function recordTimeUsed($timeUsed = 0)
    {
        $this->redis->select(13);
        $key = date('Y:m:d H:00:00', time());
        $this->redis->lPush($key, $timeUsed);
    }

    /**
     * 拦截器
     * @param string $rule
     * @return bool
     */
    public function interceptor($rule)
    {
        $res = false;
        $autoAddToBlackList = getConf('AutoAddToBlackList');

        // 执行规则过滤，拦截违规IP
        switch ($rule) {
            case 'RuleA': {
                $res = $this->verifyRuleA();
            }
                break;
            case 'RuleB': {
                $res = $this->verifyRuleB();
            }
                break;
            case 'RuleC': {
                $res = $this->verifyRuleC();
            }
                break;
            case 'RuleD': {
                $res = $this->verifyRuleD();
            }
                break;
            default : {
            }
        }

        // 判断是否开启自动拉入黑名单开关
        if ($res && $autoAddToBlackList) {
            $this->banIP($this->ipInfo['REMOTE_ADDR']);
        }

        return $res;
    }

    /**
     * 关闭Redis数据库连接
     */
    public function closeConnection()
    {
        RedisConnectionSingle::closeRedis();
    }

//--------------------------------接口CheckOptionAdmin实现--------------------------------------

    /**
     * 禁用一组IP
     * @param array $ips 被禁用的IP数组
     *
     * 数据库2
     * 键：ip:ban_list | 属性：IP  | 值：0  | 类型：set
     */
    public function banIPs($ips)
    {
        if (!is_array($ips)) {
            return;
        }

        // 选择2号数据库
        $this->redis->select(2);
        $this->redis->delete('ip:ban_list');

        // 将禁用IP添加至有序集合
        foreach ($ips as $value) {
            empty($value) ? '' : $this->redis->sAdd('ip:ban_list', trim($value));
        }
    }

    /**
     * 禁用单个IP
     * @param string $ip
     *
     * 数据库：2
     * 键：ip:ban_list | 属性：IP  | 值：0  | 类型：set
     */
    public function banIP($ip)
    {
        if (empty($ip)) {
            return;
        }

        // 选择2号数据库
        $this->redis->select(2);
        // 将禁用IP添加至黑名单
        $this->redis->sAdd('ip:ban_list', trim($ip));
    }

    /**
     * 获取禁用IP列表
     * @return string
     */
    public function getBanIpList()
    {
        $this->redis->select(2);
        $ban_list = $this->redis->sMembers('ip:ban_list');
        $res = '';
        foreach ($ban_list as $value) {
            $res .= $value . PHP_EOL;
        }
        return $res;
    }

    /**
     * 获取被禁IP数量
     * @return int
     */
    public function getBanIpsCount()
    {
        $this->redis->select(2);
        $ban_list = $this->redis->sMembers('ip:ban_list');
        return count($ban_list);
    }

    /**
     * 判断当前用户是否登录成功
     * @return array|bool 如果用户已经登录则返回包含用户ID或唯一标识的数组，未登录则返回false
     */
    public function isLogin()
    {
        $res = session('logined');
        if (empty($res)) {
            return false;
        } else {
            return json_decode($res, true);
        }
    }

    /**
     * 系统后台用户登录
     * @param string $usr 账号
     * @param String $pwd 密码
     * @return bool
     */
    public function login($usr, $pwd)
    {
        $this->redis->select(15);

        // 判断用户是否存在
        if (!$this->redis->exists($usr)) {
            return false;
        }

        // 判断密码是否正确
        $realPwd = $this->redis->hGet($usr, 'pwd');
        if ($realPwd != md5($pwd . getConf('SaltKey'))) {
            return false;
        }

        // 写入SESSION，标记登录成功
        $loginLog = array(
            'usr' => $usr,
            'loginTime' => time()
        );
        session('logined', json_encode($loginLog));

        return true;
    }

    /**
     * 后台用户登录注销
     * @param string $usr
     * @return bool
     */
    public function logout($usr)
    {
        if (session($usr)) {
            session($usr, null);
        }
        return true;
    }

    /**
     * 记录后台用户登录信息
     * @param mixed $msg
     */
    public function recordAdminLog($msg)
    {
        // 选择14号数据库
        $this->redis->select(14);
        // 存放当前账号访问日志
        $this->redis->lPush('log:login:admin', $this->ipInfo['REMOTE_ADDR'] . ':' . $this->ipInfo['REQUEST_TIME'] . '; msg:' . $msg);
    }

    public function modifyPassword($oldPwd, $newPwd)
    {
        // TODO: Implement modifyPassword() method.
    }

//---------------------------------------------拦截规则逻辑实现-----------------------------------------------------

    /**
     * RuleA，单位时间内触发基本访问规则(BaseAccessRule)N次，则返回true
     * @return bool
     *
     * 数据库：3
     * 键：'RuleA:' . IP  | 值：当前IP请求的时间戳  | 类型：list
     */
    private function verifyRuleA()
    {
        // 选择3号数据库
        $this->redis->select(3);

        // 读取规则元素
        $ruleA = getConf('RuleA', true);
        $accessFrequency = (empty($ruleA['AccessFrequency']) ? 10 : $ruleA['AccessFrequency']);
        $triggerTimeUnit = empty(strtolower($ruleA['TriggerTimeUnit'])) ? 'h' : strtolower($ruleA['TriggerTimeUnit']);

        // 判断是否违反BaseAccessRule
        if ($this->violateBaseAccessRule) {
            // 更新当前IP违反信息
            $this->redis->lPush('RuleA:' . $this->ipInfo['REMOTE_ADDR'], $this->ipInfo['REQUEST_TIME']);
            // 获取当前IP违反次数
            $violateTimes = $this->redis->lLen('RuleA:' . $this->ipInfo['REMOTE_ADDR']);

            // 如果当前IP违反的次数大于规则限定次数则判断是否违反规则
            if ($violateTimes > $accessFrequency) {
                $lastTime = $this->redis->lIndex('RuleA:' . $this->ipInfo['REMOTE_ADDR'], -1);
                // 如果第一次和最后一次触发规则的时间间隔小于规则定义的时间间隔则是违反了RuleA
                if ($this->ipInfo['REQUEST_TIME'] - $lastTime < getTimestampForTimeUnit($triggerTimeUnit)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * RuleB，单位时间内访问次数超过一定数量则触发拦截器
     * @return bool
     *
     * 数据库：3
     * 键：'RuleB:' . IP  | 值：当前IP请求的时间戳  | 类型：list
     */
    private function verifyRuleB()
    {
        // 选择3号数据库
        $this->redis->select(3);

        // 读取规则元素
        $ruleB = getConf('RuleB', true);
        $accessFrequency = (empty($ruleB['AccessFrequency']) ? 100 : $ruleB['AccessFrequency']);
        $triggerTimeUnit = empty(strtolower($ruleB['TriggerTimeUnit'])) ? 'h' : strtolower($ruleB['TriggerTimeUnit']);

        // 更新当前IP的访问记录
        $this->redis->lPush('RuleB:' . $this->ipInfo['REMOTE_ADDR'], $this->ipInfo['REQUEST_TIME']);
        // 获取当前IP的记录数目
        $accessTimes = $this->redis->lLen('RuleB:' . $this->ipInfo['REMOTE_ADDR']);

        // 如果当前IP访问的次数大于规则限定次数则判断是否违反规则
        if ($accessTimes > $accessFrequency) {
            $lastTime = $this->redis->lIndex('RuleB:' . $this->ipInfo['REMOTE_ADDR'], -1);
            // 如果第一次和最后一次访问的时间间隔小于规则定义的时间间隔则是违反了RuleB
            if ($this->ipInfo['REQUEST_TIME'] - $lastTime < getTimestampForTimeUnit($triggerTimeUnit)) {
                return true;
            }
        }

        return false;
    }

    /**
     * RuleC，单位时间内某一受保护的URL访问次数超过一定数量则触发拦截器
     * @return bool
     *
     * 数据库：3
     * 键：'RuleC:' . URL . ':' . IP  | 值：当前IP访问的时间戳  | 类型：list
     */
    private function verifyRuleC()
    {
        // 选择3号数据库
        $this->redis->select(3);

        // 读取规则元素
        $RuleC = getConf('RuleC', true);
        $accessFrequency = empty($RuleC['AccessFrequency']) ? 3 : $RuleC['AccessFrequency'];
        $triggerTimeUnit = empty(strtolower($RuleC['TriggerTimeUnit'])) ? 'm' : $RuleC['TriggerTimeUnit'];
        $urls = $RuleC['URLS'];

        // 判断当前请求的URL是否在命中规则里面
        if (empty($urls) || !in_array($this->ipInfo['SCRIPT_NAME'], $urls)) {
            return false;
        }

        $key = 'RuleC:' . $this->ipInfo['SCRIPT_NAME'] . ':' . $this->ipInfo['REMOTE_ADDR'];
        // 更新当前IP访问信息
        $this->redis->lPush($key, $this->ipInfo['REQUEST_TIME']);
        // 读取当前IP访问次数
        $accessTimes = $this->redis->lLen($key);

        // 如果当前IP访问次数大于规则限定次数则判断是否违反规则
        if ($accessTimes > $accessFrequency) {
            $lastTime = $this->redis->lIndex($key, -1);
            // 如果第一次访问时间和最后一次访问时间小于规则定义的时间间隔则是违反了RuleC
            if ($this->ipInfo['REQUEST_TIME'] - $lastTime < getTimestampForTimeUnit($triggerTimeUnit)) {
                return true;
            }
        }

        return false;
    }

    /**
     * RuleD，非白名单内的IP访问受保护的URL则触发拦截器
     * @return bool
     */
    private function verifyRuleD()
    {
        // 读取规则元素
        $ruleD = getConf('RuleD', true);
        $whiteList = $ruleD['WhiteList'];
        $urls = $ruleD['URLS'];

        if (empty($whiteList) || empty($urls)) {
            return false;
        }

        $requestUrl = $this->ipInfo['SCRIPT_NAME'];
        if (key_exists($requestUrl, $urls) && in_array($this->ipInfo['REMOTE_ADDR'], $whiteList)) {
            return false;
        }

        return true;
    }
}