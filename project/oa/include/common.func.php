<?php
/**
 * 全站共用function
 */

defined('IN_DCR') or exit('No permission.');

/**
 * 对字符串进行加密
 * @author 黄焕军
 * @param string $s 要加密的字符串
 * @return string 加密后的字符串
 */
function encrypt($s)
{
    return crypt(md5($s), 'dcr');
}

/**
 * 对字符串进行加密 这里调用encrypt函数,为encrypt函数的别名
 * @author 黄焕军
 * @param string $s 要加密的字符串
 * @return string 加密后的字符串
 */
function jiami($s)
{
    return encrypt($s);
}

/**
 * 生成javascript跳转 并自动跳转
 * @author 黄焕军
 * @param string $msg 显示信息
 * @param string $url 要跳转的地址
 * @param string $istop 是不是在父窗口中跳转
 * @return boolean 跳转到相应的网址
 */
function show_next($msg, $url, $istop = 0)
{
    if (strlen($msg) > 0) {
        if ($istop) {
            $mymsg = "<script type='text/javascript'>alert(\"" . $msg . "\");top.location.href=\"" . $url . "\";</script>";
        } else {
            $mymsg = "<script type='text/javascript'>alert(\"" . $msg . "\");location.href=\"" . $url . "\";</script>";
        }
    } else {
        if ($istop) {
            $mymsg = "<script type='text/javascript'>top.location.href=\"" . $url . "\";</script>";
        } else {
            $mymsg = "<script type='text/javascript'>location.href=\"" . $url . "\";</script>";
        }
    }
    echo $mymsg;
    exit;
}

/**
 * 返回上一页
 * @author 黄焕军
 * @param string $msg 显示信息
 * @return boolean 显示一个alert提示信息
 */
function show_back($msg = '')
{
    if (!empty($msg)) {
        echo "<script>alert(\"" . $msg . "\");history.back();</script>'";
    } else {
        echo "<script>history.back();</script>'";
    }
    exit;
}

/**
 * 页面跳转
 * @author 黄焕军
 * @param string $action_msg 提示信息
 * @param int $action_type 1为正确信息 2为错误信息
 * @param string $back_url 跳转的页面
 * @return boolean true
 */
function back_msg($action_msg, $action_type = 1, $back_url = '')
{
    if (empty($back_url)) {
        $back_url = $_SERVER[ 'HTTP_REFERER' ];
    }
    $_SESSION[ 'bullfrog_action_msg' ] = $action_msg;
    $_SESSION[ 'bullfrog_action_type' ] = $action_type;
    /*p_r( $back_url );
    exit;
    $back_url_arr = parse_url( $back_url );
    $query = $back_url_arr['query'];
    $query_arr = explode('&', $query);
    $params = array();
    $url_main = 'http://' . $back_url_arr['host'] . $back_url_arr['path'];
     foreach ($query_arr as $param)
    {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
    //$params['action_msg'] = $action_msg;
    //$params['action_type'] = $action_type;
    $query_str = http_build_query( $params );
    $back_url = $url_main . '?' . $query_str;*/
    /*if( $back_url_arr['query'] )
    {
        $back_url .= "&action_msg={$action_msg}&action_type={$action_type}";
    }else
    {
        $back_url .= "?action_msg={$action_msg}&action_type={$action_type}";
    }*/
    echo "<script type='text/javascript'>window.location.href=\"{$back_url}\";</script>";
}

/**
 * 跳转
 * @author 黄焕军
 * @param string $url 要跳转的地址
 * @return boolean 跳转到$url
 */
function redirect($url)
{
    echo "<script>location.href='" . $url . "';</script>'";
    exit;
}

/**
 * 截取字符串 能对中文进行截取
 * @author 黄焕军
 * @param string $str 要截取的字条串
 * @param string $start 开始截取的位置
 * @param string $len 截取的长度
 * @return string 截取后的字符串
 */
function my_substr($str, $start, $len)
{
    $tmpstr = "";
    $strlen = $start + $len;
    for ($i = 0; $i < $strlen; $i++) {
        if (ord(substr($str, $i, 1)) > 0xa0) {
            $tmpstr .= substr($str, $i, 3);
            $i += 2;
        } else {
            $tmpstr .= substr($str, $i, 1);
        }
    }
    return $tmpstr;
}

/**
 * 写入cookie
 * @author 黄焕军
 * @param string $key cookie名
 * @param string $value cookie值
 * @param string $kptime cookie有效期
 * @param string $pa cookie路径
 * @return boolean 返回true
 */
