<?php
date_default_timezone_set('Asia/Shanghai');
//是否开启调试
define("ADEBUG",false);
if($_GET['time'] == 1){
    echo PHP_VERSION,"<br>";
    define('ROOT_START', microtime());
    list($tmp1,$tmp2) = explode(" ",microtime());
    echo "开始时间：{$tmp2}  |  {$tmp1}<br>";
}
if($_GET['debug'] == 1 || ADEBUG)
{
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}
define('ROOT_PATH', dirname(__FILE__));
include(ROOT_PATH . '/eccore/ecmall.php');

/* 定义配置信息 */
ecm_define(ROOT_PATH . '/data/config.inc.php');


/* 启动ECMall */
ECMall::startup(array(
    'default_module'   =>  'default',
    'default_act'   =>  'index',
    'app_root'      =>  ROOT_PATH . '/app',
    'external_libs' =>  array(
        ROOT_PATH . '/includes/global.lib.php',//一些全局函数
        ROOT_PATH . '/eccore/controller/app.base.php',//基础控制器类
        ROOT_PATH . '/eccore/model/model.base.php',//模型基础类
        ROOT_PATH . '/includes/module.base.php',//模块基础类
        //ROOT_PATH . '/includes/libraries/time.lib.php',//时间函数库
    ),
));
?>
