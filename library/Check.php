<?php
/**
 * 定义系统所有的IP过滤和监视操作规范
 */
namespace Ipcheck;

interface check {
    /**
     * 记录\更新访问者信息
     * @return bool 成功返回true，失败返回false
     */
    function recordAccessInfo ();
}