function put_cookie($key, $value, $kptime = 0, $pa = "/")
{
    setcookie($key, $value, time() + $kptime, $pa);
}

/**
 * 删除cookie
 * @author 黄焕军
 * @param string $key cookie名
 * @return boolean 返回true
 */
function drop_cookie($key)
{
    setcookie($key, '', time() - 360000, "/");
}

/**
 * 获取cookie值
 * @author 黄焕军
 * @param string $key cookie名
 * @return string 获取的cookie的值
 */
function get_cookie($key)
{
    if (!isset($_COOKIE[ $key ])) {
        return '';
    } else {
        return $_COOKIE[ $key ];
    }
}

/**
 * 获取当前IP
 * @author 黄焕军
 * @return string 本机的IP
 */
function get_ip()
{
    if (!empty($_SERVER[ "HTTP_CLIENT_IP" ])) {
        $cip = $_SERVER[ "HTTP_CLIENT_IP" ];
    } else if (!empty($_SERVER[ "HTTP_X_FORWARDED_FOR" ])) {
        $cip = $_SERVER[ "HTTP_X_FORWARDED_FOR" ];
    } else if (!empty($_SERVER[ "REMOTE_ADDR" ])) {
        $cip = $_SERVER[ "REMOTE_ADDR" ];
    } else {
        $cip = '';
    }
    preg_match("/[\d\.]{7,15}/", $cip, $cips);
    $cip = isset($cips[ 0 ]) ? $cips[ 0 ] : 'unknown';
    unset($cips);

    return $cip;
}

/**
 * 获取顶级域名
 * @author 黄焕军
 * @param string $url 要操作的地址
 * @return string $url的顶级域名
 */
function get_top_url($url = '')
{
    if (empty($url)) {
        $url = $_SERVER[ 'SERVER_NAME' ];
    }
    $t_url = parse_url($url);
    $t_url = $t_url[ 'path' ];

    return $t_url;
}

/**
 * 显示提示信息
 * @author 黄焕军
 * @param string $msg 信息内容
 * @param string $msg_type 信息类型1为一般信息 2为错误信息
 * @param string $back 返回地址 如果有多个则传入数组
 * @param string $msgTitle 信息标题
 * @param boolean $is_show_next_tip 为true时显示下你可以下一步操作,为false时不显示
 * @param boolean $is_show_back 为true时显示返回,为false时不显示 版本>=1.0.5
 * @return boolean(true) 显示一个提示信息
 */
function show_msg($msg, $msg_type = 1, $back = '', $msgTitle = '信息提示', $is_show_next_tip = true, $is_show_back = true)
{
    /*
     *msg显示信息 如果要多条则传入数组
     *msg_type信息类型1为一般信息 2为错误信息
     *back为返回地址 如果有多个则传入数组
     *msgTitle为信息标题
     */
    $msg_t = '';
    if (is_array($msg)) {
        foreach ($msg as $value) {
            if ($msg_type == 2) {
                $msg_t .= "<li style='border-bottom:1px dotted #CCC;padding-left:5px;color:red;'>·$value</li>";
            } else {
                $msg_t .= "<li style='border-bottom:1px dotted #CCC;padding-left:5px;color:green;'>·$value</li>";
            }
        }
    } else {
        if ($msg_type == 2) {
            $msg_t = "<li style='border-bottom:1px dotted #CCC;padding-left:5px;color:red;'>·$msg</li>";
        } else {
            $msg_t = "<li style='border-bottom:1px dotted #CCC;padding-left:5px;color:green;'>·$msg</li>";
        }
    }
    if ($is_show_next_tip) {
        if ($is_show_back) {
            $back_t = "<li style='border-bottom:1px dotted #CCC;padding-left:5px;'>·<a style='color:#06F; text-decoration:none' href='javascript:history.back()'>返回</a></li>";
        }
        if (is_array($back)) {
            foreach ($back as $key => $value) {
                $back_t .= "<li style='border-bottom:1px dotted #CCC;padding-left:5px;'>·<a style='color:#06F; text-decoration:none' href='$value'>$key</a></li>";
            }
        }
    }
    global $web_code;
    $msg_str = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'><head><meta http-equiv='Content-Type' content='text/html; charset=$web_code' /><title>信息提示页面</title></head><body><div style='width:500px; margin:0 auto; border:1px #09F solid; font-size:12px;'>
<div style='background-color:#09F; font-size:12px;padding:5px; font-weight:bold; color:#FFF;'>$msgTitle</div>
<div><ul style='list-style:none; line-height:22px; margin:10px; padding:0'>$msg_t</ul></div>";
    if ($is_show_next_tip) {
        $msg_str .= "<div style='border:1px #BBDFF8 solid; width:96%; margin:0 auto; margin-bottom:10px;'><div style='background-color:#BBDFF8; font-size:12px;padding:5px; font-weight:bold; color:#666;'>您可以：</div>
	<div><ul style='list-style:none; line-height:22px; margin:10px; padding:0'>$back_t</ul></div></div></div>";
    }
    $msg_str .= "</body></html>";
    //$msg_str.=$msg;
    echo $msg_str;
    exit;
}

