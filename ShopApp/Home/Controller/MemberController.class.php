<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 2016/11/2 0002 下午 12:58
// +----------------------------------------------------------------------
// | Use : 说明用途，主要方面！
// +----------------------------------------------------------------------
// | Author: 小豆子 <253121301@qq.com>
// +----------------------------------------------------------------------

namespace Home\Controller;

use Think\Controller;
use Think\Upload;

class MemberController extends Controller {

    /*实例化需要的数据表*/
    public function __construct() {
        parent::__construct();
        $this->UsModel = D('Users');            //用户表
        $this->Cfg = M('Config')->find();       //公告表
        $this->OrModel = D('OrderInfo');        //订单信息表
        $this->OrGoModel = D('OrderGoods');     //订单商品表
        $this->GoModel = D('Goods');            //商品表
        $this->CollModel = D('CollectGoods');   //用户收藏表

    }
    /*来自个人中心*/
    /*修改个人信息*/
    public function index() {
        $POST = !empty($_REQUEST) ? $_REQUEST : ""; /*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "";  //接收安卓端发来的数据
        if (IS_POST) {
            //修改当前用户的密码
            $where_one['user_id'] = $POST['user_id'];
            $UsArr = $this->UsModel->Get_Us_Save($where_one);
            if (!empty($UsArr)) {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 修改成功*/
                $return = array("resultCode" => "0", "resultMsg" => "修改成功");
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 修改失败*/
                $return = array("resultCode" => "1", "resultMsg" => "修改失败");
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
        /*只留需要的字段/*web端 测试*/
        $field = "user_name,birthday,user_url,passwd_question,passwd_answer,mobile_phone,sex,user_id";
        $where['user_id'] = array('eq', $POST{'user_id'});
        $ret = $this->UsModel->Get_Us_Find($where, $field); //修改前的查看 /*web端 测试*/
        $this->assign('ret', $ret);
        $this->display();/*web端 测试*/
    }


    /*来自个人中心*/
    /*修改密码*/
    public function changePwd() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";     /*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "";          //接收安卓端发来的数据
        if (!empty($POST['uid'])) {
            if (!empty($POST['rpassword'])) {
                $_POST['password'] = $POST['password'];        //新密码
                $whereo['password'] = md5($POST['rpassword']);   //原密码
                $whereo['user_id'] = $POST['uid'];              //用户的ID
                $ret = $this->UsModel->Get_Us_Save($whereo);    //修改当前用户密码
                if ($ret) {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 修改成功*/
                    $return = array("resultCode" => "0", "resultMsg" => "修改成功");
                    echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                } else {
                    /*<自动验证>返回的错误信息*/
                    $error = $this->UsModel->getError();
                    /*三目判断没有<自动验证>返回信息就返回'添加失败'*/
                    $where = !empty($error) ? $error : "修改失败";//三目判断
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 由三目判断选择*/
                    $return = array("resultCode" => "1", "resultMsg" => "{$where}");
                    echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                }
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 修改失败*/
                $return = array("resultCode" => "1", "resultMsg" => "新密码为空");
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }

        $this->display(); /*web端 测试*/
    }

    /*来自个人中心*/
    /*查看商品收藏列表*/
    public function viewCtion() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if (!empty($POST['user_id'])) {
            $where['user_id'] = array('eq', $POST['user_id']);
            $CretArr = $this->CollModel->Get_Co_select($where);//查找ID所有收藏
            /*获取当前用户收藏的所有商品ID*/
            $uid = '';//设置空变量
            foreach ($CretArr as $k => $v) {
                /*三目判断[变量为空]执行赋值、[不为空]执行<逗号>拼接赋值*/
                $uid .= empty($uid) ? $v['goods_id'] : ',' . $v['goods_id'];
            }
            /*查找当前用户收藏的所有商品信息*/
            $where_one['goods_id'] = array('in', $uid);
            /*只留需要的字段*/
            $field = "goods_id,goods_thumb,goods_name,shop_price";
            $GooArr = $this->GoModel->Get_Go_Select($where_one, $field);
            if (!empty($GooArr)) {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功*/
                $return = array("resultCode" => "0", "resultMsg" => "获取成功", "data" => $GooArr);
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
                //$this->assign('CretNew', $GooArr);//测试
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取失败*/
                $return = array("resultCode" => "1", "resultMsg" => "获取失败");
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        } else {
            /*返回Json给安卓端 --  提示信息<给用户显示> -- 用户不存在*/
            $return = array("resultCode" => "1", "resultMsg" => "用户不存在");
            echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
            exit;
        }
        //$this->display();/*web端 测试*/
    }

