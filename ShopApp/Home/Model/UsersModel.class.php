<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 2016/11/3 0003 上午 9:22
// +----------------------------------------------------------------------
// | Use : 说明用途，主要方面！
// +----------------------------------------------------------------------
// | Author: 小豆子 <253121301@qq.com>
// +----------------------------------------------------------------------

namespace Home\Model;

use Think\Model;

class UsersModel extends Model {



    /*自动验证*/
    protected $_validate = array(
        array('user_name', 'require', '账号为空！'),
        array('password', 'require', '密码为空！'),
        array('key', 'require', '验证码为空！'),
        array('user_name', '', '帐号重复！', 0, 'unique', 1), // 在新增的时候验证name字段是否唯一

    );
    /*自动完成*/
    protected $_auto = array(
        array('birthday','0000-00-00'),//给用户默认的生日格式
        array('password','md5',1,'function') ,//用户修改和注册给密码加密
        array('reg_time','time',1,'function'),//给用户添加注册时间
    );



    /*添加*/
    public function Get_Us_Add() {
        if ($this->create()) {
            $ret = $this->add();
        } else {
            $ret = false;
        }
        return $ret;
    }

    /*查询多条*/
    public function Get_Ue_Select($where,$field) {
        $UseArr =$this->field($field)->where($where)->select();
        return $UseArr;
    }
     
    /*查询一条*/
    public function Get_Us_Find($where="",$field="") {
        $etr = $this->field($field)->where($where)->find();
        return $etr;
    }

    /*修改*/
    public function Get_Us_Save($where) {
        if($this->create()){
           $ret = $this->where($where)->save();
        }
        return $ret;
    }
}