/**
 * 获取随机字符串
 * @author 黄焕军
 * @param int $len 字符串长度
 * @param array $rand_array 要随机的字符组
 * @return string 产生的随机字符串
 */
function get_rand_str($len = 4, $rand_array = array( "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9" ))
{
    $chars = $rand_array;
    $charsLen = count($chars) - 1;
    shuffle($chars);
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[ mt_rand(0, $charsLen) ];
    }

    return $output;
}

/**
 * 格式化输出数据
 * @author 黄焕军
 * @param array $arr 要输出的数组
 * @param boolean $is_stop_output 是否停止输出流 如果为true则exit(); since>=1.0.7
 * @return true
 */
function p_r($arr, $is_stop_output = false)
{
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
    if ($is_stop_output) {
        exit;
    }
}

/**
 * 去除数组空白元素
 * @author 黄焕军
 * @param array $arr 要操作的数组
 * @return array 去重后的数组
 */
function array_remove_empty($arr)
{
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            array_remove_empty($arr[ $key ]);
        } else {
            $value = trim($value);
            if ($value == '') {
                unset($arr[ $key ]);
            } else {
                $arr[ $key ] = $value;
            }
        }
    }
    return $arr;
}

/**
 * 获取页面接收的post,get数据
 * @author 黄焕军
 * @param string $no_field 不要的字段
 * @param string $have_time 是不是要添加时间
 * @return array
 */
function getReqData($no_field = '', $have_time = 1)
{
    global $req_data;
    $no_field_arr = array();
    if (!empty($no_field)) {
        $no_field_arr = explode(',', $no_field);
    }
    if ($no_field_arr) {
        foreach ($no_field_arr as $no_field_name) {
            unset($req_data[ $no_field_name ]);
        }
    }

    if ($have_time) {
        $req_data[ 'add_time' ] = time();
        $req_data[ 'update_time' ] = time();
    }

    return $req_data;
}

/**
 * 设置select值
 * @author 黄焕军
 * @param string $value 值
 * @param string $select_id select id
 * @return string 生成的html
 */
function select_value($value, $select_id)
{
    $html = '';
    if (strlen($select_id) > 0 && strlen($value) > 0) {
        $html = "<script type=\"text/javascript\">
						$('#{$select_id}').val('{$value}');
                    </script>";
    }
    echo $html;
}

/**
 * 数组通过某值排序
 * @author 黄焕军
 * @param array $array 要排序的数组
 * @param string $key 键值
 * @return array 排好序的数组
 */
function sort_by_value($array, $key)
{
    if (is_array($array)) {
        $key_array = null;
        $new_array = null;
        for ($i = 0; $i < count($array); $i++) {
            $key_array[ $array[ $i ][ $key ] ] = $i;
        }

        ksort($key_array);

        $j = 0;

        foreach ($key_array as $k => $v) {
            $new_array[ $j ] = $array[ $v ];
            $j++;
        }

        unset($key_array);
        return $new_array;
    } else {
        return $array;
    }
}

/**
 * 获取远程数据 用GET方式
 * @author 黄焕军
 * @param string $url 地址
 * @param boolean $ssl 要不要ssh header
 * @param string $header_option curl的类型 可为PUT DELETE等
 * @return string 获取的结果
 */
function get_html($url, $ssl = false, $header_option = '')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if ($ssl) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    }
    switch ($header_option) {
        case "GET":
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            break;
        case "POST":
            curl_setopt($ch, CURLOPT_POST, true);
            break;
        case "PUT":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            break;
        case "PATCH":
            curl_setopt($ch, CULROPT_CUSTOMREQUEST, 'PATCH');
            break;
        case "DELETE":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            break;
    }
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/**
 * 获取远程数据 用POST方式
 * @author 黄焕军
 * @param string $url 地址
 * @param string $vars 要POST的数据
 * @param boolean $is_post_data_json 数据头是不是json
 * @param boolean $is_xml 数据头是不是xml
 * @param boolean $is_gati 数据头是不是gati?
 * @param boolean $is_case 要不要数据头
 * @return string 获取的结果
 */
