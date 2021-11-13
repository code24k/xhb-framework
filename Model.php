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

class Model {

    public $table;
    public $key;
    public $attribute;

    /**
     * __construct
     * @param type $param
     */
    public function __construct($param = array()) {
        $this->attribute = new \stdClass();
        if (count($param)) {
            foreach ($param as $key => $val) {
                $this->attribute->$key = $val;
            }
        }
    }

    /**
     * find
     * @param type $key
     * @param type $debug
     * @return type
     */
    public static function find($key, $debug = false) {
        $modelClass = get_called_class();
        if ($modelClass == get_class()) {
            throw new Exception('\framework\Model错误： 获取Model实例失败');
        }
        $modelClass = new $modelClass();
        if (!isset($modelClass->table)) {
            throw new Exception('\framework\Database错误：insert需指定ORM映射table');
        }
        if (!isset($modelClass->key)) {
            throw new Exception('\framework\Database错误：insert需指定ORM映射key');
        }
        return static::db()->select("select * from {$modelClass->table} where {$modelClass->key}='{$key}';", $debug)->first();
    }

    /**
     * fill
     * @return type
     */
    public function fill($array = array()) {
        foreach ($array as $key => $val) {
            $this->attribute->$key = $val;
        }
        return $this;
    }

    /**
     * save
     * @return type
     */
    public function save($debug = false) {
        $key = $this->key;
        if (!isset($this->attribute->$key) || !$this->attribute->$key) {
            throw new Exception('\framework\Model错误： save对象key值非真，对象可能为null');
        }
        $data = array();
        foreach ($this->attribute as $key_ => $val_) {
            if ($key_ == $this->key) {
                continue;
            }
            $data[$key_] = addslashes($val_);
        }
        return static::db()->where("{$this->key} = '{$this->attribute->$key}'")->update($data, $debug);
    }

    /**
     * saveConnection
     * @param type $connectConf
     * @param type $create
     * @param type $debug
     * @return type
     * @throws Exception
     */
    public function saveConnection($connectConf = array(), $create = false, $debug = false) {
        $key = $this->key;
        if (!isset($this->attribute->$key) || !$this->attribute->$key) {
            throw new Exception('\framework\Model错误： save对象key值非真，对象可能为null');
        }
        $data = array();
        foreach ($this->attribute as $key_ => $val_) {
            if ($key_ == $this->key) {
                continue;
            }
            $data[$key_] = addslashes($val_);
        }
        return static::connection($connectConf, $create)->where("{$this->key} = '{$this->attribute->$key}'")->update($data, $debug);
    }

    /**
     * delete
     * @param type $debug
     * @return type
     */
    public function delete($debug = false) {
        $key = $this->key;
        if (!isset($this->attribute->$key) || !$this->attribute->$key) {
            throw new Exception('\framework\Model错误： save对象key值非真，对象可能为null');
        }
        if (!isset($this->table) || !$this->table) {
            throw new Exception('\framework\Database错误：delete需指定ORM映射table');
        }
        if (!isset($this->key) || !$this->key) {
            throw new Exception('\framework\Database错误：delete需指定ORM映射key');
        }
        return static::db()->where("{$this->key} = '{$this->attribute->$key}'")->delete($debug);
    }

    /**
     * db
     * @return type
     */
    public static function db() {
        ini_set("magic_quotes_sybase", 0);
        ini_set("magic_quotes_runtime", 0);
        $modelClass = get_called_class();
        if ($modelClass == get_class()) {
            throw new Exception('\framework\Model错误： 获取Model实例失败');
        }
        return Database::getInstance(array('modelClass' => $modelClass));
    }

    /**
     * connection
     * @param type $confArray
     * @return type
     */
    public static function connection($connectConf = array(), $create = false) {
        ini_set("magic_quotes_sybase", 0);
        ini_set("magic_quotes_runtime", 0);
        $modelClass = get_called_class();
        if ($modelClass == get_class()) {
            throw new Exception('\framework\Model错误： 获取Model实例失败');
        }
        $connectConf = array_merge($connectConf, array('modelClass' => $modelClass));
        return Database::getInstance($connectConf, $create);
    }

    /**
     * attributeArray
     * @return type
     */
    public function attributeArray() {
        return (array) $this->attribute;
    }

    /**
     * __set
     * @param type $attriName
     * @param type $attrivalue
     */
    public function __set($attriName, $attrivalue) {
//        $this->attribute->$attriName = $attrivalue;
    }

    /**
     * __get
     * @param type $attriName
     * @return type
     */
    public function __get($attriName) {
//        if (!isset($this->attribute->$attriName)) {
//            $this->__set($attriName, null);
//        }
//        return $this->attribute->$attriName;
    }

}
