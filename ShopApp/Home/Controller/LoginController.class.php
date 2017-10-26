<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 2016/11/2 0002 上午 11:27
// +----------------------------------------------------------------------
// | Use : 说明用途，主要方面！
// +----------------------------------------------------------------------
// | Author: 小豆子 <253121301@qq.com>
// +----------------------------------------------------------------------

namespace Home\Controller;

use Think\Controller;

class LoginController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->UsModel = D('Users');//用户表
        $this->Cfg = M('Config')->find();//公告表
    }


    /*登录接口*/
    public function login() {
        //$POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        $POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if (!empty($POST['user_name'])) {
            //判断 账号 和 密码 是否为空
            if (!empty($POST['user_name']) && !empty($POST['password'])) {
                $where['user_name'] = array('eq', $POST['user_name']);//账号
                $where['password'] = array('eq', md5($POST['password']));//密码
                /*只留需要的字段*/
                $field = 'user_id,user_name,sex,birthday,mobile_phone,passwd_question,passwd_answer,user_url';
                $netArr = $this->UsModel->Get_Us_Find($where, $field);/*返回Json给安卓端 个人信息资料*/
                if ($netArr) {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 登陆成功*/
                    $bArr = array("resultCode" => "0", "resultMsg" => "登陆成功", "data" => $netArr);
                    echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                } else {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 登陆失败*/
                    $bArr = array("resultCode" => "1", "resultMsg" => "登陆失败");
                    echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                }
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 密码或账号为空*/
                $bArr = array("resultCode" => "1", "resultMsg" => "密码或账号为空");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
        $this->display();/*web端 测试*/
    }

    /*注册接口*/
    public function regis() {
        //$POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        $POST = isset($_POST) ? $_POST : "" ;//接收安卓端发来的数据
        if (!empty($POST['user_name'])) {
            /*验证手机验证码*/
            if ($this->Cfg['key'] == $POST['key']) {
                $ret = $this->UsModel->Get_Us_Add(); /*添加用户*/
                if ($ret) {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 添加成功*/
                    $bArr = array("resultCode" => "0", "resultMsg" => "添加成功");
                    echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                } else {
                    /*<自动验证>返回的错误信息*/
                    $one = $this->UsModel->getError();
                    /*三目判断没有<自动验证>返回信息就返回'添加失败'*/
                    $where = !empty($one) ? $one : "添加失败" ;//三目判断
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 由三目判断选择*/
                    $bArr = array("resultCode" => "1", "resultMsg" => "{$where}");
                    echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                }
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 手机验证码无效*/
                $bArr = array("resultCode" => "1", "resultMsg" => "手机验证码无效");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
        $this->display();/*web端 测试*/
    }

    /*密码忘记接口*/
    public function forget() {
        //$POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        $POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if (!empty($POST['uname'])) {
            //判断短信验证码
            if ($this->Cfg['key'] == $POST['key']) {
                /*where条件、是否有这个用户、如果有返回信息*/
                $where['user_name'] = $POST['uname'];
                /*field只留需要的字段*/
                $field = "user_id,user_name,sex,birthday,mobile_phone,passwd_question,passwd_answer";
                $Arr_one = $this->UsModel->Get_Us_Find($where, $field);
                if ($Arr_one) {
                    /*用上面返回信息ID 修改当前用户密码*/
                    $where_one['user_id'] = $Arr_one['user_id'];
                    $Arr_two = $this->UsModel->Get_Us_Save($where_one);//修改密码
                    if ($Arr_two) {
                        /*返回Json给安卓端 提示信息<给用户显示>  修改成功*/
                        $return = array("resultCode" => "0", "resultMsg" => "修改成功");
                        echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                        exit;
                    } else {
                        /*<自动验证>返回的错误信息*/
                        $error = $this->UsModel->getError();
                        /*三目判断没有<自动验证>返回信息就返回'添加失败'*/
                        $where = !empty($error) ? $error : "修改失败";//三目判断
                        /*返回Json给安卓端 提示信息<给用户显示> 由三目判断选择*/
                        $return = array("resultCode" => "1", "resultMsg" => "{$where}");
                        echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                        exit;
                    }
                } else {
                    /*返回Json给安卓端 提示信息<给用户显示>  账号不存在*/
                    $bArr = array("resultCode" => "1", "resultMsg" => "账号不存在");
                    echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                }
            } else {
                /*返回Json给安卓端 提示信息<给用户显示>  手机验证码无效*/
                $bArr = array("resultCode" => "1", "resultMsg" => "手机验证码无效");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
        $this->display();/*web端 测试*/
    }
}