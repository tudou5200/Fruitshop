<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 2016/11/14 0014 下午 5:17
// +----------------------------------------------------------------------
// | Use : 说明用途，主要方面！
// +----------------------------------------------------------------------
// | Author: 小豆子 <253121301@qq.com>
// +----------------------------------------------------------------------

namespace Home\Model;

use Think\Model;
use Think\Page;

class UserAddressModel extends Model {

    /*自动验证*/
    protected $_validate = array(
        array('consignee', 'require', '收件人为空！'),
        array('tel', 'require', '手机号为空！'),
        array('province', 'require', '省份为空！'),
        array('city', 'require', '城市为空！'),
        array('district', 'require', '地区为空！'),
        array('address', 'require', '详情地址为空！'),
        //array('user_name', '', '帐号重复！', 0, 'unique', 3), // 在新增的时候验证name字段是否唯一
    );
    /*自动完成*/
    protected $_auto = array(
        array('country', '1'), // 新增的时候把teacher_id字段设置为11
    );

    public function Get_Ua_find($where, $field) {
        $UaFind = $this->field($field)->where($where)->find();
        return $UaFind;
    }

    /*数据一组查询*/
    public function Get_Ua_select($where = "", $field = "") {
        $POST = isset($_POST) ? $_POST : "";
        $Uacount = $this->where($where)->count();
        $Page = new Page($Uacount, $POST['ln']);
        $Ualist = $this->field($field)->where($where)->page($POST['p'], $POST['ln'])->select();
        return $Ualist;
    }

    /*数据修改*/
    public function Get_Ua_save($where) {
        if ($this->create()) {
            $Uasave = $this->where($where)->save();
        }
        return $Uasave;
    }

    /*数据删除*/
    public function Get_Ua_delect($where) {
        $Uadelect = $this->where($where)->delete();
        return $Uadelect;
    }

    /*数据添加*/
    public function Get_Ua_add() {
        if ($this->create()) {
            $retadd = $this->add();
        }
        return $retadd;
    }
}