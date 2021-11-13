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

class File {

    /**
     * createSession
     * @param type $key
     * @param type $val
     * @return type
     */
    public static function setSession($key, $val) {
        $storagePath = storagePath() . '/session/';
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0777, true);
        }
        $sessionFile = $storagePath . $key;
        return file_put_contents($sessionFile, serialize($val));
    }

    /**
     * getSession
     * @param type $key
     * @param type $val
     * @return type
     */
    public static function getSession($key) {
        $sessionFile = storagePath() . '/session/' . $key;
        if (!file_exists($sessionFile)) {
            return null;
        }
        $str = file_get_contents($sessionFile);
        if (!$str) {
            return null;
        }
        return unserialize($str);
    }

    /**
     * writeStorageLog
     * @param type $message
     * @return type
     */
    public static function writeStorageLog($message) {
        $logDir = rootPath() . '/storage/log';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logPath = $logDir . '/' . date('Ymd') . '.log';
        if (!file_exists($logPath)) {
            touch($logPath);
        }
        return file_put_contents($logPath, $message, FILE_APPEND);
    }

    /**
     * writeFileCache
     * @param type $key
     * @param type $data
     * @return type
     */
    public static function writeFileCache($key, $data) {
        $logDir = rootPath() . '/storage/cache';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $cachePath = $logDir . '/' . $key . '.cache';
        if (!file_exists($cachePath)) {
            touch($cachePath);
        }
        return file_put_contents($cachePath, $data);
    }

    /**
     * readFileCache
     * @param type $key
     * @return type
     */
    public static function readFileCache($key) {
        $cachePath = rootPath() . '/storage/cache/' . $key . '.cache';
        if (!file_exists($cachePath)) {
            return serialize([]);
        }
        return file_get_contents($cachePath);
    }

}
