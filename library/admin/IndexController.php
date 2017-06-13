<?php
/**
 * Ipcheck后台管理入口
 */

namespace Ipcheck\Admin\Controller;

class IndexController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // 未登录用户或登录过期跳转至登录界面
        if ($this->action != 'login' && !$this->isLogin) {
            header('Location:' . $this->ipInfo['SCRIPT_NAME'] . '?a=login');
        }
    }

    /**
     * 后台管理首页
     */
    public function index()
    {
        $this->display('index');
    }

    /**
     * 用户登录页面
     */
    public function login()
    {
        $this->display('login');
    }
}