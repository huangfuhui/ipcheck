# ipcheck
一个PHP实现的应用层的IP管理工具

# 教程
<h3>一、环境要求</h3>
<ul>
    <li>PHP 5.6+</li>
    <li>Redis 3.0+</li>
    <li>phpredis 2.2.8+<a href="https://github.com/phpredis/phpredis.git">(A PHP extension for Redis)</a></li>
</ul>

<h3>二、使用方法</h3>
1、直接在项目入口处引入 ipcheck 根目录下的 check.php 文件。如：<br/>
<pre>require '../ipcheck/check.php';</pre><br/>

2、根据自己的 Redis 数据库信息，对 ipcheck/library/config 目录下的 sysconfig.php 文件进行数据配置。当然，该文件还可以配置其它一些系统参数，具体可以参考文件注释<br/>

3、根据业务的需求，适当配置拦截器的规则链。对应的配置文件是 ipcheck/library/config 目录下的 usrconfig 文件，系统默认关闭拦截器，仅仅使用基本的频率限制规则。<br/>
目前系统支持四个规则单元，他们组成了应用于拦截器的规则链，具体如下所示：<br/>

<pre>
// 配置过滤规则(FilterRules)
    'RuleA' => array(                           // 规则A，单位时间内触发基本访问规则(BaseAccessRule)N次，则触发拦截器
        'AccessFrequency' => 10,                // 单位时间内允许触发系统基本访问规则(BaseAccessRule)的次数，默认'10'
        'TriggerTimeUnit' => 'h',               // 时间单位，可选：d-天 h-时，不区分大小写，默认'h'
    ),
    'RuleB' => array(                           // 规则B，单位时间内访问次数超过一定数量则触发拦截器
        'AccessFrequency' => 100,               // 单位时间内访问次数，默认'100'
        'TriggerTimeUnit' => 'h',               // 时间单位，可选：d-天 h-时，不区分大小写，默认'h'
    ),
    'RuleC' => array(                           // 规则C，单位时间内某一受保护的URL访问次数超过一定数量则触发拦截器
        'AccessFrequency' => 3,                 // 单位时间内访问次数，默认'3'
        'TriggerTimeUnit' => 'm',               // 时间单位，可选：d-天 h-时 m-分，不区分大小写，默认'm'
        'URLS' => array(                        // 受保护的URL，可以配置多个，不必配置主机域名部分
            '/example.php',                     // 拦截器会拦截：http://hostname/example.php、hostname/example.php?a=hello类型的请求
        ),
    ),
    'RuleD' => array(                           // 规则D，非白名单内的IP访问受保护的URL则触发拦截器
        'WhiteList' => array(                   // 白名单，可配置多个IP
            '127.0.0.1',
        ),
        'URLS' => array(                        // 受保护的URL，可配置多个，不必配置主机域名部分
            '/example.php',                     // 拦截器会拦截非白名单内的IP访问：http://hostname/example.php、hostname/example.php?a=hello类型的URL
        ),
    )
</pre>

<h3>三、后台登录</h3>
配置好系统后，可以登录后台查看和管理系统的访问IP，初始账号密码是 admin/IpcheckAdmin。 <br/>
