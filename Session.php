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
 * Session
 */
class Session {

    /**
     * set
     * @param type $key
     * @param type $val
     * @return type
     */
    public static function set($key, $val) {
        return static::create($key, $val);
    }

    /**
     * get
     * @param type $key
     * @return null
     */
    public static function get($key) {
        $unserializeStream = static::unserializeStream();
        if (!$unserializeStream || !is_object($unserializeStream)) {
            return null;
        }
        return $unserializeStream->get($key);
    }

    /**
     * all
     * @return type
     */
    public static function all() {
        $unserializeStream = static::unserializeStream();
        if (!$unserializeStream || !is_object($unserializeStream)) {
            return null;
        }
        return $unserializeStream->all();
    }

    /**
     * unserializeStream
     * @return type
     */
    public static function unserializeStream() {
        $cookieId = Cookie::get(configSystem('session.sessionid'));
        if (!$cookieId) {
            return null;
        }
        return File::getSession($cookieId);
    }

    /**
     * create
     * @param type $key
     * @param type $value
     * @return type
     */
    public static function create($key, $value) {
        $unserializeStream = static::unserializeStream();
        if (!$unserializeStream || !is_object($unserializeStream)) {
            $unserializeStream = new session\SessionContainer();
        }
        $unserializeStream->set($key, $value, configSystem('session.validity'));
        return File::setSession(static::cookieId(), $unserializeStream);
    }

    /**
     * createSessionIDVal
     * @return type
     */
    public static function createSessionIDVal() {
        return Response::generateNum();
    }

    /**
     * cookieId
     * @return type
     */
    public static function cookieId() {
        $cookieId = Cookie::get(configSystem('session.sessionid'));
        if (!$cookieId) {
            $cookieId = static::createSessionIDVal();
            Cookie::create(configSystem('session.sessionid'), $cookieId, configSystem('cookie.validity'), configSystem('cookie.path'), configSystem('cookie.domain'));
        }
        return $cookieId;
    }

    /**
     * $key
     * @param type $key
     * @return boolean
     */
    public static function remove($key) {
        $unserializeStream = static::unserializeStream();
        if (!$unserializeStream || !is_object($unserializeStream)) {
            return true;
        }
        if ($unserializeStream->remove($key)) {
            return File::setSession(static::cookieId(), $unserializeStream);
        }
        return false;
    }

}
