<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 2016/11/15 0015 下午 4:42
// +----------------------------------------------------------------------
// | Use : 说明用途，主要方面！
// +----------------------------------------------------------------------
// | Author: 小豆子 <253121301@qq.com>
// +----------------------------------------------------------------------

namespace Home\Model;

use Think\Model;

class CartModel extends Model {

    /*数组查询*/
    public function Get_Ca_select($where = "", $field = "", $join = "") {
        $CaSeArr = $this->field($field)->join($join)->alias('ca')->where($where)->select();
        return $CaSeArr;

    }

    /*添加数组*/
    public function Get_Ca_add() {
        if ($this->create()) {
            $CaArr = $this->add();
        }
        return $CaArr;
    }

    /*删除数组*/
    public function Get_Ca_delete($where) {
        $retDele = $this->where($where)->delete();
        return $retDele;
    }

    /*查询一条数据*/
    public function Get_Ca_find($where = "", $field = "") {
        $ret = $this->field($field)->where($where)->find();
        return $ret;
    }

    /*修改数组*/
    public function Get_Ca_save($post) {
        if ($this->create($post)) {
            $ret = $this->save();
        }
        return $ret;
    }
}