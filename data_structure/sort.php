<?php
/*********************插入排序*****************/
/**
 * 将数组分为两个区域：已排序区（数组前段）和未排序区（数组后段）
 * 每次遍历，将当前值插入到合适的位置
 */
$arr = [5, 1, 2, 6, 4, 8, 7, 10, 150, 3];
function insert_sort($arr)
{
    foreach ($arr as $key => $value) {
        if ($key == 0) {
            continue;
        }
        $j = $key - 1;
        for (; $j >= 0 ; $j--) {
            if ($arr[$j] > $value) {
                $arr[$j + 1] = $arr[$j];
            } else {
                break;
            }
        }
        $arr[$j + 1] = $value;
    }
    return $arr;
}