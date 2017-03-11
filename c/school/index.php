<?php

require_once '../config.php';
require_once '../lib/fun.php';
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

//交验唯一ID
$state = $_GET['state'];
if (!isset($_GET['state'])) {
    echo "ID错误";
    exit;
} else {
    $_SESSION['state'] = $_GET['state'];
    $_SESSION['school_id'] = $_GET['state'];
}

//交验校园
$school = get_school_info($school_id);
if (!isset($school)) {
    //如果没有学校ID则报错
    echo "校园ID不存在";
    exit;
} else {
    $_SESSION['school'] = $school;
}

$user = wx_userinfo($appid, $secret, $redirect_uri, $state);
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

$tags = get_tags($school->id,"school");
if(!isset($tags)){
    echo "获取系统tag错误";
    exit;
}else{
    $_SESSION['tags'] = $tags;
}


//引导用户进入系统
switch ($user->state) {
    case "0" :
        //普通身份
        v("./pt_main.php");
        break;
    case "1":
        // 助理身份
        v("./zl_main.php");
        break;
    case "2":
        // 校长身份
        v("./yz_main.php");
        break;
}
?>


