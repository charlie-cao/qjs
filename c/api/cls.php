<?php

require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";

if (!isset($_REQUEST['a'])) {
    echo "参数错误";
} else {
    $json["msg"] = "fail";
    $json["start_time"] = getMillisecond();
    $json["end_time"] = "";
    $json["data"] = null;

    call_user_func($_REQUEST['a']);

    $json["end_time"] = getMillisecond();
    echo json_encode($json);
}

/**
 * 班主任删除用户
 */
function del_user(){
    global $db;
    global $json;
    $sql = "delete from sc_user_cls where cls_id=".$_REQUEST['cls_id']." and user_id=".$_REQUEST['user_id'].";";

    if ($db->exec($sql)) {
        $json['msg'] = "success";
    }else{

    }
}

function exit_cls(){
    global $db;
    global $json;
    $sql = "delete from sc_user_cls where cls_id=".$_REQUEST['cls_id']." and user_id=".$_REQUEST['user_id'].";";

    if ($db->exec($sql)) {
        $json['msg'] = "success";
    }else{

    }
}

function del_comment() {
    global $db;
    global $json;
    $sql = "delete from sc_cls_msg_comment where id=".$_REQUEST['comment_id'].";";

    if ($db->exec($sql)) {
        $json['msg'] = "success";

        //获取回复信息的列表
        $sql = "SELECT *,cmr.id as comment_id FROM `sc_cls_msg_comment` as cmr left join sc_user as u on cmr.user_id=u.id WHERE cmr.cls_msg_id = " . $_REQUEST['msg_id'] ." order by c_time desc";
        $res = $db->query($sql);
        $json['data'] = $res->fetchAll();
    } else {
        $json['msg'] = "success";
        $json['info'] = $db->errorInfo();
    }
}

function comment_cls_msg() {
    global $db;
    global $json;
    $sql = "INSERT INTO `sc_cls_msg_comment` ("
            . "`id`, "
            . "`cls_msg_id`, "
            . "`user_id`, "
            . "`content`, "
            . "`pater_id`, "
            . "`school_id`, "
            . "`c_time`, "
            . "`cls_id`) VALUES ("
            . "NULL, "
            . "'" . $_REQUEST['id'] . "', "
            . "'" . $_REQUEST['user_id'] . "', "
            . "'" . $_REQUEST['content'] . "', "
            . "'0', "
            . "'" . $_REQUEST['school_id'] . "', "
            . "'" . time() . "', "
            . "'" . $_REQUEST['cls_id'] . "');";
//    echo $sql;
    if ($db->exec($sql)) {
        $json['msg'] = "success";

        //获取回复信息的列表
        $sql = "SELECT *,cmr.id as comment_id FROM `sc_cls_msg_comment` as cmr left join sc_user as u on cmr.user_id=u.id WHERE cmr.cls_msg_id = " . $_REQUEST['id'] ." order by c_time desc";
        $res = $db->query($sql);

        $json['data'] = $res->fetchAll();
    } else {
        $json['msg'] = "success";
        $json['info'] = $db->errorInfo();
    }
}

function check_inv_code() {
    global $db;
    global $json;
    $sql = "select * from sc_cls WHERE `id` = '" . $_REQUEST['cls_id'] . "' and `cls_key`='" . $_REQUEST['code'] . "';";
    $res = $db->query($sql);
    $r = $res->fetchAll();


    if (count($r) == 1) {
        //如果用户是第一次登录 将用户加入学校
        $sql = "insert into sc_user_school set school_id=".$_REQUEST['school_id'].",user_id=".$_REQUEST['user_id'].",c_time=".time();
        $db->exec($sql);

        join_cls($_REQUEST['school_id'], $_REQUEST['cls_id'], $_REQUEST['user_id']);
        $json['msg'] = "success";
        $json['id'] = $_REQUEST['cls_id'];
    } else {
        $json['msg'] = "fail";
        $json['id'] = $_REQUEST['cls_id'];
    }
}

