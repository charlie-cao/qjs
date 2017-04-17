<?php
require_once '../config.php';
require_once '../lib/fun.php';

$sql = "select * from sc_cls_tag where cls_id=" . $_REQUEST['id'] . "  order by o asc";
$q = $db->query($sql);
$rs = $q->fetchAll();

$sql = "select * from sc_cls where id=" . $_REQUEST['id'] . "";
$q = $db->query($sql);
$cls = $q->fetch();
//var_dump($rs);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>千家师系统</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="../public/style/weui.css"/>
    <link rel="stylesheet" href="../public/style/weui2.css"/>
    <link rel="stylesheet" href="../public/style/weui3.css?1"/>
    <script src="../public/zepto.min.js"></script>
    <script>
        $(function () {
        });

        function add_tag() {
            $.prompt("", "输入标签", function (text) {
                if ($.trim(text) == "") {
                    alert("不可为空");
                } else if ($.trim(text).length>4) {
                    alert("最多四个字符");
                } else {
                    d = {};
                    d.name = text;
                    d.cls_id = '<?= $_REQUEST['id'] ?>';
                    d.school_id = '<?= $_REQUEST['school_id'] ?>';
                    $.ajax({
                        type: 'POST',
                        data: d,
                        url: '../api/admin.php?a=add_cls_tag',
                        dataType: 'json',
                        success: function (data) {
                            $.toast("成功");
                            location.reload();
                        },
                        error: function (xhr, type, e) {
                            alert(type);
                        }
                    });
                }
            }, function () {
                //取消操作
            });
        }
        function edit_tag(id) {
            $.prompt("", "编辑标签名", function (text) {
                if ($.trim(text) == "") {
                    alert("不可为空");
                } else if ($.trim(text).length>4) {
                    alert("最多四个字符");
                } else {
                    d = {};
                    d.id = id;
                    d.name = text;
                    d.cls_id = '<?= $_REQUEST['id'] ?>';
                    $.ajax({
                        type: 'POST',
                        data: d,
                        url: '../api/admin.php?a=edit_cls_tag',
                        dataType: 'json',
                        success: function (data) {
                            $.toast("成功");
                            location.reload();
                        },
                        error: function (xhr, type, e) {
                            alert(type);
                        }
                    });
                }
            }, function () {
                //取消操作
            });
        }
        function change_tag_index(id1, id2) {
            d = {};
            d.id1 = id1;
            d.id2 = id2;
            d.cls_id = '<?= $_REQUEST['id'] ?>';
            $.ajax({
                type: 'POST',
                data: d,
                url: '../api/admin.php?a=change_cls_tag_index',
                dataType: 'json',
                success: function (data) {
                    $.toast("成功");
                    location.reload();
                },
                error: function (xhr, type, e) {
                    alert(type);
                }
            });
        }

    </script>
    <style>
        .weui_cell_primary {
            padding-left: 4px;
            /*text-align: right;*/
        }

        .weui_cells {
            margin-top: 0px !important;
        }
    </style>
</head>
<body>
<div class="weui-header bg-blue">
    <div class="weui-header-left">
        <a href="school_cls.php?id=<?= $_REQUEST['school_id'] ?>" class="icon icon-109 f-white">返回</a>
    </div>
    <h1 class="weui-header-title"><?= $cls['name'] ?> 标签</h1>
    <div class="weui-header-right">
        <?php if (count($rs) <= 5) { ?>
            <a href="javascript:;" onclick="add_tag()" class="icon icon-36 f-white add">增加</a>
        <?php } else { ?>

        <?php } ?>
    </div>
</div>

<div class="weui_cells">
    <div class="weui_cell">
        <div class="weui_cell_hd">
            0
        </div>
        <div class="weui_cell_bd weui_cell_primary">
            动态
        </div>
        <div class="weui_cell_ft">
            <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_default ">系统默认标签不可编辑</a>
        </div>
    </div>

    <?php foreach ($rs as $key => $r) { ?>
        <div class="weui_cell" style="background-color: <?= ($key >= 5) ? "#ccc" : "" ?>">
            <div class="weui_cell_hd">
                <?= $key + 1 ?>
            </div>
            <div class="weui_cell_bd weui_cell_primary">
                <?= $r['name'] ?>
            </div>
            <div class="weui_cell_ft">
                <?php if (($key - 1) >= 0) { ?>
                    <a href="javascript:;" onclick="change_tag_index(<?= $rs[$key - 1]['id'] ?>,<?= $r['id'] ?>)"
                       class="weui_btn weui_btn_mini weui_btn_primary ">上升</a>
                <?php } else { ?>
                    <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_default ">上升</a>
                <?php } ?>

                <?php if (($key + 1) < count($rs)) { ?>
                    <a href="javascript:;" onclick="change_tag_index(<?= $r['id'] ?>,<?= $rs[$key + 1]['id'] ?>)"
                       class="weui_btn weui_btn_mini weui_btn_primary ">下降</a>
                <?php } else { ?>
                    <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_default ">下降</a>
                <?php } ?>

                <a href="javascript:;" onclick="edit_tag(<?= $r['id'] ?>)"
                   class="weui_btn weui_btn_mini weui_btn_primary">编辑</a>
            </div>
        </div>
    <?php } ?>
</div>

</body>
</html>