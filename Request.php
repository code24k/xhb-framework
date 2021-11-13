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

class Request {

    private static $_instance = null;

    /**
     * all
     * @return type
     */
    public function all($validator = true) {
        $all = array_merge($_GET, $_POST);
        if ($validator) {
            return static::fliterParam($all, $validator);
        }
        return $all;
    }

    /**
     * param
     * @param type $key
     * @param type $default
     * @param type $validator
     * @return type
     */
    public function param($key, $default = null, $validator = true) {
        $all = $this->all($validator);
        if (array_key_exists($key, $all)) {
            return $all[$key];
        }
        return $default;
    }

    /**
     * get
     * @param type $key
     * @param type $default
     * @return type
     */
    public function get($key, $default = null, $validator = true) {
        $get = $_GET;
        if (!$key) {
            return $validator ? static::fliterParam($get, $validator) : $get;
        }
        if (array_key_exists($key, $get)) {
            return $validator ? static::fliterParam($get[$key], $validator) : $get[$key];
        }
        return $default;
    }

    /**
     * post
     * @param type $key
     * @param type $default
     * @return type
     */
    public function post($key = '', $default = null, $validator = true) {
        $post = $_POST;
        if (!$key) {
            return $validator ? static::fliterParam($post, $validator) : $post;
        }
        if (array_key_exists($key, $post)) {
            return $validator ? static::fliterParam($post[$key], $validator) : $post[$key];
        }
        return $default;
    }

    /**
     * postValid for sql
     * @param type $key
     * @param type $default
     * @return type
     */
    public function postValid($key = '', $default = null) {
        return $this->post($key, $default, true, true);
    }

    /**
     * getValid
     * @param type $key
     * @param type $default
     * @return type
     */
    public function getValid($key = '', $default = null) {
        return $this->get($key, $default, true, true);
    }

    /**
     * fliterParam
     * @param type $param
     * @return type
     */
    public static function fliterParam($param = '', $validator = true) {
        if (is_array($param) && count($param)) {
            $data = [];
            foreach ($param as $key => $val) {
                $data[$key] = static::fliterParam($val, $validator);
            }
            return $data;
        }
        if ($validator) {
//            if ($param == '0xbf27' || $param == '0xbf5c27') {
//                throw new Exception('\framework\Request::fliterParam禁止传入0xbf27');
//            }
            return static::stringRemoveXss($param);
        }
        return $param;
    }

    /**
     * fliterParamExport
     * @param type $pageStr
     * @param type $buildQuery
     * @return type
     */
    public function fliterParamExport($pageStr = 'page', $buildQuery = true) {
        $all = $this->all();
        if (array_key_exists('page', $all)) {
            unset($all['page']);
        }
        if ($buildQuery == false) {
            return $all;
        }
        return http_build_query($all);
    }

    /**
     * mysqlFliterParam
     * @param type $param
     * @return type
     */
    public static function mysqlFliterParam($param = '') {
        return $param;
        return mysql_escape_string($param);
    }

