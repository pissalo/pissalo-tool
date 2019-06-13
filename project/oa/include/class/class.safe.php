<?php
/**
 * 安全类
 * ===========================================================
 * 版权所有 (C) 2006-2020 我不是稻草人，并保留所有权利。
 * 网站地址: http://www.dcrcms.com
 * ----------------------------------------------------------
 * 这是免费开源的软件；您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * 不允许对程序修改后再进行发布。
 * ==========================================================
 * @author:     我不是稻草人 <junqing124@126.com>
 * @version:    v1.0
 * @package class
 * @since 1.1.4
 */
namespace OA;

defined('IN_DCR') or exit('No permission.');

class ClsSafe
{

    /**
     * 去掉变量里的HTML
     * @param object $var 要去掉html的变量,可以是多维数组或字符串
     * @return object 返回去掉html后的变量
     */
    public static function noHtml($var)
    {
        if (is_array($var)) {
            for ($i = 0; $i < count($var); $i++) {
                if (is_array($var[ $i ])) {
                    $var[ $i ] = ClsSafe:: noHtml($var[ $i ]);
                } else {
                    $var[ $i ] = strip_tags($var[ $i ]);
                }
            }
            return $var;
        }
        if (is_string($var)) {
            return strip_tags($var);
        }
    }

    /**
     * 强度检测
     * @param $str 要检测的字符串
     * @return boolean 强度大于等于4返回真，否则假
     */
    public function strengthTest($str)
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
        if (in_array(substr($str, 0, 3), array( 'abc', '123', 'ABC' ))) {
            $score = 2;
        }
        if ($score >= 4) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 加密
     * @param $data 数据
     * @param string $key
     * @return array
     */
    public function encrypt($data, $key = 'jiami')
    {
        $key = md5($key);
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = '';
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= $key{$x};
            $x++;
        }
        for ($i = 0; $i < $len; $i++) {
            $str .= chr(ord($data{$i}) + ( ord($char{$i}) ) % 256);
        }
        $str_1 = base64_encode($str);
        $str_2 = get_rand_str(3);
        $str_3 = get_rand_str(4);
        return array('ack'=>1, 'msg'=>$str_2 . $str_1 . $str_3);
    }

    /**
     * 解密
     * @param $data 数据
     * @param string $key
     * @return array
     */
    public function decrypt($data, $key = 'jiami')
    {
        $key = md5($key);
        $x = 0;
        $str_4 = substr($data, 3, -4);
        $data = base64_decode($str_4);
        $len = strlen($data);
        $l = strlen($key);
        $char = '';
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
                $str .= chr(( ord(substr($data, $i, 1)) + 256 ) - ord(substr($char, $i, 1)));
            } else {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return array('ack'=>1, 'msg'=>$str);
    }
}
