<?php
/**
 * 用户配置文件
 */

return array(
    // 配置系统基本访问规则(BaseAccessRule)
    'AccessFrequency' => 10,                    // 单位时间内允许访问次数，默认'10'
    'TimeUnit' => 'm',                          // 时间单位，可选：d-天 h-时 m-分，不区分大小写，默认'm'
    'RequestRedirect' => '',                    // 非法请求重定向至第三方网站，为空则忽略该操作
    'NewRecords' => 200,                        // 保留N条最新访问IP的详细信息，默认200条

    // 配置拦截器(Interceptor)
    'UseInterceptor' => true,                   // 默认启动拦截器
    'AutoAddToBlackList' => false,              // 是否自动将拦截下来的IP加入黑名单
    'RuleChain' => array(                       // 过滤规则链，可以多条规则配合使用，默认使用'RuleA+RuleB'，
        'RuleA',                                // 过滤次序从规则链的左边到规则链的右边，一旦有一条规则链无法通过则直接拦截下来，不再往下执行
        'RuleB',
    ),

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
);