function add_cls() {
    global $db;
    global $json;
    $sql = "INSERT INTO `sc_cls` ("
            . "`id`, "
            . "`name`, "
            . "`cls_key`, "
            . "`teacher_id`, "
            . "`school_id`, "
            . "`c_time`, "
            . "`u_time`, "
            . "`is_del`) VALUES ("
            . "NULL, "
            . "'" . $_REQUEST['name'] . "', "
            . "'" . $_REQUEST['cls_key'] . "', "
            . "'" . $_REQUEST['user_id'] . "', "
            . "'" . $_REQUEST['school_id'] . "', "
            . "'" . time() . "', "
            . "'0', "
            . "'0');";
    if ($db->exec($sql)) {
        $json['msg'] = "success";
        $json['id'] = $db->lastInsertId();

        //班主任默认加入班级
        join_cls($_REQUEST['school_id'], $json['id'], $_REQUEST['user_id'], 1);

        $tag_name = array("宝宝秀","通知","活动","心得","作业");
        foreach ($tag_name as $tag){
            init_cls_tag($_REQUEST['school_id'],$json['id'],$tag);
        }

    } else {
        $json['msg'] = "success";
        $json['info'] = $db->errorInfo();
    }
}

function init_cls_tag($school_id,$cls_id,$tag_name){
    global $db;
    global $json;
    $sql = "select * from sc_cls_tag where cls_id=" . $cls_id;
    $q = $db->query($sql);
    $rs = $q->fetchAll();

    if (count($rs) > 0) {
        $o = count($rs);
    } else {
        $o = 0;
    }

    $sql = "INSERT INTO `sc_cls_tag` ("
        . "`id`, "
        . "`name`, "
        . "`school_id`, "
        . "`cls_id`, "
        . "`o`, "
        . "`c_time`, "
        . "`is_del`) VALUES ("
        . "NULL, "
        . "'" . $tag_name . "', "
        . "'" . $school_id . "', "
        . "'" . $cls_id . "', "
        . "'" . $o . "', "
        . "'" . time() . "', "
        . "'0');";

    $db->exec($sql);

}

function join_cls($school_id, $cls_id, $user_id, $is_teacher = 0) {
    global $db;
    $sql = "INSERT INTO `sc_user_cls` ("
            . "`id`, "
            . "`user_id`, "
            . "`cls_id`, "
            . "`is_teacher`, "
            . "`school_id`, "
            . "`c_time`, "
            . "`is_del`) VALUES ("
            . "NULL, "
            . "'" . $user_id . "', "
            . "'" . $cls_id . "', "
            . "'" . $is_teacher . "', "
            . "'" . $school_id . "', "
            . "'" . time() . "', "
            . "'0');";
    if ($db->exec($sql)) {
        return true;
    } else {
        return false;
    }
}

function update_user_info() {
    global $db;
    global $json;
    $sql = "UPDATE `sc_user` SET `username` = '" . $_REQUEST['username'] . "', `phone` = '" . $_REQUEST['phone'] . "' WHERE `id` = " . $_REQUEST['id'] . ";";
    if ($db->exec($sql)) {
        $json['msg'] = "success";
        $json['id'] = $_REQUEST['id'];
    } else {
        $json['msg'] = "success";
        $json['info'] = $db->errorInfo();
    }
}

function change_teacher() {
    global $db;
    global $json;

    $sql = "UPDATE `sc_user_cls` SET `is_teacher` = '0' WHERE `user_id` = " . $_SESSION['user']->id . " and `cls_id`=" . $_REQUEST['cls_id'] . ";";
    $db->exec($sql);

    $sql = "UPDATE `sc_user_cls` SET `is_teacher` = '1' WHERE `user_id` = " . $_REQUEST['user_id'] . " and `cls_id`=" . $_REQUEST['cls_id'] . ";";

    if ($db->exec($sql)) {
        $json['msg'] = "success";
        $json['id'] = $_REQUEST['user_id'];
    } else {
        $json['msg'] = "success";
        $json['info'] = $db->errorInfo();
    }
}

function update_cls_info() {
    global $db;
    global $json;
    $sql = "UPDATE `sc_cls` SET `name` = '" . $_REQUEST['name'] . "', `cls_key` = '" . $_REQUEST['cls_key'] . "' WHERE `id` = " . $_REQUEST['id'] . ";";
    if ($db->exec($sql)) {
        $json['msg'] = "success";
        $json['id'] = $_REQUEST['id'];
    } else {
        $json['msg'] = "success";
        $json['info'] = $db->errorInfo();
    }
}

