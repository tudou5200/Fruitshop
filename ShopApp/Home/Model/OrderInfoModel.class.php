<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 2016/11/11 0011 上午 9:48
// +----------------------------------------------------------------------
// | Use : 说明用途，主要方面！
// +----------------------------------------------------------------------
// | Author: 小豆子 <253121301@qq.com>
// +----------------------------------------------------------------------

namespace Home\Model;

use Think\Model;

class OrderInfoModel extends Model {


    /*自动完成*/
    protected $_auto = array(
        array('order_sn', 'time_date', 1, 'callback'), // 对update_time字段在更新的时候写入当前时间戳
    );
    /*添加时间戳*/
    public function time_date() {
        return date('YmdHis',time());
    }


    /*查询一组数据*/
    public function Get_or_select($where = "", $field = "") {
        $ret_one = $this->field($field)->where($where)->select();
        return $ret_one;
    }


    /*查询一条数据*/
    public function Get_or_find($where, $field) {
        $retFi = $this->field($field)->where($where)->find();
        return $retFi;
    }

    /*删除数据*/
    public function Get_or_delete($where = "") {
        $retDe = $this->where($where)->delete();
        return $retDe;
    }

    /*添加数据*/
    public function Get_or_add() {
        if ($this->create()) {
            $retAd = $this->add();
        }
        return $retAd;
    }
}