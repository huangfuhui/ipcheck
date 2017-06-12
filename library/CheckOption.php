<?php
/**
 * 定义系统所有的IP过滤和监视操作规范
 */

namespace Ipcheck;

interface checkOption
{
    /**
     * 记录或更新当前访问者的信息，按照用户配置只记录最新的N条访问者信息，默认N=200
     */
    function recordAccessInfo();

    /**
     * 更新当前IP的访问次数
     */
    function recordAccessTimes();

    /**
     * 检测当前IP的访问频率是否符合用户设置的标准
     * @return bool 是则返回false，否则返回true
     */
    function checkFrequency();

    /**
     * 记录当前IP访问的有效性
     * @param bool $validity true表示有效，false表示无效
     */
    function recordAccessValidity($validity = true);

    /**
     * 关闭数据库连接
     */
    function closeConnection();
}