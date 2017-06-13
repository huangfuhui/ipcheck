<?php
/**
 * 系统后台基础控制器
 */

namespace Ipcheck\Admin\Controller;

use Ipcheck\DBHandlerFactory;
use Ipcheck\Tool\InitializeClass;

class BaseController extends InitializeClass
{
    protected $DBHandler = null;                    // 数据库操作对象

    protected $isLogin = false;                     // 用户登录标记

    protected $ipInfo = array();                    // 请求IP信息
    protected $action = 'index';                    // 请求动作

    protected $dataGet = array();                   // 保存GET请求中的参数
    protected $dataPost = array();                  // 保存POST请求中的参数

    public function __construct()
    {
        parent::__construct();

        // 获取数据库操作对象
        $this->DBHandler = (new DBHandlerFactory())->getDBHandler();

        // 判断是否登录成功，并标记用户
        $this->isLogin = $this->DBHandler->isLogin();

        $this->ipInfo = self::$IPINFOS;


        empty($_GET['a']) ? $this->action = 'index' : $this->action = $_GET['a'];

        $this->dataGet = $_GET;
        $this->dataPost = $_POST;
    }
}