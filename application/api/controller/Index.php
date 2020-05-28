<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2020/3/18
 * Time: 15:31
 */
namespace app\api\controller;
include ROOT_PATH . '/extend/phpqrcode/phpqrcode.php';
use think\Db;
class Index extends Base {

    public function index() {
        $code = input('param.code',110);
        $whereCode = [
            ['code','=',$code]
        ];
        $code_exist = Db::table('mp_qrcode')->where($whereCode)->find();
        $this->assign('info',$code_exist);
        return $this->fetch();
    }

    public function chaxun() {
        $code = input('post.code','');
        $whereCode = [
            ['code','=',$code]
        ];
        try {
            $code_exist = Db::table('mp_qrcode')->where($whereCode)->find();
            if(!$code_exist) {
                return ajax('编号不存在',2);
            }
            if((time()-$code_exist['create_time']) > 120) {
                return ajax('已过期',3);
            }else {
                return ajax();
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }

    }

    public function checkCode() {
        $code = input('post.code','');
        checkPost(['code'=>$code]);
        $whereCode = [
            ['code','=',$code]
        ];
        try {
            $code_exist = Db::table('mp_qrcode')->where($whereCode)->find();
            if(!$code_exist) {
                return ajax('您所查询的数码不存在，谨防假冒，并请拨打咨询热线021-26095599。谢谢您的使用！',101);
            }
            if($code_exist['scan_time']) {
                $time = date('Y年m月d日H点i分',$code_exist['scan_time']);
            }else {
                $update_data = [
                    'scan_time' => time()
                ];
                Db::table('mp_qrcode')->where($whereCode)->update($update_data);
                $time = date('Y年m月d日H点i分',time());
            }
//            if((time()-$code_exist['create_time']) > 120) {
//                return ajax('您所查询的数码已在'.date('Y年m月d日H点i分').'查询过，该数码已失效，谨防假冒，并请拨打咨询热线021-26095599。谢谢您的使用！',102);
//            }else {
//                return ajax('您所查询的数码正常',100);
//            }
            return ajax('',102,'您所查询的数码已在'.$time.'查询过，该数码已失效，谨防假冒，并请拨打咨询热线021-26095599。谢谢您的使用！');
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }

    }

    public function test() {
        $code = input('param.code',110);
        if(strlen($code) != 16 ) {
            die('<h1>必须16位数字</h1>');
        }
        $str = substr($code,-4);
        $filename = 'qrcode/'.$code.'.png';
        $insert_data = [
            'code' => $code,
            'create_time' => time()
        ];
        $whereCode = [
            ['code','=',$code]
        ];
        $code_exist = Db::table('mp_qrcode')->where($whereCode)->find();
        if(!$code_exist) {
            Db::table('mp_qrcode')->insert($insert_data);
        }
        $value= 'https://www.psn.asia/dist/#/home?code=' . $code;
        $level = "H"; // 纠错级别：L、M、Q、H
        $size = 6; // 点的大小：1到10
        $margin = 3;
        header('Content-Type:image/png');
        \QRcode::png($value, $filename, $level, $size,$margin);

        $l1 = 25;
        $l2 = 9;
        $l3 = 8;
        $l4 = 26;
        $t = $l1 + ($l2+$l3)*2+$l4;

        $baitiao_h = ($l2+$l3)*2+$l4;
        $baitiao_w = $l2;

        $x_1 = $l1;
        $x_2 = $t-$l2;
        $x_3 = 400-$t-1;
        $x_4 = 400-$l1-$l2-1;

        $y_1 = $l1;
        $y_2 = $t-$l2;

        $y_3 = 400-$t-1;
        $y_4 = 400-$l1-$l2-1;

        $path_1 = $filename;
        $text1 = $str . ' ' . $str . ' ' . $str;
        $text2 = ' '.$str.' '.$str.' ';

        $qrcode = $this->imageresize($path_1,400,400);

        $baitiao_shu = imagecreatetruecolor($baitiao_w, $baitiao_h);
        $baitiao_heng = imagecreatetruecolor($baitiao_h, $baitiao_w);
        $color = imagecolorallocate($baitiao_shu, 255, 255, 255);
        imagefill($baitiao_shu, 0, 0, $color);
        imagefill($baitiao_heng, 0, 0, $color);
//上方竖白条
        imagecopyresampled($qrcode,$baitiao_shu,$x_1,$y_1,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));
        imagecopyresampled($qrcode,$baitiao_shu,$x_2,$y_1,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));
        imagecopyresampled($qrcode,$baitiao_shu,$x_3,$y_1,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));
        imagecopyresampled($qrcode,$baitiao_shu,$x_4,$y_1,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));

