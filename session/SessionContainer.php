<?php
/**
 * XHB framework
 * 本框架可以免费用于个人、商业场景，但禁止二次修改、打包再发布。
 * 申请著作权提交代码不得包含本框架。
 * 开源仓库地址：
 * https://gitee.com/code24k/xhb-framework
 * https://github.com/code24k/xhb-framework
 */
namespace framework\session;

class SessionContainer {

    private $container = [];

    /**
     * __construct
     * @param type $key
     * @param type $value
     */
    public function __construct(array $value = []) {
        foreach ($value as $key => $val) {
            $this->container[$key] = $val;
        }
    }

    /**
     * set
     * @param type $key
     * @param type $value
     */
    public function set($key, $value, $expirationTime = 0) {
        $this->container[$key] = [
            'value' => $value,
            'expiration_time' => time() + $expirationTime
        ];
        return true;
    }

    /**
     * get
     * @param type $key
     * @return type
     */
    public function get($key) {
        if (!array_key_exists($key, $this->container)) {
            return null;
        }
        if ($this->container[$key]['expiration_time'] <= time()) {
            return null;
        }
        return $this->container[$key]['value'];
    }

    /**
     * remove
     * @param type $key
     */
    public function remove($key) {
        unset($this->container[$key]);
        return true;
    }

    /**
     * all
     * @return type
     */
    public function all() {
        return $this->container;
    }

}