    /*来组商品收藏列表*/
    /*删除商品收藏*/
    public function delCtion() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if (!empty($POST['user_id'])) {
            /*判断用户ID和商品ID都是否为空*/
            if (!empty($POST['user_id']) && !empty($POST['goods_id'])) {
                $where_one['user_id'] = array('eq', $POST['user_id']);//用户ID
                $where_one['goods_id'] = array('eq', $POST['goods_id']);//商品ID
                /*判断收藏表里是否有此商品*/
                $rte_One = $this->CollModel->Get_Co_find($where_one);
                if ($rte_One) {
                    /*执行删除*/
                    $where_two['rec_id'] = array('eq', $rte_One['rec_id']);
                    $ret_Two = $this->CollModel->Get_Co_delete($where_two);
                    if ($ret_Two) {
                        /*返回Json给安卓端 --  提示信息<给用户显示> 提示码 = 20 -- 删除成功*/
                        $return = array("resultCode" => "0", "resultMsg" => "删除成功");
                        echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                        exit;
                    } else {
                        /*返回Json给安卓端 --  提示信息<给用户显示> 提示码 = 1 -- 删除失败*/
                        $return = array("resultCode" => "1", "resultMsg" => "删除失败");
                        echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                        exit;
                    }
                } else {
                    /*返回Json给安卓端 --  提示信息<给用户显示> 提示码 = 1 -- 收藏商品不存在*/
                    $return = array("resultCode" => "1", "resultMsg" => "收藏商品不存在");
                    echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                }
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> 提示码 = 1 -- 用户或商品为空*/
                $return = array("resultCode" => "1", "resultMsg" => "用户或商品为空");
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
    }

    /*通过个人中心查看此接口*/
    /*查看订单列表*/
    public function viewOrder() {
        $POST = !empty($_REQUEST) ? $_REQUEST : ""; /*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if (!empty($POST['user_id'])) {
            /*获取当前用户下所有订单信息*/
            $where['user_id'] = array('eq', $POST['user_id']);
            /*只留需要的字段*/
            $field = "order_id,order_status,shipping_status,order_sn,order_amount,add_time,pay_status";
            $OrModel = $this->OrModel->Get_or_select($where, $field);//订单数据

            //订单商品表
            $where_GoRO = "";//空条件站位
            $fields = "rec_id,order_id,goods_id";//只留需要的字段
            $GoOrModel = $this->OrGoModel->Get_GoOr_Select($where_GoRO, $fields);

            /*-----------找到订单内第商品-------------*/
            foreach ($OrModel as $k => $v) {
                foreach ($GoOrModel as $kk => $vv) {
                    /*将每一个订单下的商品赋值到对应的订单数组下*/
                    if ($v['order_id'] == $vv['order_id']) {
                        $OrModel[$k]['son'][] = $vv;
                    }
                }
            }

            /*-----------查找第一个商品的图片-------------*/
            //商品数组
            $where_Go = "";//空数组站位
            $field = 'goods_id,goods_thumb';//只留需要的字段
            $GoModel = $this->GoModel->Get_Go_Select($where_Go, $field);
            foreach ($OrModel as $K => $v) {
                foreach ($v['son'] as $kk => $vv) {
                    foreach ($GoModel as $kkk => $vvv) {
                        /*吧商品的详细信息放入订单详情数组内*/
                        if ($vv['goods_id'] == $vvv['goods_id']) {
                            $OrModel[$K]['goods_thumb'] = $vvv['goods_thumb'];
                        }

                    }
                }
            }
            /*获得一个商品的图片后将订单商品数组删除*/
            foreach ($OrModel as $k => $v) {
                unset($OrModel[$k]['son']);
            }

            if (!empty($OrModel)) {
                /*返回Json给安卓端 --  提示信息<给用户显示> 提示码 = 0 -- 获取成功  --  返回data<数组>给安卓端*/
                $return = array("resultCode" => "0", "resultMsg" => "获取成功", "data" => $OrModel);
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
                //$this->assign('data_list', $OrModel);//测试
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> 提示码 = 1  -- 获取失败*/
                $return = array("resultCode" => "1", "resultMsg" => "获取失败");
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
        $this->display();/*web端 测试*/
    }

    /*通过订单列表跳转到此接口*/
    /*查看订单状态(详情)*/
    public function detOrder() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if (!empty($POST['user_id'])) {
            $where['order_id'] = array('eq', $POST['order_id']);//订单信息ID查找订单商品表内容
            $where['user_id'] = array('eq', $POST['user_id']);//当前用户ID
            // fleds => 只查询需要的字段
            $fledsa = "order_id,province,city,district,shipping_id,pay_id,consignee,order_sn,order_amount
                     ,how_oos,add_time,address,pay_status,tel,order_status,shipping_status,province,district,city";
            //查看当前用户  当前订单信息 内容
            $data_list = $this->OrModel->Get_or_Find($where, $fledsa);

            /*订单总表*/
            if (!empty($data_list)) {
                /*-----------找到订单内第一个商品-------------*/
                $where_Or['r.order_id'] = $data_list['order_id'];
                /*只留需要的字段*/
                $fleds = 'r.goods_name,r.goods_number,r.goods_price,g.goods_thumb';
                /*与商品联表*/
                $join = "ecs_goods g ON r.goods_id=g.goods_id";
                /*给当前数据命名*/
                $alias = "r";
                $ret_Or = $this->OrGoModel->Get_GoOr_Select($where_Or, $fleds, $join, $alias);
                $data_list['goods_list'] = $ret_Or;
                /*订单商品表*/
                if (!empty($ret_Or)) {
                    /*返回Json给安卓端 --  提示信息<给用户显示> 提示码 = 0 -- 获取成功  --  返回data<数组>给安卓端*/
                    $return = array("resultCode" => "0", "resultMsg" => "获取成功", "data" => $data_list);
                    echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                    //$this->assign('data_list', $data_list);//web端测试用
                } else {
                    /*返回Json给安卓端 --  提示信息<给用户显示> 提示码 = 1 -- 无订单商品信息 */
                    $return = array("resultCode" => "1", "resultMsg" => "无订单商品信息");
                    echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                }
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> 提示码 = 1 -- 无订单信息 */
                $return = array("resultCode" => "1", "resultMsg" => "无订单信息");
                echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
        $this->display();/*web端 测试*/
    }

    /*删除订单*/
    public function delOrder() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if (!empty($POST['user_id'])) {
            $where['order_id'] = array('eq', $POST['order_id']);
            $where['user_id'] = array('eq', $POST['user_id']);
            /*判断订单商品表是否有数据*/
            $retOgArr = $this->OrGoModel->Get_GoOr_Select($where);
            /*只删除订单详情*/
            if (!empty($retOgArr)) {
                $retOr = $this->OrModel->Get_or_delete($where);//删除订单总表
                if ($retOr) {
                    /*返回Json给安卓端 --  提示信息<给用户显示> 提示码 = 0 -- 删除成功 */
                    $return = array("resultCode" => "0", "resultMsg" => "删除成功");
                    echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                } else {
                    /*返回Json给安卓端 --  提示信息<给用户显示> 提示码 = 0 -- 删除失败 */
                    $return = array("resultCode" => "1", "resultMsg" => "删除失败");
                    echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                }
            /*先删除订单商品表在删除订单详情*/
            } else {
                /*先删除订单商品内容*/
                $retGr = $this->OrGoModel->Get_GoOr_Delete($where);//删除订单商品表
                if ($retGr) {
                    /*后删除订单详情内容*/
                    $retOr = $this->OrModel->Get_or_delete($where);//删除订单总表
                    if ($retOr) {
                        /*返回Json给安卓端 --  提示信息<给用户显示> 提示码 = 0 -- 删除订单成功 */
                        $return = array("resultCode" => "0", "resultMsg" => "删除订单成功");
                        echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                        exit;
                    } else {
                        /*返回Json给安卓端 --  提示信息<给用户显示> 提示码 = 1 -- 删除订单详情失败 */
                        $return = array("resultCode" => "1", "resultMsg" => "删除订单详情失败");
                        echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                        exit;
                    }
                } else {
                    /*返回Json给安卓端 --  提示信息<给用户显示> 提示码 = 1 -- 删除订单商品失败 */
                    $return = array("resultCode" => "1", "resultMsg" => "删除订单商品失败");
                    echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                }
            }

        }
        $this->display();/*web端 测试*/
    }


    /*头像上传接口 -- 未与安卓对接*/
    public function upload() {

        echo file_get_contents("php://input");
        exit;
        $upload = new Upload();// 实例化上传类
        $upload->maxSize = 0;//上传的文件大小限制 (0-不做限制)
        $upload->savePath = 'img/date/'; //保存路径
        $upload->rootPath = './public/'; //保存根路径
        $upload->saveName = array('date', 'YmdHis'); //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 允许上传的文件后缀
        $upload->autoSub = true; //自动子目录保存文件
        $upload->subName = array('date', 'Ymd');//子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        $info = $upload->uploadOne($_FILES['name_url']); //uploadOne  ->   $_FILES['name_url']  必须有
        $arr = $info['savepath'] . $info['savename'];
        if (!empty($arr)) {
            /*返回Json给安卓端 --  提示信息<给用户显示> 提示码 = 1 -- 上传成功  返回文件的路径*/
            $return = array("resultCode" => "0", "resultMsg" => "上传成功", "data" => $arr);
            echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
            exit;
        } else {
            /*<自动验证>返回的错误信息*/
            $error = $upload->getError();
            /*三目判断没有<自动验证>返回信息就返回'添加失败'*/
            $where = !empty($error) ? $error : "上传失败";//三目判断
            /*返回Json给安卓端 --  提示信息<给用户显示> -- 由三目判断选择*/
            $return = array("resultCode" => "1", "resultMsg" => $where);
            echo json_encode($return, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
            exit;
        }
    }
}