//上方横白条
        imagecopyresampled($qrcode,$baitiao_heng,$x_1,$y_1,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
        imagecopyresampled($qrcode,$baitiao_heng,$x_1,$y_2,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
        imagecopyresampled($qrcode,$baitiao_heng,$x_3,$y_1,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
        imagecopyresampled($qrcode,$baitiao_heng,$x_3,$y_2,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
//左下角白条
        imagecopyresampled($qrcode,$baitiao_shu,$x_1,$y_3,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));
        imagecopyresampled($qrcode,$baitiao_shu,$x_2,$y_3,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));
        imagecopyresampled($qrcode,$baitiao_heng,$x_1,$y_3,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
        imagecopyresampled($qrcode,$baitiao_heng,$x_1,$y_4,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
//
//
        $black = imagecolorallocate($qrcode, 0, 0, 0);
//
        $font = './static/src/fonts/PingFang-Regular.ttf';//字体,字体文件需保存到相应文件夹下
        $fontSize = 6;   //字体大小
////左上角
        imagefttext($qrcode, $fontSize, 90, $x_1+$l2-3, $y_2+$l2-6, $black, $font, $text2);
        imagefttext($qrcode, $fontSize, 270, $x_2+3, $y_1+10, $black, $font, $text2);

        imagefttext($qrcode, $fontSize, 0, $x_1, $y_1+$l2-2, $black, $font, $text1);
        imagefttext($qrcode, $fontSize, 180, $x_2+10, $y_2+2, $black, $font, $text1);
//右上角
        imagefttext($qrcode, $fontSize, 90, $x_3+$l2-3, $y_2+$l2-6, $black, $font, $text2);
        imagefttext($qrcode, $fontSize, 270, $x_4+3, $y_1+10, $black, $font, $text2);

        imagefttext($qrcode, $fontSize, 0, $x_3+$l2-10, $y_1+$l2-2, $black, $font, $text1);
        imagefttext($qrcode, $fontSize, 180, $x_4+10, $y_2+2, $black, $font, $text1);
//左下角
        imagefttext($qrcode, $fontSize, 90, $x_1+$l2-3, $x_4+3, $black, $font, $text2);
        imagefttext($qrcode, $fontSize, 270, $x_2+3, $x_3+$l2, $black, $font, $text2);
        imagefttext($qrcode, $fontSize, 0, $x_1, $x_3+$l2-2, $black, $font, $text1);
        imagefttext($qrcode, $fontSize, 180, $x_2+10, $x_4+3, $black, $font, $text1);

        imagepng($qrcode);

        exit();
    }

    public function test2() {
        $code = input('param.code',110);
        if(strlen($code) != 16 ) {
            die('<h1>必须16位数字</h1>');
        }
        $str = substr($code,-4);
        $filename = 'qrcode/'.$code.'.png';
        $insert_data = [
            'code' => $code,
            'create_time' => time()
        ];
        $whereCode = [
            ['code','=',$code]
        ];
        $code_exist = Db::table('mp_qrcode')->where($whereCode)->find();
        if(!$code_exist) {
            Db::table('mp_qrcode')->insert($insert_data);
        }
        $value= 'https://map.psn.asia/?s=' . $code;
        $level = "M"; // 纠错级别：L、M、Q、H
        $size = 6; // 点的大小：1到10
        $margin = 3;
        header('Content-Type:image/png');
        \QRcode::png($value, $filename, $level, $size,$margin);

        $l1 = 34;
        $l2 = 11;
        $l3 = 12;
        $l4 = 34;
        $t = $l1 + ($l2+$l3)*2+$l4;

        $baitiao_h = ($l2+$l3)*2+$l4;
        $baitiao_w = $l2+1;


        $x_1 = $l1;
        $x_2 = $t-$l2-1;
        $x_3 = 400-$t-1;
        $x_4 = 400-$l1-$l2-1;

        $y_1 = $l1;
        $y_2 = $t-$l2-1;
        $y_3 = 400-$t-1;
        $y_4 = 400-$l1-$l2-1;

        $path_1 = $filename;
        $text1 = $str . ' ' . $str . ' ' . $str;
        $text2 = ' '.$str.' '.$str.' ';

        $qrcode = $this->imageresize($path_1,400,400);

        $bg = imagecreatetruecolor(500, 480);
        $baitiao_shu = imagecreatetruecolor($baitiao_w, $baitiao_h);
        $baitiao_heng = imagecreatetruecolor($baitiao_h, $baitiao_w);
        $color = imagecolorallocate($baitiao_shu, 255, 255, 255);
        imagefill($baitiao_shu, 0, 0, $color);
        imagefill($baitiao_heng, 0, 0, $color);
        imagefill($bg, 0, 0, $color);
//竖白条
        imagecopyresampled($qrcode,$baitiao_shu,$x_1,$y_1,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));
        imagecopyresampled($qrcode,$baitiao_shu,$x_2,$y_1,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));
        imagecopyresampled($qrcode,$baitiao_shu,$x_3,$y_1,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));
        imagecopyresampled($qrcode,$baitiao_shu,$x_4,$y_1,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));
        imagecopyresampled($qrcode,$baitiao_shu,$x_1,$y_3,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));
        imagecopyresampled($qrcode,$baitiao_shu,$x_2,$y_3,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));

