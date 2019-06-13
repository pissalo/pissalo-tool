<?php
$page_start_ime = microtime();
define('IN_DCR', true);
define('WEB_INCLUDE', str_replace("\\", '/', dirname(__FILE__)));
define('WEB_DR', str_replace("\\", '/', substr(WEB_INCLUDE, 0, -8)));
define('WEB_CLASS', WEB_INCLUDE . '/class');
define('WEB_DATA', WEB_DR . '/data');
define('WEB_CACHE', WEB_INCLUDE . '/cache');
define('WEB_LOG', WEB_INCLUDE . '/log');
define('WEB_MYSQL_BAKDATA_DIR', WEB_DR . '/data/databak');

//@set_magic_quotes_runtime( 0 );
#$magic_quotes = get_magic_quotes_gpc();

/* 初始化设置 */
@ini_set('memory_limit', '12048M');
@ini_set('session.cache_expire', 180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies', 1);
@ini_set('session.auto_start', 0);
@ini_set('display_errors', 1);
//echo WEB_INCLUDE;
//配置文件
require_once(WEB_INCLUDE . '/app.info.php');
require_once(WEB_INCLUDE . '/config.common.php');
header('Content-type:text/html;charset=' . $web_code);

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
ini_set('display_errors', 'on');

//sqlite的sqlite_escape_string
function my_sqlite_escape_string($str)
{
    if (!empty($str)) {
        return str_replace("'", "''", $str);
    } else {
        return '';
    }
}

//检查和注册外部提交的变量
foreach ($_REQUEST as $_k => $_v) {
    if (strlen($_k) > 0 && preg_match('/^(GLOBALS)/i', $_k)) {
        exit('Request var not allow!');
    }
}

function _get_request($svar)
{
    global $db_type, $magic_quotes;
    if (!$magic_quotes) {
        //开了转义
        if (is_array($svar)) {
            foreach ($svar as $_k => $_v) {
                $svar[ $_k ] = _get_request($_v);
            }
        } else {
            if ($db_type == 1) {
                $svar = trim(my_sqlite_escape_string($svar));
            } elseif ($db_type == 2) {
                $svar = trim(addslashes($svar));
            }
        }
    } else {
        //没有开转义..兼容sqlite
        if (is_array($svar)) {
            foreach ($svar as $_k => $_v) {
                $svar[ $_k ] = _get_request($_v);
            }
        } else {
            if ($db_type == 1) {
                $svar = stripslashes($svar);
                $svar = my_sqlite_escape_string($svar);
            }
        }
    }
    return $svar;
}

$req_data = array();
foreach (array( '_GET', '_POST', ) as $_request) {
    foreach ($$_request as $_k => $_v) {
        ${$_k} = _get_request($_v);
        if ('_COOKIE' != $_request) {
            $req_data[ $_k ] = _get_request($_v);
        }
    }
}
unset($_GET, $_POST);

//时区
if (PHP_VERSION > '5.1') {
    @date_default_timezone_set('PRC');
}

//Session保存路径 不建议手动修改
$session_path = WEB_INCLUDE . "/session";
if (is_writeable($session_path) && is_readable($session_path)) {
    //如果要手动修改session_save_path且后台编辑器用ckeditor的话 请修改include/editor/ckeditor/ckfinder/config.php下的session_save_path;
    session_save_path($session_path);
}

//用户访问的网站host
$web_clihost = 'http://' . $_SERVER[ 'HTTP_HOST' ];

//安全处理类
require_once(WEB_CLASS . '/class.safe.php');

//引入数据库类
require_once(WEB_DR . '/base/class/class.db.php');
require_once(WEB_DR . '/base/class/class.data.php');

function optionClassName($className)
{
    $className = str_replace('Cls', '', $className);
    $className = str_replace('OA\\', '', $className);
    $className = lcfirst($className);
    $upperList = array();
    if (preg_match_all('/[A-Z]/e', $className, $upperList)) {
        foreach ($upperList[0] as $upperWord) {
            $className = str_replace($upperWord, '_' . strtolower($upperWord), $className);
        }
    }
    //$className = preg_replace('/"[a-z]/e', 'strtoupper("$0")', $className);
    //echo $className;
    //echo '<br>';
    return $className;
}

function clsLoader($className)
{
    $className = optionClassName($className);
    $classFile = WEB_CLASS . "/class.{$className}.php";
    if (is_file($classFile)) {
        require_once($classFile);
    }
}
function clsLoaderBase($className)
{
    $className = optionClassName($className);
    $classFile = WEB_DR . "/base/class/class.{$className}.php";
    if (is_file($classFile)) {
        require_once($classFile);
    }
}

spl_autoload_register('clsLoader');
spl_autoload_register('clsLoaderBase');

//引入全站程序静态类
require_once(WEB_CLASS . '/class.app.php');

//全局常用函数
require_once(WEB_INCLUDE . '/common.func.php');

//程序版本
$version = $app_version;

function error_notice($err_no, $err_str, $err_file, $err_line)
{
    $clsLog = ClsApp:: get_cls('log');
    $clsLog->setCollection('log_error');
    $clsLog->addLog(
        array(
            'le_err_no'   => $err_no,
            'le_err_str'  => $err_str,
            'le_err_file' => $err_file,
            'le_err_line' => $err_line,
        )
    );
    //ClsApp:: log('文件' . $err_file . '第' . $err_line . '行发生错误(' . $err_no . '):' . $err_str);
}

//set_error_handler( 'error_notice', ~E_NOTICE & ~E_STRICT );