function get_cls_msg_list() {
    global $db;
    global $json;
    $page = $_REQUEST['page'];
    $size = $_REQUEST['size'];
    $start_num = ($page - 1) * $size;

    if ($_REQUEST['tag'] != 0) {
        $tag = "and tag=" . $_REQUEST['tag'];
    } else {
        $tag = "";
    }

    $sql = "select * from sc_cls_msg where is_del=0  and cls_id=" . $_REQUEST['cls_id'] . " " . $tag . " "
            . "order by c_time desc "
            . "limit " . $start_num . "," . $size . ";";

    $q_res = $db->query($sql);
    $q_res = $q_res->fetchAll();



    $tag_name = array();
    foreach ($_SESSION['cls_tags'] as $key=>$val){
        $tag_name[$val['id']] = $val['name'];
    }

    $u_id = array();
    $q_id = array();
    foreach ($q_res as $key => $q) {

        $q_res[$key]['tag_name'] = $tag_name[$q['tag']];

        $sql_u = "select * from sc_user where id=" . $q['user_id'] . "   ";
        $u_res = $db->query($sql_u);
        $q_res[$key]['user'] = $u_res->fetchAll();
        $q_res[$key]['imgs'] = json_decode($q['imgs']);

        //获取点赞的用户列表
        $sql = "SELECT * FROM `sc_cls_msg_up_relation` as cmr left join sc_user as u on cmr.user_id=u.id WHERE cmr.cls_msg_id = " . $q['id'];
        $res = $db->query($sql);
        $q_res[$key]['up_user'] = $res->fetchAll();

        //获取回复信息的列表
        $sql = "SELECT *,cmr.id as comment_id FROM `sc_cls_msg_comment` as cmr left join sc_user as u on cmr.user_id=u.id WHERE cmr.cls_msg_id = " . $q['id'] ." order by c_time desc";
        $res = $db->query($sql);
        $q_res[$key]['comment'] = $res->fetchAll();

        $q_res[$key]['c_time'] = formatTime($q_res[$key]['c_time']);
    }




    if ($q_res) {
        $json["msg"] = "success";
        $json["data"] = $q_res;
    } else {
        $json["error"] = $db->errorInfo();
    }
}

function get_new_cls_msg_list() {
    global $db;
    global $json;


    $sql = "select * from sc_cls_msg where is_del=0  "
            . "order by c_time desc ";

    $q_res = $db->query($sql);
    $q_res = $q_res->fetchAll();

    $u_id = array();
    $q_id = array();
    foreach ($q_res as $key => $q) {
        $sql_u = "select * from sc_user where id=" . $q['user_id'] . "   ";
        $u_res = $db->query($sql_u);
        $q_res[$key]['user'] = $u_res->fetchAll();

        $q_res[$key]['c_time'] = formatTime($q_res[$key]['c_time']);
    }



    if ($q_res) {
        $json["msg"] = "success";
        $json["data"] = $q_res;
    } else {
        $json["error"] = $db->errorInfo();
    }
}

function save_pic() {
    global $db;
    global $json;
    global $appid;
    global $secret;

    $jssdk = new JSSDK($appid, $secret);
    $signPackage = $jssdk->GetSignPackage();
    $accessToken = $jssdk->getAccessToken();

    if ($_REQUEST['serverId']) {
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=" . $accessToken . "&media_id=" . $_REQUEST['serverId'];

        $image_name = saveImage($url, $_REQUEST['serverId']);
        $serverId = $_REQUEST['serverId'];
        $uid = 11;
        $school_id = 0;
        $c_time = time();
        $sql = "INSERT INTO `sc_image` (`id`, "
                . "`name`, "
                . "`serverId`, "
                . "`uid`, "
                . "`school_id`, "
                . "`cls_id`, "
                . "`is_del`, "
                . "`c_time`) VALUES (NULL, "
                . "'" . $image_name . "', "
                . "'" . $serverId . "', "
                . "'" . $uid . "', "
                . "'" . $school_id . "', "
                . "'" . $_SESSION['cls_id'] . "', "
                . "0, "
                . "'" . $c_time . "');";

        if ($db->exec($sql)) {
            $src = "../" . $image_name;
            $json['src'] = $src;
        };
        $json['msg'] = $sql;
    } else {
        $json['msg'] = $accessToken;
    }
}

