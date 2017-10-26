<?php
namespace Home\Controller;
use Think\Controller;

class NewsListController extends Controller {
	
	//分页练习
	public function showpage() {
		
		
		$iniArr = I('post.'); // 接收表单提交的参数
		//***** 传入POST参数要注意这里
		$sname  = I('post.sname','')==''?I('get.sname',''):I('post.sname','');
		$whereArr = array();
		if ($sname != '') {
			$whereArr['sname'] = array('like',"%{$sname}%");
		}
		echo "<pre>";
		print_r($iniArr);
		echo "</pre>";
		
		$pStr = '';
		if (!empty($iniArr)) {
			foreach($iniArr as $key=>$val) { 
				$pStr .=   "$key=".urlencode($val).'&';
			}
		}
		
	   $seccol =	M('seccol');
	   
       $count      = $seccol->where($whereArr)->count();// 查询满足要求的总记录数	   
	   
       //***** 传入POST参数要注意这里
       //$Page       = new \Think\Page($count,2,array('sname'=>$sname));// 实例化分页类 传入总记录数和每页显示的记录数(25)
       
       //或用下面的方法也可以
       
       $Page       = new \Think\Page($count,2);// 实例化分页类 传入总记录数和每页显示的记录数(25)
       $Page -> parameter['sname']=$sname;
	   //$Page -> parameter['type']=$type;
 
       //***** 分页样式定制
       $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
       $Page->setConfig('prev', '上一页');
       $Page->setConfig('next', '下一页');
       $Page->setConfig('first', '首页');
       $Page->setConfig('end', '末页');
       //***** 这里定义分页的各个单元的显示位置
       $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
       
       $show       = $Page->show();// 分页显示输出
       
       $list = $seccol->where($whereArr)->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
       
       $this->assign('list',$list);// 赋值数据集
       
       $this->assign('page',$show);// 赋值分页输出
       
       $this->display(); // 输出模板
       
	}
	
	
	

}
?>