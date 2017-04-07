<?php

require_once '../config.php';
require_once '../lib/fun.php';

$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

//交验唯一ID
if (!isset($_GET['state'])) {
    echo "ID错误";
    exit;
} else {
    $_SESSION['state'] = $_GET['state'];
    $_SESSION['school_id'] = $_GET['state'];
}

//交验校园
$school = get_school_info($_SESSION['school_id']);
if (!isset($school)) {
    //如果没有学校ID则报错
    echo "校园ID不存在";
    exit;
} else {
    $_SESSION['school'] = $school;
}

$state = $_SESSION['state'];
//检查用户是否已经登录
if (!isset($_SESSION['user']->openid)) {
    //未登录 微信登录
    $_SESSION['user'] = wx_userinfo($appid, $secret, $redirect_uri, $state);
}

//检查用户并更新用户SESSION信息
$_SESSION['user'] = check_user($_SESSION['user']);



//注册 tag

$tags = get_school_tags($school->id,"school");
if(!isset($tags)){
    echo "获取系统tag错误";
    exit;
}else{
    $_SESSION['school_tags'] = $tags;
}
//var_dump($_SESSION['school_tags']);
//exit;

//如果用户是第一次登录 将用户加入学校
$sql = "insert into sc_user_school set school_id=".$_SESSION['school_id'].",user_id=".$_SESSION['user']->id.",c_time=".time();
$db->exec($sql);
//exit;
//引导用户进入系统
$sql = "select * from sc_user_school where school_id=".$_SESSION['school_id']." and user_id=".$_SESSION['user']->id;
$q = $db->query($sql);
$r = $q->fetch();
//var_dump($r['is_leader']);
//exit;
if($r){
    if($r['is_leader']==1){
        v("./yz_main.php");
        exit;
    }
    if($r['is_assistant']==1){
        v("./zl_main.php");
        exit;
    }
    v("./pt_main.php");
}

?>


