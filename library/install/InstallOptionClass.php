<?php
/**
 * Ipcheck系统安装器，自动挑选适合的数据库安装文件来初始化系统
 */

namespace Ipcheck\Install;

use Ipcheck\DBHandlerFactory;
use Ipcheck\RedisCheckOptionClass;

class InstallOptionClass
{
    protected $DBHandler = null;                    // 数据库操作对象

    public function __construct()
    {
        // 获取数据库操作对象
        $this->DBHandler = (new DBHandlerFactory())->getDBHandler();
    }

    public function install()
    {
        // 判断是否已经初始化系统过了
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'install.lock')) {
            // TODO:安装异常提示
            return false;
        }

        // 获取系统配置的数据库类型
        $DBType = strtolower(getConf('DBType'));
        switch ($DBType) {
            case 'redis' : {
                $installOption = new RedisInstallClass();
                // 执行系统安装操作
                $res = $installOption->install($this->DBHandler);
                // 生成安装成功的标记
                if ($res && $installOption->recordInstall()) {
                    return true;
                } else {
                    return false;
                }
            }
                break;
        }

        // TODO:安装异常提示
        return false;
    }
}