function post_html($url, $vars = '', $is_post_data_json = 0, $is_xml = 0, $is_gati = 0, $is_case = '')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $post_data = '';
    if ($is_post_data_json) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-type: text/json;charset = utf8' ));
        $post_data = json_encode($vars);
    } elseif ($is_xml) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-type: text/xml;charset = utf8' ));
        $post_data = $vars;
    } elseif ($is_gati) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json;charset=utf8' ));
        $post_data = json_encode($vars, 256);
    } else {
        $post_data = http_build_query($vars);
    }
    if (!empty($is_case)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $is_case);
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

/**
 * 将对象数组转成数组
 * @author 黄焕军
 * @param Object $obj 对象
 * @return array 数组
 */
function objarray_to_array($obj)
{
    $arr = array();
    foreach ($obj as $key => $value) {
        if (gettype($value) == "array" || gettype($value) == "object") {
            $arr[ $key ] = objarray_to_array($value);
        } else {
            $arr[ $key ] = $value;
        }
    }
    return $arr;
}

/**
 * 将多维数组转成一维数组
 * @author 黄焕军
 * @param array $data 多维数组
 * @return array 一维数组
 */
function twoarray_onearray($data)
{
    static $arr = array();
    foreach ($data as $key => $value) {
        if (gettype($value) == 'array') {
            twoarray_onearray($value);
        } else {
            $arr[] = $value;
        }
    }
    return $arr;
}

/**
 * 获取trim后的字符串长度
 * @author 黄焕军
 * @param string $str 字符串
 * @return int 字符串长度
 */
function trim_strlen($str)
{
    return strlen(trim($str));
}

/**
 * 判断字符串包含某个字符
 * @author 黄焕军
 * @param string $subject 字符串
 * @param string $search 查找的字符串
 * @return boolean 是否包含
 */
function has_str($subject, $search)
{
    $t = explode($search, $subject);
    return count($t) > 1;
}

/**
 * 把array的主key换成指定的key，然后返回新的，比如
 * $arr = array( 1=>array( 'id'=>100,'name'=>'a' ) ,  2=>array( 'id'=>101, 'name'=>'b' ) );
 * change_main_key( $arr, 'id' )
 * 结果为 array( 100=>array( 'id'=>100,'name'=>'a' ) ,  101=>array( 'id'=>101, 'name'=>'b' ) )
 * @author 黄焕军
 * @param array $arr 要处理的数组
 * @param string $main_key 多个key用,分隔。如果多个key 生成的键值用_连接
 * @param array $option $option = array( 'key_strtoupper'=>1 全转为大写, 'key_strtolower'=> 1 key转为小写 )
 * @return array 处理好的数组
 */
function change_main_key($arr, $main_key, $option = array())
{
    $arr_result = array();
    foreach ($arr as $info) {
        $main_key_arr = explode(',', $main_key);
        if (1 == count($main_key_arr)) {
            if ($option[ 'key_strtoupper' ]) {
                $info[ $main_key ] = strtoupper($info[ $main_key ]);
            }
            if ($option[ 'key_strtolowwer' ]) {
                $info[ $main_key ] = strtolower($info[ $main_key ]);
            }
            $arr_result[ $info[ $main_key ] ] = $info;
        } else {
            $key_arr = array();
            foreach ($main_key_arr as $key_name) {
                $key_arr[] = $info[ $key_name ];
            }
            $key = implode('_', $key_arr);
            $arr_result[ $key ] = $info;
        }
    }
    return $arr_result;
}

/**
 * 获取汉字的首拼音字母
 * @author 黄焕军
 * @param string $str 要获取的汉字
 * @return string 首拼音字母
 */
