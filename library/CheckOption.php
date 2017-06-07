<?php
/**
 * 定义系统所有的IP过滤和监视操作规范
 */

namespace Ipcheck;

interface checkOption
{
    /**
     * 记录\更新访问者信息
     * @return bool 成功返回true，失败返回false
     */
    function recordAccessInfo();

    /**
     * 关闭数据库连接
     */
    function closeConnection();
}