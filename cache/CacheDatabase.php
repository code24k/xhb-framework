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

class CacheDatabase extends \framework\Model {

    public $table = 'cache_detail';
    public $key = 'cache_key';

    /**
     * set
     * @param type $key
     * @param type $expiry
     * @param type $value
     * @return type
     */
    public static function set($key, $expiry, $value) {
        $static = new static();
        static::db()->where("{$static->key}='{$key}'")->delete();
        return static::db()->insert([
                    'cache_key' => $key,
                    'cache_expiry' => time() + $expiry,
                    'cache_value' => str_replace("\\", "\\\\", serialize($value))
        ]);
    }

    /**
     * get
     * @param type $key
     * @param type $default
     * @return type
     */
    public static function get($key, $default = null) {
        $static = new static();
        $cacheResult = static::db()->select("select cache_key,cache_expiry,cache_value from {$static->table} where {$static->key}='{$key}'")->first();
        if (!$cacheResult || !is_object($cacheResult)) {
            return $default;
        }
        if ($cacheResult->cache_expiry < time()) {
            return $default;
        }
        return $cacheResult->attribute->cache_value ? unserialize($cacheResult->attribute->cache_value) : $default;
    }

}
