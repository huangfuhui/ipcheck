<?php
/**
 * Ipcheck安装操作规范
 */

namespace Ipcheck\Install;

use Ipcheck\DBHandlerFactory;

interface Install
{
    /**
     * 执行系统数据库安装及一些系统配置检查等系统初始化操作
     * @param DBHandlerFactory $DBHandler 数据库操作对象
     * @return bool
     */
    function install($DBHandler = null);

    /**
     * 在 '/library/install' 目录写下安装成功的空白文件来标记系统已执行过安装操作，
     * 标记文件名称是 'install.lock'，内容可为空。
     * @param array $recordData 需要记录的安装信息
     * @return bool
     */
    function recordInstall($recordData = array());
}