<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 2016/11/14 0014 下午 10:33
// +----------------------------------------------------------------------
// | Use : 说明用途，主要方面！
// +----------------------------------------------------------------------
// | Author: 小豆子 <253121301@qq.com>
// +----------------------------------------------------------------------

namespace Home\Model;

use Think\Model;

class CollectGoodsModel extends Model {
    /*自动验证*/
    protected $_validate = array(
        array('user_id', 'require', '用户ID为空！'),
        array('goods_id', 'require', '商品ID为空！'),
    );
    /*自动完成*/
    protected $_auto = array(
        array('add_time', 'time', 3, 'function'), // 对update_time字段在更新的时候写入当前时间戳
    );

    /*数组查询*/
    public function Get_Co_select($where) {
        $Coret = $this->where($where)->select();
        return $Coret;
    }

    /*添加数据*/
    public function Get_Co_add() {
        if ($this->create()) {
            $retCo = $this->add();
        }
        return $retCo;
    }

    /*查询一条*/
    public function Get_Co_find($where) {
        $retCo = $this->where($where)->find();
        return $retCo;
    }

    /*删除数据*/
    public function Get_Co_delete($where) {
        $delCo = $this->where($where)->delete();
        return $delCo;
    }
}