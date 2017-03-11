<?php

require_once '../config.php';
require_once '../lib/fun.php';

//var_dump($_REQUEST);

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
        
     $sql = "INSERT INTO `sc_school_msg` (`id`, "
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
            . "'".$data['name']."', "
            . "'".$data['headimg']."', "
            . "'".$data['school_id']."', "
            . "'".$data['user_id']."', "
            . "'".$data['content']."', "
            . "'".$data['tag']."', "
            . "'".$data['up']."', "
            . "'".$data['c_time']."', "
            . "'".$data['imgs']."', "
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


?>

