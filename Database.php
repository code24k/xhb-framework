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
 * Database
 * @author xhb
 */
class Database {

    private $db_host;
    private $db_database;
    private $db_username;
    private $db_password;
    private $db_charset;
    private $conn;
    private $result;
    private $sql;
    private $modelClass;
    private static $instance;

    /**
     * __construct
     */
    public function __construct($param = array()) {
        $this->db_host = configSystem('database.mysql.host');
        $this->db_database = configSystem('database.mysql.database');
        $this->db_username = configSystem('database.mysql.username');
        $this->db_password = configSystem('database.mysql.password');
        $this->db_charset = configSystem('database.mysql.charset');
        foreach ($param as $key => $val) {
            $this->$key = $val;
        }
    }

    /**
     * getInstance
     * @return type
     */
    public static function getInstance($param = array(), $create = false) {
        if ($create == true) {
            return new static($param);
        } else {
            static::$instance = new static($param);
        }
        return static::$instance;
    }

    /**
     * connect
     */
    public function connect() {
//        if ($this->db_pconnect == true) {
//            $this->conn = mysqli_connect($this->db_host, $this->db_username, $this->db_password);
//        } else {
//            $this->conn = mysqli_connect($this->db_host, $this->db_username, $this->db_password);
//        }
        $this->conn = mysqli_connect($this->db_host, $this->db_username, $this->db_password);
        if (!$this->conn) {
            throw new Exception('\Framework\Database错误：数据库连接失败 ', $this->db_database);
        }
        if (!mysqli_select_db($this->conn, $this->db_database)) {
            throw new Exception('\Framework\Database错误：数据库不可用 ', $this->db_database);
        }
//        mysqli_query($this->conn, "SET NAMES $this->db_charset");
        mysqli_query($this->conn, "SET NAMES utf8");
        return $this;
    }

    /**
     * select
     * @param type $sql
     * @param type $debug
     * @return \\Framework\Database
     */
    public function select($sql = '', $debug = false) {
        if ($sql == '') {
            throw new Exception('\Framework\Database错误：sql为空');
        }
        $this->sql = str_replace(';', '', $sql) . ';';
        if ($debug) {
            throw new Exception("调试SQL " . $this->sql);
        }
        return $this;
    }

    /**
     * where
     * @param type $sql
     * @param type $debug
     * @return \\Framework\Database
     */
    public function where($sql = '', $debug = false) {
        return $this->select($sql, $debug);
    }

    /**
     * query
     */
    public function query($resultmode = MYSQLI_STORE_RESULT) {
        $result = mysqli_query($this->conn, $this->sql, $resultmode);
        //dd(memory_get_usage());
        if ($resultmode == MYSQLI_USE_RESULT) {
            return $result;
        }
        $this->result = $result;
        return $this;
    }

    /**
     * fetch
     * @return type
     */
    public function fetch($fetchMode = MYSQLI_ASSOC) {
        if ($this->result) {
            return $this->result->fetch_array($fetchMode);
        }
        return [];
    }

    /**
     * cursor
     * while ($row = $results->fetch_assoc())
     * $uresult->close();
     * @return type
     */
    public function cursor() {
        return $this->connect()->query(MYSQLI_USE_RESULT);
    }

    /**
     * get
     * @return type
     */
    public function get() {
        $this->connect()->query();
        $allRows = array();
        while ($rows = $this->fetch()) {
            if (!count($rows)) {
                continue;
            }
            if ($this->modelClass) {
                $allRows[] = new $this->modelClass($rows);
            } else {
                $allRows[] = $rows;
            }
        }
        return $allRows;
    }

    /**
     * first
     * @return type
     */
    public function first() {
        $fetch = $this->connect()->query()->fetch();
        if (count($fetch)) {
            return $this->modelClass ? new $this->modelClass($fetch) : $fetch;
        }
        return null;
    }

    /**
     * insert
     * @param type $array
     * @param type $debug
     * @return type
     */
    public function insert($array, $debug = false) {
        if (!$this->modelClass) {
            throw new Exception('\Framework\Database错误：insert需指定ORM映射类名');
        }
        $modelClass = new $this->modelClass();
        if (!isset($modelClass->table)) {
            throw new Exception('\Framework\Database错误：insert需指定ORM映射table');
        }
        if (!isset($modelClass->key)) {
            throw new Exception('\Framework\Database错误：insert需指定ORM映射key');
        }
        if (!count($array)) {
            throw new Exception('\Framework\Database错误：insert需指定参数为数组');
        }
        $this->connect();
        $into = array();
        $values = array();
        foreach ($array as $key => $val) {
            $into[] = $key;
            $values[] = '\'' . $val . '\'';
        }
        $intoStr = '(' . implode(',', $into) . ')';
        $valuesStr = '(' . implode(',', $values) . ')';

        $this->sql = 'insert into ' . $modelClass->table . ' ' . $intoStr . ' values ' . $valuesStr . ';';
        if ($debug) {
            throw new Exception($this->sql);
        }
        unset($modelClass, $into, $values, $intoStr, $valuesStr);
        mysqli_query($this->conn, $this->sql);
        return mysqli_insert_id($this->conn);
    }

    /**
     * update
     * @param type $array
     * @param type $debug
     * @return type
     */
    public function update($array, $debug = false) {
        if (!$this->modelClass) {
            throw new Exception('\Framework\Database错误：update需指定ORM映射类名');
        }
        $modelClass = new $this->modelClass();
        if (!isset($modelClass->table)) {
            throw new Exception('\Framework\Database错误：update需指定ORM映射table');
        }
        if (!isset($modelClass->key)) {
            throw new Exception('\Framework\Database错误：update需指定ORM映射key');
        }
        if (!count($array)) {
            throw new Exception('\Framework\Database错误：update需指定参数为数组');
        }
        $this->connect();
        $set = array();
        foreach ($array as $key => $val) {
            $set[] = $key . '=\'' . $val . '\'';
        }
        $this->sql = 'update ' . $modelClass->table . ' set ' . implode(',', $set) . ' where ' . $this->sql . ';';
        if ($debug) {
            throw new Exception($this->sql);
        }
        unset($modelClass);
        mysqli_query($this->conn, $this->sql);
        return mysqli_affected_rows($this->conn);
    }

    /**
     * delete
     * @param type $debug
     * @return type
     */
    public function delete($debug = false) {
        $modelClass = new $this->modelClass();
        if (!isset($modelClass->table)) {
            throw new Exception('\Framework\Database错误：update需指定ORM映射table');
        }
        if (!isset($modelClass->key)) {
            throw new Exception('\Framework\Database错误：update需指定ORM映射key');
        }
        $this->connect();
        $this->sql = 'delete from ' . $modelClass->table . ' where ' . $this->sql . ';';
        if ($debug) {
            throw new Exception($this->sql);
        }
        unset($modelClass);
        mysqli_query($this->conn, $this->sql);
        return mysqli_affected_rows($this->conn);
    }

    /**
     * count
     * @return type
     */
    public function count() {
        return (int) $this->first()->attribute->count;
    }

}
