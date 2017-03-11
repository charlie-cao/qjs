<?php

require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";

$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();
$accessToken = $jssdk->getAccessToken();

if ($_REQUEST['serverId']) {
    $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=" . $accessToken . "&media_id=" . $_REQUEST['serverId'];

    $image_name = saveImage($url, $_REQUEST['serverId']);
    $serverId = $_REQUEST['serverId'];
    $uid = 11;
    $school_id = 11;
    $c_time = time();
    $sql = "INSERT INTO `sc_image` (`id`, `name`, `serverId`, `uid`, `school_id`, `is_del`, `c_time`) VALUES (NULL, '" . $image_name . "', '" . $serverId . "', '" . $uid . "', '" . $school_id . "', 0, '" . $c_time . "');";

    $msg = array();
    if ($db->exec($sql)) {
        $src = "http://qjs.isqgame.com/c/" . $image_name;
        $msg['src'] = $src;
    };
    $msg['msg'] = $sql;
} else {
    $msg['msg'] = $accessToken;
}
echo json_encode($msg);

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


?>

