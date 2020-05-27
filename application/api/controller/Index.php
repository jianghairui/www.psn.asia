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
        if(!$code_exist) {
            die('编号不存在');
        }
        $this->assign('info',$code_exist);
        return $this->fetch();
    }

    public function test() {
        $code = input('param.code',110);
        $filename = 'qrcode/'.$code.'.jpg';
        $insert_data = [
            'code' => $code,
            'create_time' => time()
        ];
        $whereCode = [
            ['code','=',$code]
        ];
        $code_exist = Db::table('mp_qrcode')->where($whereCode)->find();
        if(!$code_exist) {
//            Db::table('mp_qrcode')->insert($insert_data);
        }
        $value= 'https://www.psn.asia/code?code=' . $code;
        $level = "H"; // 纠错级别：L、M、Q、H
        $size = 6; // 点的大小：1到10
        $margin = 3;
        header('Content-Type:image/png');
        \QRcode::png($value, false, $level, $size,$margin);
        exit($filename);
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


    //刷新二维码
    public function refreshQrode() {
        if(request()->isPost()) {
            $val['id'] = input('post.id');
            checkInput($val);
            try {
                $whereCollect = [
                    ['id','=',$val['id']]
                ];
                $collect_exist = Db::table('t_collect')->where($whereCollect)->find();
                if(!$collect_exist) {
                    return ajax('非法参数',-1);
                }

                $value= 'http://tjtcy.cn/dist/#/collectionsdetail?id=' . $collect_exist['id'];
                $filename = 'upload/qrcode/'.$collect_exist['id'].'.jpg';

                $this->genQrcode($value,$filename);
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            return ajax($filename . '?' . mt_rand(1,1000));
        }
    }


    //生成二维码
    private function genQrcode($value = '',$filename = '') {
        $level = "M"; // 纠错级别：L、M、Q、H
        $size = 6; // 点的大小：1到10
        $margin = 3;
        header('Content-Type:image/png');
        \QRcode::png($value, $filename, $level, $size,$margin);
        return $filename;
    }


}