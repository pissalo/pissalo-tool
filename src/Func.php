<?php

defined('IN_DCR') or exit('No permission.');
/**
 * 生成javascript跳转 并自动跳转
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
 * 返回页面并提示信息
 * @param $action_msg   提示信息
 * @param int $action_type 1为正确信息 2为错误信息
 * @param string $back_url
 */
function back_msg($action_msg, $action_type = 1, $back_url = '')
{
    if (empty($back_url)) {
        $back_url = $_SERVER['HTTP_REFERER'];
    }
    $_SESSION['bullfrog_action_msg'] = $action_msg;
    $_SESSION['bullfrog_action_type'] = $action_type;
    echo "<script type='text/javascript'>window.location.href=\"{$back_url}\";</script>";
}

/**
 * 跳转
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
 * @param string $str 要截取的字条串
 * @param string $start 开始截取的位置
 * @param string $len 截取的长度
 * @return string 截取后的字符串
 */
function my_substr($str, $start, $len)
{
    $tmpstr = "";
    $strlen = $start + $len;
    for ($i = 0 ; $i < $strlen ; $i++) {
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
 * @param string $key cookie名
 * @return boolean 返回true
 */
function drop_cookie($key)
{
    setcookie($key, '', time() - 360000, "/");
}

/**
 * 获取cookie值
 * @param string $key cookie名
 * @return string 获取的cookie的值
 */
function get_cookie($key)
{
    if (!isset($_COOKIE[$key])) {
        return '';
    } else {
        return $_COOKIE[$key];
    }
}

/**
 * 获取当前IP
 * @return string 本机的IP
 */
function get_ip()
{
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $cip = $_SERVER["HTTP_CLIENT_IP"];
    } else {
        if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            if (!empty($_SERVER["REMOTE_ADDR"])) {
                $cip = $_SERVER["REMOTE_ADDR"];
            } else {
                $cip = '';
            }
        }
    }
    preg_match("/[\d\.]{7,15}/", $cip, $cips);
    $cip = isset($cips[0]) ? $cips[0] : 'unknown';
    unset($cips);
    
    return $cip;
}

/**
 * 获取顶级域名
 * @param string $url 要操作的地址
 * @return string $url的顶级域名
 */
function get_top_url($url = '')
{
    if (empty($url)) {
        $url = $_SERVER['SERVER_NAME'];
    }
    $t_url = parse_url($url);
    $t_url = $t_url['path'];
    
    return $t_url;
}

/**
 * 获取随机字符串
 * @param int $len 字符串长度
 * @return string 产生的随机字符串
 */
function get_rand_str(
    $len = 4,
    $rand_array = array (
        "a",
        "b",
        "c",
        "d",
        "e",
        "f",
        "g",
        "h",
        "i",
        "j",
        "k",
        "l",
        "m",
        "n",
        "o",
        "p",
        "q",
        "r",
        "s",
        "t",
        "u",
        "v",
        "w",
        "x",
        "y",
        "z",
        "A",
        "B",
        "C",
        "D",
        "E",
        "F",
        "G",
        "H",
        "I",
        "J",
        "K",
        "L",
        "M",
        "N",
        "O",
        "P",
        "Q",
        "R",
        "S",
        "T",
        "U",
        "V",
        "W",
        "X",
        "Y",
        "Z",
        "0",
        "1",
        "2",
        "3",
        "4",
        "5",
        "6",
        "7",
        "8",
        "9"
    )
) {
    $chars = $rand_array;
    $charsLen = count($chars) - 1;
    shuffle($chars);
    $output = "";
    for ($i = 0 ; $i < $len ; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    
    return $output;
}

/**
 * 格式化输出数据
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
 * @since 1.0.8
 * @param array $arr 要操作的数组
 * @return array 去重后的数组
 */
function array_remove_empty(&$arr, $trim = true)
{
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            array_remove_empty($arr[$key]);
        } else {
            $value = trim($value);
            if ($value == '') {
                unset($arr[$key]);
            } elseif ($trim) {
                $arr[$key] = $value;
            }
        }
    }
    return $arr;
}

