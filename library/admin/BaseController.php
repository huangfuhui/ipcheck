<?php
/**
 * 系统后台基础控制器
 */

namespace Ipcheck\Admin;

use Ipcheck\DBHandlerFactory;
use Ipcheck\Tool\InitializeClass;

class BaseController extends InitializeClass
{
    protected $DBHandler = null;                    // 数据库操作对象

    protected $IsLogin = false;                     // 用户登录标记

    protected $DataGet = array();                   // 保存GET请求中的参数
    protected $DataPost = array();                  // 保存POST请求中的参数

    public function __construct()
    {
        parent::__construct();

        // 获取数据库操作对象
        $this->DBHandler = (new DBHandlerFactory())->getDBHandler();

        // 判断是否登录成功，并标记用户
        $this->IsLogin = $this->DBHandler->isLogin();

        $this->DataGet = $_GET;
        $this->DataPost = $_POST;
    }
}