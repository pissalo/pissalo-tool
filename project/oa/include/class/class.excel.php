<?php
/**
 * 后台管理员类
 * ===========================================================
 * 版权所有 (C) 2006-2020 我不是稻草人，并保留所有权利。
 * 网站地址: http://www.dcrcms.com
 * ----------------------------------------------------------
 * 这是免费开源的软件；您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * 不允许对程序修改后再进行发布。
 * ==========================================================
 * @author:     我不是稻草人 <junqing124@126.com>
 * @version:    v1.0.3
 * @package class
 * @since 1.0.8
 */
namespace OA;

require_once(WEB_INCLUDE . '/third_class/phpexcel/PHPExcel.php');

class ClsExcel
{

    private $excelName;

    public function __construct($excelName = '')
    {
        $this->excelName = $excelName;
    }

    /**
     * 把excel读成数组
     * 多维数组内 一个sheet一个数组
     * $sheet_name 表示要读取的哪个sheet_name 如果没有，则全部读取
     * @return boolean
     */
    public function read($sheetName = '')
    {
        $objExcel = \PHPExcel_IOFactory::load($this->excelName);
        $sheetCount = $objExcel->getSheetCount();
        $data = array();
        for ($i = 0; $i < $sheetCount; $i++) {
            $objSheet = $objExcel->getSheet($i);
            $sheetTitle = $objSheet->getTitle();
            if ($sheetName && $sheetName != $sheetTitle) {
            } else {
                $data[$i]['name'] = $objSheet->getTitle();
                $data[$i]['data'] = $objSheet->toArray(null, true, true, true);
            }
        }
        return $data;
    }

    /**
     * 把excel读成数组
     * data是要写的数据
     * ruler为写的规则
     * @return boolean
     */
    public function write($data, $ruler)
    {
        header("Content-Type: application/vnd.ms-execl");
        header("Content-Disposition: attachment; filename={$this->excelName}");
        header("Pragma: no-cache");
        header("Expires: 0");
        /*first line*/
        foreach ($ruler as $sheetRuler) {
            $title = mb_convert_encoding($sheetRuler['title'], 'gbk', 'utf-8');
            echo "{$title}\t";
        }
        echo "\t\n";

        /*start of second line*/
        foreach ($data as $info) {
            foreach ($ruler as $sheetField => $sheetInfo) {
                echo $info[$sheetField] . "\t";
            }
            echo "\t\n";
        }
    }
}