/**
 * 页面输出信息 弄这个function的目的是想页面所有的测试信息都用这个。以后不想有测试信息直接注释p_r($str)就OK了 ^_^ 懒人一枚唉...
 * @since 1.1.0
 * @param string $str 信息内容
 * @return true
 */
function msg($str)
{
    p_r($str);
}


/**
 * 获取页面接收的post,get数据
 * @since 1.1.1
 * @param string $no_field 不要的字段
 * @param string $have_time 是不是要添加时间
 * @return array
 */
function get_req_data($no_field = '', $have_time = 1)
{
    global $req_data;
    $no_field_arr = array ();
    if (!empty($no_field)) {
        $no_field_arr = explode(',', $no_field);
    }
    if ($no_field_arr) {
        foreach ($no_field_arr as $no_field_name) {
            unset($req_data[$no_field_name]);
        }
    }
    
    if ($have_time) {
        $req_data['add_time'] = time();
        $req_data['update_time'] = time();
    }
    
    return $req_data;
}

/**
 * 设置select值
 * @param $value
 * @param $select_id
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
 * header_option可为PUT DELETE
 * @param $url
 * @param bool $ssl
 * @param string $header_option
 * @return mixed
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
        case "GET" :
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            break;
        case "POST":
            curl_setopt($ch, CURLOPT_POST, true);
            break;
        case "PUT" :
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
 * @param $url
 * @param string $vars
 * @param int $is_post_data_json
 * @param int $is_xml
 * @param int $is_gati
 * @param string $is_case
 * @return mixed
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-type: text/json;charset = utf8'));
        $post_data = json_encode($vars);
    } elseif ($is_xml) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-type: text/xml;charset = utf8'));
        $post_data = $vars;
        
    } elseif ($is_gati) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=utf8'));
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
 * @param $obj
 * @return array
 */
function objarray_to_array($obj)
{
    $arr = array ();
    foreach ($obj as $key => $value) {
        if (gettype($value) == "array" || gettype($value) == "object") {
            $arr[$key] = objarray_to_array($value);
        } else {
            $arr[$key] = $value;
        }
    }
    return $arr;
}

/**
 * 将多维数组转成一维数组
 * @param $data
 * @return array
 */
