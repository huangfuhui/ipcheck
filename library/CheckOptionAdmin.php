<?php
/**
 * 定义系统后台管理操作规范
 */

namespace Ipcheck;

interface CheckOptionAdmin
{

    /**
     * 判断当前IP是否登录
     * @return bool
     */
    function isLogin();

    /**
     * 用户登录
     * @param int $usr 用户名
     * @param String $pwd 密码
     * @return bool
     */
    function login($usr, $pwd);

    /**
     * 修改用户密码
     * @param String $oldPwd 旧密码
     * @param String $newPwd 新密码
     * @return bool
     */
    function modifyPassword($oldPwd, $newPwd);
}