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

class Response {

    /**
     * json
     * @param type $array
     * @return type
     */
    public static function json($array = []) {
        Header::headerJson();
        return json_encode($array);
    }

    /**
     * jsonp
     * @param \framework\Request $request
     * @param type $array
     * @return type
     */
    public static function jsonp(Request $request, $array = []) {
        return $request->get('jsoncallback') . '(' . json_encode($array) . ')';
    }

    /**
     * setSeoTitle
     * @param type $title
     * @return boolean
     */
    public static function setSeoTitle($title) {
        $GLOBALS['app']['seo']['title'] = $title;
        return true;
    }

    /**
     * getSeoTitle
     * @return type
     */
    public static function getSeoTitle() {
        return $GLOBALS['app']['seo']['title'];
    }

    /**
     * setMenuTitle
     * @param type $title
     * @return boolean
     */
    public static function setMenuTitle($title) {
        $GLOBALS['app']['menu']['title'] = $title;
        return true;
    }

    /**
     * getMenuTitle
     * @param type $title
     * @return boolean
     */
    public static function getMenuTitle() {
        return $GLOBALS['app']['menu']['title'];
    }

    /**
     * getSystemTitle
     * @return type
     */
    public static function getSystemTitle() {
        return configSystem('application.system.title');
    }

    /**
     * setSeoKeywords
     * @param type $keywords
     * @return boolean
     */
    public static function setSeoKeywords($keywords) {
        $GLOBALS['app']['seo']['keywords'] = $keywords;
        return true;
    }

    /**
     * getSeoKeywords
     * @return type
     */
    public static function getSeoKeywords() {
        return $GLOBALS['app']['seo']['keywords'];
    }

    /**
     * setSeoDescription
     * @param type $keywords
     * @return boolean
     */
    public static function setSeoDescription($keywords) {
        $GLOBALS['app']['seo']['description'] = $keywords;
        return true;
    }

    /**
     * getSeoDescription
     * @return type
     */
    public static function getSeoDescription() {
        return $GLOBALS['app']['seo']['description'];
    }

    /**
     * generateNum
     * @return string
     */
    public static function generateNum() {
        return md5(uniqid(mt_rand(), true));
        $uuid = substr($charid, 0, 8) . substr($charid, 8, 4) . substr($charid, 12, 4) . substr($charid, 16, 4) . substr($charid, 20, 12);
        return $uuid;
    }

}
