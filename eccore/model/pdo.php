<?php

/**
 * pdo 公用类库
 * ============================================================================
 * 修改mysql为pdo接口用于兼容php7
 * guohongbin 2016-11-17
 * ============================================================================
 */

class cls_pdo
{
    public static $dbtype = "";//类型如：mysql|sqlite
    public static $dbhost = "";//地址
    public static $dbport = "";//端口
    public static $dbname = "";//数据库名
    public static $dbuser = "";//用户名
    public static $dbpass = "";//密码
    public static $charset = "";//编码


    private $_link_id = null;//pdo语句对象
    public $db = null;//连接池|链接对象
    public $prefix = '';//表前缀
    public $query_log = array();//保存查询命令，调试的时候可能用到

    /**
     * 构造函数
     * @param $host
     * @param $port
     * @param $name
     * @param $user
     * @param $password
     * @param string $charset
     */
    public function __construct($params,$charset = 'UTF8')
    {
        self::$dbtype = $params['type'];//mysql';
        self::$dbhost = $params['host'];
        self::$dbport = $params['port'];
        self::$dbname = $params['name'];
        self::$dbuser = $params['user'];
        self::$dbpass = $params['password'];
        self::$charset = $charset;
        $this->prefix = isset($params['prefix']) ? $params['prefix'] : "";

        $this->db = $this->connect();
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->db = null;
    }

    /**
     * @user ghb
     * @note 连接数据库
     */
    public function connect()
    {
        try {
            $db =  new PDO( self::$dbtype . ':host=' . self::$dbhost . ';port=' . self::$dbport . ';dbname=' . self::$dbname . ';charset='.self::$charset, self::$dbuser, self::$dbpass);
            return $db;
        }
        catch ( PDOException $e ) {
            die ( "连接错误:" . $e->getMessage () );
        }
    }

    /**
     * @user ghb
     * @note 执行一个sql命令
     * @param $sql sql命令
     * @param array $execute 预处理数据
     * @return PDOStatement
     */
    public function query($sql,array $execute = array())
    {
        try{
            $this->query_log[] = array(
                "sql"   => $sql,
                "execute" => $execute,
            );
            $res = $this->db->prepare($sql);
            if($res == false) throw new PDOException('数据库服务器不能成功地处理语句！');

            //保存pdo语句对象
            $this->_link_id = $res;

            $res->execute($execute);
            //检查错误
            if($res->errorCode() != '00000'){
                $this->getPDOError($res,$sql);
            }
            else{
                return $res;
            }
        }
        catch(PDOException $e)
        {
            $this->getPDOError($res,$sql,$e->getMessage());
        }
    }

    /**
     * @user ghb
     * @note 返回结果集中的所有数组
     * @param $sql
     * @param array $execute
     * @param int $fetch_style
     * @return array
     */
    public function getAll($sql,$execute = array(),$fetch_style = PDO::FETCH_ASSOC)
    {
        $query = $this->query($sql,$execute);
        $res = $query->fetchAll($fetch_style);
        return $res;
    }

    /**
     * @user ghb
     * @note 以主键索引形式返回结果集(为了兼容ecmall)
     * @param $sql
     * @param array $execute
     * @return array
     */
    public function getAllWithIndex($sql,$index_key,$execute = array())
    {
        $result = $this->getAll($sql,$execute);
        $rtn = array();
        if(!empty($result)){
            foreach($result as &$row)
            {
                $rtn[$row[$index_key]] = $row;
            }
        }
        unset($result);
        return $rtn;
    }

    /**
     * @user ghb
     * @note 获取一个值
     * @param $sql
     * @param array $execute
     * @param int $fetch_style
     * @return mixed|string
     */
    public function getOne($sql,$execute = array(),$fetch_style = PDO::FETCH_ASSOC)
    {
        $res = $this->query($sql,$execute);
        $result = $res->fetch($fetch_style);
        if(empty($result)){
            return "";
        }
        else{
            //reset($result);//将数组的内部指针指向第一个单元
            return current($result);
        }
    }

    /**
     * @user ghb
     * @note 返回结果集中一行数据
     * @param $sql
     * @param array $execute
     * @param int $fetch_style
     * @return mixed
     */
    public function getRow($sql,$execute = array(),$fetch_style = PDO::FETCH_ASSOC)
    {
        $res = $this->query($sql,$execute);
        $result = $res->fetch($fetch_style);
        return $result;
    }

    /**
     * @user ghb
     * @note 返回结果中一列数据
     * @param $sql
     * @param array $execute
     * @param int $fetch_argument
     * @return array
     */
    public function getCol($sql,$execute = array(),$fetch_argument = 0)
    {
        $res = $this->query($sql,$execute);
        $result = $res->fetchAll(PDO::FETCH_COLUMN,$fetch_argument);
        return $result;
    }

    /**
     * @user ghb
     * @note 返回受影响行数
     * @return mixed
     */
    public function affected_rows()
    {
        return $this->_link_id->rowCount();
    }

    /**
     * @user ghb
     * @note 返回最后一条插入数据的主键
     * @return mixed
     */
    public function insert_id()
    {
        return $this->db->lastInsertId();
    }





    /**
     * @user ghb
     * @note 错误处理
     * @param $res
     * @param string $msg
     */
    public function getPDOError($res,$sql,$msg = '')
    {
        $error = $res->errorInfo();
        $key = md5(implode("",$error));
        if(ADEBUG || $_GET['debug'] == 1){
            $html = <<<html
<b>MySQL server error report:</b><br />
Key:{$key}<br />
SQL:{$sql}<br />
Error:{$error[1]}<br />
Errno:{$error[2]}<br />
html;
        }
        else{
            $html = <<<html
<b>MySQL server error report:</b><br />
Key:{$key}<br />
html;
        }

        //这是抛出的异常
        if($msg){
            $html.= "Message:{$msg}<br />";
        }
        echo $html;

        //关闭调试才会存文件，到时候以key值去文件中找错误信息
        if(!ADEBUG){
            //保存错误信息至文件
            $filename = ROOT_PATH."/temp/logs/".date("Y-m-d").".txt";
            $data = <<<html
SQL:{$sql}
Error:{$error[1]}
Errno:{$error[2]}
html;
            if(file_exists($filename)){
                $data = "\r\n时间：".date("Y-m-d H:i:s")."   Key:{$key}  \r\n".$data."\r\n";
            }
            else{
                $data = "时间：".date("Y-m-d H:i:s")."   Key:{$key}\r\n".$data."\r\n";
            }
            file_put_contents($filename,$data,FILE_APPEND);
        }
        exit();
    }
}
?>