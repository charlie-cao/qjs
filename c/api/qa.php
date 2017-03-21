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
        case "save_voice":
            save_voice();
            break;
        case "get_expert_list":
            get_expert_list();
            break;
        case "get_new_expert_list":
            get_new_expert_list();
            break;
        case "send_question":
            send_question();
            break;
        case "get_qa_list":
            get_qa_list();
            break;
        case "get_new_qa_list":
            get_new_qa_list();
            break;
        case "update_user_info":
            update_user_info();
            break;
        case "up_qa":
            up_qa();
            break;
    }

    $json["end_time"] = getMillisecond();
    echo json_encode($json);
}

function update_user_info() {
    global $db;
    global $json;
    $sql = "UPDATE `sc_user` SET `username` = '" . $_REQUEST['username'] . "', `memo` = '" . $_REQUEST['memo'] . "' WHERE `id` = " . $_REQUEST['id'] . ";";
    if ($db->exec($sql)) {
        $json['msg'] = "success";
        $json['id'] = $_REQUEST['id'];
    } else {
        $json['msg'] = "success";
        $json['info'] = $db->errorInfo();
    }
}

function save_voice() {
    global $db;
    global $json;
    global $appid;
    global $secret;

    $jssdk = new JSSDK($appid, $secret);
    $signPackage = $jssdk->GetSignPackage();
    $accessToken = $jssdk->getAccessToken();


    if ($_REQUEST['serverId']) {
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=" . $accessToken . "&media_id=" . $_REQUEST['serverId'];

        $file_name = saveVoice($url, $_REQUEST['serverId']);

        $serverId = $_REQUEST['serverId'];
        $user_id = $_REQUEST['user_id'];
        $school_id = $_REQUEST['school_id'];
        $question_id = $_REQUEST['question_id'];
        $c_time = time();


        $sql = "INSERT INTO `sc_voice` (`id`, "
                . "`name`, "
                . "`serverId`, "
                . "`uid`, "
                . "`school_id`, "
                . "`question_id`, "
                . "`is_del`, "
                . "`c_time`) VALUES (NULL, "
                . "'" . $file_name . "', "
                . "'" . $serverId . "', "
                . "'" . $user_id . "', "
                . "'" . $school_id . "', "
                . "'" . $question_id . "', "
                . "0, "
                . "'" . $c_time . "');";

        if ($db->exec($sql)) {
            $sql = "UPDATE `sc_question` SET `answer_content` = '" . $serverId . "' WHERE `id` = " . $question_id . ";";
            if ($db->exec($sql)) {
                $json['msg'] = "success";
            };
        };
    } else {
        $json['msg'] = "error";
    }
}

