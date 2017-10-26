<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 2016/11/11 0011 上午 10:49
// +----------------------------------------------------------------------
// | Use : 说明用途，主要方面！
// +----------------------------------------------------------------------
// | Author: 小豆子 <253121301@qq.com>
// +----------------------------------------------------------------------

namespace Home\Model;

use Think\Model;

class OrderGoodsModel extends Model {


    /*数组查询*/
    public function Get_GoOr_Select($where_Or = "", $field = "", $join = "", $alias = "") {
        $GoOr = $this->alias($alias)->join($join)->field($field)->where($where_Or)->select();
        return $GoOr;
    }

    /*删除*/
    public function Get_GoOr_Delete($where_Or = "") {

        $GoDe = $this->where($where_Or)->delete();
        return $GoDe;
    }

    /*添加*/
    public function Get_GoOr_Add($data) {
        if ($this->create()) {
            $ret = $this->add($data);
        }
        return $ret;
    }

    /*添加*/
    public function Get_GoOr_Addall($data) {
        $ret = $this->addAll($data, $options = array(), $replace = false);
        return $ret;
    }

    /*查询一条数据*/
    public function Get_GoOr_Find($where) {
        $ret = $this->where($where)->find();
        return $ret;
    }
}