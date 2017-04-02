<?php
/**
 * 千家师入口
 * 必须参数 state = 校园ID
 */
require_once '../config.php';
require_once '../lib/fun.php';

$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

//交验唯一ID
$state = $_GET['state'];
if (!isset($_GET['state'])) {
    echo "ID错误";
    exit;
} else {
    $_SESSION['state'] = $school_id = $_GET['state'];
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

//检查用户是否已经登录
if (!isset($_SESSION['user']->openid)) {
    //未登录 微信登录
    $_SESSION['user'] = wx_userinfo($appid, $secret, $redirect_uri, $state);
}

//检查用户并更新用户SESSION信息
$_SESSION['user'] = check_user($_SESSION['user']);

//判断是否校园专家 根据是否为专家身份进行用户引入
$sql = "select * from sc_user_school where user_id=".$_SESSION['user']->id." and school_id=".$_SESSION['school']->id;
$q = $db->query($sql);
$r = $q->fetch();

if($r['is_leader']==1 || $r['is_teacher']==1||$r['is_assistant']==1){
    v("./zj_main.php");
}else{
    v("./pt_main.php");
}

exit;

//引导用户进入系统
switch ($_SESSION['user']->is_expert) {
    case "0" :
        //  普通身份
        v("./pt_main.php");
        break;
    case "1" :
        //  专家身份
        v("./zj_main.php");
        break;
}
?>