<?php

require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";


/**
 * 简单service 类
 * 直接根据访问的方法名称直接访问相应的接口函数
 * 回头封装一个
 */


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
 *
 */
function update_user_info()
{
    global $db;
    global $json;
    $sql = "UPDATE `sc_user` SET `username` = '" . $_REQUEST['username'] . "', `memo` = '" . $_REQUEST['memo'] . "', `small_memo` = '" . $_REQUEST['small_memo'] . "' WHERE `id` = " . $_REQUEST['id'] . ";";
    if ($db->exec($sql)) {
        $json['msg'] = "success";
        //重置user
        $sql = "select * from sc_user where id='" . $_REQUEST['id'] . "' limit 1";
        $res = $db->query($sql);
        $user = $res->fetch(PDO::FETCH_OBJ);
        $_SESSION['user'] = $user;

        $json['id'] = $_REQUEST['id'];
    } else {
        $json['msg'] = "success";
        $json['info'] = $db->errorInfo();
    }
}

function add_play_num()
{
    global $db;
    global $json;
    $sql = "update sc_question set play_num = (play_num+1) WHERE  id=" . $_REQUEST['question_id'];
    $db->exec($sql);
    $json['msg'] = "success";
}

/**
 * 保存语音
 * @global type $db
 * @global type $json
 */
function save_voice()
{
    global $db;
    global $json;

    if ($_REQUEST['serverId']) {

        $question_id = $_REQUEST['question_id'];

        // serverId 为第一次上传的ID
        // mediaId 为当前可播放的ID
        $serverId = $mediaId = $_REQUEST['serverId'];
        save_voice_to_server($mediaId);

        $user_id = $_REQUEST['user_id'];
        $school_id = $_REQUEST['school_id'] ? $_REQUEST['school_id'] : "null";
        $c_time = time();


        $sql = "INSERT INTO `sc_voice` (`id`, "
            . "`mediaId`, "
            . "`serverId`, "
            . "`user_id`, "
            . "`school_id`, "
            . "`question_id`, "
            . "`is_del`, "
            . "`c_time`) VALUES (NULL, "
            . "'" . $mediaId . "', "
            . "'" . $serverId . "', "
            . "'" . $user_id . "', "
            . $school_id . ", "
            . "'" . $question_id . "', "
            . "0, "
            . "'" . $c_time . "');";


        if ($db->exec($sql)) {
            $sql = "UPDATE `sc_question` SET `answer_content` = '" . $mediaId . "',`answer_time` = '" . time() . "' WHERE `id` = " . $question_id . ";";
            if ($db->exec($sql)) {

                $sql = "select * from sc_question where id=" . $question_id;
                $q = $db->query($sql);
                $res = $q->fetch();

                WX_send_msg_answer($res['answer_user_id'], $res['question_user_id'], $res['id'], $res['question_content']);

                $json['msg'] = "success";
            };
        } else {
            $json['msg'] = $sql;
        };
    } else {
        $json['msg'] = "error";
    }
}

/**
 * 保存音频到服务器
 * @param type $serverId
 * @return type 媒体ID
 */
function save_voice_to_server($serverId)
{
    global $appid;
    global $secret;

    $voice_path = "../upload/voice/";
    $jssdk = new JSSDK($appid, $secret);
    $signPackage = $jssdk->GetSignPackage();
    $accessToken = $jssdk->getAccessToken();

    $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=" . $accessToken . "&media_id=" . $serverId;
    //文件路径
    $save_path = $voice_path . $serverId . ".amr";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    $d = curl_exec($ch);
    curl_close($ch);
    $fp = fopen($save_path, 'w');
    fwrite($fp, $d);
    fclose($fp);
    return $serverId;
}

/**
 * 音频实效，重新上传且更新音频
 * 返回：新的音频ID
 */
