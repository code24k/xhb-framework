<?php
/**
 * XHB framework
 * 本框架可以免费用于个人、商业场景，但禁止二次修改、打包再发布。
 * 申请著作权提交代码不得包含本框架。
 * 开源仓库地址：
 * https://gitee.com/code24k/xhb-framework
 * https://github.com/code24k/xhb-framework
 */
namespace framework\cache;

class Cache {

    /**
     * driver
     * @return \framework\cache\CacheDatabase|\framework\cache\CacheFile|\framework\cache\CacheRedis
     */
    public static function driver() {
        $driver = env('CACHE_DRIVER', 'file');
        switch ($driver) {
            case 'file':
                return new CacheFile();
            case 'database':
                return new CacheDatabase();
            case 'redis':
                return new CacheRedis();
        }
        return new CacheFile();
    }

    /**
     * set
     * @param type $key
     * @param type $expiry
     * @param type $value
     * @return type
     */
    public static function set($key, $expiry, $value) {
        return static::driver()->set($key, $expiry, $value);
    }

    /**
     * get
     * @param type $key
     * @param type $default
     * @return type
     */
    public static function get($key, $default = null) {
        return static::driver()->get($key, $default);
    }

    /**
     * remember
     * @param type $key
     * @param type $expiry
     * @param type $value
     * @return type
     */
    public static function remember($key, $expiry, $value) {
        $cacheGet = static::get($key);
        if ($cacheGet) {
            return $cacheGet;
        }
        static::set($key, $expiry, value($value));
        return static::get($key);
    }

}
