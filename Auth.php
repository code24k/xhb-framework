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

class Auth {

    /**
     * create
     * @param type $auth memeber|admin|api
     * @return type
     */
    public static function refresh($key, $auth) {
        return Session::set('auth.' . $key, $auth);
    }

    /**
     * member
     * @return type
     */
    public static function member() {
        return Session::get('auth.member');
    }

    /**
     * admin
     * @return type
     */
    public static function admin() {
        return Session::get('auth.admin');
    }

    /**
     * hasLogin
     * @param type $key
     * @return type
     */
    public static function hasLogin($key = 'member') {
        return Session::get('auth.' . $key) ? true : false;
    }
    
    /**
     * remove
     * @param type $key
     * @return type
     */
    public static function remove($key){
        return Session::remove($key);
    }

}
