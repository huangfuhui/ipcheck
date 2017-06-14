<?php
/**
 * 系统管理后台用户API
 */

namespace Ipcheck\Admin\API;

class UsrAPI extends BaseAPI
{
    /**
     * 用户登录API
     */
    public function login()
    {
        $usr = $this->dataPost['usr'];
        $pwd = $this->dataPost['pwd'];

        // 参数判空
        if (empty($usr) || empty($pwd)) {
            $this->result['data'] = '账号或密码不能置空！';
            // 提前返回
            $this->returnAjax();
        }

        // 判断身份合法性
        if ($this->DBHandler->login($usr, $pwd)) {
            $this->result['code'] = 1;
            $this->result['data'] = 'admin.php?c=index';
        } else {
            $this->result['data'] = '账号或密码错误！';
        }
        $this->returnAjax();
    }
}