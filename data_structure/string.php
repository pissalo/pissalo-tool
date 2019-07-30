<?php
/*****************回文**********************
 * 一个字符串是否正、反读一致。
 * @param $str
 * @return bool
 */
function is_palindrome($str)
{
    $count = strlen($str);
    $mid_num = ceil($count / 2);
    $flag = true;
    for ($i = 0 ; $i < $mid_num ; $i++) {
        if ($str[$i] == $str[$count - $i - 1]) {
            continue;
        } else {
            $flag = false;
            break;
        }
    }
    return $flag;
}


/****BF匹配字符串(暴力匹配)**********
 * 逐一匹配字符串，每次匹配如果失败，则只往后移动一位。
 * @param $main_str
 * @param $need_str
 * @return bool
 */
function batch_string_by_bf($main_str, $need_str)
{
    $main_len = strlen($main_str);
    $need_len = strlen($need_str);
    $max_num = $main_len - $need_len;
    $flag = false;
    for ($i = 0 ; $i < $max_num ; $i++) {
        for ($j = 0 ; $j < $need_len ; $j++) {
            echo "j:{$j}<br>";
            if ($main_str[$j + $i] == $need_str[$j]) {
                $flag = true;
                continue;
            } else {
                $flag = false;
                break;
            }
        }
        if ($flag) {
            break;
        }
    }
    return $flag;
}

/*********KMP字符串匹配**********
 * 每次匹配失败，不一定往后移动一位。
 * 在部分匹配成功的情况下，移动的位数=已匹配数-对应的部分匹配值，
 * 否则，向后移动一位继续匹配。
 */