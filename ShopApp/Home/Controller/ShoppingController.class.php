<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 2016/11/9 0009 下午 6:11
// +----------------------------------------------------------------------
// | Use : 说明用途，主要方面！
// +----------------------------------------------------------------------
// | Author: 小豆子 <253121301@qq.com>
// +----------------------------------------------------------------------

namespace Home\Controller;

use Think\Controller;

class ShoppingController extends Controller {


    /*实例化数据库*/
    public function __construct() {
        parent::__construct();
        $this->UaAddrModel = D('UserAddress');//用户收货地址表
        $this->UsModel = D('Users');//用户表
        $this->Cfg = M('Config')->find();//公告表
        $this->CartModel = D('Cart');//购物车
        $this->GoModel = D('Goods');//商品
        $this->CoGoModel = D('CollectGoods');//用户收藏表
        $this->OrInfoModel = D('OrderInfo');//订单信息表
        $this->OrGoModel = D('OrderGoods');//订单商品表
        $this->GoMode = D('Goods');//商品表
    }


    /*加入收藏*/
    public function addCtion() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "" ;//接收安卓端发来的数据
        if (!empty($POST['user_id'])) {
            $CoGoArr = $this->CoGoModel->Get_Co_add();
            if (!empty($CoGoArr)) {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 收藏添加成功*/
                $bArr = array("resultCode" => "0", "resultMsg" => "添加成功");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            } else {
                /*<自动验证>返回的错误信息*/
                $one = $this->CoGoModel->getError();
                /*三目判断没有<自动验证>返回信息就返回'收藏添加失败'*/
                $where = !empty($one) ? $one : "收藏添加失败";//三目判断
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 由三目判断选择*/
                $bArr = array("resultCode" => "0", "resultMsg" => $where);
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
        $this->display();/*web端 测试*/
    }

    /*是否加入收藏*/
    public function judgeCtion() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "" ;//接收安卓端发来的数据
        if (!empty($POST['user_id'])) {
            $where['user_id'] = array('eq', $POST['user_id']);
            $where['goods_id'] = array('eq', $POST['goods_id']);
            $CoGoRet = $this->CoGoModel->Get_Co_find($where);
            if ($CoGoRet) {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 已加入收藏*/
                $bArr = array("resultCode" => "0", "resultMsg" => "已加入收藏", "judge" => 0);
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 未加入收藏*/
                $bArr = array("resultCode" => "0", "resultMsg" => "未加入收藏", "judge" => 1);
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        } else {
            /*返回Json给安卓端 --  提示信息<给用户显示> -- 非法用户或非法商品*/
            $bArr = array("resultCode" => "1", "resultMsg" => "非法用户或非法商品", "judge" => 1);
            echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
            exit;
        }


    }


    /*加入购物车*/
    public function shopCart() {
        //$POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        $POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if (!empty($POST['user_id'])) {
            /*判断数据是否存在*/
            $where['goods_id'] = $POST['goods_id'];
            $CartArr_One = $this->GoMode->Get_Go_Find($where);
            if ($CartArr_One) {
                $whereOne['user_id'] = $POST['user_id'];
                $whereOne['goods_id'] = $POST['goods_id'];
                //判断购物车是否有数据
                $CartFind_One = $this->CartModel->Get_Ca_find($whereOne);
                if (!empty($CartFind_One)) {
                    //使用查询的数据去增加已有的商品数量
                    $_POST['rec_id'] = $CartFind_One['rec_id'];
                    $_POST['goods_number'] = intval($CartFind_One['goods_number'] + $_POST['goods_number']);
                    $_POST['goods_name'] = $CartArr_One['goods_name'];
                    $CaSaArr = $this->CartModel->Get_Ca_save();
                    if ($CaSaArr) {
                        /*返回Json给安卓端 --  提示信息<给用户显示> -- 添加成功*/
                        $bArr = array("resultCode" => "0", "resultMsg" => "添加成功");
                        echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                        exit;
                    } else {
                        /*返回Json给安卓端 --  提示信息<给用户显示> -- 添加失败*/
                        $bArr = array("resultCode" => "1", "resultMsg" => "添加失败");
                        echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                        exit;
                    }
                } else {
                    /*商品存在 吧商品信息存入购物车表*/
                    $_POST['goods_sn'] = $CartArr_One['goods_sn'];//把货号存到购物车
                    $_POST['goods_name'] = $CartArr_One['goods_name'];
                    $CartArr_Two = $this->CartModel->Get_Ca_add();
                    if ($CartArr_Two) {
                        /*返回Json给安卓端 --  提示信息<给用户显示> -- 添加成功*/
                        $bArr = array("resultCode" => "0", "resultMsg" => "添加成功");
                        echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                        exit;
                    } else {
                        /*返回Json给安卓端 --  提示信息<给用户显示> -- 添加失败*/
                        $bArr = array("resultCode" => "1", "resultMsg" => "添加失败");
                        echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                        exit;
                    }
                }
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取失败*/
                $bArr = array("resultCode" => "1", "resultMsg" => "获取失败");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
        $this->display();/*web端 测试*/
    }

    /*购物车删除*/
    public function shopCartDelete() {
        //$POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        $POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if (!empty($POST['user_id'])) {
            $where['rec_id'] = $POST['rec_id'];
            $CarDel = $this->CartModel->Get_Ca_delete($where);
            if (!empty($CarDel)) {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 删除成功*/
                $bArr = array("resultCode" => "0", "resultMsg" => "删除成功");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 删除失败*/
                $bArr = array("resultCode" => "1", "resultMsg" => "删除失败");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }

        $this->display();
    }


    /*购物车列表*/
    public function shopCartList() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "" ;//接收安卓端发来的数据
        if (!empty($POST['user_id'])) {
            /*查看当前用户购物车内的商品*/
            $where['user_id'] = $POST['user_id'];

            $fieldGo = "goods_id,goods_thumb,goods_number"; /*只留需要的字段*/
            /*获取所以商品*/
            $GoArr = $this->GoModel->Get_Go_Selects('', $fieldGo);//商品表

            /*------------------------------------------*/

            $fieldCa = "rec_id,goods_name,goods_id,goods_price,goods_number"; /*只留需要的字段*/
            /*获取当前用户所以得购物车内的商品*/
            $CartArr = $this->CartModel->Get_Ca_select($where, $fieldCa);//购物车表


            /*吧对应的商品图片和库存放入 购物车的对应商品内*/
            foreach ($GoArr as $k => $v) {
                foreach ($CartArr as $kk => $vv) {
                    if ($v['goods_id'] == $vv['goods_id']) {
                        $CartArr[$kk]['goods_thumb'] = $v['goods_thumb'];//商品图片
                        $CartArr[$kk]['goods_number_all'] = $v['goods_number'];//商品总库存
                    }
                }
            }
            if (!empty($CartArr)) {

                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data>*/
                $bArr = array("resultCode" => "0", "resultMsg" => "获取成功", "data" => $CartArr);
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
                //$this->assign('CartArr', $CartArr);//测试
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取但是  data给一个空[]  安卓端判断使用*/
                $bArr = array("resultCode" => "0", "resultMsg" => "获取成功", "data" => "[]");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }

        }
        $this->display();/*web端 测试*/


    }


    /*购物车结算*/
    public function shopEment() {
        //$POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        $POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if (!empty($POST['user_id'])) {

            //接收安卓数据并且转换成数组
            $jsonArr = json_decode($POST['goods_list'], true);
            $CaArr = '';//用于存数添加成功后的数值相加
            //批量修改 //购物车数量变动就修改
            foreach ($jsonArr as $k => $v) {
                $CaArr .= $this->CartModel->Get_Ca_save($jsonArr[$k]);
            }

            /*查看当前用户购物车内的商品*/
            $where['user_id'] = $POST['user_id'];

            $fieldGo = "goods_id,goods_thumb,goods_number"; /*只留需要的字段*/
            /*获取所以商品*/
            $GoArr = $this->GoModel->Get_Go_Selects('', $fieldGo);//商品表

            /*------------------------------------------*/

            $fieldCa = "rec_id,goods_name,goods_id,goods_price,goods_number"; /*只留需要的字段*/
            /*获取当前用户所以得购物车内的商品*/
            $CartArr = $this->CartModel->Get_Ca_select($where, $fieldCa);//购物车表


            /*吧对应的商品图片和库存放入 购物车的对应商品内*/
            foreach ($GoArr as $k => $v) {
                foreach ($CartArr as $kk => $vv) {
                    if ($v['goods_id'] == $vv['goods_id']) {
                        $CartArr[$kk]['goods_thumb'] = $v['goods_thumb'];//商品图片
                        $CartArr[$kk]['goods_number_all'] = $v['goods_number'];//商品总库存
                    }
                }
            }


            //            /*判断每个商品当前数量 是否超过总库存*/
            foreach ($CartArr as $v) {
                //当前库存 与 购物车数量相当 不提示失败信息
                if ($v['goods_number_all'] == $v['goods_number']) {
                    $resultMsg = '';
                } else {
                    //当前库存  小于  购物车数量  提示失败信息
                    if ($v['goods_number_all'] < $v['goods_number']) {
                        $resultMsg = '1';
                    }
                }
            }

            if ($resultMsg == '') {

                if (!empty($CartArr)) {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data>*/
                    $bArr = array("resultCode" => "0", "resultMsg" => "获取成功");
                    echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                } else {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取失败*/
                    $bArr = array("resultCode" => "1", "resultMsg" => "获取失败");
                    echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                }
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取失败*/
                $bArr = array("resultCode" => "2", "resultMsg" => "获取成功");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }


        }
        $this->display();/*web端 测试*/
    }

