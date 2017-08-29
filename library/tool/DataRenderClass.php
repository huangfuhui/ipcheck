<?php
/**
 * 数据渲染工具
 */

namespace Ipcheck\Tool;

class DataRenderClass
{
    static $ROOT = '';              // 系统根目录
    static $HTML_PATH = '';         // HTML文件夹
    static $CSS_PATH = '';          // CSS文件夹
    static $JS_PATH = '';           // JS文件夹

    public function __construct()
    {
        $length = strrpos(__FILE__, 'library' . DIRECTORY_SEPARATOR . 'tool' . DIRECTORY_SEPARATOR . 'DataRenderClass.php');
        self::$ROOT = substr(__FILE__, 0, $length);

        self::$HTML_PATH = 'public/html/';
        self::$CSS_PATH = 'public/css/';
        self::$JS_PATH = 'public/js/';
    }

    /**
     * 引入HTML文件
     * @param string $html 需要引入的HTML文件名，无需后缀，大小写敏感
     * @param array $htmlData 需要渲染的数据
     */
    public function display($html = '', $htmlData = array())
    {
        // 空HTML文件则返回指定文件内容
        if (empty($html)) {
            require self::$ROOT . self::$HTML_PATH . 'EMPTY.html';
            return;
        }

        $file = self::$ROOT . self::$HTML_PATH . $html . '.html';
        if (file_exists($file)) {
            require $file;
        } else {
            require self::$ROOT . self::$HTML_PATH . 'NOT_FOUND.html';
        }
    }
}