<?php
/**
 * 文件处理的基类
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
namespace OA;

defined('IN_DCR') or exit('No permission.');

class ClsFile
{
    /** @var string $text 文件内容 */
    private $text;
    /** @var string $file_name 当前目录 或者文件 */
    private $file_name;
    
    /**
     * 构造函数
     * @param string $file_name 文件名
     */
    public function __construct($file_name = '')
    {
        $this-> file_name = $file_name;
    }
    
    /**
     * 设置要写入的字符串，save_fo_file保存到文件的内容就是这个
     * @param string $txt 字符串
     * @return boolean 返回true
     */
    public function setText($txt)
    {
        $this-> text = $txt;
        
        return true;
    }
    
    /**
     * 设置当前操作的文件名
     * @param string $file_name 文件名
     * @return boolean
     */
    public function setFileName($file_name)
    {
        $this-> file_name = $file_name;
    }
    
    /**
     * 把set_text设置的字符串写到到文件中 save_to_file
     * @param boolean $is_convert 是不是转换 把<#dcr#form转成<form,</#dcr#form>转成</from>,<#dcr#textarea转成<textarea,</#dcr#textarea>转成</textarea> 适用版本>=1.0.4
     * @param boolean $write_mode 写入文件模式
     * @return boolean 成功返回true 失败:如果返回r1 则表示文件不存在 r2为文件不可写
     */
    public function write($is_convert = false, $write_mode = 'w')
    {
        $result = array();
        $file_name = $this->file_name;
        $file_handle = fopen($file_name, $write_mode);
        if ($file_handle) {
            if (is_writable($file_name)) {
                if ($is_convert) {
                    $this-> text = str_ireplace('<#dcr#form', '<form', $this-> text);
                    $this-> text = str_ireplace('</#dcr#form>', '</form>', $this-> text);
                    $this-> text = str_ireplace('<#dcr#textarea', '<textarea', $this-> text);
                    $this-> text = str_ireplace('</#dcr#textarea>', '</textarea>', $this-> text);
                }
                $rs = fwrite($file_handle, $this-> text);
                @fclose($file_handle);
                $result = array( 'ack'=> 1 );
            } else {
                $result = array( 'ack'=> 0, 'msg'=> '不可写', 'error_id'=> '1000' );
                
                //return 'r2';
            }
        } else {
            $result = array( 'ack'=> 0, 'msg'=> 'fopen失败，可能无权限', 'error_id'=> '1001' );
        }
        @fclose($file_handle);
        return $result;
    }
    
    /**
     * 返回文件的内容 get_content
     * @param boolean $is_convert 是不是转换 把<form转成<#dcr#form,</from>转成</#dcr#form>,<textarea 转成<#dcr#textarea,</textarea>转成</#dcr#textarea> 适用版本>=1.0.4
     * @return boolean 成功返回true 失败:如果返回r1 表示目录不存在 r2为文件不可操作
     */
    public function read($is_convert = false)
    {
        $file_name = $this->file_name;
        $fp = fopen($file_name, 'r');
        if ($fp) {
            while (!feof($fp)) {
                $content .= fgets($fp, 4096);
            }
        } else {
            return false;
        }
        fclose($fp);
        if ($is_convert) {
            $content = str_ireplace('<form', '<#dcr#form', $content);
            $content = str_ireplace('</form>', '</#dcr#form>', $content);
            $content = str_ireplace('<textarea', '<#dcr#textarea', $content);
            $content = str_ireplace('</textarea>', '</#dcr#textarea>', $content);
        }
        
        return $content;
    }
}
