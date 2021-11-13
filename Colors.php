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

class Colors {

    private static $_foregroundColors = [
        'black' => '0;30',
        'dark_gray' => '1;30',
        'blue' => '0;34',
        'light_blue' => '1;34',
        'green' => '0;32',
        'light_green' => '1;32',
        'cyan' => '0;36',
        'light_cyan' => '1;36',
        'red' => '0;31',
        'light_red' => '1;31',
        'purple' => '0;35',
        'light_purple' => '1;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'light_gray' => '0;37',
        'white' => '1;37',
    ];
    private static $_backgroundColors = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47',
    ];

    public function __construct() {
        
    }

    /**
     * getColoredString
     * @param type $string
     * @param type $foregroundColor
     * @param type $backgroundColor
     * @return string
     */
    public static function getColoredString($string, $foregroundColor = null, $backgroundColor = null) {
        $coloredString = "";
        if (array_key_exists($foregroundColor, static::$_foregroundColors)) {
            $coloredString .= "\033[" . static::$_foregroundColors[$foregroundColor] . "m";
        }
        if (array_key_exists($backgroundColor, static::$_backgroundColors)) {
            $coloredString .= "\033[" . static::$_backgroundColors[$backgroundColor] . "m";
        }
        $coloredString .= $string . "\033[0m";
        return $coloredString;
    }

    /**
     * getForegroundColors
     * @return type
     */
    public static function getForegroundColors() {
        return array_keys(static::$_foregroundColors);
    }

    /**
     * getBackgroundColors
     * @return type
     */
    public static function getBackgroundColors() {
        return array_keys(static::$_backgroundColors);
    }

}
