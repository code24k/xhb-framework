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
 * Route
 * @author xhb
 */
class Route {

    private static $_route = [];
    private static $_spread = false;
    private static $_search = [];

    /**
     * group
     * @param array $middleware
     * @param function $handle
     */
    public static function group($middleware, $handle) {
        if (!is_array($middleware) || !count($middleware)) {
            throw new Exception('\framework错误：Route::group $middleware需声明为数组');
        }
        if (!array_key_exists('domain', $middleware)) {
            throw new Exception('\framework错误：Route::group 路由需声明分站域名');
        }
        if (!env('APP_ROOT_DOMAIN')) {
            throw new Exception('.env未声明APP_ROOT_DOMAIN');
        }
        $domainPrefix = str_replace(rootDomain(), "", filter_input(INPUT_SERVER, 'SERVER_NAME'));
        $GLOBALS['app']['domainPrefix'] = $domainPrefix;
        //普通解析
        if (static::isFixedGroup($middleware['domain'], $domainPrefix)) {
            static::$_spread = true;
            return value($handle);
        } else {
            //判断泛解析
            if (static::isCoordinateGroup($middleware['domain']) && static::$_spread == false) {
                $routePrefix = str_replace("*.", "", $middleware['domain']);
                $urlPrefix = "";
                $firstCoordinate = strpos($domainPrefix, ".");
                if ($firstCoordinate !== false) {
                    $firstCoordinate = intval($firstCoordinate) + 1;
                    $urlPrefix = substr($domainPrefix, $firstCoordinate, strlen($domainPrefix));
                }
                //二级泛解析
                if ($urlPrefix == $routePrefix) {
                    static::$_spread = true;
                    return value($handle);
                }
                //一级泛解析
                if (static::$_spread == false) {
                    return value($handle);
                }
            }
        }
        return null;
    }

    /**
     * 固定解析
     * @param type $middlewareDomain
     * @param type $domainPrefix
     * @return type
     */
    public static function isFixedGroup($middlewareDomain, $domainPrefix) {
        if (is_array($middlewareDomain)) {
            return in_array($domainPrefix, $middlewareDomain);
        }
        return $middlewareDomain == $domainPrefix;
    }

    /**
     * 泛解析
     * @param type $middlewareDomain
     * @return boolean
     */
    public static function isCoordinateGroup($middlewareDomain) {
        if (is_array($middlewareDomain)) {
            return false;
        }
        if (strpos($middlewareDomain, "*") !== false) {
            return true;
        }
        return false;
    }

    /**
     * get
     * @param type $path
     * @param type $call
     * @return boolean
     */
    public static function get($path, $call, $beforeMiddleware = 'middle.default', $afterMiddleware = 'middle.default') {
        return static::restful($path, $call, 'get', $beforeMiddleware, $afterMiddleware);
    }

    /**
     * post
     * @param type $path
     * @param type $call
     * @return type
     */
    public static function post($path, $call, $beforeMiddleware = 'middle.default', $afterMiddleware = 'middle.default') {
        return static::restful($path, $call, 'post', $beforeMiddleware, $afterMiddleware);
    }

    /**
     * any
     * @param type $path
     * @param type $call
     * @return boolean
     */
    public static function any($path, $call, $beforeMiddleware = 'middle.default', $afterMiddleware = 'middle.default') {
        return static::restful($path, $call, 'any', $beforeMiddleware, $afterMiddleware);
    }

    /**
     * restful
     * @param type $path
     * @param type $call
     * @param type $act
     * @param type $beforeMiddleware
     * @param type $afterMiddleware
     * @return boolean
     */
    public static function restful($path, $call, $act = 'any', $beforeMiddleware = 'middle.default', $afterMiddleware = 'middle.default') {
        $temp = explode('@', $call);
        if (!is_array($temp) || !count($temp)) {
            return false;
        }
        static::$_route[$path] = array(
            'request' => $act,
            'controller' => current($temp),
            'method' => end($temp),
            'beforeMiddleware' => static::middlewareMap($beforeMiddleware, '\framework\Middleware'),
            'afterMiddleware' => static::middlewareMap($afterMiddleware, '\framework\Middleware'),
        );
        $GLOBALS['app']['route'] = static::$_route;
        return true;
    }

