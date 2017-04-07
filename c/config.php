<?php

//debug
ini_set("display_errors", "Off");
error_reporting(E_ALL);
ini_set('date.timezone','Asia/Shanghai'); 

//session
session_start();

//db
$dbms = 'mysql';     //数据库类型 oracle 用ODI,对于开发者来说，使用不同的数据库，只要改这个，不用记住那么多的函数了
$host = 'localhost'; //数据库主机名
$dbName = 'schoolcms';  //使用的数据库
$user = 'root';      //数据库连接用户名
$pass = 'MYWy9CaA44uQZW3u';          //对应的密码
$dsn = "$dbms:host=$host;dbname=$dbName";


try {
    $db = new PDO($dsn, $user, $pass); //初始化一个PDO对象，就是创建了数据库连接对象$dbh
    $db->query("SET NAMES utf8;");
} catch (PDOException $e) {
    die("Error!: " . $e->getMessage() . "<br/>");
}

//公共号id
$appid = "wx8dfb25342ca7f279";
$secret = "d35b3b82fc417eeae0c663fd59eb32a3";

//服务器
$server_host = 'http://'.$_SERVER['HTTP_HOST'];

//短语音路径
$voice_path = "../upload/voice/";

//照片存储路径
$image_path = "../upload/image/";


?>
