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

class Exception extends \Exception {

    /**
     * output
     * @return string
     */
    public function output() {
        $message = static::stringFormat($this->message(), $this->getLine(), $this->getFile(), $this->getTraceAsString());
        if (env('APP_DEBUG', false) == false) {
            return File::writeStorageLog($message);
        }
        return str_replace("\n", "<br />", $message);
    }

    /**
     * getMessage
     * @return type
     */
    public function message() {
        return $this->getMessage();
    }

    /**
     * traceAsString
     * @return type
     */
    public function traceAsString() {
        return $this->getTraceAsString();
    }

    /**
     * line
     * @return type
     */
    public function line() {
        return $this->getLine();
    }

    /**
     * file
     * @return type
     */
    public function file() {
        return $this->getFile();
    }

    /**
     * catchError
     * @return type
     */
    public static function fastCgiCatchError() {
        $_error = error_get_last();
        if ($_error && in_array($_error['type'], array(1, 4, 16, 64, 256, 4096, E_ALL))) {
            if (env('APP_DEBUG', false) == true) {
                $message = str_replace("\n", "<br />", static::stringFormat($_error['message'], $_error['line'], $_error['file']));
                return View::output($message);
            }
            $message = static::stringFormat($_error['message'], $_error['line'], $_error['file']);
            return File::writeStorageLog($message);
        }
    }

    /**
     * cliCatchError
     * @return type
     */
    public static function cliCatchError() {
        $_error = error_get_last();
        if ($_error && in_array($_error['type'], array(1, 4, 16, 64, 256, 4096, E_ALL))) {
            $message = static::stringFormat($_error['message'], $_error['line'], $_error['file']);
            File::writeStorageLog($message);
            return View::output(Colors::getColoredString($message, "red", "cyan"));
        }
    }

    /**
     * stringFormat
     * @param type $message
     * @param type $getLine
     * @param type $getFile
     * @param type $getTraceAsString
     * @return type
     */
    public static function stringFormat($message, $getLine, $getFile, $getTraceAsString = null) {
        $message = '[' . date('Y-m-d H:i:s') . ']抛出异常:' . $message . "\n";
        if ($getLine) {
            $message .= '异常行号：' . $getLine . "\n";
        }
        if ($getFile) {
            $message .= '所在文件：' . $getFile . "\n";
        }
        if ($getTraceAsString) {
            $message .= $getTraceAsString . "\n";
        }
        return str_replace(rootPath(), "", $message);
    }

}
