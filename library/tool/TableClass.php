<?php
/**
 * 表格工具，用于生成表格、渲染数据
 *
 * User: huangfuhui
 * Date: 2017/9/14
 * Email: huangfuhui@outlook.com
 */

namespace Ipcheck\Tool;


class TableClass
{
    private $th = array();              // 表头
    private $data = array();            // 数据

    private $table = '';                // 表格HTML

    public function __construct($th, $data = array())
    {
        $this->th = $th;
        $this->data = $data;
    }

    public function generateTable()
    {
        // 表格头部
        $dataHead = '<tr>';
        foreach ($this->th as $head) {
            $dataHead .= '<th>' . $head . '</th>' . PHP_EOL;
        }
        $dataHead .= '</tr>';

        // 表格数据
        $dataRows = '';
        foreach ($this->data as $index => $data) {
            if ($index % 2 == 0) {
                $class = 'double_tr';
            } else {
                $class = 'single_tr';
            }
            $dataRows .= "<tr class='$class'>";
            foreach ($data as $k => $v) {
                $dataRows .= '<td>' . $v . '</td>' . PHP_EOL;
            }
            $dataRows .= '</tr>';
        }

        $this->table = <<<HTML
<table>
    $dataHead
    $dataRows
</table>
HTML;
        return $this->table;
    }
}