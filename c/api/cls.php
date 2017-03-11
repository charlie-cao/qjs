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
        case "update_cls_info":
            update_cls_info();
            break;
        case "get_cls_msg_list":
            get_cls_msg_list();
            break;
        case "get_new_cls_msg_list":
            get_new_cls_msg_list();
            break;
        case "send_cls_msg":
            send_cls_msg();
            break;
        case "del_cls_msg":
            del_cls_msg();
            break;
        case "up_cls_msg":
            up_cls_msg();
            break;
    }
    $json["end_time"] = getMillisecond();
    echo json_encode($json);
}

function update_user_info(){
        global $db;
        global $json;
        $sql = "UPDATE `sc_user` SET `username` = '".$_REQUEST['username']."', `phone` = '".$_REQUEST['phone']."' WHERE `id` = ".$_REQUEST['id'].";";
        if($db->exec($sql)){
            $json['msg'] = "success";
            $json['id'] = $_REQUEST['id'];
        }else{
            $json['msg'] = "success";
            $json['info'] = $db->errorInfo();
        }
}

function update_cls_info(){
        global $db;
        global $json;
echo         $sql = "UPDATE `sc_cls` SET `name` = '".$_REQUEST['name']."', `cls_key` = '".$_REQUEST['cls_key']."' WHERE `id` = ".$_REQUEST['id'].";";
        if($db->exec($sql)){
            $json['msg'] = "success";
            $json['id'] = $_REQUEST['id'];
        }else{
            $json['msg'] = "success";
            $json['info'] = $db->errorInfo();

        }
}

function get_cls_msg_list(){
    global $db;
    global $json;
    $page = $_REQUEST['page'];
    $size = $_REQUEST['size'];
    $start_num = ($page - 1) * $size;

    if($_REQUEST['tag']!=0){
        $tag = "and tag=".$_REQUEST['tag'];
    }else{
        $tag="";
    }

    $sql = "select * from sc_cls_msg where is_del=0  and cls_id=".$_REQUEST['cls_id']." ".$tag." "
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

        $q_res[$key]['c_time'] = formatTime($q_res[$key]['c_time']);
    }



    if ($q_res) {
        $json["msg"] = "success";
        $json["data"] = $q_res;
    } else {
        $json["error"] = $db->errorInfo();
    }
}
function get_new_cls_msg_list(){
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
                . "'" . $school_id . "', "
                . "0, "
                . "'" . $c_time . "');";

        if ($db->exec($sql)) {
            $src = "../upload/" . $image_name;
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
    if ($_REQUEST['name']) {

        $data = array();
        $data['name'] = $_REQUEST['name'];
        $data['school_id'] = "0";
        $data['user_id'] = $_REQUEST['user_id'];
        $data['content'] = $_REQUEST['content'];
        $data['tag'] = $_REQUEST['tag'];
        $data['headimg'] = $_REQUEST['headimg'];
        $data['up'] = "0";
        $data['c_time'] = time();
        $imgs = json_encode($_REQUEST['files']);
        $data['imgs'] = $imgs;

        $sql = "INSERT INTO `sc_cls_msg` (`id`, "
                . "`name`, "
                . "`headimg`, "
                . "`school_id`, "
                . "`user_id`, "
                . "`content`, "
                . "`tag`, "
                . "`up`, "
                . "`c_time`, "
                . "`imgs`, "
                . "`is_del`) "
                . "VALUES (NULL, "
                . "'" . $data['name'] . "', "
                . "'" . $data['headimg'] . "', "
                . "'" . $data['school_id'] . "', "
                . "'" . $data['user_id'] . "', "
                . "'" . $data['content'] . "', "
                . "'" . $data['tag'] . "', "
                . "'" . $data['up'] . "', "
                . "'" . $data['c_time'] . "', "
                . "'" . $data['imgs'] . "', "
                . "'0');";


        $msg = array();
        if ($db->exec($sql)) {
            $msg['id'] = $db->lastInsertId();
        };
        $msg['msg'] = "success";
    } else {
        $msg['msg'] = $db->errorInfo();
    }
    echo json_encode($msg);
}


function del_cls_msg() {
    global $db;
    global $json;
    $sql = "UPDATE `sc_cls_msg` SET `is_del` = '1' WHERE `sc_cls_msg`.`id` = " . $_REQUEST['id'] . ";";
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
    } else {
        $sql = "UPDATE `sc_cls_msg` SET `up` = `up`-1 WHERE `sc_cls_msg`.`id` = " . $_REQUEST['id'] . ";";
    }

    if ($db->exec($sql)) {
        $json['msg'] = "success";
        $json['data'] = $_REQUEST['id'];
    } else {
        $json['msg'] = "success";
        $json['data'] = array();
    };
}

?>