function update_voice()
{
    global $db;
    global $json;

    $serverId = $_REQUEST['serverId'];
    $question_id = $_REQUEST['question_id'];
//    $json['msg'] = "success";
//    $json['msg'] = $_REQUEST['serverId'];
    //通过当前的serverID 获取 原始serverID 并上传更新音频
    $res = upload_voice_to_wx($serverId);
    $res = json_decode($res);

    $json['data']->mediaId = $res->media_id;

    if ($res->media_id) {
        $sql = "UPDATE `sc_question` SET `answer_content` = '" . $res->media_id . "' WHERE `id` = " . $question_id . ";";

        if ($db->exec($sql)) {

            $sql = "UPDATE `sc_voice` SET `mediaId` = '" . $res->media_id . "' WHERE `question_id` = " . $question_id . ";";
            if ($db->exec($sql)) {
//                $json['msg'] = "xxx".$res->media_id;
//                $json['msg'] = "success";
//                $json['data']->mediaId = $res->media_id;
            };
        };
    }
}

/**
 * 上传多媒体文件到微信服务器
 * @global type $appid
 * @global type $secret
 * @global string $voice_path
 * @param type $serverId
 * @return type
 */
function upload_voice_to_wx($serverId)
{
    global $appid;
    global $secret;

    $jssdk = new JSSDK($appid, $secret);
    $signPackage = $jssdk->GetSignPackage();
    $accessToken = $jssdk->getAccessToken();

    $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=" . $accessToken . "&type=voice";

    $voice_path = "../upload/voice/";

    $file = realpath($voice_path . $serverId . ".amr"); //要上传的文件
    $fields['media'] = '@' . $file;


//    return $fields['media'];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        return curl_error($ch);
    }
    curl_close($ch);
    return ($result);
}

function get_school_expert_list()
{
    global $db;
    global $json;
    $page = $_REQUEST['page'];
    $size = $_REQUEST['size'];
    $start_num = ($page - 1) * $size;

    $sql = "select * from sc_user where school_id=" . $_REQUEST['school_id'] . " is_expert=1 and is_assistant=1 and is_leader=1 and is_teacher=1 and  is_del=0  "
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

/**
 * 获取专家
 * @global type $db
 * @global type $json
 */

function get_expert_list()
{
    global $db;
    global $json;
    $page = $_REQUEST['page'];
    $size = $_REQUEST['size'];
    $start_num = ($page - 1) * $size;

    $sql = "select * from sc_user where is_expert=1 and  is_del=0  "
        . "order by up_num desc "
        . "limit " . $start_num . "," . $size . ";";

    /**
     * 更新星级
     * 答题数量 + 获得赞的数量 /5 这些需要同步更新
     *
     */
    $q = $db->query($sql);
    $res = $q->fetchAll();

    foreach ($res as $key => $val) {
        $star_num = 3;
        $num = $val['up_num'] + $val['ans_num'] / 5;
        if ($num > 0 && $num < 10) {
            $star_num = 3;
        } elseif ($num >= 10 && $num < 40) {
            $star_num = 4;
        } elseif ($num >= 40 && $num < 80) {
            $star_num = 5;
        } elseif ($num >= 80 && $num < 160) {
            $star_num = 5;
        } elseif ($num >= 160) {
            $star_num = 5;
        }
        $res[$key]['star_num'] = $star_num;
    }

    if ($res) {
        $json["msg"] = "success";
        $json["data"] = $res;
    } else {
        $json["error"] = $db->errorInfo();
    }
}

/**
 * 获取专家
 * @global type $db
 * @global type $json
 */

function school_get_expert_list()
{
    global $db;
    global $json;
    $page = $_REQUEST['page'];
    $size = $_REQUEST['size'];
    $start_num = ($page - 1) * $size;


    if ($_REQUEST['tag'] == 0) {
        //校内专家
         $sql = "select * from sc_user_school as us LEFT JOIN sc_user as u ON us.user_id= u.id "
            . " where (us.is_leader=1 OR us.is_teacher=1 OR us.is_assistant=1) and  (u.is_del=0 and us.school_id = ".$_REQUEST['school_id'].")"
            . " order by u.up_num desc "
            . "limit " . $start_num . "," . $size . ";";
    } else {
        //校外专家
        $sql = "select * from sc_user where is_expert=1 and  is_del=0  "
            . "order by up_num desc "
            . "limit " . $start_num . "," . $size . ";";
    }


    /**
     * 更新星级
     * 答题数量 + 获得赞的数量 /5 这些需要同步更新
     *
     */
    $q = $db->query($sql);
    $res = $q->fetchAll();

    foreach ($res as $key => $val) {
        $star_num = 0;
        $num = $val['up_num'] + $val['ans_num'] / 5;
        if ($num >= 0 && $num < 10) {
            $star_num = 3;
        } elseif ($num >= 10 && $num < 40) {
            $star_num = 4;
        } elseif ($num >= 40 && $num < 80) {
            $star_num = 5;
        } elseif ($num >= 80 && $num < 160) {
            $star_num = 5;
        } elseif ($num >= 160) {
            $star_num = 5;
        }
        $res[$key]['star_num'] = $star_num;
    }

    if ($res) {
        $json["msg"] = "success";
        $json["data"] = $res;
    } else {
        $json["error"] = $db->errorInfo();
    }
}


/**
 * 加载最新的专家
 * @todo 合并到获取专家
 */
function get_new_expert_list()
{

}

//提问
function send_question()
{
    global $db;
    global $json;
    global $price_money;

     $school_id = isset($_REQUEST['school_id']) ? $_REQUEST['school_id'] : "NULL";

     $sql = "INSERT INTO `sc_question` (`id`, "
        . "`school_id`, "
        . "`question_user_id`, "
        . "`answer_user_id`, "
        . "`question_content`, "
        . "`money`, "
        . "`up_num`, "
        . "`c_time`, "
        . "`is_del`) VALUES (NULL, "
        . $school_id . ", "
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

        //用户问题提出的账单记录
        $q_id = $db->lastInsertId();
        //一个问题的价格
        $money = $price_money;
        pay_log($school_id, $_REQUEST['question_user_id'], $_REQUEST['answer_user_id'], $money, $q_id);

        //发送通知到被提问的专家
        WX_send_msg_question($_REQUEST['question_user_id'], $_REQUEST['answer_user_id'], $q_id, $_REQUEST['content']);

    } else {
        $json['msg'] = $db->errorInfo();
    };
}

