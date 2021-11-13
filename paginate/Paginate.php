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

class Paginate {

    public $page = 0;
    public $pageKey = 'page';
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
    public function __construct(\framework\Request $request, \framework\Database $db, $limit = 10, $pageKey = 'page', $page = 0) {
        $this->request = $request;
        $this->pageKey = $pageKey;
        $this->db = $db;
        $this->setLimit($limit);
        $this->setPage($pageKey);
        $this->setOffset();
        if (isCli()) {
            $this->setCliPage($page);
        }
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
     * @param type $pageKey
     */
    public function setPage($pageKey) {
        if (!$pageKey) {
            return;
        }
        $this->page = intval($this->request->get($pageKey, 0));
        if ($this->page <= 0) {
            $this->page = 1;
        }
    }

    /**
     * setCliPage
     * @param type $pageKey
     */
    public function setCliPage($page) {
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
     * appends
     * @param type $appends
     */
    public function appends($appends = []) {
        $this->uri = \framework\Route::datail('route');
        if (is_array($appends) && count($appends)) {
            $this->uri .= '?' . http_build_query($appends);
        } else {
            $param = $this->request->fliterParam($this->request->all());
            unset($param[$this->pageKey]);
            if (count($param)) {
                $this->uri .= '?' . http_build_query($param);
            }
        }
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
     * render
     * @return type
     */
    public function render() {
        if ($this->sumPage <= 5) {
            $html = ['<div class="layui-box layui-laypage layui-laypage-default">'];
            if ($this->getPage() <= 1) {
                $html[] = '<a href="javascript:;" class="layui-laypage-prev layui-disabled" data-page="0">上一页</a>';
            } else {
                $html[] = '<a href="' . $this->previousPage() . '" class="layui-laypage-prev" data-page="0">上一页</a>';
            }
            for ($i = 1; $i <= $this->sumPage; $i++) {
                if ($this->getPage() == $i) {
                    $html[] = '<span class="layui-laypage-curr"><em class="layui-laypage-em"></em><em>' . $i . '</em></span>';
                } else {
                    $html[] = '<a href="' . $this->currentPage($i) . '" data-page="' . $i . '">' . $i . '</a>';
                }
            }
            if ($this->getPage() < $this->sumPage) {
                $html[] = '<a href="' . $this->nextPage() . '" class="layui-laypage-next" data-page="' . $i . '">下一页</a>';
            } else {
                $html[] = '<a href="javascript:;" class="layui-laypage-next" data-page="' . $i . '">下一页</a>';
            }
            $html[] = '</div>';
            return implode('', $html);
        }
        $html = ['<div class="layui-box layui-laypage layui-laypage-default">'];
        if ($this->getPage() <= 1) {
            $html[] = '<a href="javascript:;" class="layui-laypage-prev layui-disabled" data-page="0">上一页</a>';
        } else {
            $html[] = '<a href="' . $this->previousPage() . '" class="layui-laypage-prev" data-page="0">上一页</a>';
        }

        //结束<5补齐
        if ($this->getPage() > 2) {
            if (($this->getPage() - 4) > 0) {
                $html[] = '<a href="' . $this->currentPage($this->getPage() - 4) . '" data-page="' . ($this->getPage() - 4) . '">' . ($this->getPage() - 4) . '</a>';
            }
            if (($this->getPage() - 3) > 0) {
                $html[] = '<a href="' . $this->currentPage($this->getPage() - 3) . '" data-page="' . ($this->getPage() - 3) . '">' . ($this->getPage() - 3) . '</a>';
            }
            if (($this->getPage() - 2) > 0) {
                $html[] = '<a href="' . $this->currentPage($this->getPage() - 2) . '" data-page="' . ($this->getPage() - 2) . '">' . ($this->getPage() - 2) . '</a>';
            }
            if (($this->getPage() - 1) > 0) {
                $html[] = '<a href="' . $this->currentPage($this->getPage() - 1) . '" data-page="' . ($this->getPage() - 1) . '">' . ($this->getPage() - 1) . '</a>';
            }
        }
        //初始1开始
        if ($this->getPage() <= 2) {
            if (($this->getPage() - 2) > 0) {
                $html[] = '<a href="' . $this->currentPage($this->getPage() - 2) . '" data-page="' . ($this->getPage() - 2) . '">' . ($this->getPage() - 2) . '</a>';
            }
            if (($this->getPage() - 1) > 0) {
                $html[] = '<a href="' . $this->currentPage($this->getPage() - 1) . '" data-page="' . ($this->getPage() - 1) . '">' . ($this->getPage() - 1) . '</a>';
            }
        }
        $html[] = '<span class="layui-laypage-curr"><em class="layui-laypage-em"></em><em>' . $this->getPage() . '</em></span>';
        if (($this->getPage() + 1) <= $this->sumPage) {
            $html[] = '<a href="' . $this->currentPage($this->getPage() + 1) . '" data-page="' . ($this->getPage() + 1) . '">' . ($this->getPage() + 1) . '</a>';
        }
        if (($this->getPage() + 2) <= $this->sumPage) {
            $html[] = '<a href="' . $this->currentPage($this->getPage() + 2) . '" data-page="' . ($this->getPage() + 2) . '">' . ($this->getPage() + 2) . '</a>';
        }
        //初始<5补齐
        if (($this->getPage() - 1) <= 0) {
            if (($this->getPage() + 3) <= $this->sumPage) {
                $html[] = '<a href="' . $this->currentPage($this->getPage() + 3) . '" data-page="' . ($this->getPage() + 3) . '">' . ($this->getPage() + 3) . '</a>';
            }
        }
        if (($this->getPage() - 2) <= 0) {
            if (($this->getPage() + 4) <= $this->sumPage) {
                $html[] = '<a href="' . $this->currentPage($this->getPage() + 4) . '" data-page="' . ($this->getPage() + 4) . '">' . ($this->getPage() + 4) . '</a>';
            }
        }

        if ($this->getPage() < $this->sumPage) {
            $html[] = '<a href="' . $this->nextPage() . '" class="layui-laypage-next" data-page="' . $i . '">下一页</a>';
        } else {
            $html[] = '<a href="javascript:;" class="layui-laypage-next layui-disabled" data-page="' . $i . '">下一页</a>';
        }
        $html[] = '</div>';
        return implode('', $html);
    }

    /**
     * __destruct
     */
    public function __destruct() {
        unset($this->db);
        unset($this->request);
    }

}
