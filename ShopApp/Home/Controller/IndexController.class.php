<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller {


    public function __construct() {
        parent::__construct();
        $this->CateModel = D('Category');//分类
        $this->GoModel = D('Goods');//商品
        $this->GoGaModel = D('GoodsGallery');//商品图片
        $this->TTModle = D('TouchTemplate');//广告位置
        $this->PiModle = D('Picture');//轮播图
        $this->AcModle = D('AdCustom');//广告图
        $this->baModel = D('Brand');

    }

    /*展示所有接口*/

    public function index() {
        $this->display();
    }

    /*首页接口*/
    public function indexx() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if ($POST['action'] == 'index') {
            /*获取当前所有大类  顶层大类 只留3个*/
            $where['parent_id'] = array('eq', 0);
            $field = "cat_id,cat_name,parent_id";
            $parr = $this->CateModel->where($where)->field($field)->limit(3)->select();
            /*获取所有子类  并且删除所有顶层大类 */
            $arr = $this->CateModel->field($field)->select();
            $arrs = getOneW($arr, 'cat_id', 'parent_id');
            foreach ($arrs as $k => $v) {
                if ($v['parent_id'] == 0) {
                    unset($arrs[$k]);
                }
            }

            /*数组处理  当前子类等于这个大类 就存入这个数组*/
            foreach ($parr as $k => $v) {
                foreach ($arrs as $kk => $vv) {
                    if ($vv['parent_id'] == $v['cat_id']) {
                        $parr[$k]['data_list2'][] = $vv;
                    }
                }
            }
            //删除多余的子分类内容 只留4个
            foreach ($parr as $k => $v) {
                if (count($v['data_list2']) > 4) {
                    foreach ($v['data_list2'] as $kk => $vv) {
                        if ($kk > 3) {
                            unset($parr[$k]['data_list2'][$kk]);
                        }
                    }
                }
            }

            /*数组处理   获取当前所有子类  获取所有商品   */
            $this->GoModel = M('Goods');
            $field = "goods_id,goods_name,goods_thumb,shop_price,cat_id";
            $garr = $this->GoModel->field($field)->select();
            /*判断所有商品有当前循环子类ID  就吧当前商品存入当前数组*/
            foreach ($parr as $k => $v) {
                foreach ($v['data_list2'] as $kk => $vv) {
                    foreach ($garr as $kkk => $vvv) {
                        if ($vvv['cat_id'] == $vv['cat_id']) {
                            $parr[$k]['goods_list'][] = $vvv;
                        }
                    }
                }
            }
            //删除多余的商品内容 只留6个
            foreach ($parr as $k => $v) {
                if (count($v['goods_list']) > 6) {
                    foreach ($v['goods_list'] as $kk => $vv) {
                        if ($kk > 5) {
                            unset($parr[$k]['goods_list'][$kk]);
                        }
                    }
                }
            }
            /*数组的合并处理*/
            $data['data_list1'] = $parr;//3个大类  大类下的所有子类  子类里的所有商品
            $data['img_list'] = $this->PiModle->Get_Pt_Select();//轮播图
            $field = "as_places,url,goods_id";
            $data['img_data'] = $this->AcModle->Get_Ac_Select('', $field);//特色水果

            if (!empty($parr)) {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓data*/
                $bArr = array("resultCode" => "0", "resultMsg" => "获取成功", "data" => $data);
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取失败  */
                $bArr = array("resultCode" => "1", "resultMsg" => "获取失败");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
        $this->display();
    }

    /*分类列表接口*/
    public function classify() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";//测试用
        //$POST = isset($_POST) ? $_POST : "";//正式
        if ($POST['action'] == "cat_one") {
            /*获取所有 顶层分类*/
            $where['parent_id'] = array('eq', 0);//获取 顶层大分类
            $field = "cat_id,cat_name,parent_id,cat_ico";//只留需要的字段
            $Cat_Arr_One = $this->CateModel->Get_Ca_select($where, $field);
            /*获取所有分类  并且删除所有顶层分类*/
            $fields = "cat_id,cat_name,parent_id";//只留需要的字段
            $CaArr_one = $this->CateModel->Get_Ca_select('', $fields);
            $Cat_Arr_Two = getOneW($CaArr_one, 'cat_id', 'parent_id');//递归获取分类数据
            foreach ($Cat_Arr_Two as $k => $v) {
                if ($v['parent_id'] == 0) {
                    unset($Cat_Arr_Two[$k]);//删除parent_id等于0的父类分类
                }
            }


            /*获取大类下所有子类 并且放入对应的父类*/
            foreach ($Cat_Arr_One as $k => $v) {
                foreach ($Cat_Arr_Two as $kk => $vv) {
                    //判断当前 吧cat_id父类下的parent_id子类 单独提取
                    if ($vv['parent_id'] == $v['cat_id']) {
                        $Cat_Arr_One[$k]['data_list2'][] = $vv;
                    }
                }
                //把上面的每一个数组放回他当前的父分类里
            }


            if (!empty($CaArr_one)) {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data_list1>*/
                $bArr = array("resultCode" => "0", "resultMsg" => "获取成功", "data_list1" => $Cat_Arr_One);
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                $this->assign('CaArr_one', $Cat_Arr_One);
                exit;
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取失败  */
                $bArr = array("resultCode" => "1", "resultMsg" => "获取失败");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
        $this->display();/*web端 测试*/
    }

    /*分类列表搜索*/
    public function classSearch() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";//测试用
        //$POST = isset($_POST) ? $_POST : "";//正式
        if ($POST['action'] == "cat_list") {

            $order = "goods_id desc";//默认商品ID倒序 必须是条件的第一个条件
            //销量排序
            if (!empty($POST['order_sales'])) {
                switch ($POST['order_sales']) {
                    case 1:
                        $order = "sales_volume_base desc";//正序
                        break;
                    case 2:
                        $order = "sales_volume_base asc";//倒序
                        break;
                }
            }

            //价格排序
            if (!empty($POST['order_price'])) {
                switch ($POST['order_price']) {
                    case 1:
                        $order = "shop_price desc";//正序
                        break;
                    case 2:
                        $order = "shop_price asc";//倒序
                        break;
                }
            }

            /*获取 当前cat_id*/
            $where_go['cat_id'] = array('eq', $POST['cat_id']);

            /*-------------------------此区间查询用户当前的子分类----------------*/

            //品牌搜索
            if (!empty($POST['brand_id'])) {
                $where_go['brand_id'] = $POST['brand_id'];
                //$where_go['_logic'] = 'AND';//用于区间查询把两个条件 合并一起
            }

            //查最小金额
            if (!empty($POST['price_min'])) {
                $where_go['shop_price'] = array('gt', $POST['price_min']);//大于最小的
            }


            //查最大金额
            if (!empty($POST['price_max'])) {
                $where_gos['shop_price'] = array('lt', $POST['price_max']);//小于最大的
                $where_gos['_logic'] = 'AND';//用于区间查询吧两个条件 合并一起
                $where_gos['_complex'] = $where_go;
            } else {
                $where_gos = $where_go;
            }

            /*---------------当前分类下的商品---------------*/

            $fields = "goods_id,brand_id,goods_name,goods_thumb,shop_price,market_price,sales_volume_base";//只留需要的字段
            $retArr_go = $this->GoModel->Get_Go_Select($where_gos, $fields, $order);

            /*查看 当前分类下的  所以子分类*/
            $where_one['parent_id'] = array('eq', $POST['cat_id']);
            $fieldca = "cat_id,cat_name,parent_id";//只留需要的字段
            $retArr_one = $this->CateModel->Get_Ca_select($where_one, $fieldca, $order);

            //拼接 当前分类下的所以子分类ID
            $names_id = "";
            foreach ($retArr_one as $k => $v) {
                $names_id .= empty($names_id) ? $v['cat_id'] : ',' . $v['cat_id'];
            }

            $where_han['cat_id'] = array('in', $names_id);
            //品牌搜索

            /*-----------------------此区间查询  用于子分类下的小分类里-------------------*/
            if (!empty($POST['brand_id'])) {
                $where_han['brand_id'] = $POST['brand_id'];

            }

            //查最小金额
            if (!empty($POST['price_min'])) {
                $where_han['shop_price'] = array('gt', $POST['price_min']);//大于最小的
            }
            //查最大金额
            if (!empty($POST['price_max'])) {
                $where_hans['shop_price'] = array('lt', $POST['price_max']);//小于最大的
                $where_hans['_logic'] = 'AND';//用于区间查询吧两个条件 合并一起
                $where_hans['_complex'] = $where_han;
            } else {
                $where_hans = $where_han;
            }


            /*--------------当前所以子分类下的所有商品--------------*/

            $field = "goods_id,goods_name,brand_id,goods_thumb,shop_price,market_price,sales_volume_base";
            $retGoArr = $this->GoModel->Get_Go_Select($where_hans, $field, $order);

            //吧当前分类下的商品  和   当前分类下子分类商品 放在一个数组
            $ON_Arr = array();
            foreach ($retArr_go as $k => $v) {
                $ON_Arr[] = $v;
            }
            foreach ($retGoArr as $Kk => $vv) {
                $ON_Arr[] = $vv;
            }

            /*获取 所以品牌数据*/
            $cateGetArr = $this->cateGetList($POST['cat_id']);
            $cateBrand_id = "";
            foreach ($cateGetArr as $k => $v) {
                $cateBrand_id .= empty($cateBrand_id) ? $v['brand_id'] : ',' . $v['brand_id'];
            }

            $whereBrand['brand_id'] = array('in', $cateBrand_id);

            /*-------------------获取所以得品牌----------------------*/

            $BarndRetOne['brand_name'] = "品牌";
            $BarndRetOne['name'] = "brand_name";
            $fieldBr = "brand_id,brand_name";//只显示需要的字段
            $BarndRetOne['brand_list'] = $this->baModel->Get_Br_Select($whereBrand, $fieldBr);

            //删除  安卓端  不需要的数据
            foreach ($ON_Arr as $k => $v) {
                if (!empty($v['brand_id'])) {
                    unset($ON_Arr[$k]['brand_id']);
                }
            }


            if (!empty($ON_Arr)) {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data_list1>*/
                $bArr = array("resultCode" => "0", "resultMsg" => "获取成功", "data_list1" => $ON_Arr, "brand_data" => $BarndRetOne);

                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取失败  */
                $bArr = array("resultCode" => "1", "resultMsg" => "获取失败");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);/*让json支持汉字不乱码*/
                exit;
            }

        }
        $this->display();/*web端 测试*/
    }

    //分类筛选用的数组
    public function cateGetList($where_post = '') {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";//测试用
        //$POST = isset($_POST) ? $_POST : "";//正式
        if (!empty($where_post)) {
            $where_go['cat_id'] = $where_post;
            /*---------------当前分类下的商品---------------*/
            $fields = "goods_id,brand_id,goods_name,goods_thumb,shop_price,market_price,sales_volume_base";//只留需要的字段
            $retArr_go = $this->GoModel->Get_Go_Select($where_go, $fields);

            /*查看 当前分类下的  所以子分类*/
            $where_one['parent_id'] = array('eq', $POST['cat_id']);
            $fieldca = "cat_id,cat_name,parent_id";//只留需要的字段
            $retArr_one = $this->CateModel->Get_Ca_select($where_one, $fieldca);

            //拼接 当前分类下的所以子分类ID
            $names_id = "";
            foreach ($retArr_one as $k => $v) {
                $names_id .= empty($names_id) ? $v['cat_id'] : ',' . $v['cat_id'];
            }

            $where_han['cat_id'] = array('in', $names_id);

            /*--------------当前所以子分类下的所有商品--------------*/
            $field = "goods_id,goods_name,brand_id,goods_thumb,shop_price,market_price,sales_volume_base";
            $retGoArr = $this->GoModel->Get_Go_Select($where_han, $field);

            //吧当前分类下的商品  和   当前分类下子分类商品 放在一个数组
            $ON_Arr = array();
            foreach ($retArr_go as $k => $v) {
                $ON_Arr[] = $v;
            }
            foreach ($retGoArr as $Kk => $vv) {
                $ON_Arr[] = $vv;
            }
        }
        return $ON_Arr;
    }


    /*普通搜索列表*/
    public function search() {
        //$POST = !empty($_REQUEST) ? $_REQUEST : "";//测试用
        $POST = isset($_POST) ? $_POST : "";//正式
        if ($POST['action'] == "resul") {
            if (!empty($POST['search'])) {
                $order = "goods_id desc";//默认商品ID倒序 必须是条件的第一个条件
                //销量排序
                if (!empty($POST['order_sales'])) {
                    switch ($POST['order_sales']) {
                        case 1:
                            $order = "sales_volume_base desc";//正序
                            break;
                        case 2:
                            $order = "sales_volume_base asc";//倒序
                            break;
                    }
                }
                //价格排序
                if (!empty($POST['order_price'])) {
                    switch ($POST['order_price']) {
                        case 1:
                            $order = "shop_price desc";//正序
                            break;
                        case 2:
                            $order = "shop_price asc";//倒序
                            break;
                    }
                }
                /*按照商品名称查询数据*/
                $where['goods_name'] = array('like', "%" . $POST['search'] . "%");
                /*只留需要的字段*//*查询结果集需要的字段*/
                $field = "goods_id,goods_name,goods_thumb,market_price,shop_price,sales_volume_base";
                $GoArr = $this->GoModel->Get_Go_Select($where, $field, $order);
                if (!empty($GoArr)) {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data> */
                    $bArr = array("resultCode" => "0", "resultMsg" => "获取成功", "data" => $GoArr);
                    echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                } else {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取失败  */
                    $bArr = array("resultCode" => "1", "resultMsg" => "获取失败");
                    echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                }
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 没有填写搜索数据  */
                $bArr = array("resultCode" => "1", "resultMsg" => "没有填写搜索数据");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
        $this->display();/*web端 测试*/
    }

    /*商品详情*/
    public function dateCtion() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";//测试用
        //$POST = isset($_POST) ? $_POST : "";//正式
        if ($POST['action'] == "detail") {
            /*按照商品ID查询*/
            $where['goods_id'] = array('eq', $POST['goods_id']);
            /*只留需要的字段*/
            $field = "goods_desc,goods_id,goods_name,cat_id,goods_number,goods_thumb,market_price,shop_price,sales_volume_base";
            $retGo = $this->GoModel->Get_Go_Find($where, $field);
            /*将详情图片组用,号炸开 并且存入大数组*/
            $retGo['img_list'] = explode(',', $retGo['goods_desc']);
            unset($retGo['goods_desc']);//删除需要的字段
            /*只留需要的字段  按照商品ID获取商品图片*/
            $fields = "thumb_url";
            $GoGaArr = $this->GoGaModel->Get_GoGa_Select($where, $fields);
            foreach ($GoGaArr as $v) {
                /*循环存入大数组*/
                $retGo['data_url'][] .= $v['thumb_url'];
            }
            /* 获得当前商品内容的描述HTML  安卓有BUG 暂时取消*/
            //$retGo['data_url'] = "Index/goodslist/id/" . "{$POST['goods_id']}" . ".html";
            if (!empty($retGo)) {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data>*/
                $bArr = array("resultCode" => "0", "resultMsg" => "获取成功", "data" => $retGo);
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取失败  */
                $bArr = array("resultCode" => "1", "resultMsg" => "获取失败");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
    }


    /*查询子分类接口*/
    public function catelist() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if (!empty($POST['cat_id'])) {
            //用当前 分类主ID 获取下一级分类所以数据
            $whereone['parent_id'] = array('eq', $POST['cat_id']);
            $field = "cat_id,cat_name";
            $cateArrone = $this->CateModel->Get_Ca_select($whereone, $field);
            // 将所以分类数据的 主ID 拼接成字符串
            $Cate_Id_All = '';
            foreach ($cateArrone as $k => $v) {
                $Cate_Id_All .= empty($Cate_Id_All) ? $v['cat_id'] : ',' . $v['cat_id'];
            }

            /*查看 是否 是最后一级分类*/

            $wheretwo['parent_id'] = array('in', $Cate_Id_All);
            $cateArrtow = $this->CateModel->Get_Ca_select($wheretwo, $field);
            /*判断  最后一级是否可查*/
            if (!empty($cateArrtow)) {
                $type = 0;
            } else {
                $type = 1;
            }

            if ($cateArrone) {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data>*/
                $bArr = array("resultCode" => "0", "resultMsg" => "获取成功", "cat_list" => $cateArrone, "type" => $type);
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取失败 */
                $bArr = array("resultCode" => "1", "resultMsg" => "获取失败");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }
    }

    /*商品详情介绍页  --  特殊情况 停止使用*/
    public function goodslist() {
        $GET = isset($_GET) ? $_GET : "";
        $where['goods_id'] = $GET['id'];
        $retGo = $this->GoModel->Get_Go_Find($where, 'goods_desc');
        $this->assign('retGo', $retGo);
        $this->display();/*web端 测试*/

    }


    /*热更新/配送/支付*/
    public function action() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        $CoModel = M('Config');//确认热更新表
        $ToPaModel = M('TouchPayment');//支付表
        $ShPiModel = M('Shipping');//配送表
        switch ($POST['action']) {
            case "vers": //是否需要热更新
                /*查询总表而更新  只显示安卓需要字段*/
                $CoArr = $CoModel->field('version')->find();
                if (!empty($CoArr)) {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data>*/
                    $bArr = array("resultCode" => "0", "resultMsg" => "获取成功", "data" => $CoArr);
                    echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                } else {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取失败  */
                    $bArr = array("resultCode" => "1", "resultMsg" => "获取失败");
                    echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                }
                break;
            case "pay"://支付方式
                /*查询支付方式 并且 只留需要的字段*/
                $ToPaArr = $ToPaModel->field("pay_name,pay_id,pay_code")->select();
                if (!empty($ToPaArr)) {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data>*/
                    $bArr = array("resultCode" => "0", "resultMsg" => "获取成功", "data" => $ToPaArr);
                    echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                } else {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取失败  */
                    $bArr = array("resultCode" => "1", "resultMsg" => "获取失败");
                    echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                }
                break;
            case "ship"://配送方式
                /*查询配送方式 并且 只留需要的字段*/
                $ShPiArr = $ShPiModel->field("shipping_id,shipping_name,shipping_code")->select();
                if (!empty($ShPiArr)) {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data>*/
                    $bArr = array("resultCode" => "0", "resultMsg" => "获取成功", "data" => $ShPiArr);
                    echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                } else {
                    /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取失败  */
                    $bArr = array("resultCode" => "1", "resultMsg" => "获取失败");
                    echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                    exit;
                }
                break;
        }
    }


    /*安卓获取城市数据表 热更新*/
    public function region() {
        $POST = !empty($_REQUEST) ? $_REQUEST : "";/*web端 测试*/
        //$POST = isset($_POST) ? $_POST : "";//接收安卓端发来的数据
        if ($POST['action'] == 'reg') {
            $RegModel = M('Region');//城市地区县
            /*获取城市信息  并且 只留需要的字段*/
            $retArr = $RegModel->field('region_id,parent_id,region_name,region_type')->select();
            if ($retArr) {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取成功  返回数据给安卓<data>*/
                $bArr = array("resultCode" => "0", "resultMsg" => "获取成功", "data" => $retArr);
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            } else {
                /*返回Json给安卓端 --  提示信息<给用户显示> -- 获取失败 */
                $bArr = array("resultCode" => "1", "resultMsg" => "获取失败");
                echo json_encode($bArr, JSON_UNESCAPED_UNICODE);/*让json支持汉字不乱码*/
                exit;
            }
        }

    }

}