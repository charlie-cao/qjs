<?php

require_once '../config.php';
require_once '../lib/fun.php';

if (!isset($_REQUEST['a'])) {
    echo "参数错误";
} else {
    $json["msg"] = "fail";
    $json["data"] = null;
    switch ($_REQUEST['a']) {
        case "save_pic":
        case "save_audio":
        break;
    
        case "update_user_info":
        case "update_shcool_info":
        case "update_cls_info":
            
        case "get_old_school_msg":
        case "get_new_school_msg":
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
        
        case "get_old_cls_msg":
        case "get_new_cls_msg":
            break;
        case "send_cls_msg":
            send_school_msg();
            break;
        case "del_cls_msg":
            del_school_msg();
            break;
        case "up_cls_msg":
            up_school_msg();
            break;        
    }
    echo json_encode($json);
}

function send_school_msg(){

}
//删除大家庭的信息
function del_school_msg() {
    global $db;
    global $json;
    $sql = "UPDATE `sc_school_msg` SET `is_del` = '1' WHERE `sc_school_msg`.`id` = ".$_REQUEST['id'].";";
    if ($db->exec($sql)) {
        $json['msg'] = "success";
        $json['data'] = $_REQUEST['id'];
    } else {
        $json['msg'] = "success";
        $json['data'] = array();
    };
}

//删除大家庭的信息
function up_school_msg() {
    global $db;
    global $json;
    if($_REQUEST['type']=="up"){
        $sql = "UPDATE `sc_school_msg` SET `up` = `up`+1 WHERE `sc_school_msg`.`id` = ".$_REQUEST['id'].";";
    }else{
        $sql = "UPDATE `sc_school_msg` SET `up` = `up`-1 WHERE `sc_school_msg`.`id` = ".$_REQUEST['id'].";";
    }
//    echo $sql;
    if ($db->exec($sql)) {
        $json['msg'] = "success";
        $json['data'] = $_REQUEST['id'];
    } else {
        $json['msg'] = "success";
        $json['data'] = array();
    };
}

/**
      * desription 压缩图片
      * @param sting $imgsrc 图片路径
      * @param string $imgdst 压缩后保存路径
      */
     function image_png_size_add($imgsrc, $imgdst) {

         list($width, $height, $type, $str) = getimagesize($imgsrc, $info);
         if ($width > 600) {
             $new_width = 600;
             $new_height = (600 / $width) * $height;
         } else {
             $new_width = $width;
             $new_height = $height;
         }


         switch ($type) {
             case 1:
                 $giftype = check_gifcartoon($imgsrc);
                 if ($giftype) {
                     header('Content-Type:image/gif');
                     $image_wp = imagecreatetruecolor($new_width, $new_height);
                     $image = imagecreatefromgif($imgsrc);
                     imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                     imagejpeg($image_wp, $imgdst, 75);
                     imagedestroy($image_wp);
                 }
                 break;
             case 2:
                 header('Content-Type:image/jpeg');
                 $image_wp = imagecreatetruecolor($new_width, $new_height);
                 $image = imagecreatefromjpeg($imgsrc);
                 imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                 imagejpeg($image_wp, $imgdst, 75);
                 imagedestroy($image_wp);
                 break;
             case 3:
                 header('Content-Type:image/png');
                 $image_wp = imagecreatetruecolor($new_width, $new_height);
                 $image = imagecreatefrompng($imgsrc);
                 imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                 imagejpeg($image_wp, $imgdst, 75);
                 imagedestroy($image_wp);
                 break;
         }
     }

     function check_gifcartoon($image_file) {
         $fp = fopen($image_file, 'rb');
         $image_head = fread($fp, 1024);
         fclose($fp);
         return preg_match("/" . chr(0x21) . chr(0xff) . chr(0x0b) . 'NETSCAPE2.0' . "/", $image_head) ? false : true;
     }

?>