<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 2016/11/11 0011 上午 10:52
// +----------------------------------------------------------------------
// | Use : 说明用途，主要方面！
// +----------------------------------------------------------------------
// | Author: 小豆子 <253121301@qq.com>
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model;
use Think\Page;

class GoodsModel extends Model{

    /**
     *  数组查询
     * @param 查询条件 $where_Go
     * @param 只留需要的字段 $field
     * @param page 分批传输数据  $field
     * @return 结果集
     */
    public function Get_Go_Select($where_Go="",$field="",$order='',$group='') {
        $counArr = $this->where($where_Go)->count();
        $Page =  new Page($counArr,$_POST['ln']);
        $GoSele = $this->field($field)->order($order)->group($group)->page($_POST['p'],$_POST['ln'])->where($where_Go)->select();
        return $GoSele;
    }

    /*无分页的数据调取*/
    public function Get_Go_Selects($where_Go="",$field="") {

        $GoSele = $this->field($field)->where($where_Go)->select();
        return $GoSele;
    }

    /**
     *  单条查询
     * @param 查询条件 $where_Go
     * @param 只留需要的字段 $field
     * @return 结果集
     */
    public function Get_Go_Find($where_Go,$field) {
        $GoFind = $this->field($field)->where($where_Go)->find();
        return $GoFind;
    }


}