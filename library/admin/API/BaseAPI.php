<?php
/**
 * 后台基础API类，继承自基础控制器
 */

namespace Ipcheck\Admin\API;

use Ipcheck\Admin\Controller\BaseController;

class BaseAPI extends BaseController
{
    protected $result = array(
        'code' => 0,
        'data' => array(),
    );
}