/*
账单记录
*/
function pay_log($school_id, $pay_user_id, $get_user_id, $money, $q_id)
{
    global $db;
    $pay_user = get_user_info_by_id($pay_user_id);
    $get_user = get_user_info_by_id($get_user_id);
    $sql = "INSERT INTO `sc_pay_log` (`id`, "
        . "`school_id`, "
        . "`pay_user_id`, "
        . "`get_user_id`, "
        . "`pay_username`, "
        . "`get_username`, "
        . "`money`, "
        . "`server_info`, "
        . "`q_id`, "
        . "`state`, "
        . "`c_time`, "
        . "`is_del`) VALUES (NULL, "
        . $school_id . ", "
        . "'" . $pay_user_id . "', "
        . "'" . $get_user_id . "', "
        . "'" . $pay_user['username'] . "', "
        . "'" . $get_user['username'] . "', "
        . "'" . $money . "', "
        . "'SERVER', "
        . "'" . $q_id . "', "
        . "'0', "
        . "'" . time() . "', "
        . "'0');";

    $db->exec($sql);

}

function get_user_info_by_id($id)
{
    global $db;
    $sql = "select * from sc_user where id=" . $id;
    $q = $db->query($sql);
    $res = $q->fetch();
    return $res;
}

/*
  家长提问通知
 */
function WX_send_msg_question($from_user_id, $to_user_id, $q_id, $content)
{
    global $server_host;

    $jssdk = new JSSDK($appid, $secret);
    $signPackage = $jssdk->GetSignPackage();
    $accessToken = $jssdk->getAccessToken();

    $f_user = get_user_info_by_id($from_user_id);
    $t_user = get_user_info_by_id($to_user_id);

    $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $accessToken;
    $msg = new stdClass();
    $msg->touser = $t_user['openid'];
//    $msg->touser = "ohBSOxN-FL_bELvCLponQmVOm4IA";
    $msg->template_id = "WOge1NmdnORpP6D_lHWWZCOIQKv5rEPhEGgGqQgMd8o";
    //回复界面链接
    $msg->url = $server_host . "/c/qa/g_send_answer.php?state=" . $q_id;
    $msg->topcolor = "#FF0000";
    $msg->data = new stdClass();

    $msg->data->first = new stdClass();
    $msg->data->first->value = "家长提问";
    $msg->data->first->color = "#173177";

    $msg->data->keyword1 = new stdClass();
    $msg->data->keyword1->value = $f_user['username'];
    $msg->data->keyword1->color = "#173177";

    $msg->data->keyword2 = new stdClass();
    $msg->data->keyword2->value = date("Y-m-d h:i:sa");
    $msg->data->keyword2->color = "#173177";

    $msg->data->keyword3 = new stdClass();
    $msg->data->keyword3->value = $content;
    $msg->data->keyword3->color = "#173177";

    $msg->data->remark = new stdClass();
    $msg->data->remark->value = "请及时回答问题";
    $msg->data->remark->color = "#173177";

    $postJosnData = json_encode($msg);

    WX_POST($url, $postJosnData);
}

