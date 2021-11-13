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
 * Application
 * @author xhb
 */
class Application {

    public static $_routeSreach = null;

    /**
     * run
     * @author xhb
     */
    public static function run($argv = []) {
        date_default_timezone_set('Asia/Shanghai');
        header("Content-type: text/html; charset=utf-8");
        error_reporting(0);
        require_once $GLOBALS['app']['framework_path'].'/Function.php';
        spl_autoload_register(function($classname) {
            $filename = rootPath() . '/' . str_replace("\\", "/", $classname) . '.php';
            if (file_exists($filename)) {
                include_once($filename);
            }
        });
        if (isCli()) {
            return static::runCli($argv);
        }
        return static::runFastCgi();
    }

    /**
     * runCli
     */
    public static function runCli($argv) {
        register_shutdown_function(function() {
            return \framework\Exception::cliCatchError();
        });
        $container = null;
        try {
            if ($argv[1] == 'version') {
                return Colors::getColoredString("framework.mvc v3.0", "green", "black");
            }
            $argArr = explode('@', $argv[1]);
            if (!is_array($argArr) || count($argArr) != 2) {
                throw new Exception('控制器参数错误 ' . $argv[1]);
            }
            $controller = str_replace("/", "\\", $argArr[0]);
            $method = $argArr[1];
            $container = new $controller;
            if (!is_object($container)) {
                throw new Exception('不存在的控制器' . $controller);
            }
            if (!method_exists($container, $method)) {
                throw new Exception('不存在的控制器方法' . $method);
            }
            $paramter = [
                new Request()
            ];
            if (count($argv) > 2) {
                for ($i = 2; $i <= count($argv); $i++) {
                    $paramter[] = $argv[$i];
                }
            }
            $container = call_user_func_array([$controller, $method], $paramter);
        } catch (Exception $ex) {
            $container = Colors::getColoredString($ex->getMessage(), "red", "black");
        }
        return Colors::getColoredString($container, "white", "cyan");
    }

    /**
     * runFastCgi
     * @return type
     * @throws Exception
     */
    public static function runFastCgi() {
        foreach (glob(appPath() . '/function/*.php') as $file) {
            require_once $file;
        }
        register_shutdown_function(function() {
            return \framework\Exception::fastCgiCatchError();
        });
        $container = null;
        try {
            if (!file_exists(appPath() . '/route.php')) {
                throw new Exception('\Framework错误：Application::run 路由不存在 /App/route.php');
            }
            include_once(appPath() . '/route.php');
            static::$_routeSreach = Route::search(filter_input(INPUT_SERVER, 'REQUEST_URI'));
            if (static::$_routeSreach == null) {
                throw new Exception('\Framework错误：Application::run 未找到有效路由');
            }
            static::callBeforeMiddleware();
            $container = static::callContainer();
            static::callAfterMiddleware();
        } catch (\framework\Exception $ex) {
            $container = $ex->output();
            if (env('APP_DEBUG', false) == false) {
                return Header::header500();
            }
        }
        return View::output($container);
    }

    /**
     * callContainer
     * @return type
     */
    public static function callContainer() {
        $appController = new static::$_routeSreach['controller'];
        $paramMethod = static::$_routeSreach['method'];
        if (!is_object($appController)) {
            throw new Exception('\Framework错误：Application::run 控制器对象为空 ' . static::$_routeSreach['controller']);
        }
        if (!method_exists($appController, static::$_routeSreach['method'])) {
            throw new Exception('\Framework错误：Application::run 不存在的方法 ' . static::$_routeSreach['controller'] . '->' . $paramMethod);
        }
        return call_user_func_array([$appController, static::$_routeSreach['method']], static::$_routeSreach['paramter']);
    }

    /**
     * callBeforeMiddleware
     * @return type
     */
    public static function callBeforeMiddleware() {
        $beforeMiddleware = new static::$_routeSreach['beforeMiddleware'];
        if ($beforeMiddleware->context(static::$_routeSreach['paramter'][0]) == false) {
            return View::output(null);
        }
    }

    /**
     * callAfterMiddleware
     * @return type
     */
    public static function callAfterMiddleware() {
        $afterMiddleware = new static::$_routeSreach['afterMiddleware'];
        if ($afterMiddleware->context(static::$_routeSreach['paramter'][0]) == false) {
            return View::output(null);
        }
    }

}
