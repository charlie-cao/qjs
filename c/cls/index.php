<?php
/*
慢成长入口 根据校园id 分库
*/

require_once '../config.php';
require_once '../lib/fun.php';
$redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];



if(isset($_GET['test'])){
    $_SESSION['test'] = $_GET['test'];
}



//交验唯一ID
$state = $_GET['state'];
if(!isset($_GET['state'])){
    echo "ID错误";
    exit;
}else{
    $s = explode("-",$_GET['state']);

    $school_id = $s[0];
    $cls_id = $s[1];

    $_SESSION['state'] =  $_GET['state'];
    $_SESSION['school_id'] =  $school_id;
    $_SESSION['cls_id'] =  $cls_id;
}

//echo $cls_id;
//var_dump($_SESSION);
//exit;
//交验校园
$school = get_school_info($_SESSION['school_id']);
if(!isset($school)){
    //如果没有学校ID则报错
    echo "校园ID不存在";
    exit;
}else{
    $_SESSION['school'] = $school;
}

//交验班级
$cls = get_cls_info($cls_id);
if(!isset($cls)){
    //如果没有学校ID则报错
    echo "班级ID不存在";
    exit;
}else{
    $_SESSION['cls'] = $cls;
}

//交验用户

$user = wx_userinfo($appid, $secret, $redirect_uri, $_SESSION['state']);
//检查该用户是否已经入库，如果没有-》入库，如果已经入库-》更新最后登录时间，获取用户身份
$user = check_user($user);
if (!isset($user)) {
    //身份验证错误，禁止登录
        echo "该用户不存在";
        exit;
} else {
    //用户更新信息后需要再次访问入口文件 更新用户个人信息
    $_SESSION['user'] = $user;
}


//注册 tag

$tags = get_tags($school->id,"cls");
if(!isset($tags)){
    echo "获取系统tag错误";
    exit;
}else{
    $_SESSION['tags'] = $tags;
}


//var_dump($_REQUEST['state']);
//exit;
//测试使用
if($_SESSION['test']){
    if($_SESSION['test'] == "pt"){
        $sql = "update sc_user set is_teacher = 0 where id = ".$user->id;
        $db->exec($sql);
        $_SESSION['user']->is_teacher = 0;
    }
    if($_SESSION['test'] == "yz"){
        $sql = "update sc_user set is_teacher = 1 where id = ".$user->id;
        $db->exec($sql);
        $_SESSION['user']->is_teacher = 1;
    }
    if($_SESSION['test'] == "bzr"){
        $sql = "update sc_user set is_teacher = 1 where id = ".$user->id;
        $db->exec($sql);
        $_SESSION['user']->is_teacher = 1;
    }
}
var_dump($_SESSION);

//var_dump($_SESSION);
//exit;
    //引导用户进入系统
$sql = "select * from sc_user_cls where user_id=".$_SESSION['user']->id." and cls_id=".$_SESSION['cls']->id ;
$res = $db->query($sql);
$cls_user =  $res->fetch();

if($cls_user['is_teacher']==1){
    //当前班级班主任
    $_SESSION['user']->is_now_cls_teacher = 1;
}else{
    //普通班主任
    $_SESSION['user']->is_now_cls_teacher = 0;
}

switch ($_SESSION['user']->is_now_cls_teacher) {
    case "0" :
    //  普通身份
        v("./pt_main.php");
    break;
    case "1":
    //  班主任身份
        v("./teacher_main.php");
    break;
    }
?>