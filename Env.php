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
 * Env
 */
class Env {

    public static $_env = [];

    /**
     * env
     */
    public static function make($key, $default = null) {
        if (!count(static::$_env)) {
            static::load();
        }
        if (!array_key_exists($key, static::$_env)) {
            return $default;
        }
        $value = static::$_env[$key];
        if ($value === false) {
            return value($default);
        }
        $value = str_replace("\r", "", str_replace("\n", "", $value));
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return;
        }
        return $value;
    }

    /**
     * make
     * @param type $key
     * @param type $default
     * @return type
     */
    public static function load() {
//        $confArr = static::envArray();
//        if (!count($confArr)) {
//            return $default;
//        }
//        if (key_exists($key, $confArr)) {
//            return $confArr[$key];
//        }
//        if (in_array($confArr[$key] == 'false')) {
//            return false;
//        }
//        if (in_array($confArr[$key] == 'true')) {
//            return true;
//        }
//        return $default;
        $envPath = $env = rootPath() . '/.env';
        if (!file_exists($envPath)) {
            return false;
        }
        $string = file_get_contents($envPath);
        if (!$string) {
            return;
        }
        $arr = explode("\n", $string);
        if (!count($arr)) {
            return;
        }
        foreach ($arr as $val) {
            if (!$val) {
                continue;
            }
            $env = explode("=", $val);
            if (count($env) != 2) {
                continue;
            }
            static::$_env[$env[0]] = $env[1];
        }
        return true;
    }

    /**
     * envArray
     * @return type
     */
    public static function envArray() {
        $env = file_get_contents(rootPath() . '/.env');
        if (!$env) {
            return array();
        }
        $conf = explode("\n", $env);
        if (!count($conf)) {
            return array();
        }
        $kv = array();
        foreach ($conf as $line) {
            $temp = explode("=", trim($line));
            if (!is_array($temp)) {
                continue;
            }
            if (count($temp) < 2) {
                continue;
            }
            $kv[$temp[0]] = $temp[1];
        }
        return $kv;
    }

}
