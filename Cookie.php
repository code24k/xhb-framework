<?php
/**
 * XHB framework
 * 本框架可以免费用于个人、商业场景，但禁止二次修改、打包再发布。
 * 申请著作权提交代码不得包含本框架。
 * 开源仓库地址：
 * https://gitee.com/code24k/xhb-framework
 * https://github.com/code24k/xhb-framework
 */
namespace framework;

/**
 * Cookie
 * @author xhb
 */
class Cookie {

    /**
     * set
     * @param type $key
     * @param type $val
     * @param type $time
     * @return type
     */
    public static function set($key, $val, $time) {
        return static::create($key, $val, time() + $time);
    }

    /**
     * get
     * @param type $key
     * @return null
     */
    public static function get($key) {
        if (isset($_COOKIE[$key]) && $_COOKIE[$key]) {
            return $_COOKIE[$key];
        }
        return null;
    }

    /**
     * create
     * @param type $key
     * @param type $val
     * @param type $time
     * @param type $path
     * @param type $domain
     * @return boolean
     */
    public static function create($key, $val, $time, $path = '/', $domain = '') {
        if ($domain != '') {
            setcookie($key, $val, $time, $path, $domain);
            return true;
        }
        setcookie($key, $val, time() + $time, $path);
        return true;
    }

    /**
     * isCookie
     */
    public static function isCookie($key) {
        return key_exists($key, $_COOKIE);
    }

}
