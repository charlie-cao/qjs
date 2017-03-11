<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
 <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
  <link rel="stylesheet" href="../public/style/weui.css"/>
  <link rel="stylesheet" href="../public/style/weui2.css"/>
  <link rel="stylesheet" href="../public/style/weui3.css"/>
      <script src="../public/zepto.min.js"></script>
      <script>
  $(function(){
$('#ms1').click(function(){
$('#msg1').fadeIn();
$('#msg2,#msg3').fadeOut();
})
$('#ms2').click(function(){
$('#msg1').fadeOut();
$('#msg2').fadeIn();$('#msg3').fadeOut();
})	

$('#ms3').click(function(){
$('#msg1').fadeOut();
$('#msg2').fadeOut();$('#msg3').fadeIn();
})  


$('#ms4').click(function(){
$('#msg1,#msg3').fadeOut();
$('#msg2').fadeOut();$('#msg4').fadeIn();
}) 
	  });    
      
      </script>
</head>

<body ontouchstart style="background-color: #f8f8f8;">




<div class="weui_msg " id="msg1">
        <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_info"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">小二班</h2>
            <p class="weui_msg_desc">邀请码 12345，请分享该链接
                到学生家长的微信或朋友圈，家长接收邀请后请审核。</p>
        </div>
        <div class="weui_opr_area">
            <p class="weui_btn_area">

            <div class="page-bd-15">

                <div class="weui-share" onclick="$(this).fadeOut();$(this).removeClass('fadeOut')">
                    <div class="weui-share-box">
                        点击右上角发送给指定朋友或分享到朋友圈 <i></i>
                    </div>
                </div>

                <a onclick="$('.weui-share').show().addClass('fadeIn');" class="weui_btn weui_btn_primary" href="javascript:void(0)" ><i class='icon icon-12 f20'></i>分享到家长</a>

                <a href="teacher_menu.php" class="weui_btn weui_btn_default">返回班级管理</a>
            </p>
        </div>
        <div class="weui_extra_area">
            
        </div>
    </div>


<div class="weui_msg hide" id="msg2">
        <div class="weui_icon_area"><i class="weui_icon_warn weui_icon_msg"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">操作失败</h2>
            <p class="weui_msg_desc">内容详情，可根据实际需要安排，如果换行则不超过规定长度</p>
        </div>
        <div class="weui_opr_area">
            <p class="weui_btn_area">
                <a href="javascript:;" class="weui_btn weui_btn_primary">推荐操作</a>
                <a href="javascript:;" class="weui_btn weui_btn_default">辅助操作</a>
            </p>
        </div>
        <div class="weui_extra_area">
            
        </div>
    </div>
    
    
 <div class="weui_msg hide" id='msg3'>
        <div class='weui_msg_box'><p><i class="icon icon-40 f20 f-green"></i>现在还没有数据</p></div>
    </div>     
 
 
       <div class="weui_msg_img hide" id='msg4'>
      <div class="weui_msg_com"><div onclick="$('#msg4').fadeOut();" class="weui_msg_close"><i class="icon icon-95"></i></div><div class="weui_msg_src">
      <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAArIAAAGFBAMAAADzwA07AAAAIVBMVEXr6+vPz8/X19fp6ene3t7k5OTm5ubh4eHT09Pb29vR0dHqLrSfAAACyklEQVR42uzBgQAAAACAoP2pF6kCAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAGB27GdHaSiO4vgJ5e+s5lzAFlZ2Y1yWoIm6AjPJbItG3VJdmLiiOg9Aow8AybinK1/Tey9ldDttl+eTFC6w+4b+2luRNi2XcIJl8v/H6quHhf9BHiXnFlbGE6wep/D8+gm8Dp8CnEAeJePOBz4n7XKssi254gFWzEkVUWVb0uEeVklz7rxS2ZYM/PnfZ8zEz4atyrYkYOjn63emAPI5VLYtxczHe+HnbWxc7K9fVLYFxwjAIur5eVuGQLcgn6lsczkTYDNDOQb6Nma/oJWqbGMLV/EYIg6BAffIaG5iTlW2sSFXQDFGblzDAwqTIIi5VdmmRrZbYI/F3FXeDbj3uQ8q29SA1/ZYYcgtFkzeMAXQ50llmwo4RYcpRlwhj5DN4RShyjZWTHA1Bwa233Fm435w4onKNnY0yAwQ8BrFFGuezVS2sc0c6xBAPA04Rswz8+9Z7VBl61lwG48BrCd+IEQ33i36DB8eNKpsHUP+KPcAMuMvYgYXnFzS71S2jhF/8+D/mq+YIotwURh4GVOVraPHXy4dOsznfrNb3XUh9kv3nqhsHQEjlw5dFsa97qudAnK/RI8GKltLTANfkyEQlFECfOMBGDFKAaw5Vtl61lW1wifMaW7fMkpcZJr7u+fkTmXr2VRbgqM/+3slrZ+wPtP7A5WtZ8FTVfgA63VhYyZw3tGaJSrbkuDTe1Re3t1/hIiIiIiIiIiIiIj8ZQ8OBAAAAACA/F8bQVVVVVVVVVVVVVVVVVVVhT04EAAAAAAA8n9tBFVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVpT04JAAAAAAQ9P+10RMAAAAAAAAAAAAAAADAArTtXKLPR7LcAAAAAElFTkSuQmCC"></div>
      <p class="weui_msg_comment">长按上方二维码3秒识别二维码</p></div></div>   
</body>
</html>