function get_char_sm($str)
{
    $firstchar_ord = ord(strtoupper($str{0}));
    if (( $firstchar_ord >= 65 and $firstchar_ord <= 91 ) or ( $firstchar_ord >= 48 and $firstchar_ord <= 57 )) {
        return $str{0};
    }
    $s = iconv("UTF-8", "gb2312", $str);
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if ($asc >= -20319 and $asc <= -20284) {
        return "A";
    }
    if ($asc >= -20283 and $asc <= -19776) {
        return "B";
    }
    if ($asc >= -19775 and $asc <= -19219) {
        return "C";
    }
    if ($asc >= -19218 and $asc <= -18711) {
        return "D";
    }
    if ($asc >= -18710 and $asc <= -18527) {
        return "E";
    }
    if ($asc >= -18526 and $asc <= -18240) {
        return "F";
    }
    if ($asc >= -18239 and $asc <= -17923) {
        return "G";
    }
    if ($asc >= -17922 and $asc <= -17418) {
        return "H";
    }
    if ($asc >= -17417 and $asc <= -16475) {
        return "J";
    }
    if ($asc >= -16474 and $asc <= -16213) {
        return "K";
    }
    if ($asc >= -16212 and $asc <= -15641) {
        return "L";
    }
    if ($asc >= -15640 and $asc <= -15166) {
        return "M";
    }
    if ($asc >= -15165 and $asc <= -14923) {
        return "N";
    }
    if ($asc >= -14922 and $asc <= -14915) {
        return "O";
    }
    if ($asc >= -14914 and $asc <= -14631) {
        return "P";
    }
    if ($asc >= -14630 and $asc <= -14150) {
        return "Q";
    }
    if ($asc >= -14149 and $asc <= -14091) {
        return "R";
    }
    if ($asc >= -14090 and $asc <= -13319) {
        return "S";
    }
    if ($asc >= -13318 and $asc <= -12839) {
        return "T";
    }
    if ($asc >= -12838 and $asc <= -12557) {
        return "W";
    }
    if ($asc >= -12556 and $asc <= -11848) {
        return "X";
    }
    if ($asc >= -11847 and $asc <= -11056) {
        return "Y";
    }
    if ($asc >= -11055 and $asc <= -10247) {
        return "Z";
    }
    return null;
}

/**
 * 数组移除指定的key
 * @author 黄焕军
 * @param array $array 要处理的数组
 * @param string $v 指定的键
 * @return array 新数组
 */
function array_remove($array, $v)
{
        // $array为操作的数组，$v为要删除的值
    foreach ($array as $key => $value) {
        if ($value == $v) {       //删除值为$v的项
            unset($array[ $key ]);    //unset()函数做删除操作
        }
    }
    return $array;
}

/**
 * 字符串去空
 * @author 黄焕军
 * @param string $str 要去空的字符串
 * @return string 处理后的字符串
 */
function replace_empty($str)
{
    $pattern = '/\'/';
    $new_str = preg_replace($pattern, ' ', $str, -1);
    return $new_str;
}

/**
 * 字符串长度截取
 * @author 黄焕军
 * @param string $str 截取的字符串
 * @param int $length 截取的长度
 * @return string 新字符串
 */
function str_length($str, $length)
{
    if (strlen(trim($str)) > $length) {
        $new_str = substr($str, 0, $length);
    } else {
        $new_str = trim($str);
    }
    return $new_str;
}

/**
 * 上传excel后自动获取excel内容
 * @author 黄焕军
 * @param string $input_name html上form里的file组件name
 * @return string excel内容
 */
function getUploadExcelContent($input_name)
{

    require_once(WEB_CLASS . '/class.upload.php');
    $clsUpload = new \OA\ClsUpload($input_name);
    $fileInfo = $clsUpload->upload(WEB_DR . "/share/uploads/", '', array());
    $excelFile = $fileInfo[ 'filename' ];
    if (empty($excelFile)) {
        return array( 'ack' => 0, 'error_id' => 1001, 'msg' => '请excel文件' );
    }

    require_once(WEB_CLASS . '/class.excel.php');
    $excelFilePath = WEB_DR . "/share/uploads/" . $excelFile;
    $ClsExcel = new \OA\ClsExcel($excelFilePath);
    $data = $ClsExcel->read();
    if (count($data) == 0) {
        return array( 'ack' => 0, 'error_id' => 1002, 'msg' => '获取excel失败' );
        //show_msg( '数据获取失败', 2 );
    } else {
        return array( 'ack' => 1, 'data' => $data, 'file_name' => $excelFile );
    }
}

/**
 * 二维数组排序
 * @author 黄焕军
 * @param array $array 要排序的数组
 * @param string $key 按哪个key排
 * @param string $order 顺序
 * @return array 排好序的数组
 */
function muti_arr_sort($array, $key, $order = 'asc')
{

    $arr_nums = $arr = array();
    foreach ($array as $k => $v) {
        $arr_nums[ $k ] = $v[ $key ];
    }

    if ($order == 'asc') {
        asort($arr_nums);
    } else {
        arsort($arr_nums);
    }

    foreach ($arr_nums as $k => $v) {
        $arr[ $k ] = $array[ $k ];
    }

    return $arr;
}

