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

class CacheFile {

    /**
     * set
     * @param type $key
     * @param type $expiry
     * @param type $value
     * @return type
     */
    public static function set($key, $expiry, $value) {
        $data = serialize([
            'expiry' => time() + $expiry,
            'value' => $value
        ]);
        return \framework\File::writeFileCache($key, $data);
    }

    /**
     * get
     * @param type $key
     * @param type $default
     * @return type
     */
    public static function get($key, $default = null) {
        $content = \framework\File::readFileCache($key);
        if (!$content) {
            return $default;
        }
        $data = unserialize($content);
        if (!is_array($data) || !array_key_exists('expiry', $data) || !array_key_exists('value', $data)) {
            return $default;
        }
        if ($data['expiry'] < time()) {
            return $default;
        }
        return $data['value'];
    }

}
