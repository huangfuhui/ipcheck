<?php
/**
 * 用户配置文件
 */

return array(
    // 配置系统访问规则
    'AccessFrequency' => 10,                    // 单位时间内允许访问次数，默认'10'
    'TimeUnit' => 'm',                          // 时间单位，可选：d-天 h-时 m-分，不区分大小写，默认'm'
    'NewRecords' => 200,                        // 保留N条最新访问IP的详细信息，默认200条
);