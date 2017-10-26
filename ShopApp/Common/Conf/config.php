<?php
return array(
    //'配置项'=>'配置值'
    // 开启页面调试信息
    //'SHOW_PAGE_TRACE' => true,

    /* 数据库设置 */
    'DB_TYPE'        => 'mysql',         // 数据库类型
    'DB_HOST'        => '127.0.0.1',     // 服务器地址
    'DB_NAME'        => 'apple',       // 数据库名
    'DB_USER'        => 'root',          // 用户名
    'DB_PWD'         => '1q2w3e4r~',              // 密码
    'DB_PORT'        => '3306',          // 端口
    'DB_PREFIX'      => 'ecs_',          // 数据库表前缀    '
    'DB_CHARSET'     => 'utf8',          // 数据库编码默认采用utf8

    //设置模板定界符
    'TMPL_L_DELIM'   => '<{',            // 模板引擎普通标签开始标记
    'TMPL_R_DELIM'   => '}>',            // 模板引擎普通标签结束标记

    //订单
    'order_status'   => array(
        0 => "未确认",
        1 => "确认",
        2 => "已取消",
        3 => "无效",
        4 => "退货",
    ),

    //物流
    'shippng_status' => array(
        0 => "未发货",
        1 => "已发货",
        2 => "已收货",
        4 => "退货",
    ),

    //支付
    'pay_status'     => array(
        0 => "未付款",
        1 => "付款中",
        2 => "已付款",
    ),
);