<?php

require_once '../config.php';
require_once '../lib/fun.php';

if (!isset($_REQUEST['a'])) {
    echo "参数错误";
} else {
    $json["msg"] = "fail";
    $json["start_time"] = getMillisecond();
    $json["end_time"] = "";
    $json["data"] = null;
    switch ($_REQUEST['a']) {
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
    }

    $json["end_time"] = getMillisecond();
    echo json_encode($json);
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

    $sql = "select * from sc_question where is_del=0  "
            . "order by c_time desc "
            . "limit " . $start_num . "," . $size . ";";

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

?>