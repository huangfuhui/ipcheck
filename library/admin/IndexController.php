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
            redirect($this->ipInfo['SCRIPT_NAME'] . '?a=login');
        }
    }

    /**
     * 后台管理首页
     */
    public function index()
    {
        $this->recentRecord();
    }

    /**
     * 用户登录页面
     */
    public function login()
    {
        // 如果用户已经登录则跳转至首页
        if ($this->isLogin) {
            redirect($this->ipInfo['SCRIPT_NAME'] . '?a=index');
        }

        $this->display('login');
    }

    /**
     * 最新记录
     */
    public function recentRecord()
    {
        // 表头
        $htmlBody = <<<HTML
<table>
    <tr>
        <th>编号</th>
        <th>IP地址</th>
        <th>上次访问时间</th>
    </tr>
HTML;

        // 统计最新的IP访问记录数目
        $list_count = $this->DBHandler->redis->lLen('ip:access_record');
        if (empty($page) || !is_numeric($page) || $page <= 0) {
            $page = 0;
        } else {
            $page -= 1;
        }

        // 获取最近访问的IP信息
        $recent_record = $this->DBHandler->redis->lRange('ip:access_record', $page * 15, ($page + 1) * 15 - 1);

        // 循环输出列表的每一行
        foreach ($recent_record as $key => $value) {
            if (++$key % 2 == 0) {
                $tr_class = 'double_tr';
            } else {
                $tr_class = 'single_tr';
            }

            $id = $key + $page * 15;

            // 将获取到的IP信息从JSON转换成Array，并且格式化目标数据
            $IPInfo = json_decode($value, true);
            $IPAddress = $IPInfo['REMOTE_ADDR'];
            $time = date('Y-m-d H:i:s', $IPInfo['REQUEST_TIME']);

            // 将数据嵌入表格的行元素中
            $htmlBody .= <<<HTML
    <tr class="{$tr_class}">
        <td>{$id}</td>
        <td>{$IPAddress}</td>
        <td>{$time}</td>
    </tr>
HTML;
        }

        // 表结尾
        $htmlBody .= <<<HTML
</table>
HTML;

        $this->display('index', array('html_body' => $htmlBody));
    }

    /**
     * 总体记录
     */
    public function totalRecord()
    {

    }

    /**
     * 访问统计
     */
    public function accessCount()
    {

    }

    /**
     * 访问禁止
     */
    public function banRecord()
    {

    }
}