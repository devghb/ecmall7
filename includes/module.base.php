<?php

/* 接口BaseModule */
!defined('ROOT_PATH') && exit('Forbidden');

/**
 *    模块前台控制器基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class IndexbaseModule extends BaseApp
{
    function __construct()
    {
        $this->IndexbaseModule();
    }
    function IndexbaseModule()
    {
        parent::__construct();
    }
    function _config_view()
    {
        parent::_config_view();
        define_module();
        $this->_view->_template = MODULE_ABSPATH . '/templates';
        $this->_view->_res_base = MODULE_WEBPATH . '/templates';
    }
    /**
     *    获取分页信息
     *
     *    @author    Garbin
     *    @return    array
     */
    function _get_page($page_per = 10)
    {
        $page = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']);
        $start = ($page -1) * $page_per;

        return array('limit' => "{$start},{$page_per}", 'curr_page' => $page, 'pageper' => $page_per);
    }

    /**
     * 格式化分页信息
     * @param   array   $page
     * @param   int     $num    显示几页的链接
     */
    function _format_page(&$page, $num = 7)
    {
        $page['page_count'] = ceil($page['item_count'] / $page['pageper']);
        $mid = ceil($num / 2) - 1;
        if ($page['page_count'] <= $num)
        {
            $from = 1;
            $to   = $page['page_count'];
        }
        else
        {
            $from = $page['curr_page'] <= $mid ? 1 : $page['curr_page'] - $mid + 1;
            $to   = $from + $num - 1;
            $to > $page['page_count'] && $to = $page['page_count'];
        }

        /*
        if (preg_match('/[&|\?]?page=\w+/i', $_SERVER['REQUEST_URI']) > 0)
        {
            $url_format = preg_replace('/[&|\?]?page=\w+/i', '', $_SERVER['REQUEST_URI']);
        }
        else
        {
            $url_format = $_SERVER['REQUEST_URI'];
        }
        */

        /* 生成app=goods&act=view之类的URL */
        if (preg_match('/[&|\?]?page=\w+/i', $_SERVER['QUERY_STRING']) > 0)
        {
            $url_format = preg_replace('/[&|\?]?page=\w+/i', '', $_SERVER['QUERY_STRING']);
        }
        else
        {
            $url_format = $_SERVER['QUERY_STRING'];
        }

        $page['page_links'] = array();
        $page['first_link'] = ''; // 首页链接
        $page['first_suspen'] = ''; // 首页省略号
        $page['last_link'] = ''; // 尾页链接
        $page['last_suspen'] = ''; // 尾页省略号
        for ($i = $from; $i <= $to; $i++)
        {
            $page['page_links'][$i] = url("{$url_format}&page={$i}");
        }
        if (($page['curr_page'] - $from) < ($page['curr_page'] -1) && $page['page_count'] > $num)
        {
            $page['first_link'] = url("{$url_format}&page=1");
            if (($page['curr_page'] -1) - ($page['curr_page'] - $from) != 1)
            {
                $page['first_suspen'] = '..';
            }
        }
        if (($to - $page['curr_page']) < ($page['page_count'] - $page['curr_page']) && $page['page_count'] > $num)
        {
            $page['last_link'] = url("{$url_format}&page=" . $page['page_count']);
            if (($page['page_count'] - $page['curr_page']) - ($to - $page['curr_page']) != 1)
            {
                $page['last_suspen'] = '..';
            }
        }

        $page['prev_link'] = $page['curr_page'] > $from ? url("{$url_format}&page=" . ($page['curr_page'] - 1)) : "";
        $page['next_link'] = $page['curr_page'] < $to ? url("{$url_format}&page=" . ($page['curr_page'] + 1)) : "";
    }
}

/**
 *    定义模型路径常量
 *
 *    @author    Garbin
 *    @param    none
 *    @return    void
 */
function define_module()
{
    /* 相对路径 */
    define('MODULE_RELPATH', 'external/modules/' . MODULE);

    /* 绝对路径 */
    define('MODULE_ABSPATH', ROOT_PATH . '/' . MODULE_RELPATH);

    /* URI */
    define('MODULE_WEBPATH', SITE_URL . '/' . MODULE_RELPATH);
}

?>
