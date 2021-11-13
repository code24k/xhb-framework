<?php
/**
 * XHB framework
 * 本框架可以免费用于个人、商业场景，但禁止二次修改、打包再发布。
 * 申请著作权提交代码不得包含本框架。
 * 开源仓库地址：
 * https://gitee.com/code24k/xhb-framework
 * https://github.com/code24k/xhb-framework
 */
namespace framework\paginate;

class PaginateCli {

    public $page = 0;
    public $limit = 10;
    public $offset = 0;
    public $countRows = 0;
    public $sumPage = 0;
    private $db;
    private $appends;
    private $uri;
    private $nextPage;
    private $previousPage;
    private $request;
    public $results = [];

    /**
     * __construct
     * @param \framework\Request $request
     * @param \framework\Database $db
     * @param type $limit
     * @param type $pageKey
     * @param type $page
     */
    public function __construct(\framework\Request $request, \framework\Database $db, $limit = 10, $page = 0) {
        $this->request = $request;
        $this->db = $db;
        $this->setLimit($limit);
        $this->setPage($page);
        $this->setOffset();
    }

    /**
     * setLimit
     * @param type $limit
     */
    public function setLimit($limit) {
        $this->limit = $limit;
    }

    /**
     * getLimit
     * @return type
     */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * setPage
     * @param type $page
     */
    public function setPage($page) {
        $this->page = intval($page);
        if ($this->page <= 0) {
            $this->page = 1;
        }
    }

    /**
     * getPage
     * @return type
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * setOffset
     */
    public function setOffset() {
        $this->offset = $this->getPage() <= 1 ? 0 : ($this->getPage() * $this->getLimit()) - $this->getLimit();
    }

    /**
     * getOffset
     * @return type
     */
    public function getOffset() {
        return $this->offset;
    }

    /**
     * setCountRows
     * @param type $countRows
     */
    public function setCountRows($countRows) {
        $this->countRows = $countRows;
    }

    /**
     * getCountRows
     * @return type
     */
    public function getCountRows() {
        return $this->countRows;
    }

    /**
     * setSumPage
     */
    public function setSumPage() {
        $this->sumPage = $this->getCountRows() % $this->getLimit() > 0 ? (intval($this->getCountRows() / $this->getLimit()) + 1) : ($this->getCountRows() / $this->getLimit());
        if ($this->getPage() < $this->sumPage) {
            $this->previousPage = $this->getPage() - 1;
            $this->nextPage = $this->getPage() + 1;
        } else {
            $this->previousPage = $this->sumPage - 1;
            $this->nextPage = $this->sumPage;
        }
    }

    /**
     * getSumPage
     * @return type
     */
    public function getSumPage() {
        return $this->sumPage;
    }

    /**
     * getSelect
     * @param type $debug
     * @return type
     */
    public function setSelectCount($select = '', $debug = false, $countKey = 'count') {
        $select = str_replace(';', '', $select) . ';';
        if ($debug) {
            dd($select);
        }
        $countRows = $this->db->select($select)->first();
        if (!isset($countRows->attribute->$countKey)) {
            throw new \framework\Exception('countKey不存在');
        }
        $this->setCountRows($countRows->attribute->$countKey);
        $this->setSumPage();
    }

    /**
     * setSelectResults
     * @param type $select
     * @param type $debug
     */
    public function setSelectResults($select = '', $debug = false) {
        $select = str_replace(';', '', $select);
        $select .= ' limit ' . $this->getLimit() . ' offset ' . $this->getOffset() . ';';
        if ($debug) {
            dd($select);
        }
        $this->results = $this->db->select($select)->get();
    }

    /**
     * previousPage
     * @return type
     */
    public function previousPage() {
        if (!$this->hasQuestionmark()) {
            return $this->uri . '?' . $this->pageKey . '=' . $this->previousPage;
        }
        return $this->uri . '&' . $this->pageKey . '=' . $this->previousPage;
    }

    /**
     * nextPage
     * @return type
     */
    public function nextPage() {
        if (!$this->hasQuestionmark()) {
            return $this->uri . '?' . $this->pageKey . '=' . $this->nextPage;
        }
        return $this->uri . '&' . $this->pageKey . '=' . $this->nextPage;
    }

    /**
     * currentPage
     * @param type $page
     * @return type
     */
    public function currentPage($page = 0) {
        if ($page > 0) {
            if (!$this->hasQuestionmark()) {
                return $this->uri . '?page=' . $page;
            }
            return $this->uri . '&page=' . $page;
        }
        if (!$this->hasQuestionmark()) {
            return $this->uri . '?page=' . $this->page;
        }
        return $this->uri . '&page=' . $this->page;
    }

    /**
     * has ?
     * @return boolean
     */
    public function hasQuestionmark() {
        $parseUrl = parse_url($this->uri);
        if (!array_key_exists('query', $parseUrl)) {
            return false;
        }
        return true;
    }

    /**
     * __destruct
     */
    public function __destruct() {
        unset($this->db);
        unset($this->request);
    }

}
