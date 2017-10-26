<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 2016/11/9 0009 下午 5:21
// +----------------------------------------------------------------------
// | Use : 说明用途，主要方面！
// +----------------------------------------------------------------------
// | Author: 小豆子 <253121301@qq.com>
// +----------------------------------------------------------------------

namespace Home\Controller;

use Think\Controller;

class AddressController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->UaModel = D('UserAddress');//用户收货地址表
        $this->Cfg = M('Config')->find();//公共表
    }


    /*查看收货人地址列表*/
    public function viewAddress() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if (!empty($POST)) {
            $where_one['user_id'] = $POST['user_id'];
            /*留需要的字段*/
            $field = "address_id,user_id,consignee,province,city,district,address,tel";
            $UaArr = $this->UaModel->Get_Ua_select($where_one, $field);
            if (!empty($UaArr)) {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功 返回<data>数组给安卓*/
                $return = array("resultCode" => "0", "resultMsg" => "获取成功", "data" => $UaArr);
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 无收货人信息请添加*/
                $return = array("resultCode" => "1", "resultMsg" => "无收货人信息请添加");
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
        $this->display();/*web端 测试*/
    }

    /*删除收货人地址*/
    public function delAddress() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if (!empty($POST['user_id'])) {
            /*判断用户是否存在 和 判断收藏是否存在  如都存在执行删除*/
            $where_one['address_id'] = $POST['address_id'];
            $where_one['user_id'] = $POST['user_id'];
            /*收藏ID不等于空才执行删除*/
            if(!empty($POST['address_id'])){
                /*按条件执行删除*/
                $UaArr = $this->UaModel->Get_Ua_delect($where_one);
                if (!empty($UaArr)) {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 删除成功*/
                    $return = array("resultCode" => "0", "resultMsg" => "删除成功");
                    echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                } else {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 删除失败*/
                    $return = array("resultCode" => "1", "resultMsg" => "删除失败");
                    echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                }
            }else{
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 收藏不存在*/
                $return = array("resultCode" => "1", "resultMsg" => "收藏不存在");
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }


        }
    }

    /*添加收货人地址*/
    public function addAddress() {
        //$POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        $POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if (!empty($POST['user_id'])) {
            /*添加数据到用户收货地址表*/
            $UaArr = $this->UaModel->Get_Ua_add();
            if (!empty($UaArr)) {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 添加成功  返回数据给安卓*/
                $return = array("resultCode" => "0", "resultMsg" => "添加成功", "address_id" => $UaArr);
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            } else {
                /*<自动验证>返回的错误信息*/
                $error = $this->UaModel->getError();
                /*三目判断没有<自动验证>返回信息就返回'添加失败'*/
                $where = !empty($error) ? $error : "添加失败";//三目判断
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 由三目判断选择*/
                $return = array("resultCode" => "1", "resultMsg" => $where);
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
        $this->display();/*web端 测试*/
    }

    /*修改收货人地址*/
    public function editAddress() {
        //$POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        $POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据

        if (IS_POST) {
            $this->UaModel = D('UserAddress');
            $where_one['address_id'] = $POST['address_id'];
            $where_one['user_id'] = $POST['uid'];
            $UaArr_two = $this->UaModel->Get_Ua_save($where_one);
            if (!empty($UaArr_two)) {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 修改成功  */
                $return = array("resultCode" => "0", "resultMsg" => "修改成功");
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            } else {
                /*<自动验证>返回的错误信息*/
                $error = $this->UaModel->getError();
                /*三目判断没有<自动验证>返回信息就返回'添加失败'*/
                $where = !empty($error) ? $error : "修改失败";//三目判断
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 由三目判断选择*/
                $return = array("resultCode" => "0", "resultMsg" => "修改成功");
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }

        /*以下测试数据 web端 测试数据*/
        $this->UaModel = D('UserAddress');
        $where_one['address_id'] = $POST['address_id'];
        $where_one['user_id'] = $POST['user_id'];
        /*只留需要的字段*/
        $field = "address_id,user_id,consignee,province,city,district,address,tel,zipcode";
        $UaArr_one = $this->UaModel->Get_Ua_find($where_one, $field);
        $this->assign('UaArr_one', $UaArr_one);
        $this->display();/*web端 测试*/

    }


}