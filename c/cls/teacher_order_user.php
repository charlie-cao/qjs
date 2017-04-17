<?php
require_once '../config.php';
require_once '../lib/fun.php';
require_once "../lib/jssdk.php";
check_login();
//
$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="../public/style/weui.css"/>
    <link rel="stylesheet" href="../public/style/weui2.css"/>
    <link rel="stylesheet" href="../public/style/weui3.css?1"/>
    <script src="../public/zepto.min.js"></script>
    <script src="../public/iscroll.js"></script>
    <script>
        $(function () {
//            $('.weui_tab').tab();
            $(".weui_btn_warn").click(function (e) {
                $.confirm("通过?", "确认?", function () {
                    $.toast("成功!");
                }, function () {
                    //取消操作
                });
            })
        });
    </script>
</head>

<body ontouchstart style="background-color: #f8f8f8;">


<div class="weui_tab tab-bottom">
    <div class="weui_tab_bd">

        <div class="weui-header bg-green">
                <div class="weui-header-left">
                    <a href="teacher_menu.php" class="icon icon-109 f-white">返回</a>
                </div>
            <h1 class="weui-header-title">审核成员</h1>
            <div class="weui-header-right"> </div>
        </div>


        <div class="weui_cells" style="margin-top: 0px;">
            <div class="weui_cells_title">待审核</div>

            <div class="weui_cell">
                <div class="weui_cell_hd">
                    <img
                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC4AAAAuCAMAAABgZ9sFAAAAVFBMVEXx8fHMzMzr6+vn5+fv7+/t7e3d3d2+vr7W1tbHx8eysrKdnZ3p6enk5OTR0dG7u7u3t7ejo6PY2Njh4eHf39/T09PExMSvr6+goKCqqqqnp6e4uLgcLY/OAAAAnklEQVRIx+3RSRLDIAxE0QYhAbGZPNu5/z0zrXHiqiz5W72FqhqtVuuXAl3iOV7iPV/iSsAqZa9BS7YOmMXnNNX4TWGxRMn3R6SxRNgy0bzXOW8EBO8SAClsPdB3psqlvG+Lw7ONXg/pTld52BjgSSkA3PV2OOemjIDcZQWgVvONw60q7sIpR38EnHPSMDQ4MjDjLPozhAkGrVbr/z0ANjAF4AcbXmYAAAAASUVORK5CYII="
                            alt="" style="width:20px;margin-right:5px;display:block">
                </div>
                <div class="weui_cell_bd weui_cell_primary">
                    <p>张二 于 2017-2-13 申请 加入 小二班级 附言</p>
                </div>
                <div class="weui_cell_ft">
                    <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_warn">审核通过</a>
                    <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_warn">拒绝通过</a>
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd">
                    <img
                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC4AAAAuCAMAAABgZ9sFAAAAVFBMVEXx8fHMzMzr6+vn5+fv7+/t7e3d3d2+vr7W1tbHx8eysrKdnZ3p6enk5OTR0dG7u7u3t7ejo6PY2Njh4eHf39/T09PExMSvr6+goKCqqqqnp6e4uLgcLY/OAAAAnklEQVRIx+3RSRLDIAxE0QYhAbGZPNu5/z0zrXHiqiz5W72FqhqtVuuXAl3iOV7iPV/iSsAqZa9BS7YOmMXnNNX4TWGxRMn3R6SxRNgy0bzXOW8EBO8SAClsPdB3psqlvG+Lw7ONXg/pTld52BjgSSkA3PV2OOemjIDcZQWgVvONw60q7sIpR38EnHPSMDQ4MjDjLPozhAkGrVbr/z0ANjAF4AcbXmYAAAAASUVORK5CYII="
                            alt="" style="width:20px;margin-right:5px;display:block">
                </div>
                <div class="weui_cell_bd weui_cell_primary">
                    <p>张二 于 2017-2-13 申请 加入 小二班级 附言</p>
                </div>
                <div class="weui_cell_ft">
                    <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_warn">审核通过</a>
                    <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_warn">拒绝通过</a>
                </div>
            </div>

            <div class="weui_cells" style="margin-top: 0px;">
                <div class="weui_cells_title">审核通过</div>

                <div class="weui_cell">
                    <div class="weui_cell_hd">
                        <img
                                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC4AAAAuCAMAAABgZ9sFAAAAVFBMVEXx8fHMzMzr6+vn5+fv7+/t7e3d3d2+vr7W1tbHx8eysrKdnZ3p6enk5OTR0dG7u7u3t7ejo6PY2Njh4eHf39/T09PExMSvr6+goKCqqqqnp6e4uLgcLY/OAAAAnklEQVRIx+3RSRLDIAxE0QYhAbGZPNu5/z0zrXHiqiz5W72FqhqtVuuXAl3iOV7iPV/iSsAqZa9BS7YOmMXnNNX4TWGxRMn3R6SxRNgy0bzXOW8EBO8SAClsPdB3psqlvG+Lw7ONXg/pTld52BjgSSkA3PV2OOemjIDcZQWgVvONw60q7sIpR38EnHPSMDQ4MjDjLPozhAkGrVbr/z0ANjAF4AcbXmYAAAAASUVORK5CYII="
                                alt="" style="width:20px;margin-right:5px;display:block">
                    </div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <p>张二 于 2017-2-13 申请 加入 小二班级 附言</p>
                    </div>
                    <div class="weui_cell_ft">
                    </div>
                </div>
                <div class="weui_cell">
                    <div class="weui_cell_hd">
                        <img
                                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC4AAAAuCAMAAABgZ9sFAAAAVFBMVEXx8fHMzMzr6+vn5+fv7+/t7e3d3d2+vr7W1tbHx8eysrKdnZ3p6enk5OTR0dG7u7u3t7ejo6PY2Njh4eHf39/T09PExMSvr6+goKCqqqqnp6e4uLgcLY/OAAAAnklEQVRIx+3RSRLDIAxE0QYhAbGZPNu5/z0zrXHiqiz5W72FqhqtVuuXAl3iOV7iPV/iSsAqZa9BS7YOmMXnNNX4TWGxRMn3R6SxRNgy0bzXOW8EBO8SAClsPdB3psqlvG+Lw7ONXg/pTld52BjgSSkA3PV2OOemjIDcZQWgVvONw60q7sIpR38EnHPSMDQ4MjDjLPozhAkGrVbr/z0ANjAF4AcbXmYAAAAASUVORK5CYII="
                                alt="" style="width:20px;margin-right:5px;display:block">
                    </div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <p>张二 于 2017-2-13 申请 加入 小二班级 附言</p>
                    </div>
                    <div class="weui_cell_ft">
                    </div>
                </div>




        </div>

    </div>
    
</div>
</body>
</html>
