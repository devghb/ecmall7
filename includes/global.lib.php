<?php
/**
 * @user
 * @note 定义缓存
 * @param array $options
 * @return MemcacheServer|PhpCacheServer
 */
function &cache_server($options = array())
{
    $host = "";
    $port = "";
    //缓存类型 1.默认文件缓存2.memcached缓存
    $server = isset($options['server']) ? trim($options['server']) : CACHE_SERVER;
    if($server == 'memcached'){
        $host = isset($options['host']) ? trim($options['host']) : MEMCACHE_HOST;
        $port = isset($options['port']) ? trim($options['port']) : MEMCACHE_PORT;
    }
    import('cache.lib');
    $key = md5($server.$host.$port);
    static $CS = array();
    if (!isset($CS[$key]))
    {
        switch ($server)
        {
            case 'memcached':
                $CS[$key] = new MemcacheServer(array(
                    'host'  => $host,
                    'port'  => $port,
                ));
            break;
            default:
                $CS[$key] = new PhpCacheServer;
                $CS[$key]->set_cache_dir(ROOT_PATH . '/temp/caches');
            break;
        }
    }

    return $CS[$key];
}

/**
 *    获取环境变量
 *
 *    @author    Garbin
 *    @param     string $key
 *    @param     mixed  $val
 *    @return    mixed
 */
function &env($key, $val = null)
{
    !isset($GLOBALS['EC_ENV']) && $GLOBALS['EC_ENV'] = array();
    $vkey = $key ? strtokey("{$key}", '$GLOBALS[\'EC_ENV\']') : '$GLOBALS[\'EC_ENV\']';
    if ($val === null)
    {
        /* 返回该指定环境变量 */
        $v = eval('return isset(' . $vkey . ') ? ' . $vkey . ' : null;');

        return $v;
    }
    else
    {
        /* 设置指定环境变量 */
        eval($vkey . ' = $val;');

        return $val;
    }
}


/**
 *    获取邮件内容
 *
 *    @author    Garbin
 *    @param     string $mail_tpl
 *    @param     array  $var
 *    @return    array
 */
function get_mail($mail_tpl, $var = array())
{
    $subject = '';
    $message = '';

    /* 获取邮件模板 */
    $model_mailtemplate =& af('mailtemplate');
    $tpl_info   =   $model_mailtemplate->getOne($mail_tpl);
    if (!$tpl_info)
    {
        return false;
    }

    /* 解析其中变量 */
    $tpl =& v(true);
    $tpl->direct_output = true;
    $tpl->assign('site_name', Conf::get('site_name'));
    $tpl->assign('site_url', SITE_URL);
    $tpl->assign('mail_send_time', local_date('Y-m-d H:i', gmtime()));
    foreach ($var as $key => $val)
    {
        $tpl->assign($key, $val);
    }
    $subject = $tpl->fetch('str:' . $tpl_info['subject']);
    $message = $tpl->fetch('str:' . $tpl_info['content']);

    /* 返回邮件 */

    return array(
        'subject'   => $subject,
        'message'   => $message
    );
}

/**
 *    获取邮件发送网关
 *
 *    @author    Garbin
 *    @return    object
 */
function &get_mailer()
{
    static $mailer = null;
    if ($mailer === null)
    {
        /* 使用mailer类 */
        import('mailer.lib');
        $sender     = Conf::get('site_name');
        $from       = Conf::get('email_addr');
        $protocol   = Conf::get('email_type');
        $host       = Conf::get('email_host');
        $port       = Conf::get('email_port');
        $username   = Conf::get('email_id');
        $password   = Conf::get('email_pass');
        $mailer = new Mailer($sender, $from, $protocol, $host, $port, $username, $password);
    }

    return $mailer;
}

function i18n_code()
{
    $code = 'zh-CN';
    $lang_code = substr(LANG, 0, 2);
    switch ($lang_code)
    {
        case 'sc':
            $code = 'zh-CN';
        break;
        case 'tc':
            $code = 'zh-TW';
        break;
        default:
            $code = 'zh-CN';
        break;
    }

    return $code;
}

/**
 *    从字符串获取指定日期的结束时间(24:00)
 *
 *    @author    Garbin
 *    @param     string $str
 *    @return    int
 */
function gmstr2time_end($str)
{
    return gmstr2time($str) + 86400;
}

/**
 *    获取URL地址
 *
 *    @author    Garbin
 *    @param     mixed $query
 *    @return    string
 */
function url($query)
{

    $url = 'index.php?' . $query;

    return str_replace('&', '&amp;', $url);
}


/**
 *    计算剩余时间
 *
 *    @author    Garbin
 *    @param     string $format
 *    @param     int $time;
 *    @return    string
 */
function lefttime($time, $format = null)
{
    $lefttime = $time - gmtime();
    if ($lefttime < 0)
    {
        return '';
    }
    if ($format === null)
    {
        if ($lefttime < 3600)
        {
            $format = Lang::get('lefttime_format_1');
        }
        elseif ($lefttime < 86400)
        {
            $format = Lang::get('lefttime_format_2');
        }
        else
        {
            $format = Lang::get('lefttime_format_3');
        }
    }
    $d = intval($lefttime / 86400);
    $lefttime -= $d * 86400;
    $h = intval($lefttime / 3600);
    $lefttime -= $h * 3600;
    $m = intval($lefttime / 60);
    $lefttime -= $m * 60;
    $s = $lefttime;

    return str_replace(array('%d', '%h', '%i', '%s'),array($d, $h,$m, $s), $format);
}


/**
 * 多维数组排序（多用于文件数组数据）
 *
 * @author Hyber
 * @param array $array
 * @param array $cols
 * @return array
 *
 * e.g. $data = array_msort($data, array('sort_order'=>SORT_ASC, 'add_time'=>SORT_DESC));
 */
function array_msort($array, $cols)
{
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
    }
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
    }
    $eval = substr($eval,0,-1).');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k,1);
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
        }
    }
    return $ret;
}


?>
