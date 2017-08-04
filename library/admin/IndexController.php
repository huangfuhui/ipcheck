<?php
/**
 * Ipcheck后台管理入口
 */

namespace Ipcheck\Admin\Controller;

use Ipcheck\Tool;

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
        if (empty($this->dataGet['page']) || !is_numeric($this->dataGet['page']) || $this->dataGet['page'] <= 0) {
            $page = 0;
        } else {
            $page = $this->dataGet['page'] - 1;
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

        // 数据分页
        $pageSelectorTool = new Tool\PageSelectorClass('', ceil($list_count / 15));
        $htmlBody .= $pageSelectorTool->getSelector($page + 1);

        $this->display('index', array('html_body' => $htmlBody, 'menu' => 'recentRecord'));
    }

    /**
     * 总体记录
     */
    public function totalRecord()
    {
        // 表头
        $htmlBody = <<<HTML
<table>
    <tr>
        <th>编号</th>
        <th>IP地址</th>
        <th>访问次数</th>
    </tr>
HTML;

        // 获取总记录数
        $record_count = $this->DBHandler->redis->zCard('ip:access_times');
        // 获取页码
        if (empty($this->dataGet['page']) || !is_numeric($this->dataGet['page']) || $this->dataGet['page'] <= 0) {
            $page = 0;
        } else {
            $page = $this->dataGet['page'] - 1;
        }

        // 总访问次数
        $access_times = $this->DBHandler->redis->zRevRange('ip:access_times', $page * 15, ($page + 1) * 15 - 1, true);
        $count = count($access_times);

        for ($i = 0; $i < $count; $i++) {
            $id = $i + 1 + $page * 15;
            if ($id % 2 == 0) {
                $tr_class = 'double_tr';
            } else {
                $tr_class = 'single_tr';
            }

            // 遍历访问数组 $access_times
            $ip_address = key($access_times);
            $ip_access_times = $access_times[$ip_address];
            next($access_times);

            // 将数据嵌入表单格中
            $htmlBody .= <<<HTML
    <tr class="{$tr_class}">
        <td>{$id}</td>
        <td>{$ip_address}</td>
        <td>{$ip_access_times}</td>
    </tr>
HTML;
        }

        // 闭合表格
        $htmlBody .= <<<HTML
</table>
HTML;

        // 数据分页
        $pageSelector = new Tool\PageSelectorClass('', ceil($record_count / 15));
        $htmlBody .= $pageSelector->getSelector($page + 1);

        $this->display('index', array('html_body' => $htmlBody, 'menu' => 'totalRecord'));
    }

    /**
     * 访问统计
     */
    public function accessCount()
    {
        // 获取有效访问和无效访问信息
        for ($i = 0; $i < 7; $i++) {
            $date[$i] = date('y-m-d', time() - $i * 86400);
            $res = $this->DBHandler->redis->zScore('ip:effective_access', $date[$i]);
            $res ? $effective_access[$i] = $res : $effective_access[$i] = 0;
            $res = $this->DBHandler->redis->zScore('ip:invalid_access', $date[$i]);
            $res ? $invalid_access[$i] = $res : $invalid_access[$i] = 0;
            $total_access[$i] = $effective_access[$i] + $invalid_access[$i];
        }

        // 拼接数据渲染插件
        $htmlBody = <<<HTML
<script type="text/javascript" src="public/js/echarts.min.js"></script>
<div id="echarts" style="width: 700px;height: 400px;margin: 20px auto;">
    <script type="text/javascript">
        var myChart = echarts.init(document.getElementById('echarts'));
        var option = {
            title: {
                text: '访问统计'
            },
            tooltip: {},
            legend: {
                data:['总访问量','有效访问','无效访问']
            },
            xAxis: {
                data: ['{$date[6]}','{$date[5]}','{$date[4]}','{$date[3]}','{$date[2]}','{$date[1]}','{$date[0]}']
            },
            yAxis: {},
            series: [{
                name: '总访问量',
                type: 'bar',
                data: [
                    {$total_access[6]},{$total_access[5]},{$total_access[4]},{$total_access[3]},
                    {$total_access[2]},{$total_access[1]},{$total_access[0]}
                ]
            },{
                name: '有效访问',
                type: 'bar',
                data: [
                    {$effective_access[6]},{$effective_access[5]},{$effective_access[4]},{$effective_access[3]},
                    {$effective_access[2]},{$effective_access[1]},{$effective_access[0]}
                ]
            },{
                name: '无效访问',
                type: 'bar',
                data: [
                    {$invalid_access[6]},{$invalid_access[5]},{$invalid_access[4]},{$invalid_access[3]},
                    {$invalid_access[2]},{$invalid_access[1]},{$invalid_access[0]}
                ]
            }]
        };
        myChart.setOption(option);
    </script>
</div>
HTML;

        $this->display('index', array('html_body' => $htmlBody, 'menu' => 'accessCount'));
    }

    /**
     * 访问禁止
     */
    public function banRecord()
    {
        if (!empty($_POST['ips'])) {
            $ips = explode(PHP_EOL, trim($_POST['ips']));
            $this->DBHandler->banIP($ips);
        }
        $banIpList = $this->DBHandler->getBanIpList();

        // 拼接 HTML
        $htmlBody = <<<HTML
<div class="ban_record_example">
Access Denied Example :<br />
    <div>
        127.0.0.1<br />
        10.0.0.2<br />
        172.16.0.1<br />
        192.168.0.1<br />
    </div>
</div>
<div class="ban_record_text">
    <form action="" method="post">
        <textarea name="ips" rows="22" cols="40">{$banIpList}</textarea>
        <input type="submit" value="submit" />
    </form>
</div>
HTML;

        $this->display('index', array('html_body' => $htmlBody, 'menu' => 'banRecord'));
    }
}