    /**
     * 是否是AJAx提交的
     * @return bool
     */
    function isAjax() {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 是否是GET提交的
     */
    function isGet() {
        return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
    }

    /**
     * 是否是POST提交
     * @return int
     */
    function isPost() {
        return ($_SERVER['REQUEST_METHOD'] == 'POST') ? 1 : 0;
    }

    /**
     * isPostVerify
     * @return type
     */
    function isPostVerify() {
        return ($_SERVER['REQUEST_METHOD'] == 'POST' && checkurlHash($GLOBALS['verify']) && (empty($_SERVER['HTTP_REFERER']) || preg_replace("~https?:\/\/([^\:\/]+).*~i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("~([^\:]+).*~", "\\1", $_SERVER['HTTP_HOST']))) ? 1 : 0;
    }

    /**
     * domainPrefix
     * @return type
     */
    public function domainPrefix() {
        return Route::domainPrefix();
    }

    /**
     * isHttps
     * @return boolean
     */
    public static function isHttps() {
        if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
            return true;
        }
        return false;
    }

    /**
     * httpsPrefix
     * @return string
     */
    public static function httpsPrefix() {
        if (static::isHttps()) {
            return 'https://';
        }
        return 'http://';
    }

    /**
     * isEmail
     * @param type $email
     * @return boolean
     */
    public function isEmail($email) {
        $pattern = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
        if (preg_match($pattern, $email)) {
            return true;
        }
        return false;
    }

    /**
     * ip
     * @return type
     */
    public function ip() {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * ip2long
     * @return type
     */
    public function ip2long() {
        return ip2long($this->ip());
    }

    /**
     * redirect
     * @param type $url
     */
    public static function redirect($url) {
        header('Location: ' . $url);
        exit();
    }

    /**
     * instance
     * @return type
     */
    public static function instance() {
        if (!static::$_instance) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    /**
     * csrfToken
     * @return type
     */
    public static function csrfToken() {
        $csrfToken = '_' . Response::generateNum();
        if (Session::set('_csrf_token', $csrfToken)) {
            return $csrfToken;
        }
        return '';
    }

    /**
     * csrfTokenCreate
     * @return type
     */
    public function csrfTokenCreate() {
        return static::csrfToken();
    }

    /**
     * csrfTokenVali
     * @return boolean
     */
    public function csrfTokenVali() {
        if (!Session::get('_csrf_token', '') || !$this->param('_csrf_token', '')) {
            return false;
        }
        if ($this->param('_csrf_token', '') == Session::get('_csrf_token', '')) {
            return true;
        }
        return false;
    }

    /**
     * csrfTokenRemove
     * @return type
     */
    public function csrfTokenRemove() {
        return Session::remove('_csrf_token');
    }

    /**
     * valiValidatecode
     * @return boolean
     */
    public function valiValidatecode() {
        if ($this->post('validatecode', '') != \framework\Session::get('validatecode')) {
            return false;
        }
        return true;
    }

    /**
     * 检测域名格式
     * @param type $domain
     * @return boolean
     */
    function checkDomain($domain) {
        return !empty($domain) && strpos($domain, '--') === false &&
                preg_match('/^([a-z0-9]+([a-z0-9-]*(?:[a-z0-9]+))?\.)?[a-z0-9]+([a-z0-9-]*(?:[a-z0-9]+))?(\.us|\.tv|\.org\.cn|\.org|\.net\.cn|\.net|\.mobi|\.me|\.la|\.info|\.hk|\.gov\.cn|\.edu|\.com\.cn|\.com|\.co\.jp|\.co|\.cn|\.cc|\.biz|\.io)$/i', $domain) ? true : false;
    }

    /**
     * stringRemoveXss
     * @param type $html
     * @return type
     */
    public static function stringRemoveXss($html) {
        preg_match_all("/\<([^\<]+)\>/is", $html, $ms);

        $searchs[] = '<';
        $replaces[] = '&lt;';
        $searchs[] = '>';
        $replaces[] = '&gt;';

        if ($ms[1]) {
            $allowtags = 'img|a|font|div|table|tbody|caption|tr|td|th|br|p|b|strong|i|u|em|span|ol|ul|li|blockquote';
            $ms[1] = array_unique($ms[1]);
            foreach ($ms[1] as $value) {
                $searchs[] = "&lt;" . $value . "&gt;";

                $value = str_replace('&amp;', '_uch_tmp_str_', $value);
                $value = static::stringHtmlspecialchars($value);
                $value = str_replace('_uch_tmp_str_', '&amp;', $value);

                $value = str_replace(array('\\', '/*'), array('.', '/.'), $value);
                $skipkeys = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate',
                    'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange',
                    'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick',
                    'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate',
                    'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete',
                    'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel',
                    'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart',
                    'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop',
                    'onsubmit', 'onunload', 'javascript', 'script', 'eval', 'behaviour', 'expression', 'style', 'class');
                $skipstr = implode('|', $skipkeys);
                $value = preg_replace(array("/($skipstr)/i"), '.', $value);
                if (!preg_match("/^[\/|\s]?($allowtags)(\s+|$)/is", $value)) {
                    $value = '';
                }
                $replaces[] = empty($value) ? '' : "<" . str_replace('&quot;', '"', $value) . ">";
            }
        }
        $html = str_replace($searchs, $replaces, $html);
        return $html;
    }

    /**
     * stringHtmlspecialchars
     * @param type $string
     * @param type $flags
     * @return type
     */
    public static function stringHtmlspecialchars($string, $flags = null) {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = static::stringHtmlspecialchars($val, $flags);
            }
        } else {
            if ($flags === null) {
                $string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
                if (strpos($string, '&amp;#') !== false) {
                    $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
                }
            } else {
                if (PHP_VERSION < '5.4.0') {
                    $string = htmlspecialchars($string, $flags);
                } else {
                    if (!defined('CHARSET') || (strtolower(CHARSET) == 'utf-8')) {
                        $charset = 'UTF-8';
                    } else {
                        $charset = 'ISO-8859-1';
                    }
                    $string = htmlspecialchars($string, $flags, $charset);
                }
            }
        }
        return $string;
    }

    public function uri() {
        
    }

    /**
     * apiValidator
     * @return type
     */
    public function apiValidator() {
        if (!$this->param('unixtime', '')) {
            return statusFailure('缺少unixtime参数');
        }
        if (intval($this->param('unixtime', '')) < strtotime('-3 monute')) {
            return statusFailure('unixtime请求已超时');
        }
        if (!$this->param('sign', '')) {
            return statusFailure('缺少sign参数');
        }
        $admin = \app\model\AdminUser::db()->select("select * from admin_user where user='admin';")->first();
        if (!$admin) {
            return statusFailure('未找到系统用户，请检查');
        }
        if (md5($this->param('unixtime', '') . $admin->attribute->token) != $this->param('sign', '')) {
            return statusFailure('sign错误，请检查');
        }
        return statusSuccess();
    }

}
