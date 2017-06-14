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
        'data' => null,
    );

    /**
     * 格式化输出结果给客户端
     * @param string $format 需要返回的格式
     */
    protected function returnAjax($format = 'json')
    {
        $format = strtolower($format);
        switch ($format) {
            case 'json' : {
                echo json_encode($this->result);
            }
                break;
        }
        exit;
    }
}