<?php
require_once '../config.php';
require_once '../lib/fun.php';

check_login();
//var_dump($_SESSION['user']);
$sql = "select * from sc_user where id = " . $_REQUEST['id'] . " limit 1";
$res = $db->query($sql);
$user = $res->fetch();


//error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
//require_once 'log.php';

//初始化日志
//$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
//$log = Log::Init($logHandler, 15);

//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

//①、获取用户openid
$tools = new JsApiPay();
//$openId = $tools->GetOpenid();
$openId = $_SESSION['user']->openid;
//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody("test");
$input->SetAttach("test");
$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
$input->SetTotal_fee("1");
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("test");
$input->SetNotify_url("http://qjs.isqgame.com/WxpayAPI_php_v3/example/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
//printf_info($order);
$jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
//$editAddress = $tools->GetEditAddressParameters();

//var_dump($user);
?>

<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
        <link rel="stylesheet" href="../public/style/weui.css" />
        <link rel="stylesheet" href="../public/style/weui2.css" />
        <link rel="stylesheet" href="../public/style/weui3.css" />
        <script src="../public/zepto.min.js"></script>
        <script>

            $(function () {
                                    $.showLoading();
                                    setTimeout(function() {
                                              $.hideLoading();
                                            }, 300)
                var max = $('#count_max').text();
                $('#content').on('input', function () {
                    var text = $(this).val();
                    var len = text.length;
                    $('#count').text(len);
                    if (len > max) {
                        $(this).closest('.weui_cell').addClass('weui_cell_warn');
                    } else {
                        $(this).closest('.weui_cell').removeClass('weui_cell_warn');
                    }
                });

                //    var $form = $("#form");
                //    $form.form();
                $("#formSubmitBtn").on("click", function () {
                    if ($("#content").val() == "") {
                        $.toast("内容不能为空", "forbidden");
                    } else {
                        callpay();
                    }
                });
            })


	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
//				alert(res.err_code+res.err_desc+res.err_msg);

                if(res.err_msg=="get_brand_wcpay_request:ok"){

                        var d = $('#sendMsg').serializeArray();
                        $.ajax({
                            type: 'POST',
                            data: d,
                            url: '../api/qa.php?a=send_question',
                            dataType: 'json',
                            success: function (data) {
                                console.log(data);
                                $.toast("成功");
                                window.history.back(-1);
                            }
                        });

                }else if(res.err_msg=="get_brand_wcpay_request:cancel"){
                    alert("支付取消，请重新支付");
                }else if(res.err_msg=="get_brand_wcpay_request:fail"){
                    alert(res.err_msg +"支付失败，请重新支付");
                }
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
        </script>
    </head>

    <body ontouchstart style="background-color: #f8f8f8;">
        <div class="weui-header bg-green">
            <div class="weui-header-left"> <a href="pt_expert_list.php" class="icon icon-109 f-white">返回</a>  </div>
            <h1 class="weui-header-title">提问</h1>
            <div class="weui-header-right"></div>
        </div>
        <div style="text-align: center; padding: 10px;">
            <img class="" src="<?= $user['headimgurl'] ?>"  style="    width: 80px;
                 padding: 20px;
                 border-radius: 80px;">

            <div class="weui-loadmore weui-loadmore-line">
                <span class="weui-loadmore-tips"><?= $user['username'] ?></span>
            </div>
            <p class="weui_media_desc" style="padding: 20px;">
                <?= $user['memo'] ?>
            </p>
        </div>



        <div class="weui_cells ">

            <form id="sendMsg">

                <input type="hidden" name="question_user_id" value="<?= $_SESSION['user']->id ?>">
                <input type="hidden" name="answer_user_id" value="<?= $user['id'] ?>">
                <div class="weui_cell">
                    <div class="weui_cell_bd weui_cell_primary">
                        <textarea id="content" name="content" class="weui_textarea" placeholder="提问内容" rows="3"></textarea>
                        <div class="weui_textarea_counter"><span id='count'>0</span>/<span id='count_max'>60</span></div>
                    </div>
                </div>

            </form>
        </div>
        <div class="weui_btn_area">
            <a class="weui_btn weui_btn_primary" href="javascript:;" id="formSubmitBtn" >付费提问 10元</a>
        </div>

    </body>
</html>
