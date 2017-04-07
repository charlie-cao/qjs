<?php
/*
慢成长入口 根据校园id 分库
*/

require_once '../config.php';
require_once '../lib/fun.php';

$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

//交验唯一ID
if (!isset($_GET['state'])) {
    echo "访问异常，请反馈给管理员";
    exit;
} else {
    //在会话中保存state
    $_SESSION['state'] = $_GET['state'];
//    var_dump($_REQUEST);
//    exit;
    //检查用户并更新用户SESSION信息
    if (!isset($_SESSION['user']->openid)) {
        //未登录 微信登录
        $_SESSION['user'] = wx_userinfo($appid, $secret, $redirect_uri, $_SESSION['state']);
    }
    $_SESSION['user'] = check_user($_SESSION['user']);


    //分解state获取校园ID和班级ID
    $s = explode("-", $_SESSION['state']);
    $school_id = $s[0];
    $cls_id = $s[1];


    //获取校园信息
    $school = get_school_info($school_id);
    if (!($school)) {
        //如果没有学校ID则报错
        echo "校园不存在";
        exit;
    } else {
        $_SESSION['school_id'] = $school_id;
        $_SESSION['school'] = $school;
    }


    //如果有定义班级ID则直接进入班级，否则从用户的信息的最后登录ID中获得班级ID。如果为用户没有最终访问的班级，则显示校区的班级列表供用户选择。
    //如果用户跨越校区怎么办(last_cls_id为其他校园的ID)？ 在本校区中搜索班级ID，如果有，则进入，如果没有则进入本校区班级列表。
    if ($cls_id!="") {
        $_SESSION['cls_id'] = $cls_id;
    } else {

        /**
         *  测试用
         **/
//        if(!$_SESSION['cls_id']){
//            $_SESSION['cls_id'] = 0;
//        }
        /**
         * end;
         */
//        $_SESSION['user'] = check_user($_SESSION['user']);
//        var_dump($_SESSION['user']);
//        exit;
        if ($_SESSION['user']->last_cls_id!="") {
            //判断用户最后访问的班级是否在当前校园
            $sql = "select * from sc_user_cls where school_id=".$_SESSION['school_id']." and cls_id=".$_SESSION['user']->last_cls_id;
            $q = $db->query($sql);
            $r = $q->fetch();
            if($r){
                $_SESSION['cls_id'] = $_SESSION['user']->last_cls_id;
            }else{
//                var_dump($_SESSION['user']);
                v("./index_cls_list.php?school_id=" . $_SESSION['school_id']);
                exit;
            }
        } elseif(isset($_SESSION['user']->openid)) {
            //这个跳转很关键
            //只有用户已经注册过了才会进行跳转，否则需要用户认证。
            v("./index_cls_list.php?school_id=" . $_SESSION['school_id']);
            exit;
        }
    }

    //获取班级
    $cls = get_cls_info($_SESSION['cls_id']);
    if (!isset($cls)) {
        //如果没有学校ID则报错
        echo "班级ID不存在";
        exit;
    } else {
        $_SESSION['cls'] = $cls;
    }

    //注册 tag
    $tags = get_cls_tags($school->id, $cls->id);
    if (!isset($tags)) {
        echo "获取系统tag错误";
        exit;
    } else {
        $_SESSION['cls_tags'] = $tags;
    }

//如果用户是第一次登录 将用户加入学校
    $sql = "insert into sc_user_school set school_id=" . $_SESSION['school_id'] . ",user_id=" . $_SESSION['user']->id . ",c_time=" . time();
    $db->exec($sql);

////如果用户是第一次登录班级 将用户加入班级
//    $sql = "insert into sc_user_cls set school_id=" . $_SESSION['school_id'] . ",cls_id=" . $_SESSION['cls_id'] . ",user_id=" . $_SESSION['user']->id . ",c_time=" . time();
//    $db->exec($sql);


    $sql = "select * from sc_user_cls where user_id=" . $_SESSION['user']->id . " and school_id=" . $_SESSION['school_id'] . " and cls_id=" . $_SESSION['cls']->id;
    $res = $db->query($sql);
    $cls_user = $res->fetch();

    if ($cls_user['is_teacher'] == 1) {
        //当前班级班主任
        $_SESSION['user']->is_now_cls_teacher = 1;
        v("./teacher_main.php?cls_id=" . $_SESSION['cls_id']);
    } else {
        //普通班主任
        $_SESSION['user']->is_now_cls_teacher = 0;
        v("./pt_main.php?cls_id=" . $_SESSION['cls_id']);
    }

}


?>