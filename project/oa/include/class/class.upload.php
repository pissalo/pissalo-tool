<?php

namespace OA;

defined('IN_DCR') or exit('No permission.');

/**
 * 上传类
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
 * @since 1.0.8
 */
class ClsUpload
{
    private $allowFile; //允许的文件类型
    private $maxFileSize; //文件最大上传大小
    private $inputName; //上传框的name

    /**
     * 构造函数
     * @param string $inputName 上传文件框的名字
     */
    public function __construct($inputName)
    {
        //初始化数据
        $this->inputName = $inputName;
        $this->allowFile = array(
            'audio/mp3',
            'image/jpg',
            'image/jpeg',
            'image/png',
            'image/pjpeg',
            'image/gif',
            'image/bmp',
            'image/x-png',
            'application/vnd.ms-excel',
            'application/kset',
            'application/x-ms-excel',
            'application/octet-stream',
            'application/vnd.ms-execl',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/ms-excel',
            'text/plain'
        );
        $this->maxFileSize = 50000000;
    }

    /**
     * 设置允许上传的文件类型
     * @param array 允许的文件类型
     * @return true
     */
    public function setAllowFile($allowFile)
    {
        $this->allowFile = $allowFile;
    }

    /**
     * 设置允许上传的文件大小
     * @param int 允许的文件最大值
     * @return true
     */
    public function setMaxFileSize($maxFileSize)
    {
        $this->maxFileSize = $maxFileSize;
    }

    /**
     * 上传文件
     * @param string $dirName 文件上传后放的目录名 为空时当前目录
     * @param string $fileName 文件名
     * @param array $sl 缩略图参数 array('width'=>100,'height'=>100,'newpic'=>1,'sl_name'=''); newpic=1时生成新的缩略图 0为覆盖原图 sl_name表示缩略图名 如果为空则用默认的
     * @param array $dirFeiLei 目录分类 type表示类型 caishu为扩展参数 默认为按日期 (含)1.0.6版本以后有效 现在有效且默认的是array('type'=>'date','caishu'=>'Y_m_d') 为了这个参数 几乎改写了上传过程 大家请以后台上传文件为参考 ^_^ 从这些个变量得出一个结论 最好不要对原变量来改 比如fileName让他从头到尾都是从外传来的值 而用新的变量来记录处理的变量
     * @param string $extendsName 扩展名，有要改扩展名的，请写这
     * @return array|boolean 上传成功filename为原图 sl_filename为缩略图 上传错误则error=1 error_msg表示错误信息 没有文件上传是为false
     */
    public function upload(
        $dirName = '',
        $fileName = '',
        $sl = array(),
        $dirFeiLei = array('type' => 'date', 'caishu' => 'Y_m_d'),
        $extendsName = ''
    ) {
        global $web_watermark_type, $web_watermark_txt, $web_watermark_weizhi;
        $inputName = $this->inputName;
        $returnValue = array();//返回的结果
        if (is_uploaded_file($_FILES[$inputName]['tmp_name'])) {
            $file = $_FILES[$inputName];
            if ($this->maxFileSize < $file["size"]) {
                show_msg('您上传的文件大小[' . $file["size"] . ']超过文件上传大小限制', 2);
                return array('error' => 1, 'error_msg' => '您上传的文件超过文件上传大小限制');
            }
            if (!in_array($file["type"], $this->allowFile)) {
                show_msg('你上传的文件文件类型[' . $file["type"] . ']不在允许上传的类型之内', 2);
                return array('error' => 1, 'error_msg' => '你上传的文件不在允许上传的类型之内');
            }
            if (is_array($dirFeiLei) && !empty($dirFeiLei['type']) && !empty($dirFeiLei['caishu'])) {
                if ('date' == $dirFeiLei['type']) {
                    $dirCanShu = date($dirFeiLei['caishu']);//因为参数产生的多的字符串
                }
            }


            $realFileName = '';//真实的FileName 这里是为了区别以前的路径名 fileName是全路径 real_fileName是不含目录分类参数带来的的路径
            $realSlFileName = '';//真实的缩略图FileName 意思同上
            $realDirName = '';//真实的目录名 意思同上 这三个变量主要是为了区别加了dirFeiLei这个参数产生的字符串 ^_^

            $oldName = $file['tmp_name'];
            $pinfo = pathinfo($file["name"]);
            if (empty($extendsName)) {
                $ftype = $pinfo['extension'];
            } else {
                $ftype = $extendsName;
            }
            if (strlen($fileName) == 0) {
                $realFileName = date(ymdhms) . rand(1000, 9999) . "." . $ftype;
            } else {
                $t_a = explode('.', $fileName);
                if (count($t_a) > 1) {
                    $realFileName = $fileName;
                } else {
                    $realFileName = $fileName . "." . $ftype;
                }
            }

            if (!empty($dirCanShu)) {
                $realDirName = $dirName . '/' . $dirCanShu;
            } else {
                $realDirName = $dirName;
            }
            if (!file_exists($realDirName)) {
                @mkdir($realDirName);
            }
            $fileName = $realDirName . '/' . $realFileName;

            if (!move_uploaded_file($oldName, $fileName)) {
                return false;
            } else {
                if (!empty($dirCanShu)) {
                    $realFileName = $dirCanShu . '/' . $realFileName;
                    $realSlFileName = $dirCanShu . '/' . $realSlFileName;
                }
                return array('filename' => $realFileName);
            }
        } else {
            //没有上传文件
            return false;
        }
    }

    /**
     * 获取缩略图名 1.0.6后大改写 原来是全路径 现在只是非全路径了 注意！！！
     * @param string $fileName 图片文件名
     * @return string 缩略图名
     */
    public static function getSl($fileName)
    {
        $bName = explode('.', $fileName);
        return $bName[0] . '_sl.' . $bName[1];
    }
}
