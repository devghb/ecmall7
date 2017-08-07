<?php
class Index extends DefaultModule
{
	function __construct()
	{
		parent::__construct();
	}

    /**
     * 入口
     */
	public function main()
	{
	    header('Location:/index.php?module=wanjia&act=pc_goods&op=detail&gid=93550');
	    return;
		//请求转发
		$op = isset($_GET['op']) && !empty($_GET['op']) ? addslashes($_GET['op']) : 'index';
        $op = ACT."_{$op}";
        if(!method_exists($this,$op)){
            die("missing_op");
        }
		$this->$op();
	}

    /**
     * 首页
     */
    public function index_index()
    {
        //http://test.artxun.cn/aaa/aaa/aaa/wap_tool_CreateBidOrder?goods_id=1156&mid=7de2fba851118fbf7ddce99eca7537e6
        //goods_id : 拍品id
//mid : md5(goods_id.'YPWJCreateBidOrder')
        echo "http://test.artxun.cn/aaa/aaa/aaa/wap_tool_CreateBidOrder?goods_id=1405&mid=".md5('1405YPWJCreateBidOrder');
        return;
        echo "session相关:<br>";
        $_SESSION['name'] = '小明';//写
        var_dump($_SESSION['name']);//读
        //unset($_SESSION['name']);//销毁某一个session值
        //session_destroy();//销毁当前用户所有session
        echo "<br>============================================================================<br>";
         //------------------模板相关
        echo "模板相关:<br><br>";
        $this->assign("title", "api");
        $this->assign("name", "api");
        $this->assign("tip","(一)");
        $this->assign("array",array(
            1=>"A",2=>"B"));
        $this->assign("time",time());
        $this->display("default.index.html");
        echo "<br>============================================================================<br>";

        //------------------数据库相关
        //查询
        $wx_caiji = &m("wx_caiji");
        $select = $wx_caiji->find(array(
            "conditions"    => "",
            "fields"         => "id,`no`,time,`from`",
            "order"          => "id desc",
            "limit"          => "0,2",
            "count"          => true
        ));
        echo "查询:",'<pre>'.print_r($select,true).'<pre>',"共：".$wx_caiji->getCount(),"条<br>";
        echo "============================================================================<br>";
        $page = $this->_get_page(10);//每页几条
        $page['item_count'] = $wx_caiji->getCount();//共多少条
        $this->_format_page($page);
        echo '分页<pre>'.print_r($page,true).'</pre>';
        echo "============================================================================<br>";
        //修改
        $edit = $wx_caiji->edit("id = 2",array(
            "`from`"    => time(),
            "`no`"    => "11",
        ));
        //如果重复修改返回的受影响行数也是0
        echo "修改：受影响行数".$edit,"<br>";
        echo "============================================================================<br>";
        //添加
        $add = $wx_caiji->add(array(
            "`from`"  => time(),
            "time"    => 1,
        ));
        echo "添加：id=".$add,"<br>";
        echo "============================================================================<br>";
        //删除(我们通常只做逻辑删除，不物理删除)
        $delete = $wx_caiji->drop("id = 1");
        echo "删除:受影响行数".$delete,"<br>";
        echo "============================================================================<br>";
        echo "执行的SQL:<br>";
        print_r($wx_caiji->db->query_log);
        echo "============================================================================<br>";
        //----------------------缓存相关
        echo "缓存相关:<br>";
        //文件缓存
        $key = "abc";
        $value = "value";
        $time = "10";//有效时间(秒)
        $cache = &cache_server();
        $cache->set($key,$value,$time);
        echo "设置：{$key}:{$value}","<br>";
        echo "获取：",$cache->get($key),"<br>";
        //$cache->delete("abc");
        //$cache->clear();
        //memcache缓存
        /*
        $cache = &cache_server(array(
            'server'  => 'memcached',
            'host'    => '192.168.3.123',
            'port'    => '123456',
        ));
        $cache->set("abc","value",time() + 3600);
        $cache->get("abc");
        $cache->delete("abc");
        */
        echo "============================================================================<br>";
    }
}