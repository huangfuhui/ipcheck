<?php
/**
 * 分页工具
 */

namespace Ipcheck\Tool;

class PageSelectorClass
{
    private $baseURL;           // 基础URL
    private $pageCount;         // 总页数
    private $currentPage;       // 当前页数

    private $paramName;         // 页码参数名
    private $appendParam;       // 是否属于URL追加参数

    public function __construct($baseURL, $pageCount, $paramName = 'page', $appendParam = false)
    {
        $this->baseURL = $baseURL;
        $this->pageCount = $pageCount;
        $this->paramName = $paramName;
        $this->appendParam = $appendParam;
    }

    /**
     * 渲染输出页码选择器
     * @param int $selectPage
     * @return string
     */
    public function getSelector($selectPage = 1)
    {
        // 如果选择的页码大于总页码则默认取最大的页码数，反之则取第一页
        if ($selectPage > $this->pageCount) {
            $this->currentPage = $this->pageCount;
        } elseif ($selectPage < 1) {
            $this->currentPage = 1;
        } else {
            $this->currentPage = $selectPage;
        }

        $pageHTML = <<<HTML
<div class="page_selector">
    <div class="show_page">
HTML;

        // 尽量使被选中的页码居中显示
        $pageForSpan = '';
        $startPage = 1;
        $endPage = 1;
        if ($this->currentPage - 3 > 0 && $this->currentPage + 3 <= $this->pageCount) {
            $startPage = $this->currentPage - 3;
            $endPage = $this->currentPage + 3;
        } elseif ($this->currentPage - 3 < 1 && $this->currentPage + 3 > $this->pageCount) {
            $startPage = 1;
            $endPage = $this->pageCount;
        } elseif ($this->currentPage - 3 < 1 && $this->currentPage + 3 <= $this->pageCount) {
            $startPage = 1;
            $endPage = $startPage + 6;
            $endPage > $this->pageCount ? $endPage = $this->pageCount : null;
        } elseif ($this->currentPage - 3 > 0 && $this->currentPage + 3 > $this->pageCount) {
            $startPage = $this->pageCount - 6;
            $startPage < 0 ? $startPage = 1 : null;
            $endPage = $this->pageCount;
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            $url = $this->renderPageURL($i);
            if ($i == $this->currentPage) {
                $pageForSpan .= '<a class="page_url selected_url" href="' . $url . '"><span>' . $i . '</span></a>';
            } else {
                $pageForSpan .= '<a class="page_url" href="' . $url . '"><span>' . $i . '</span></a>';
            }
        }

        $firstPageURL = $this->renderPageURL(1);
        $lastPageUrl = $this->renderPageURL($this->pageCount);
        $this->currentPage < $this->pageCount ? $nextPageUrl = $this->renderPageURL($this->currentPage + 1)
            : $nextPageUrl = $this->renderPageURL($this->currentPage);
        $this->currentPage > 1 ? $frontPageUrl = $this->renderPageURL($this->currentPage - 1)
            : $frontPageUrl = $this->renderPageURL($this->currentPage);

        $pageHTML .= <<<HTML
    <a href="$firstPageURL"><span>首 页</span></a><a href="$frontPageUrl"><span>上一页</span></a>$pageForSpan<a href="$nextPageUrl"><span>下一页</span></a><a href="$lastPageUrl"><span>尾 页</span></a>
</div>
    <div class="show_page_info"><span>总共 $this->pageCount 页, 当前 $this->currentPage 页</span></div>
</div>
HTML;
        return $pageHTML;
    }

    /**
     * 渲染页码的URL
     * @param $page
     * @return string
     */
    private function renderPageURL($page)
    {
        if ($this->appendParam) {
            return $this->baseURL . '&' . $this->paramName . '=' . $page;
        } else {
            return $this->baseURL . '?' . $this->paramName . '=' . $page;
        }
    }
}