<?php
/**
 * 系统管理后台用户API
 */

namespace Ipcheck\Admin\API;

class UsrAPI extends BaseAPI
{
    /**
     * 用户登录API
     * @return array
     */
    public function login()
    {
        return $this->result;
    }
}