<?php
/**
 * 以Redis作为数据库的系统初始化与安装
 */

namespace Ipcheck\Install;

use Ipcheck\RedisCheckOptionClass;

class RedisInstallClass implements Install
{
    /**
     * 执行安装系统操作，选择15和14号数据库作为后台初始数据库，默认删除当前Redis数据库所有原始数据
     * @param RedisCheckOptionClass $DBHandler 数据库操作对象
     * @return bool
     */
    public function install($DBHandler = null)
    {
        // 获取Redis连接对象
        $redis = $DBHandler->redis;
        // 删除当前数据库数据
        $redis->flushAll();
        // 选择15号数据库，用于存放用户个人信息
        $redis->select(15);
        $redis->hSet('admin', 'pwd', md5('IpcheckAdmin' . getConf('SaltKey')));         // 密码
        $redis->hSet('admin', 'phone', '0');                                            // 电话
        $redis->hSet('admin', 'email', '0');                                            // 邮箱

        // 检查数据库是否成功插入数据
        if ($redis->hGet('admin', 'pwd') == md5('IpcheckAdmin' . getConf('SaltKey'))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 生成安装成功的标记
     * @param array $recordData 需要记录的安装信息
     * @return bool|int
     */
    public function recordInstall($recordData = array())
    {
        $res = file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'install.lock', '');
        if ($res === false) {
            return false;
        } else {
            return true;
        }
    }
}