function saveVoice($path, $name) {
    $file_name = $name . ".amr";
    $image_save_name = "../upload/" . $name . ".amr";
    $ch = curl_init($path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    $img = curl_exec($ch);
    curl_close($ch);
    $fp = fopen($image_save_name, 'w');
    fwrite($fp, $img);
    fclose($fp);
    return $file_name;
}

/* 实在不行就上传 */

function upVoice() {
    
}

function get_expert_list() {
    global $db;
    global $json;
    $page = $_REQUEST['page'];
    $size = $_REQUEST['size'];
    $start_num = ($page - 1) * $size;

    $sql = "select * from sc_user where is_expert=1 and  is_del=0  "
            . "order by add_time desc "
            . "limit " . $start_num . "," . $size . ";";

    $select = $db->query($sql);

    if ($select) {
        $json["msg"] = "success";
        $json["data"] = $select->fetchAll();
    } else {
        $json["error"] = $db->errorInfo();
    }
}

function get_new_expert_list() {
    global $db;
    global $json;
    $page = $_REQUEST['page'];
    $size = $_REQUEST['size'];
    $start_num = ($page - 1) * $size;

    $sql = "select * from sc_user where is_expert=1 and  is_del=0  "
            . "order by add_time desc "
            . "limit " . $start_num . "," . $size . ";";

    $select = $db->query($sql);

    if ($select) {
        $json["msg"] = "success";
        $json["data"] = $select->fetchAll();
    } else {
        $json["error"] = $db->errorInfo();
    }
}

//提问
function send_question() {
    global $db;
    global $json;
    $sql = "INSERT INTO `sc_question` (`id`, "
            . "`question_user_id`, "
            . "`answer_user_id`, "
            . "`question_content`, "
            . "`money`, "
            . "`up_num`, "
            . "`c_time`, "
            . "`is_del`) VALUES (NULL, "
            . "'" . $_REQUEST['question_user_id'] . "', "
            . "'" . $_REQUEST['answer_user_id'] . "', "
            . "'" . $_REQUEST['content'] . "', "
            . "'0', "
            . "'0', "
            . "'" . time() . "', "
            . "'0');";
    if ($db->exec($sql)) {
        $json['msg'] = "success";
        $json['id'] = $db->lastInsertId();
    } else {
        $json['msg'] = $db->errorInfo();
    };
}

function get_qa_list() {
    global $db;
    global $json;
    $page = $_REQUEST['page'];
    $size = $_REQUEST['size'];
    $start_num = ($page - 1) * $size;

    if ($_REQUEST['tag'] == 0) {
        $sql = "select * from sc_question where answer_content is not null and  is_del=0   "
                . "order by c_time desc "
                . "limit " . $start_num . "," . $size . ";";
    } else {
        $sql = "select * from sc_question where answer_content is not null and  is_del=0   "
                . "order by play_num desc "
                . "limit " . $start_num . "," . $size . ";";
    }

    $q_res = $db->query($sql);
    $q_res = $q_res->fetchAll();

    $u_id = array();
    $q_id = array();
    foreach ($q_res as $key => $q) {
        $sql_u = "select * from sc_user where id=" . $q['question_user_id'] . "   ";
        $u_res = $db->query($sql_u);
        $q_res[$key]['question_user'] = $u_res->fetchAll();

        $sql_u = "select * from sc_user where id=" . $q['answer_user_id'] . "   ";
        $u_res = $db->query($sql_u);
        $q_res[$key]['answer_user'] = $u_res->fetchAll();

       //获取点赞的用户列表
        $sql = "SELECT * FROM `sc_question_up_relation` as cmr left join sc_user as u on cmr.user_id=u.id WHERE cmr.school_msg_id = " . $q['id'];
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

function get_new_qa_list() {
    global $db;
    global $json;

    $sql = "select * from sc_question where is_del=0  "
            . "order by c_time desc ";

    $q_res = $db->query($sql);
    $q_res = $q_res->fetchAll();

    $u_id = array();
    $q_id = array();
    foreach ($q_res as $key => $q) {
        $sql_u = "select * from sc_user where id=" . $q['question_user_id'] . "   ";
        $u_res = $db->query($sql_u);
        $q_res[$key]['question_user'] = $u_res->fetchAll();

        $sql_u = "select * from sc_user where id=" . $q['answer_user_id'] . "   ";
        $u_res = $db->query($sql_u);
        $q_res[$key]['answer_user'] = $u_res->fetchAll();

        $q_res[$key]['c_time'] = formatTime($q_res[$key]['c_time']);
    }



    if ($q_res) {




        $json["msg"] = "success";
        $json["data"] = $q_res;
    } else {
        $json["error"] = $db->errorInfo();
    }
}
function up_qa() {
    global $db;
    global $json;
    if ($_REQUEST['type'] == "up") {
        $sql = "UPDATE `sc_question` SET `up_num` = `up_num`+1 WHERE `sc_question`.`id` = " . $_REQUEST['id'] . ";";
        $db->exec($sql);
        $sql = "INSERT INTO `sc_question_up_relation` (`id`, `user_id`, `school_msg_id`, `c_time`) VALUES (NULL, '" . $_REQUEST['user_id'] . "', '" . $_REQUEST['id'] . "', '" . time() . "');";
        $db->exec($sql);
    } else {
        $sql = "UPDATE `sc_question` SET `up_num` = `up_num`-1 WHERE `sc_question`.`id` = " . $_REQUEST['id'] . ";";
        $db->exec($sql);
        $sql = "DELETE FROM `sc_question_up_relation` WHERE `user_id` = '" . $_REQUEST['user_id'] . "' and `school_msg_id` = '" . $_REQUEST['id'] . "'";
        $db->exec($sql);
    }

    //成功返回所有的点赞信息
    $sql = "select * from sc_question_up_relation where `school_msg_id` = '" . $_REQUEST['id'] . "'";
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