    /**
     * middlewareMap
     * @param type $key
     * @param type $default
     * @return type
     */
    public static function middlewareMap($key, $default) {
        $conf = configSystem('middleware', $default);
        if (!array_key_exists($key, $conf)) {
            return $default;
        }
        return $conf[$key];
    }

    /**
     * allRule
     * @return type
     */
    public static function allRule() {
        return isset($GLOBALS['app']['route']) ? $GLOBALS['app']['route'] : array();
    }

    /**
     * domainPrefix
     * @return type
     */
    public static function domainPrefix() {
        return isset($GLOBALS['app']['domainPrefix']) ? $GLOBALS['app']['domainPrefix'] : '';
    }

    /**
     * search
     * @return type
     */
    public static function search($REQUEST_URI) {
//        preg_match_all('/\/\w+\/\w+\/\w+/', $REQUEST_URI, $match);
//        if (!$match[0] || !isset($match[0][0]) || !$match[0][0]) {
//            throw new Exception('\Framework错误：Route::search ' . $REQUEST_URI . ' 不合法');
//        }
//        $requestURIStr = $match[0][0];
        $route = static::allRule();
        if (!count($route)) {
            throw new Exception('\Framework错误：Route::search 没有有效路由');
        }
        $REQUEST_METHOD = strtolower(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));
        $REQUEST_URI_ARR = explode("/", static::masterRoute($REQUEST_URI));
        foreach ($route as $routeKey => $routeVal) {
            if (static::searchMatchRoute($routeKey, $REQUEST_URI_ARR) == false) {
                continue;
            }
            if (static::searchMatchRequest($routeVal, $REQUEST_METHOD) == false) {
                throw new Exception('\Framework错误：Route::search ' . $REQUEST_URI . ' 请求类型错误 ' . $routeVal['request'] . '=>' . $REQUEST_METHOD);
            }
            $routeVal['route'] = count($REQUEST_URI_ARR) ? implode('/', $REQUEST_URI_ARR) : '';
            $routeVal['paramter'] = static::searchParamter($routeKey, $REQUEST_URI_ARR);
            static::$_search = $routeVal;
            return static::$_search;
        }
        return null;
    }

    /**
     * routeDatail
     * @param type $key
     * @return type
     */
    public static function datail($key = null) {
        if (!$key) {
            return static::$_search;
        }
        return array_key_exists($key, static::$_search) ? static::$_search[$key] : null;
    }

    /**
     * masterRoute
     * @param type $REQUEST_URI
     * @return string
     */
    public static function masterRoute($REQUEST_URI) {
        $parse = parse_url($REQUEST_URI);
        if (!array_key_exists('path', $parse)) {
            return '';
        }
        return $parse['path'];
    }

    /**
     * searchMatchRoute
     * @param type $routeKey
     * @param type $REQUEST_URI_ARR
     * @return boolean
     */
    public static function searchMatchRoute($routeKey, $REQUEST_URI_ARR) {
        $ROUTE_ARR = explode("/", $routeKey);
        if (count($REQUEST_URI_ARR) != count($ROUTE_ARR)) {
            return false;
        }
        foreach ($ROUTE_ARR as $key => $val) {
            if ($val == '{cin}') {
                continue;
            }
            if ($ROUTE_ARR[$key] != $REQUEST_URI_ARR[$key]) {
                return false;
            }
        }
        return true;
    }

    /**
     * searchMatchRequest
     * @param type $routeVal
     * @param type $REQUEST_METHOD
     * @return boolean
     */
    public static function searchMatchRequest($routeVal, $REQUEST_METHOD) {
        if ($routeVal['request'] == 'any') {
            return true;
        }
        if ($routeVal['request'] != $REQUEST_METHOD) {
            return false;
        }
        return true;
    }

    /**
     * searchParamter
     * @param type $routeKey
     * @param type $REQUEST_URI_ARR
     * @return array
     */
    public static function searchParamter($routeKey, $REQUEST_URI_ARR) {
        $ROUTE_ARR = explode("/", $routeKey);
        $param = [
            new \framework\Request()
        ];
        if (count($REQUEST_URI_ARR) != count($ROUTE_ARR)) {
            return $param;
        }
        foreach ($ROUTE_ARR as $key => $val) {
            if ($val == '{cin}') {
                $param[] = $REQUEST_URI_ARR[$key];
                continue;
            }
        }
        return $param;
    }

}