function twoarray_onearray($data)
{
    static $arr = array ();
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
 * @param $str
 * @return int
 */
function trim_strlen($str)
{
    return strlen(trim($str));
}

/**
 * 字符串包含
 * @param $subject
 * @param $search
 * @return bool
 */
function has_str($subject, $search)
{
    $t = explode($search, $subject);
    return count($t) > 1;
}

/**
 * 截取指定开始结束字符串里的字符
 * @param $str
 * @param $start_str
 * @param $end_str
 * @return mixed
 */
function jequ_str($str, $start_str, $end_str)
{
    $t = explode($start_str, $str);
    $str = $t[1];
    $t = explode($end_str, $str);
    $str = $t[0];
    return $str;
}

/**
 * 修改数组主键
 * @param $arr
 * @param $main_key
 * @param array $option
 * @return array
 */
function change_main_key($arr, $main_key, $option = array ())
{
    $arr_result = array ();
    $sp_str = $option['sp_str'] ? $option['sp_str'] : '_';
    foreach ($arr as $info) {
        $main_key_arr = explode(',', $main_key);
        if (1 == count($main_key_arr)) {
            if ($option['key_strtoupper']) {
                $info[$main_key] = strtoupper($info[$main_key]);
            }
            if ($option['key_strtolowwer']) {
                $info[$main_key] = strtolower($info[$main_key]);
            }
            $arr_result[$info[$main_key]] = $info;
        } else {
            $key_arr = array ();
            foreach ($main_key_arr as $key_name) {
                $key_arr[] = $info[$key_name];
            }
            $key = implode($sp_str, $key_arr);
            $arr_result[$key] = $info;
        }
    }
    return $arr_result;
}

/**
 * 获取汉字的首拼音
 * @param $str
 * @return null|string
 */
function get_char_sm($str)
{
    $firstchar_ord = ord(strtoupper($str{0}));
    if (($firstchar_ord >= 65 and $firstchar_ord <= 91) or ($firstchar_ord >= 48 and $firstchar_ord <= 57)) {
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
 * 删除数组中指定值
 * @param $array
 * @param $v
 * @return mixed
 */
function array_remove($array, $v)
{        // $array为操作的数组，$v为要删除的值
    foreach ($array as $key => $value) {
        if ($value == $v) {       //删除值为$v的项
            unset($array[$key]);    //unset()函数做删除操作
        }
    }
    return $array;
}

/**
 * 字符串长度截取
 * @since 1.1.1
 * @param string $str替换的字符串
 * @param int $leng 截取的长度
 * @return string $newstr 新字符串
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
 * 将数组转换为大写数字
 * @param $num
 * @return string
 */
function get_amount($num)
{
    $c1 = "零壹贰叁肆伍陆柒捌玖";
    $c2 = "分角元拾佰仟万拾佰仟亿";
    $num = round($num, 2);
    $num = $num * 100;
    if (strlen($num) > 10) {
        return "数据太长，没有这么大的钱吧，检查下";
    }
    $i = 0;
    $c = "";
    while (1) {
        if ($i == 0) {
            $n = substr($num, strlen($num) - 1, 1);
        } else {
            $n = $num % 10;
        }
        $p1 = substr($c1, 3 * $n, 3);
        $p2 = substr($c2, 3 * $i, 3);
        if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元')) && $i != 0) {
            $c = $p1 . $p2 . $c;
        } elseif ($n != 0 && $i != 0) {
            $c = $p1 . $c;
        }
        $i = $i + 1;
        $num = $num / 10;
        $num = (int)$num;
        if ($num == 0) {
            break;
        }
    }
    $j = 0;
    $slen = strlen($c);
    while ($j < $slen) {
        $m = substr($c, $j, 6);
        if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
            $left = substr($c, 0, $j);
            $right = substr($c, $j + 3);
            $c = $left . $right;
            $j = $j - 3;
            $slen = $slen - 3;
        }
        $j = $j + 3;
    }
    if (substr($c, strlen($c) - 3, 3) == ' 零') {
        $c = substr($c, 0, strlen($c) - 3);
    }
    if (empty($c)) {
        return " 零元";
    } else {
        return $c . "";
    }
}

/**
 * 全角字符转换为半角
 * @param string $str
 * @return string
 */
function qj2bj($str)
{
    $arr = array (
        '０' => '0',
        '１' => '1',
        '２' => '2',
        '３' => '3',
        '４' => '4',
        '５' => '5',
        '６' => '6',
        '７' => '7',
        '８' => '8',
        '９' => '9',
        'Ａ' => 'A',
        'Ｂ' => 'B',
        'Ｃ' => 'C',
        'Ｄ' => 'D',
        'Ｅ' => 'E',
        'Ｆ' => 'F',
        'Ｇ' => 'G',
        'Ｈ' => 'H',
        'Ｉ' => 'I',
        'Ｊ' => 'J',
        'Ｋ' => 'K',
        'Ｌ' => 'L',
        'Ｍ' => 'M',
        'Ｎ' => 'N',
        'Ｏ' => 'O',
        'Ｐ' => 'P',
        'Ｑ' => 'Q',
        'Ｒ' => 'R',
        'Ｓ' => 'S',
        'Ｔ' => 'T',
        'Ｕ' => 'U',
        'Ｖ' => 'V',
        'Ｗ' => 'W',
        'Ｘ' => 'X',
        'Ｙ' => 'Y',
        'Ｚ' => 'Z',
        'ａ' => 'a',
        'ｂ' => 'b',
        'ｃ' => 'c',
        'ｄ' => 'd',
        'ｅ' => 'e',
        'ｆ' => 'f',
        'ｇ' => 'g',
        'ｈ' => 'h',
        'ｉ' => 'i',
        'ｊ' => 'j',
        'ｋ' => 'k',
        'ｌ' => 'l',
        'ｍ' => 'm',
        'ｎ' => 'n',
        'ｏ' => 'o',
        'ｐ' => 'p',
        'ｑ' => 'q',
        'ｒ' => 'r',
        'ｓ' => 's',
        'ｔ' => 't',
        'ｕ' => 'u',
        'ｖ' => 'v',
        'ｗ' => 'w',
        'ｘ' => 'x',
        'ｙ' => 'y',
        'ｚ' => 'z',
        '（' => '(',
        '）' => ')',
        '〔' => '(',
        '〕' => ')',
        '【' => '[',
        '】' => ']',
        '〖' => '[',
        '〗' => ']',
        '“' => '"',
        '”' => '"',
        '‘' => '\'',
        '’' => '\'',
        '｛' => '{',
        '｝' => '}',
        '《' => '<',
        '》' => '>',
        '％' => '%',
        '＋' => '+',
        '—' => '-',
        '－' => '-',
        '～' => '~',
        '：' => ':',
        '。' => '.',
        '、' => ',',
        '，' => ',',
        '、' => ',',
        '；' => ';',
        '？' => '?',
        '！' => '!',
        '…' => '-',
        '‖' => '|',
        '”' => '"',
        '’' => '`',
        '‘' => '`',
        '｜' => '|',
        '〃' => '"',
        '　' => ' ',
        '×' => '*',
        '￣' => '~',
        '．' => '.',
        '＊' => '*',
        '＆' => '&',
        '＜' => '<',
        '＞' => '>',
        '＄' => '$',
        '＠' => '@',
        '＾' => '^',
        '＿' => '_',
        '＂' => '"',
        '￥' => '$',
        '＝' => '=',
        '＼' => '\\',
        '／' => '/'
    );
    return strtr($str, $arr);
}

/**
 * 通过产品1688链接获取1688产品ID
 * @param string $url 1688产品链接
 * @return string
 */
function get_aliba_pid_by_url($url)
{
    preg_match('/https:\/\/detail.1688.com\/offer\/[0-9]+/', $url, $match_arr);
    $tmp_arr = explode('/', $match_arr[0]);
    $ali_pid = array_pop($tmp_arr);
    $flag = is_numeric($ali_pid);
    //不是数字则设置为空
    if (!$flag) {
        $ali_pid = null;
    }
    return $ali_pid;
}

/**
 * 根据海关编码获取对应信息
 * @param string $hs_code 海关编码
 * @return array
 */
function get_hscode_info($hs_code)
{
    $return_msg = array ();
    $url = "http://www.likecha.com/tools/hscode/{$hs_code}.html";
    $html = get_html($url);
    preg_match_all("/<td class=\"inforcenter\">.*<\/td>/", $html, $matches);
    if ($matches[0]) {
        $find_arr = array ('<td class="inforcenter">', '>', '<');
        $tmp_str = str_replace($find_arr, '', $matches[0][5]);
        $tmp_arr = explode('/', $tmp_str);
        if ($tmp_arr[0]) {
            $return_msg['ack'] = 1;
            $return_msg['unit'] = $tmp_arr[0];  //第一法定单位
            if ($tmp_arr[2]) {
                $return_msg['unit_2'] = $tmp_arr[1];//第二法定单位
            } else {
                $return_msg['unit_2'] = "无";//第二法定单位
                
            }
        } else {
            $return_msg['ack'] = 0;
            $return_msg['msg'] = '没有找到该海关编码的信息！';
        }
    } else {
        $return_msg['ack'] = 0;
        $return_msg['msg'] = '没有找到该海关编码的信息！';
    }
    return $return_msg;
}