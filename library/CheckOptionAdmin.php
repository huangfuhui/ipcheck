<?php
/**
 * 定义系统后台管理操作规范
 */

namespace Ipcheck;

interface CheckOptionAdmin
{

    /**
     * 禁用IP
     * @param array $ips 被禁用的IP数组
     */
    function banIP($ips);

    /**
     * 获取禁用IP列表
     * @return string
     */
    function getBanIpList();

    /**
     * 判断当前IP是否登录
     * @return array|bool 如果用户已经登录则返回包含用户ID或唯一标识的数组，未登录则返回false
     */
    function isLogin();

    /**
     * 用户登录
     * @param string $usr 用户名
     * @param String $pwd 密码
     * @return bool
     */
    function login($usr, $pwd);

    /**
     * 用户登出
     * @param string $usr 用户名
     * @return bool
     */
    function logout($usr);

    /**
     * 记录系统后台操作日志信息
     * @param mixed $msg 需要记录的日志信息
     * @return bool
     */
    function recordAdminLog($msg);

    /**
     * 修改用户密码
     * @param String $oldPwd 旧密码
     * @param String $newPwd 新密码
     * @return bool
     */
    function modifyPassword($oldPwd, $newPwd);
}