function WX_send_msg_answer($from_user_id, $to_user_id, $q_id, $content)
{
    global $server_host;
    global $appid;
    global $secret;
    $jssdk = new JSSDK($appid, $secret);
    $signPackage = $jssdk->GetSignPackage();
    $accessToken = $jssdk->getAccessToken();

    $f_user = get_user_info_by_id($from_user_id);
    $t_user = get_user_info_by_id($to_user_id);

    $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $accessToken;
    $msg = new stdClass();
    $msg->touser = $t_user['openid'];
    $msg->template_id = "E4spqzoIt19uSNZH88KZgko3S8wHJGAuKpp8yZjCVng";
    $msg->url = $server_host . "/c/qa/g_my_question.php";
//    $msg->url = "http://qjs.isqgame.com/c/qa/pt_my_question_list.php";

    $msg->topcolor = "#FF0000";
    $msg->data = new stdClass();

    $msg->data->first = new stdClass();
    $msg->data->first->value = "您的问题已经得到回复";
    $msg->data->first->color = "#173177";

    $msg->data->keyword1 = new stdClass();
    $msg->data->keyword1->value = $f_user['username'];
    $msg->data->keyword1->color = "#173177";

    $msg->data->keyword2 = new stdClass();
    $msg->data->keyword2->value = date("Y-m-d h:i:sa");
    $msg->data->keyword2->color = "#173177";

    $msg->data->keyword3 = new stdClass();
    $msg->data->keyword3->value = "您的问题:" . $content;
    $msg->data->keyword3->color = "#173177";

    $msg->data->remark = new stdClass();
    $msg->data->remark->value = "已经得到回答，请及时查看。";
    $msg->data->remark->color = "#173177";

    $postJosnData = json_encode($msg);

    WX_POST($url, $postJosnData);
}

/**
 * 访问微信接口POST方法 用于发送通知
 * @param type $url
 * @param type $postJosnData
 */
function WX_POST($url, $postJosnData)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postJosnData);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $data = curl_exec($ch);
//    var_dump($data);
}

/**
 * 获取提问回答
 * @global type $db
 * @global type $json
 */
function get_qa_list()
{
    global $db;
    global $json;
    $page = $_REQUEST['page'];
    $size = $_REQUEST['size'];
    $start_num = ($page - 1) * $size;

    //0 为最新
    //1 为最值
    //2 为校内
    if ($_REQUEST['tag'] == 0) {
        $sql = "select * from sc_question where answer_content is not null and   is_del=0 and school_id is NULL   "
            . "order by answer_time desc "
            . "limit " . $start_num . "," . $size . ";";
    } elseif ($_REQUEST['tag'] == 1){
        $sql = "select * from sc_question where answer_content is not null and  is_del=0 and school_id is NULL   "
            . "order by play_num desc "
            . "limit " . $start_num . "," . $size . ";";
    }elseif ($_REQUEST['tag'] == 2){
        $sql = "select * from sc_question where answer_content is not null and  is_del=0 and school_id = ".$_REQUEST['school_id']." "
            . "order by answer_time desc "
            . "limit " . $start_num . "," . $size . ";";

    }

    //如果是校内的则加上校内标签。

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

        $q_res[$key]['c_time'] = formatTime($q_res[$key]['answer_time']);

        if($q_res[$key]['answer_user'][0]['small_memo']==""||$q_res[$key]['answer_user'][0]['small_memo'] == null){
            $q_res[$key]['answer_user'][0]['small_memo']="特约专家";
        }
    }


    if ($q_res) {
        $json["msg"] = "success";
        $json["data"] = $q_res;
    } else {
        $json["error"] = $db->errorInfo();
    }
}


function get_new_qa_list()
{

}

/**
 * 对问答点赞
 * @global type $db
 * @global type $json
 */
function up_qa()
{
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