//横白条
        imagecopyresampled($qrcode,$baitiao_heng,$x_1,$y_1,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
        imagecopyresampled($qrcode,$baitiao_heng,$x_1,$y_2,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
        imagecopyresampled($qrcode,$baitiao_heng,$x_3,$y_1,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
        imagecopyresampled($qrcode,$baitiao_heng,$x_3,$y_2,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
        imagecopyresampled($qrcode,$baitiao_heng,$x_1,$y_3,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
        imagecopyresampled($qrcode,$baitiao_heng,$x_1,$y_4,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));

        $black = imagecolorallocate($qrcode, 0, 0, 0);
        $font = './static/src/fonts/DroidSansChinese.ttf';//字体,字体文件需保存到相应文件夹下
        $font = './static/src/fonts/consolab.ttf';//字体,字体文件需保存到相应文件夹下
        $fontSize = 8;   //字体大小

        $extrainfo = null;
//左上角
        imagefttext($qrcode, $fontSize, 90, $x_1+$l2-3, $y_2+4, $black, $font, $text2);
        imagefttext($qrcode, $fontSize, 270, $x_2+3, $y_1+$l2-2, $black, $font, $text2);

        imagefttext($qrcode, $fontSize, 0, $x_1, $y_1+$l2-2, $black, $font, $text1);
        imagefttext($qrcode, $fontSize, 180, $x_2+12, $y_2+2, $black, $font, $text1);
//右上角
        imagefttext($qrcode, $fontSize, 90, $x_3+$l2-2, $y_2+4, $black, $font, $text2);
        imagefttext($qrcode, $fontSize, 270, $x_4+3, $y_1+$l2-2, $black, $font, $text2);

        imagefttext($qrcode, $fontSize, 0, $x_3+2, $y_1+$l2-2, $black, $font, $text1);
        imagefttext($qrcode, $fontSize, 180, $x_4+12, $y_2+2, $black, $font, $text1);
//左下角
        imagefttext($qrcode, $fontSize, 90, $x_1+$l2-3, $y_4+4, $black, $font, $text2);
        imagefttext($qrcode, $fontSize, 270, $x_2+3, $y_3+$l2-2, $black, $font, $text2);
        imagefttext($qrcode, $fontSize, 0, $x_1, $x_3+$l2-2, $black, $font, $text1);
        imagefttext($qrcode, $fontSize, 180, $x_2+12, $x_4+3, $black, $font, $text1);

        imagecopyresampled($bg,$qrcode,50,0,0,0,imagesx($qrcode),imagesy($qrcode),imagesx($qrcode),imagesy($qrcode));

        imagefttext($bg, 35, 0, 8, 420, $black, $font, $this->fenzu($code));

        imagepng($bg);

        exit();
    }

    public function test3() {
        $code = input('param.code',110);
        if(strlen($code) != 16 ) {
            die('<h1>必须16位数字</h1>');
        }
        $str = substr($code,-4);
        $filename = 'qrcode/'.$code.'.png';
        $insert_data = [
            'code' => $code,
            'create_time' => time()
        ];
        $whereCode = [
            ['code','=',$code]
        ];
        $code_exist = Db::table('mp_qrcode')->where($whereCode)->find();
        if(!$code_exist) {
            Db::table('mp_qrcode')->insert($insert_data);
        }
        $value= 'https://map.psn.asia/?s=' . $code;
        $level = "M"; // 纠错级别：L、M、Q、H
        $size = 6; // 点的大小：1到10
        $margin = 3;
        header('Content-Type:image/png');
        \QRcode::png($value, $filename, $level, $size,$margin);

        $l1 = 34;
        $l2 = 11;
        $l3 = 12;
        $l4 = 34;
        $t = $l1 + ($l2+$l3)*2+$l4;

        $baitiao_h = ($l2+$l3)*2+$l4;
        $baitiao_w = $l2+1;


        $x_1 = $l1;
        $x_2 = $t-$l2-1;
        $x_3 = 400-$t-1;
        $x_4 = 400-$l1-$l2-1;

        $y_1 = $l1;
        $y_2 = $t-$l2-1;
        $y_3 = 400-$t-1;
        $y_4 = 400-$l1-$l2-1;

        $path_1 = $filename;
        $text1 = $str . ' ' . $str . ' ' . $str;
        $text2 = ' '.$str.' '.$str.' ';

        $qrcode = $this->imageresize($path_1,400,400);

        $bg = imagecreatetruecolor(500, 480);
        $baitiao_shu = imagecreatetruecolor($baitiao_w, $baitiao_h);
        $baitiao_heng = imagecreatetruecolor($baitiao_h, $baitiao_w);
        $color = imagecolorallocate($baitiao_shu, 255, 255, 255);
        imagefill($baitiao_shu, 0, 0, $color);
        imagefill($baitiao_heng, 0, 0, $color);
        imagefill($bg, 0, 0, $color);
//竖白条
        imagecopyresampled($qrcode,$baitiao_shu,$x_1,$y_1,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));
        imagecopyresampled($qrcode,$baitiao_shu,$x_2,$y_1,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));
        imagecopyresampled($qrcode,$baitiao_shu,$x_3,$y_1,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));
        imagecopyresampled($qrcode,$baitiao_shu,$x_4,$y_1,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));
        imagecopyresampled($qrcode,$baitiao_shu,$x_1,$y_3,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));
        imagecopyresampled($qrcode,$baitiao_shu,$x_2,$y_3,0,0,imagesx($baitiao_shu),imagesy($baitiao_shu),imagesx($baitiao_shu),imagesy($baitiao_shu));

