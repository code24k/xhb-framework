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

class Header {

    /**
     * headerJson
     */
    public static function headerJson() {
        header('Content-type: application/json');
    }

	/**
     * accessControl
     */
    public function accessControl() {
        header('Access-Control-Allow-Origin:*');
    }

    /**
     * headerUtf8
     */
    public static function headerUtf8() {
        header("Content-type: text/html; charset=utf-8");
    }

    /**
     * header500
     * @return type
     */
    public static function header500() {
        Header::headerUtf8();
        return View::output('500');
    }

    /**
     * header403
     * @return type
     */
    public static function header404() {
        return View::output('<h1>404</h1>');
    }

    /**
     * header403
     * @return type
     */
    public static function header403() {
        return View::output('<h1>Forbidden 403</h1>');
    }

}
