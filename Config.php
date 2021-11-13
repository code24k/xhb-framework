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

class Config {

    public static $require = array();

    /**
     * make
     * @param type $path
     * @param type $default
     * @param type $system
     * @return string
     */
    public static function make($path, $default = '', $system = false) {
        $keyArr = explode('.', $path);
        if (!key_exists($path, static::$require)) {
            if (count($keyArr) > 0) {
                if ($system == true) {
                    static::$require[$path] = require rootPath() . '/config/' . $keyArr[0] . '.php';
                } else {
                    static::$require[$path] = require rootPath() . '/app/config/' . $keyArr[0] . '.php';
                }
            } else {
                static::$require[$path] = array();
            }
        }
        $result = static::iterationArray($keyArr, static::$require[$path]);
        return $result ? $result : $default;
    }

    /**
     * iterationArray
     * @param type $keyArr
     * @param type $search
     * @return type
     */
    public static function iterationArray($keyArr, $search = array()) {
        array_shift($keyArr);
        if (!count($keyArr)) {
            return $search;
        }
        $key = current($keyArr);
        if (!is_array($search) || !key_exists($key, $search)) {
            return $search;
        }
        return static::iterationArray($keyArr, $search[$key]);
    }

}
