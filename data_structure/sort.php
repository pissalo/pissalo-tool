<?php
$arr = [5, 1, 2, 6, 4, 8, 7, 10, 150, 3];

/*********************插入排序*****************/
/**
 * 算法:
 * 将数组分为两个区域：已排序区（数组前段）和未排序区（数组后段）
 * 每次遍历，将当前值插入到合适的位置
 */
/**
 * 优缺点:
 * 时间复杂度是O(n^2)；没有额外的存储空间，是原地排序算法；
 * 不涉及相等元素位置交换，是稳定的排序算法。
 * 插入排序的时间复杂度和冒泡排序一样，也不是很理想，
 * 但是插入排序不涉及数据交换，从更细粒度来区分，性能要略优于冒泡排序。
 */
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

/********************选择排序****************/
/**
 * 选择排序的时间复杂度也是 O(n^2)；
 * 由于不涉及额外的存储空间，所以是原地排序；
 * 由于涉及非相邻元素的位置交换，所以是不稳定的排序算法
 */
/**
 * 算法:
 * 选择排序算法的实现思路有点类似插入排序，也分已排序区间和未排序区间。
 * 但是选择排序每次会从未排序区间中找到最小的元素，将其放到已排序区间的末尾
 */
function choose_sort($arr)
{
    $count = count($arr);
    foreach ($arr as $key => $value) {
        $j = $key + 1;
        for (; $j < $count ; $j++) {
            if ($arr[$key] > $arr[$j]) {
                $tmp = $arr[$key];
                $arr[$key] = $arr[$j];
                $arr[$j] = $tmp;
            }
        }
    }
    return $arr;
}

/********************快速排序****************/
/**
 * 优点：
 * 快速排序是原地排序算法，时间复杂度和归并排序一样，
 * 也是 O(nlogn)，这个时间复杂度数据量越大，越优于 O(n^2)
 *
 * 缺点:
 * 因为涉及到数据的交换，有可能破坏原来相等元素的位置排序，
 * 所以是不稳定的排序算法
 *
 * 规则:
 * 选定一个标记元素，将小于标记元素的放到左边，
 * 否则放到右边，递归处理左、右直到数组大小小于2.
 */

/**
 * 调用入口
 * @param $arr
 * @return mixed
 */
function fast_sort($arr)
{
    if (count($arr) < 2) {
        return $arr;
    }
    quick_sort($arr, 0, count($arr) - 1);
    return $arr;
}

/**
 * 递归处理
 * @param array $arr 数组
 * @param int $start 起始下标
 * @param int $end 终止下标
 */
function quick_sort(&$arr, $start, $end)
{
    if ($start >= $end) {
        return;
    }
    //获取中间下标，并进行排序
    $mid_num = get_mid($arr, $start, $end);
    //处理左分支
    quick_sort($arr, $start, $mid_num - 1);
    //处理右分支
    quick_sort($arr, $mid_num + 1, $end);
}

/**
 * 实际排序函数
 * @param $arr
 * @param $start
 * @param $end
 * @return mixed
 */
function get_mid(&$arr, $start, $end)
{
    //将最后一个元素作为标记元素
    $value = $arr[$end];
    $first_num = $start;
    /**
     * 将比标记元素小的值移到$start-$first_num中，
     * 则$first_num+1~$end中的元素皆大于标记元素
     */
    for (; $start < $end ; $start++) {
        if ($arr[$start] < $value) {
            $tmp = $arr[$first_num];
            $arr[$first_num] = $arr[$start];
            $arr[$start] = $tmp;
            $first_num++;
        }
    }
    //最后将标记元素移至中间
    $tmp = $arr[$first_num];
    $arr[$first_num] = $value;
    $arr[$end] = $tmp;
    //返回中间下标
    return $first_num;
}