/**
 * 将二维数组转成字符串
 * @author 黄焕军
 * @param array $arr 数组
 * @param boolean $split_type 默认用逗号否则用单引号
 * @return string 生成的字符串
 */
function arr2str($arr, $split_type = true)
{
    foreach ($arr as $v) {
        $v = join(",", $v); //可以用implode将一维数组转换为用逗号连接的字符串
        $temp[] = $v;
    }
    $t = "";
    foreach ($temp as $v) {
        if ($split_type) {
            $t .= $v . ",";
        } else {
            $t .= "'" . $v . "'" . ",";
        }
    }
    $t = substr($t, 0, -1);
    return $t;
}

/**
 * 全角字符转换为半角
 * @author 黄焕军
 * @param string $str 要转换的字符串
 * @return string 转换好的字符串
 */
function qj2bj($str)
{
    $arr = array(
        '０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4', '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
        'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E', 'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
        'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O', 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
        'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y', 'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
        'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i', 'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
        'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's', 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
        'ｙ' => 'y', 'ｚ' => 'z',
        '（' => '(', '）' => ')', '〔' => '(', '〕' => ')', '【' => '[', '】' => ']', '〖' => '[', '〗' => ']', '“' => '"', '”' => '"',
        '‘' => '\'', '’' => '\'', '｛' => '{', '｝' => '}', '《' => '<', '》' => '>', '％' => '%', '＋' => '+', '—' => '-', '－' => '-',
        '～' => '~', '：' => ':', '。' => '.', '、' => ',', '，' => ',', '、' => ',', '；' => ';', '？' => '?', '！' => '!', '…' => '-',
        '‖' => '|', '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"', '　' => ' ', '×' => '*', '￣' => '~', '．' => '.', '＊' => '*',
        '＆' => '&', '＜' => '<', '＞' => '>', '＄' => '$', '＠' => '@', '＾' => '^', '＿' => '_', '＂' => '"', '￥' => '$', '＝' => '=',
        '＼' => '\\', '／' => '/'
    );
    return strtr($str, $arr);
}

/**
 * 本function用于把用户提交的数据把指定前缀的数据抓到数据表中
 * $data = array( 'a_a1'=> 'a1', 'a_a2'=> 'a2', 'b_b1'=> 'b1' );
 * $qz = 'a';
 * 本function返回 array( 'a_a1'=> 'a1', 'a_a2'=> 'a2' );
 * @author 黄焕军
 * @param array $data 数据源
 * @param string $qz 哪个前缀的抓取
 * @return array 抓取过后的数据
 */
function get_data_form_req_data($data, $qz)
{
    $qz_len = strlen($qz);
    $result = array();
    foreach ($data as $key => $value) {
        if (substr($key, 0, $qz_len) == $qz) {
            $result[ $key ] = $value;
        }
    }
    return $result;
}

/**
 * 获取权限标题
 * @author 黄焕军
 * @param int permission_id 权限id
 * @return string 标题
 */
function get_permission_title($permission_id)
{
    global $clsPermissions;
    $clsPermissions->setPermissionId($permission_id);
    $title_permissions_info = $clsPermissions->getTitle();
    $title_permissions = $title_permissions_info['msg'];
    return $title_permissions;
}

/**
 * 检查密码强度
 * @param string $str 密码明文
 * @return boolean 检测结果
 */
function passwordCheck($str)
{
    $score = 0;
    if (preg_match("/[0-9]+/", $str)) {
        $score++;
    }
    if (preg_match("/[0-9]{3,}/", $str)) {
        $score++;
    }
    if (preg_match("/[a-z]+/", $str)) {
        $score++;
    }
    if (preg_match("/[a-z]{3,}/", $str)) {
        $score++;
    }
    if (preg_match("/[A-Z]+/", $str)) {
        $score++;
    }
    if (preg_match("/[A-Z]{3,}/", $str)) {
        $score++;
    }
    if (preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/", $str)) {
        $score += 2;
    }
    if (preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]{3,}/", $str)) {
        $score++;
    }
    if (strlen($str) >= 10) {
        $score++;
    }

    if (strlen($str) < 8) {
        $score = 2;
    }
    if (in_array(substr($str, 0, 3), array('abc', '123', 'ABC'))) {
        $score = 2;
    }
    if ($score >= 4) {
        return true;
    } else {
        return false;
    }
}
