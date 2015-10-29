<?php
/**
 * cookie支持
 *
 * @category typecho
 * @package Cookie
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * cookie支持
 *
 * @author qining
 * @category typecho
 * @package Cookie
 */
class Util_Session
{
    /**
     * 前缀
     * 
     * @var string
     * @access private
     */
    private static $_prefix = '__sisome_';

    /**
     * 设置前缀 
     * 
     * @param string $url
     * @access public
     * @return void
     */
    public static function setPrefix($url)
    {
        self::$_prefix = md5($url);
    }

    /**
     * 获取前缀 
     * 
     * @access public
     * @return string
     */
    public static function getPrefix()
    {
        return self::$_prefix;
    }

    /**
     * 获取指定的COOKIE值
     *
     * @access public
     * @param string $key 指定的参数
     * @param string $default 默认的参数
     * @return mixed
     */
    public static function get($key, $default = NULL){
        $key = self::$_prefix . $key;
        $value = isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
        return empty($value) ? $default : $value;
    }

    /**
     * 设置指定的COOKIE值
     *
     * @access public
     * @param string $key 指定的参数
     * @param mixed $value 设置的值
     * @param integer $expire 过期时间,默认为0,表示随会话时间结束
     * @return void
     */
    public static function set($key, $value, $expire = 0)
    {
        $key = self::$_prefix . $key;
        $_SESSION[$key] = $value;
    }

    /**
     * 删除指定的COOKIE值
     *
     * @access public
     * @param string $key 指定的参数
     * @return void
     */
    public static function delete($key)
    {
        $key = self::$_prefix . $key;
        if (!isset($_SESSION[$key])) {
            return;
        }
        unset($_SESSION[$key]);
    }
}

