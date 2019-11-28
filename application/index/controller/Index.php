<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2019/11/28
 * Time: 14:31
 */
namespace app\index\controller;

class Index extends Base {

    public function index() {
        return $this->fetch();
    }

}