<?php
//字符串截取
function str_cut($sourcestr,$cutlength,$suffix='...')
{
    $str_length = strlen($sourcestr);
    if($str_length <= $cutlength) {
        return $sourcestr;
    }
    $returnstr='';  
    $n = $i = $noc = 0;
    while($n < $str_length) {
            $t = ord($sourcestr[$n]);
            if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $i = 1; $n++; $noc++;
            } elseif(194 <= $t && $t <= 223) {
                $i = 2; $n += 2; $noc += 2;
            } elseif(224 <= $t && $t <= 239) {
                $i = 3; $n += 3; $noc += 2;
            } elseif(240 <= $t && $t <= 247) {
                $i = 4; $n += 4; $noc += 2;
            } elseif(248 <= $t && $t <= 251) {
                $i = 5; $n += 5; $noc += 2;
            } elseif($t == 252 || $t == 253) {
                $i = 6; $n += 6; $noc += 2;
            } else {
                $n++;
            }
            if($noc >= $cutlength) {
                break;
            }
    }
    if($noc > $cutlength) {
            $n -= $i;
    }
    $returnstr = substr($sourcestr, 0, $n);
 

    if ( substr($sourcestr, $n, 6)){
          $returnstr = $returnstr . $suffix;//超过长度时在尾处加上省略号
      }
    return $returnstr;
}


/**
 * 递归
 * @param 数组源 $data
 * @param 分类祖id $zid
 * @param 主键id，字段名称 $uid
 * @param 分类id，字段名称 $pid
 * @return multitype:递归数组
 */
function tree($data,$uid,$pid,$zid=0) {
    $list = array();//定义一个空数组
    foreach($data as $v) {//循环遍历开始
        if($v[$pid] == $zid) {//递归条件，判断递归是否结束
            $v['son'] = tree($data,$uid,$pid,$v[$uid]);//循环递归
            if(empty($v['son'])) {
                unset($v['son']);//完成后去掉空值
            }
            array_push($list, $v);//递归结束，返回数组(此句代码等同于$list[] = $v;)
        }
    }
    return $list;
}

/**
 * 迭代（父级与子级之间层次清晰版）
 * @param 数组源                 $arr
 * @param 主键id      $uid
 * @param 字段主要name $uname
 * @param 父id       $pid
 * @param 祖id       $zid
 * @param 层级标识              $lev
 * @return multitype:string
 */
function getOneY($arr,$uid,$uname,$pid,$zid=0,$lev=0){
    //迭代
    $task=array($zid);//定义一个任务数组，并且让其不为空（放$zid=0）
    $tree=array();//结果数组
    while (!empty($task)){
        $flg=false;//自定义一个开关，关
        foreach ($arr as $k=>$v){//开始循环遍历
            if ($v[$pid]==$zid) {//判断遍历是否可以继续进行
                $v[$uname] = str_repeat('&nbsp;', $lev).'|--'.$v[$uname];
                $tree[] = $v;
                array_push($task, $v[$uid]);//将子类id放入任务数组作为父id
                $zid=$v[$uid];
                $lev=$lev+3;
                unset($arr[$k]);//取到最底层了，将此家族干掉
                $flg=true;//自定义一个开关，开
            }
        }
        if ($flg==false) {
            array_pop($task);//去掉最后一个值
            $zid=end($task);//取出最后一个值
            $lev=$lev-3;//层级减掉2
        }
    }
    return $tree;
}

/**
 * 迭代（父级与子级之间无错层版）
 * @param 数组源    $arr
 * @param 主键id $uid
 * @param 父id  $pid
 * @param 祖id  $zid
 * @return array
 */
function getOneW($arr,$uid,$pid,$zid=0){
    //迭代
    $task=array($zid);//定义一个任务数组，并且让其不为空（放$zid=0）
    $tree=array();//结果数组
    while (!empty($task)){
        $flg=false;//自定义一个开关，关
        foreach ($arr as $k=>$v){//开始循环遍历
            if ($v[$pid]==$zid) {//判断遍历是否可以继续进行
                $tree[] = $v;
                array_push($task, $v[$uid]);//将子类id放入任务数组作为父id
                $zid=$v[$uid];
                unset($arr[$k]);//取到最底层了，将此家族干掉
                $flg=true;//自定义一个开关，开
            }
        }
        if ($flg==false) {
            array_pop($task);//去掉最后一个值
            $zid=end($task);//取出最后一个值
        }
    }
    return $tree;
}