function saveImage($path, $name) {
    $image_name = "upload/" . $name . ".jpg";
    $image_save_name = "../upload/" . $name . ".jpg";
    $ch = curl_init($path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    $img = curl_exec($ch);
    curl_close($ch);
    $fp = fopen($image_save_name, 'w');
    fwrite($fp, $img);
    fclose($fp);
    return $image_name;
}

function send_cls_msg() {
    global $db;
    global $json;

    if ($_REQUEST['name']) {

        $data = array();
        $data['school_id'] = $_REQUEST['school_id'];
        $data['user_id'] = $_REQUEST['user_id'];
        $data['cls_id'] = $_REQUEST['cls_id'];
        $data['content'] = $_REQUEST['content'];
        $data['tag'] = $_REQUEST['tag']?$_REQUEST['tag']:"NULL";
        $data['up'] = "0";
        $data['c_time'] = time();

        if (isset($_REQUEST['files'])) {
            $imgs = json_encode($_REQUEST['files']);
        } else {
            $imgs = "[]";
        }
        $data['imgs'] = $imgs;

        $sql = "INSERT INTO `sc_cls_msg` (`id`, "
                . "`school_id`, "
                . "`user_id`, "
                . "`cls_id`, "
                . "`content`, "
                . "`tag`, "
                . "`up`, "
                . "`c_time`, "
                . "`imgs`, "
                . "`is_del`) "
                . "VALUES (NULL, "
                . "'" . $data['school_id'] . "', "
                . "'" . $data['user_id'] . "', "
                . "'" . $data['cls_id'] . "', "
                . "'" . $data['content'] . "', "
                 . $data['tag'] . ", "
                . "'" . $data['up'] . "', "
                . "'" . $data['c_time'] . "', "
                . "'" . $data['imgs'] . "', "
                . "'0');";



        if ($db->exec($sql)) {
            $json['id'] = $db->lastInsertId();
        };
        $json['msg'] = "success";
    } else {
        $json['msg'] = $db->errorInfo();
    }
}

function del_cls_msg() {
    global $db;
    global $json;
    $sql = "UPDATE `sc_cls_msg` SET `is_del` = '1' WHERE `id` = " . $_REQUEST['id'] . ";";
    if ($db->exec($sql)) {
        $json['msg'] = "success";
        $json['data'] = $_REQUEST['id'];
    } else {
        $json['msg'] = "success";
        $json['data'] = array();
    };
}

function up_cls_msg() {
    global $db;
    global $json;
    if ($_REQUEST['type'] == "up") {
        $sql = "UPDATE `sc_cls_msg` SET `up` = `up`+1 WHERE `sc_cls_msg`.`id` = " . $_REQUEST['id'] . ";";
        $db->exec($sql);
        $sql = "INSERT INTO `sc_cls_msg_up_relation` (`id`, `user_id`, `cls_msg_id`, `c_time`) VALUES (NULL, '" . $_REQUEST['user_id'] . "', '" . $_REQUEST['id'] . "', '" . time() . "');";
        $db->exec($sql);
    } else {
        $sql = "UPDATE `sc_cls_msg` SET `up` = `up`-1 WHERE `sc_cls_msg`.`id` = " . $_REQUEST['id'] . ";";
        $db->exec($sql);
        $sql = "DELETE FROM `sc_cls_msg_up_relation` WHERE `user_id` = '" . $_REQUEST['user_id'] . "' and `cls_msg_id` = '" . $_REQUEST['id'] . "'";
        $db->exec($sql);
    }

    //成功返回所有的点赞信息
    $sql = "select * from sc_cls_msg_up_relation where `cls_msg_id` = '" . $_REQUEST['id'] . "'";
    $q_res = $db->query($sql);
    $q_res = $q_res->fetchAll();

    $u_id = array();
    $q_id = array();
    $user = [];
    foreach ($q_res as $key => $q) {
        $sql_u = "select nickname from sc_user where id=" . $q['user_id'] . "   ";
        $u_res = $db->query($sql_u);
        $user[] = $u_res->fetch();
    }

    if ($q_res) {
        $json["msg"] = "success";
        $json["data"] = $user;
    } else {
        $json["error"] = $db->errorInfo();
    }
}

?>