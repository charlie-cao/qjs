<?php

function httpGet($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
}

function wx_userinfo($appid, $secret, $redirect_uri, $state="")
{
    $redirect_uri = urlencode($redirect_uri);
    $wx_sing_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect";
    if (!isset($_GET['code'])) {
        header("location:{$wx_sing_url}");
    }

    $code = $_GET['code'];

    $wx_access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";

    $res = (httpGet($wx_access_token_url));
    $res = json_decode($res);

//var_dump($res);
    $access_token = $res->access_token;
    $openid = $res->openid;
    $wx_userinfo_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";

    return json_decode(httpGet($wx_userinfo_url));
}

function login_base($appid, $secret, $redirect_uri, $state)
{
    $redirect_uri = urlencode($redirect_uri);
    $wx_sing_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state={$state}#wechat_redirect";
    if (!isset($_GET['code'])) {
        header("location:{$wx_sing_url}");
    }

    $code = $_GET['code'];

    $wx_access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";

    return json_decode(httpGet($wx_access_token_url));
}

function sk_encode($str)
{
    return $str;
}

function sk_decode($str)
{
    return $str;
}

/**
 * 通用的交验用户
 * 检查并重置 session['user']
 */
function check_user2()
{

}

function check_user($user)
{
    // open db
    global $db;
    // select user
    $sql = "select * from sc_user where openid='" . $user->openid . "' limit 1";
    $res = $db->query($sql);
    $res->setFetchMode(PDO::FETCH_OBJ);
    $rs = $res->fetch();


    if ($rs == false) {
        //如果用户没有在系统中注册
        //注册
        //INSERT INTO `schoolcms`.`sc_user` (`id`, `schoolkey`, `username`, `openid`, `nickname`, `language`, `city`, `province`, `country`, `headimgurl`, `sex`, `phone`, `state`, `add_time`, `upd_time`) VALUES (NULL, '11', '', '', '', '', '', '', '', '', '0', '', '0', '0', CURRENT_TIMESTAMP);
        $sql = "INSERT INTO `sc_user` ("
            . "`id`,"
            . "`username`, "
            . "`openid`, "
            . "`nickname`, "
            . "`language`, "
            . "`city`, "
            . "`province`, "
            . "`country`, "
            . "`headimgurl`, "
            . "`sex`, "
            . "`phone`, "
            . "`state`, "
            . "`add_time`, "
            . "`upd_time`) "
            . "VALUES ("
            . "NULL, "
            . "'" . $user->nickname . "', "
            . "'" . $user->openid . "', "
            . "'" . $user->nickname . "', "
            . "'" . $user->language . "', "
            . "'" . $user->city . "', "
            . "'" . $user->province . "', "
            . "'" . $user->country . "', "
            . "'" . $user->headimgurl . "', "
            . "'" . $user->sex . "', "
            . "'', "
            . "'0', "
            . "'" . time() . "', "
            . "CURRENT_TIMESTAMP);";

        if ($db->exec($sql)) {
            $user->state = 0;
        } else {

        }
    } else {
        //如果用户已经在系统中注册
        //获取用户身份
//        $user->state = $rs->state;
        //是否需要同步一下除了身份之外的所有用户信息
        //更新最近登录时间
        $sql = "UPDATE `sc_user` SET `last_time` = '" . time() . "' WHERE `openid` = '" . $user->openid . "';";
        $db->exec($sql);
        //更新登录次数
    }
    $sql = "select * from sc_user where openid='" . $user->openid . "' limit 1";
    $res = $db->query($sql);
    $user = $res->fetch(PDO::FETCH_OBJ);

    // return user or false
    return $user;
}

function check_school($school_key)
{
    //在服务端获取当前school key
    // open db
    global $db;
    // select user
    $sql = "select * from sc_school where school_key='" . $school_key . "' limit 1";
    $res = $db->query($sql);
    $rs = $res->fetch();
    return $rs;
}

function v($url)
{
    header("location:{$url}");
}

function check_login()
{
//    var_dump($_SESSION['user']);
    if (!isset($_SESSION['user']->openid)) {
        v("index.php");
    }
}

function get_school_info($id)
{
    global $db;
    $sql = "select * from sc_school where id='" . $id . "' limit 1";
    $res = $db->query($sql);
    return $res->fetch(PDO::FETCH_OBJ);
}

function get_cls_info($id)
{
    global $db;
    $sql = "select * from sc_cls where id='" . $id . "' limit 1";
    $res = $db->query($sql);
    return $res->fetch(PDO::FETCH_OBJ);
}

function get_userinfo($id)
{
    global $db;
    $sql = "select * from sc_user where openid='" . $id . "' limit 1";
    $res = $db->query($sql);
    return $res->fetch(PDO::FETCH_OBJ);
}

function get_tags($id, $type)
{
    global $db;
    $sql = "select * from sc_tag where school_id=" . $id . " and type='" . $type . "' order by o;";
    $res = $db->query($sql);
    return $res->fetchAll(PDO::FETCH_OBJ);
}


function get_school_tags($school_id)
{
    global $db;
    $sql = "select * from sc_school_tag where school_id=" . $school_id . " order by o asc limit 4;";
    $res = $db->query($sql);
    return $res->fetchAll();
}

function get_cls_tags($school_id,$cls_id)
{
    global $db;
    $sql = "select * from sc_cls_tag where school_id=" . $school_id . " and cls_id=".$cls_id." order by o asc limit 4;";
    $res = $db->query($sql);
    return $res->fetchAll();
}

function getMillisecond()
{
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}

function formatTime($time)
{
    $now = time();
    $day = date('Y-m-d', $time);
    $today = date('Y-m-d');

    $dayArr = explode('-', $day);
    $todayArr = explode('-', $today);

//距离的天数，这种方法超过30天则不一定准确，但是30天内是准确的，因为一个月可能是30天也可能是31天
    $days = ($todayArr[0] - $dayArr[0]) * 365 + (($todayArr[1] - $dayArr[1]) * 30) + ($todayArr[2] - $dayArr[2]);
//距离的秒数
    $secs = $now - $time;

    if ($todayArr[0] - $dayArr[0] > 0 && $days > 3) {//跨年且超过3天
        return date('Y-m-d', $time);
    } else {

        if ($days < 1) {//今天
            if ($secs < 60)
                return $secs . '秒前';
            elseif ($secs < 3600)
                return floor($secs / 60) . "分钟前";
            else
                return floor($secs / 3600) . "小时前";
        } else if ($days < 2) {//昨天
            $hour = date('h', $time);
            return "昨天" . $hour . '点';
        } elseif ($days < 3) {//前天
            $hour = date('h', $time);
            return "前天" . $hour . '点';
        } else {//三天前
            return date('m月d号', $time);
        }
    }
}

function formatTimeYmdHis($time){
    return date('Y-m-d H:i:s', $time);
}