<?php
/**
 * 数据库适配器，用于适配各种数据库连接，目前仅支持Redis。
 */

namespace Ipcheck;

interface DBAdapter
{
    /**
     * 连接数据库，返回连接对象
     * @param $address string 数据库地址
     * @param $port int 数据库端口
     * @param $userName string 用户名
     * @param $password string 密码
     * @return mixed 数据库连接对象
     */
    function getConnection($address, $port, $userName, $password);

    /**
     * 关闭数据库连接
     * @param mixed $connection 待关闭的数据库连接
     */
    function close($connection);
}