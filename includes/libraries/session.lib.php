<?php
/**
 * SESSION 公用类库
 * ============================================================================
 *
 * ============================================================================
 * $Id: session.lib.php 2016-11-18 $
 */

class SessionProcessor implements SessionHandlerInterface
{
    private $cache = null;
    private $session_name = '';
    private $session_id = '';

    private $lifetime = 1296000;//有效时间默认15天
    private $session_cookie_path   = '/';
    private $session_cookie_domain = '';
    private $session_cookie_secure = false;

    public $ip = "";

    public function __construct($session_name = 'ART_ID')
    {
        //缓存服务应该用memcached或redis
        //todo 现在用文件缓存测试
        //$this->cache = &cache_server();
        $this->cache = &cache_server(array(
            'server'  => 'memcached',
            'host'    => 'eb1d157145074a61.m.cnbjalicm12pub001.ocs.aliyuncs.com',
            'port'    => '11211',
        ));

        $this->session_cookie_path = COOKIE_PATH;
        $this->session_cookie_domain = COOKIE_DOMAIN;

        $this->session_name = $session_name;
        session_name($this->session_name); // 自定义session_name
        $this->ip = real_ip();
        //生成session_id
        if(empty($_COOKIE[$this->session_name])){
            $this->session_id = $this->gen_session_id();
        }
        else{
            $this->session_id = $_COOKIE[$this->session_name];
        }
        //写session
        $this->set_session_id();

    }

    public function open($savePath, $sessionName)
    {
        //echo "打开{$savePath}:{$sessionName}<br>";

        return true;
    }
    public function close()
    {
        //echo "关闭<br>";

        return true;
    }
    public function read($id)
    {
        //echo "读{$id}<br>";
        return $this->cache->get($id);
    }
    public function write($id, $data)
    {
        //echo "写{$id}:{$data}<br>";
        $this->cache->set($id,$data,$this->lifetime);

        return true;
    }
    public function destroy($id)
    {
        echo "销毁：{$id}<br>";
        $this->cache->delete($id);
        file_put_contents(ROOT_PATH.'/temp/abcd.txt',$id);
        return true;
    }
    public function gc($maxlifetime)
    {
        echo "清理{$maxlifetime}<br>";

        return true;
    }

    /**
     * @user ghb
     * @note 把session_id 写到cookie里
     */
    public function set_session_id()
    {
        session_id($this->session_id);
        session_set_cookie_params($this->lifetime, $this->session_cookie_path, $this->session_cookie_domain, $this->session_cookie_secure);
        //php里设一个cookie,如果马上去获取，
        //得不到cookie值可能是因为还没写到客户端吧
        //$_COOKIE[$this->session_name] = $this->session_id;//让cookie立马生效
    }

    /**
     * @user ghb
     * @note 生成一个session_id
     * @return string
     */
    function gen_session_id()
    {
        $charid = md5(uniqid(mt_rand(), true)) . sprintf('%08x', crc32(ROOT_PATH . $this->ip) . rand(0,10000));
        $uuid = substr($charid, 0, 8)
            .substr($charid, 8, 4)
            .substr($charid,12, 4)
            .substr($charid,16, 4)
            .substr($charid,20,12);
        return $charid;
    }
    function get_session_id()
    {
        return $this->session_id;
    }
    function my_session_start()
    {
        session_set_save_handler($this, true);
        return session_start();
    }
}
//$handler = new SessionProcessor();
//$handler->my_session_start();
//$_SESSION['a'] = 123;
//$_SESSION['b'] = 1;
//unset($_SESSION['abc']);
?>