    /*订单确认*/
    public function shopConfirm() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "" ;//接收安卓端发来的数据
        if (!empty($POST['user_id'])) {
            /*--------------获取收货人信息---------------*/
            $whereAdder['address_id'] = array('eq', $POST['address_id']);
            $UaAddrArr = $this->UaAddrModel->Get_Ua_find($whereAdder);
            $_POST['consignee']  = $UaAddrArr['consignee'];//收件人名称
            $_POST['province']  = $UaAddrArr['province'];//省
            $_POST['city']  = $UaAddrArr['city'];//市
            $_POST['district']  = $UaAddrArr['district'];//县
            $_POST['address']  = $UaAddrArr['address'];//收件人详细地址
            $_POST['pay_status']  = 0;//收件人详细地址
            $_POST['shippng_status']  = 1;//收件人详细地址
            $_POST['order_status']  = 1;//收件人详细地址
            $_POST['add_time'] = time();
            $_POST['goods_amount'] = $POST['total'];
            $_POST['order_amount'] = $POST['total'];

            //自动创建数据  获取订单信息表内容 并且存入订单信息表
            $retone = $this->OrInfoModel->Get_or_add();
            $OrInfoWhere['order_id'] = $retone;
            $fieldOn = "order_sn";
            $OrInfoArr = $this->OrInfoModel->Get_or_find($OrInfoWhere,$fieldOn);
            if (!empty($retone)) {
                //获得 商品 内容 存入订单商品列表
                if (empty($POST['rec_id_all'])) {
                    /*----------立即购买-------------*/
                    /*-------------购买的商品-------------*/
                    $whereGoFind['goods_id'] = array('eq', $POST['goods_id']);
                    $field = "goods_id,goods_sn,goods_name,market_price,shop_price";
                    $retGo = $this->GoMode->Get_Go_Find($whereGoFind, $field);

                    /*---------添加到订单表---------*/
                    $retGo['order_id'] = $retone;
                    $retGo['goods_number'] = $POST['goods_number'];
                    $rettwo = $this->OrGoModel->Get_GoOr_Add($retGo);//添加订单商品表
                    if (!empty($rettwo)) {
                        /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data>*/
                        $bArr = array("resultCode" => "0", "resultMsg" => "订单成功" , 'data' => $OrInfoArr);
                        echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                        exit;
                    } else {
                        /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data>*/
                        $bArr = array("resultCode" => "1", "resultMsg" => "订单失败");
                        echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                        exit;
                    }
                } else {

                    /*----------购物车结算-------------*/
                    $whereCa['rec_id'] = array('in', $POST['rec_id_all']);
                    $field = "rec_id,goods_id,goods_name,goods_sn,market_price,goods_price";
                    $retCar = $this->CartModel->Get_Ca_select($whereCa,$field);//获取所有购物车内容
                    //将刚生产的订单详情表id循环添加到 每一个商品下
                    foreach ($retCar as $k => $v) {
                        $retCar[$k]['order_id'] = $retone;
                    }

                    //获取所以购物车ID
                    $CarIdAll = '';
                    foreach ($retCar as $k => $v) {
                        $CarIdAll .= empty($CarIdAll) ? $v['rec_id'] : ',' . $v['rec_id'];
                    }

                    //删除不需要的字段
                    foreach ($retCar as $k => $v) {
                        unset($retCar[$k]['rec_id']);
                    }
                    //执行addAll批量添加
                    $rettwos = $this->OrGoModel->Get_GoOr_Addall($retCar);
                    if (!empty($rettwos)) {
                        //订单生成后删除购物车内相应的内容
                        $whereCaDe['rec_id'] = array('in', $CarIdAll);
                        $rettwo = $this->CartModel->Get_Ca_delete($whereCaDe);
                        if (!empty($rettwo)) {
                            /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data>*/
                            $bArr = array("resultCode" => "0", "resultMsg" => "订单成功" ,'data'=> $OrInfoArr);
                            echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                            exit;
                        } else {
                            /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data>*/
                            $bArr = array("resultCode" => "1", "resultMsg" => "订单失败");
                            echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                            exit;
                        }
                    }
                }
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data>*/
                $bArr = array("resultCode" => "1", "resultMsg" => "获取失败");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }

        $act = isset($_GET['act']) ? $_GET['act'] : '';
        $this->assign('act', $act);
        $this->display();
    }

}