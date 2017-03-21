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

    switch ($_REQUEST['a']) {
        case "save_pic":
            save_pic();
            break;
        case "update_user_info":
            update_user_info();
            break;
        case "update_school_info":
            update_school_info();
            break;
        case "get_school_msg_list":
            get_school_msg_list();
            break;
        case "get_new_school_msg_list":
            get_new_school_msg_list();
            break;
        case "send_school_msg":
            send_school_msg();
            break;
        case "del_school_msg":
            del_school_msg();
            break;
        case "up_school_msg":
            up_school_msg();
            break;
        case "add_cls":
            add_cls();
            break;
        case "check_inv_code":
            check_inv_code();
            break;

    }
    $json["end_time"] = getMillisecond();
    echo json_encode($json);
}


function check_inv_code() {
    global $db;
    global $json;
    $sql = "select * from sc_cls WHERE `id` = '" . $_REQUEST['school_id'] . "' and `school_key`='" . $_REQUEST['code'] . "';";
    $res = $db->query($sql);
    $r = $res->fetchAll();


    if (count($r) == 1) {
        join_cls($_REQUEST['school_id'], $_REQUEST['school_id'], $_REQUEST['user_id']);
        $json['msg'] = "success";
        $json['id'] = $_REQUEST['school_id'];
    } else {
        $json['msg'] = "fail";
        $json['id'] = $_REQUEST['school_id'];
    }
}

function add_cls() {
    global $db;
    global $json;
    $sql = "INSERT INTO `sc_cls` ("
            . "`id`, "
            . "`name`, "
            . "`school_key`, "
            . "`teacher_id`, "
            . "`school_id`, "
            . "`c_time`, "
            . "`u_time`, "
            . "`is_del`) VALUES ("
            . "NULL, "
            . "'" . $_REQUEST['name'] . "', "
            . "'" . $_REQUEST['school_key'] . "', "
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
    } else {
        $json['msg'] = "success";
        $json['info'] = $db->errorInfo();
    }
}

function join_cls($school_id, $school_id, $user_id, $is_teacher = 0) {
    global $db;
    $sql = "INSERT INTO `sc_user_cls` ("
            . "`id`, "
            . "`user_id`, "
            . "`school_id`, "
            . "`is_teacher`, "
            . "`school_id`, "
            . "`c_time`, "
            . "`is_del`) VALUES ("
            . "NULL, "
            . "'" . $user_id . "', "
            . "'" . $school_id . "', "
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

function update_school_info() {
    global $db;
    global $json;
    $sql = "UPDATE `sc_cls` SET `name` = '" . $_REQUEST['name'] . "', `school_key` = '" . $_REQUEST['school_key'] . "' WHERE `id` = " . $_REQUEST['id'] . ";";
    if ($db->exec($sql)) {
        $json['msg'] = "success";
        $json['id'] = $_REQUEST['id'];
    } else {
        $json['msg'] = "success";
        $json['info'] = $db->errorInfo();
    }
}

function get_school_msg_list() {
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

    $sql = "select * from sc_school_msg where is_del=0  and school_id=" . $_REQUEST['school_id'] . " " . $tag . " "
            . "order by c_time desc "
            . "limit " . $start_num . "," . $size . ";";

    $q_res = $db->query($sql);
    $q_res = $q_res->fetchAll();

    $u_id = array();
    $q_id = array();
    foreach ($q_res as $key => $q) {
        $sql_u = "select * from sc_user where id=" . $q['user_id'] . "   ";
        $u_res = $db->query($sql_u);
        $q_res[$key]['user'] = $u_res->fetchAll();
        $q_res[$key]['imgs'] = json_decode($q['imgs']);

        //获取点赞的用户列表
        $sql = "SELECT * FROM `sc_school_msg_up_relation` as cmr left join sc_user as u on cmr.user_id=u.id WHERE cmr.school_msg_id = " . $q['id'];
        $res = $db->query($sql);
        $q_res[$key]['up_user'] = $res->fetchAll();

        $q_res[$key]['c_time'] = formatTime($q_res[$key]['c_time']);
    }




    if ($q_res) {
        $json["msg"] = "success";
        $json["data"] = $q_res;
    } else {
        $json["error"] = $db->errorInfo();
    }
}

function get_new_school_msg_list() {
    global $db;
    global $json;


    $sql = "select * from sc_school_msg where is_del=0  "
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
                . "`is_del`, "
                . "`c_time`) VALUES (NULL, "
                . "'" . $image_name . "', "
                . "'" . $serverId . "', "
                . "'" . $uid . "', "
                . "'" . $_SESSION['school_id'] . "', "
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

function send_school_msg() {
    global $db;
    global $json;

    if ($_REQUEST['name']) {

        $data = array();
        $data['school_id'] = $_REQUEST['school_id'];
        $data['user_id'] = $_REQUEST['user_id'];
        $data['content'] = $_REQUEST['content'];
        $data['tag'] = $_REQUEST['tag'];
        $data['up'] = "0";
        $data['c_time'] = time();

        if (isset($_REQUEST['files'])) {
            $imgs = json_encode($_REQUEST['files']);
        } else {
            $imgs = "[]";
        }
        $data['imgs'] = $imgs;

        $sql = "INSERT INTO `sc_school_msg` (`id`, "
                . "`school_id`, "
                . "`user_id`, "
                . "`content`, "
                . "`tag`, "
                . "`up`, "
                . "`c_time`, "
                . "`imgs`, "
                . "`is_del`) "
                . "VALUES (NULL, "
                . "'" . $data['school_id'] . "', "
                . "'" . $data['user_id'] . "', "
                . "'" . $data['content'] . "', "
                . "'" . $data['tag'] . "', "
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

function del_school_msg() {
    global $db;
    global $json;
    $sql = "UPDATE `sc_school_msg` SET `is_del` = '1' WHERE `id` = " . $_REQUEST['id'] . ";";
    if ($db->exec($sql)) {
        $json['msg'] = "success";
        $json['data'] = $_REQUEST['id'];
    } else {
        $json['msg'] = "success";
        $json['data'] = array();
    };
}

function up_school_msg() {
    global $db;
    global $json;
    if ($_REQUEST['type'] == "up") {
        $sql = "UPDATE `sc_school_msg` SET `up` = `up`+1 WHERE `sc_school_msg`.`id` = " . $_REQUEST['id'] . ";";
        $db->exec($sql);
        $sql = "INSERT INTO `sc_school_msg_up_relation` (`id`, `user_id`, `school_msg_id`, `c_time`) VALUES (NULL, '" . $_REQUEST['user_id'] . "', '" . $_REQUEST['id'] . "', '" . time() . "');";
        $db->exec($sql);
    } else {
        $sql = "UPDATE `sc_school_msg` SET `up` = `up`-1 WHERE `sc_school_msg`.`id` = " . $_REQUEST['id'] . ";";
        $db->exec($sql);
        $sql = "DELETE FROM `sc_school_msg_up_relation` WHERE `user_id` = '" . $_REQUEST['user_id'] . "' and `school_msg_id` = '" . $_REQUEST['id'] . "'";
        $db->exec($sql);
    }

    //成功返回所有的点赞信息
    $sql = "select * from sc_school_msg_up_relation where `school_msg_id` = '" . $_REQUEST['id'] . "'";
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
    }}

?>