<?php
namespace OA;

include "../../include/common.inc.php";
// include "class.captcha.php";
session_start();

class ClsCaptcha
{
    private $width;
    private $height;
    private $code_num;
    private $code;
    private $im;
    private $session;
    private $str;

    public function __construct($session_name, $width = 80, $height = 20, $code_num = 4)
    {
        $this->width = $width;
        $this->height = $height;
        $this->code_num = $code_num;
        $this->session = $session_name;
    }

    public function showImg()
    {
        //创建图片
        $this->createImg();
        //设置干扰元素
        $this->setDisturb();
        //设置验证码
        $this->setCaptcha();
        $this->setWarping();
        $captcha = md5(strtoupper($this->getCaptcha()));
        $_SESSION[ 'admin_yz' ] = $captcha;
        //输出图片
        $this->outputImg();
    }

    public function setWarping()
    {
        $rgb = array();
        $direct = rand(0, 1);
        //$direct=0.9;
        $width = imagesx($this->im);
        $height = imagesy($this->im);
        $level = $width / 30;
        for ($j = 0; $j < $height; $j++) {
            for ($i = 0; $i < $width; $i++) {
                $rgb[ $i ] = imagecolorat($this->im, $i, $j);
            }
            for ($i = 0; $i < $width; $i++) {
                $r = sin($j / $height * 2 * M_PI - M_PI * 0.5) * ( $direct ? $level : -$level );
                imagesetpixel($this->im, $i + $r, $j, $rgb[ $i ]);
            }
        }
    }

    public function getCaptcha()
    {
        return $this->code;
    }

    private function createImg()
    {
        $this->im = imagecreatetruecolor($this->width, $this->height);
        $bgColor = imagecolorallocate($this->im, 255, 255, 255);
        imagefill($this->im, 0, 0, $bgColor);
    }

    private function setDisturb()
    {
        $area = ( $this->width * $this->height ) / 20;
        $disturbNum = ( $area > 250 ) ? 250 : $area;
        //加入点干扰
        for ($i = 0; $i < $disturbNum; $i++) {
            $color = imagecolorallocate($this->im, rand(0, 255), rand(0, 255), rand(0, 255));
            imagesetpixel($this->im, rand(1, $this->width - 2), rand(1, $this->height - 2), $color);
        }
        //加入弧线
        for ($i = 0; $i <= 5; $i++) {
            $color = imagecolorallocate($this->im, rand(128, 255), rand(125, 255), rand(100, 255));
            imagearc($this->im, rand(0, $this->width), rand(0, $this->height), rand(30, 300), rand(20, 200), 50, 30, $color);
        }
    }

    private function createCode()
    {
        $this->str = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ";

        for ($i = 0; $i < $this->code_num; $i++) {
            $this->code .= $this->str[ rand(0, strlen($this->str) - 1) ];
        }
    }

    private function setCaptcha()
    {
        $this->createCode();
        for ($i = 0; $i < $this->code_num; $i++) {
            $color = imagecolorallocate($this->im, rand(0, 90), rand(0, 90), rand(0, 90));
            $size = rand(floor($this->height / 5), floor($this->height / 3));//$size = 5;
            $x = floor($this->width / $this->code_num) * $i + 5;
            $y = rand(0, $this->height - 20);//$y=15;
            imagechar($this->im, $size, $x, $y, $this->code{$i}, $color);
            // imagestring($this->im, 0, $x+8, $y+38, $this->str[rand(0, strlen($this->str) - 1)], $color);
            // imagestring($this->im, 0, $x+20, $y+30, $this->str[rand(0, strlen($this->str) - 1)], $color);
            // imagestring($this->im, 0, $x+22, $y+32, '-', $color);
            // imagestring($this->im, 0, 10, 35, '_', $color);
        }
    }

    private function outputImg()
    {
        ob_clean();//先清除缓存防止图像无法输出
        header("Content-type:image/jpeg");
        imagejpeg($this->im);
        imagedestroy($this->im);
    }
}

$ClsCaptcha = new ClsCaptcha('admin_yz', 105, 43);
$ClsCaptcha->showImg();