//横白条
        imagecopyresampled($qrcode,$baitiao_heng,$x_1,$y_1,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
        imagecopyresampled($qrcode,$baitiao_heng,$x_1,$y_2,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
        imagecopyresampled($qrcode,$baitiao_heng,$x_3,$y_1,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
        imagecopyresampled($qrcode,$baitiao_heng,$x_3,$y_2,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
        imagecopyresampled($qrcode,$baitiao_heng,$x_1,$y_3,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));
        imagecopyresampled($qrcode,$baitiao_heng,$x_1,$y_4,0,0,imagesx($baitiao_heng),imagesy($baitiao_heng),imagesx($baitiao_heng),imagesy($baitiao_heng));

        $black = imagecolorallocate($qrcode, 0, 0, 0);
        $font = './static/src/fonts/DroidSansChinese.ttf';//字体,字体文件需保存到相应文件夹下
        $fontSize = 9;   //字体大小

        $extrainfo = null;
//左上角
        imagefttext($qrcode, $fontSize, 90, $x_1+$l2-3, $y_2+2, $black, $font, $text2);
        imagefttext($qrcode, $fontSize, 270, $x_2+3, $y_1+$l2-2, $black, $font, $text2);

        imagefttext($qrcode, $fontSize, 0, $x_1-2, $y_1+$l2-2, $black, $font, $text1);
        imagefttext($qrcode, $fontSize, 180, $x_2+14, $y_2+2, $black, $font, $text1);
//右上角
        imagefttext($qrcode, $fontSize, 90, $x_3+$l2-2, $y_2+2, $black, $font, $text2);
        imagefttext($qrcode, $fontSize, 270, $x_4+3, $y_1+$l2-2, $black, $font, $text2);

        imagefttext($qrcode, $fontSize, 0, $x_3, $y_1+$l2-2, $black, $font, $text1);
        imagefttext($qrcode, $fontSize, 180, $x_4+14, $y_2+2, $black, $font, $text1);
//左下角
        imagefttext($qrcode, $fontSize, 90, $x_1+$l2-3, $y_4+2, $black, $font, $text2);
        imagefttext($qrcode, $fontSize, 270, $x_2+3, $y_3+$l2-2, $black, $font, $text2);
        imagefttext($qrcode, $fontSize, 0, $x_1-2, $x_3+$l2-2, $black, $font, $text1);
        imagefttext($qrcode, $fontSize, 180, $x_2+14, $x_4+3, $black, $font, $text1);

        imagecopyresampled($bg,$qrcode,50,0,0,0,imagesx($qrcode),imagesy($qrcode),imagesx($qrcode),imagesy($qrcode));

        imagefttext($bg, 35, 0, 14, 420, $black, $font, $this->fenzu($code,'  '));

        imagepng($bg);

        exit();
    }

    public function imageresize($filename,$newwidth,$newheight){

        if(!empty($filename) && file_exists($filename)){
            list($width, $height) = getimagesize($filename);
            $thumb = imagecreatetruecolor($newwidth, $newheight);

            $suffix = strrchr($filename,'.');
            switch($suffix){
                case '.gif':
                    $source = imagecreatefromgif($filename);
                    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                    break;
                case '.png':
                    $source = imagecreatefrompng($filename);
                    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                    break;
                case '.jpg':
                    $source = imagecreatefromjpeg($filename);
                    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                    break;
                case '.bmp':
                    $source = imagecreatefromwbmp($filename);
                    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                    break;
            }
            return $thumb;
        }else {
            die('INVALID IMAGE');
        }
    }





    private function fenzu($str,$split= ' ') {
        $arr=str_split($str,4);//4的意知思就是每4个为一组道
        $str=implode($split,$arr);
        return $str;
    }


}