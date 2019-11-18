<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo C('WEB_NAME');?>-后台管理</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="shortcut icon" href="/favicon.ico" />
    	<link rel="bookmark" href="/favicon.ico" />
	    <link href="<?php echo C('ADMIN_CSS_PATH');?>/bootstrap.min.css" rel="stylesheet">
		<link href="<?php echo C('ADMIN_CSS_PATH');?>/font-awesome.min.css" rel="stylesheet">
		<!-- <link href="<?php echo C('ADMIN_CSS_PATH');?>/public.css" rel="stylesheet"> -->
		<link href="<?php echo C('ADMIN_CSS_PATH');?>/common.css" rel="stylesheet">
		<script src="<?php echo C('ADMIN_JS_PATH');?>/jquery-1.11.1.min.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/bootstrap.min.js" type="text/javascript"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/common.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/jquery.slimscroll.min.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/admin_template.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/admin.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/layer/layer.js"></script>
      <!-- Vue, element, 间距工具类 相关 -->
      <link rel="stylesheet" href="/smartGarden/Public/Admin/Css//util/flex.css">
      <link href="https://unpkg.com/basscss@8.0.2/css/basscss.min.css" rel="stylesheet">
      <script src="https://cdn.jsdelivr.net/npm/vue@2.6.0"></script>
      <link rel="stylesheet" href="/smartGarden/Public/Admin/element/index.css">
      <script src="https://unpkg.com/element-ui/lib/index.js"></script>
      <!-- Vue, element, 间距工具类 相关 -->
    </head>
    <body class="overflow-hidden">

<link href="<?php echo C('ADMIN_JS_PATH');?>/layui/css/layui.css" rel="stylesheet">
<div class="padding-md">
    <!-- 面包屑导航 -->
    <div>
        <ul class="breadcrumb">
            当前位置：
            <li><a href="/smartGarden/manager.php?s=/Index/main"> 主页</a></li>
            <li><a href="/smartGarden/manager.php?s=/Member/memberList">员工管理</a></li>
        </ul>
    </div>
    <div class="smart-widget" style="margin-bottom: 1px;">
        <div class="smart-widget-inner">
            <div class="smart-widget-body">
                <div class="header-text">
                    员工管理
                </div>
                <div class="info-line">
                    <a href="javascript:memberAdd()" class="btn btn-sm btn-info">
                        <i class="fa fa-plus"></i>增加员工
                    </a>
                    <a href="javascript:manager('delete')" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="bottom" title="批量删除员工" data-original-title="批量删除员工">删除员工</a>
                    <a href="javascript:manager('changeManager')" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="bottom" title="批量切换管理人员" data-original-title="批量切换管理人员">切换管理人员</a>
                    <div class="searchTop-form form-inline pull-right" >
                        <div class="form-group">
                            <input type="text" id="filename" class="form-control" readonly="">
                        </div>
                        <a class="btn btn-sm btn-info" id="selector">
                            选择文件
                        </a>
                        <button type="button" class="btn btn-warning btn-sm hide" id="media_upload">上传</button>
                        <a href="javascript:fnImport();" class="btn btn-sm btn-info">
                            导入
                        </a>
                        <a href="/smartGarden/Public/Admin/File/01.xlsx" class="btn btn-sm btn-info form-inline">
                            下载模板
                        </a>
                    </div>
                </div>
                <div class="info-line">
                    <form class="searchTop-form form-inline pull-right p-l-10" id="search-form" action="/smartGarden/manager.php?s=/Member/memberList" method="post">
                        <div class="form-group">
                            <label for="position">职位</label>
                            <select class="form-control searchbody input-sm" name="position" id="position" onchange="changePosition(this)">
                                <option value="0">--全部--</option>
                                <?php if(is_array($positionList)): $i = 0; $__LIST__ = $positionList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pl): $mod = ($i % 2 );++$i;?><option value="<?php echo ($pl["id"]); ?>"<?php if($position == $pl['id']): ?>selected<?php endif; ?>><?php echo ($pl["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                        <div class="form-group" id="member_manager">
                            <label for="parent_id">管理人员</label>
                            <select class="form-control searchbody input-sm" name="parent_id" id="parent_id">
                                <option value="0">--全部--</option>
                                <?php if(is_array($managerList)): $i = 0; $__LIST__ = $managerList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sl): $mod = ($i % 2 );++$i;?><option value="<?php echo ($sl["userid"]); ?>"<?php if($parent_id == $sl['userid']): ?>selected<?php endif; ?>><?php echo ($sl["realname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">员工状态</label>
                            <select class="form-control searchbody input-sm" name="status" id="status">
                                <option value="">--全部--</option>
                                <option value="1" <?php if($status == 1): ?>selected<?php endif; ?> >在岗</option>
                                <option value="2" <?php if($status == 2): ?>selected<?php endif; ?> >未上岗</option>
                                <option value="3" <?php if($status == 3): ?>selected<?php endif; ?> >离岗</option>
                                <option value="4" <?php if($status == 4): ?>selected<?php endif; ?> >未上岗但有考勤</option>
                                <option value="5" <?php if($status == 5): ?>selected<?php endif; ?> >未上岗且无考勤</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="keywords">关键字：</label>
                            <input type="text" class="form-control input-sm" id="keywords" name="keywords" value="<?php echo ($keywords); ?>" placeholder="根据手机号、姓名查询">
                        </div>
                        <a id="search" url="/smartGarden/manager.php?s=/Member/memberList" class="btn btn-sm btn-success">搜索</a>
                        <a id="excel" url="/smartGarden/manager.php?s=/Member/memberExcel" class="btn btn-sm btn-success">导出</a>
                    </form>
                    
                </div>
                <div class="smart-widget-inner">
                    <table class="table table-striped table-bordered m-top-sm" id="dataTable">
                        <thead>
                        <tr>
                            <th>
                                <div class="custom-checkbox">
                                    <input type="checkbox" id="checkall" class="check-all" >
                                    <label for="checkall"></label>
                                </div>
                            </th>
                            <th>#</th>
                            <th>姓名</th>
                            <th>手机号码</th>
                            <th>员工号</th>
                            <th>职位</th>
                            <th>管理人员</th>
                            <th>设备IEMI</th>
                            <th>性别</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(is_array($list)): foreach($list as $k=>$v): ?><tr id="data-<?php echo ($v['uid']); ?>">
                                <td>
                                    <div class="custom-checkbox">
                                        <input type="checkbox" value="<?php echo ($v['userid']); ?>" name="userids[]" id="userid-<?php echo ($v['userid']); ?>" class="ids" >
                                        <label for="userid-<?php echo ($v['userid']); ?>"></label>
                                    </div>
                                </td>
                                <td><?php echo ($k +1 + $number); ?></td>
                                <!-- <td><?php echo ($v['userid']); ?></td> -->
                                <td><?php echo ($v['realname']); ?></td>
                                <td><?php echo ($v['mobile']); ?></td>
                                <td><?php echo ($v['job_number']); ?></td>
                                <td><?php echo (getPositionName($v['position'])); ?></td>
                                <td>
                                    <?php echo ($v['parent_name']); ?>
                                </td>
                                <td><?php echo ($v['machine_imei']); ?></td>
                                <td>
                                    <?php if($v['sex'] == 1): ?><span>男</span>
                                        <?php elseif($v['sex'] == 2): ?>
                                        <span>女</span>
                                        <?php else: ?>
                                        <span>未知</span><?php endif; ?>
                                </td>
                                <td>
                                    <?php if($v['job_status'] == 1): ?><span class="label label-success">在岗</span>
                                        <?php elseif($v['job_status'] == 2): ?>
                                            <span class="label label-danger">未上岗</span>
                                        <?php else: ?>
                                        <span class="label label-warning">离岗</span><?php endif; ?>
                                </td>
                                <td>
                                    <a href="javascript:showInfo(<?php echo ($v['userid']); ?>);"><i class="fa fa-info-circle m-right-xs"></i>详情</a>&nbsp;|
                                    <a href="javascript:memberEdit(<?php echo ($v['userid']); ?>)"><i class="fa fa-pencil-square-o m-right-xs"></i>编辑</a>&nbsp;|
                                    <a href="javascript:memberDelete(<?php echo ($v['userid']); ?>)" class="text-danger"><i class="fa  fa-trash-o fa-fw"></i>删除</a>&nbsp;|
                                    <a href="javascript:machineBind(<?php echo ($v['userid']); ?>);"><i class="fa fa-link"></i>绑定设备</a>
                                    <?php if($v['machine_imei']): ?>&nbsp;|&nbsp;<a href="javascript:machineUnbind(<?php echo ($v['userid']); ?>)"><i class="fa fa-unlink"></i>解绑设备</a>
                                        &nbsp;|&nbsp;<a href="/smartGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>" class="text-success"><i class="fa fa-info-circle m-right-xs"></i>员工轨迹</a><?php endif; ?>
                                    <?php if($v['area_id']): ?>&nbsp;|&nbsp; <a href="/smartGarden/manager.php?s=/Map/areaInfo/id/<?php echo ($v['area_id']); ?>" class="text-success"><i class="fa fa-info-circle m-right-xs"></i>所属区域</a><?php endif; ?>
                                    <!--<?php if($v['is_lock'] == 1): ?>-->
                                        <!--<a href="javascript:memberLock(<?php echo ($v['userid']); ?>,<?php echo ($v['is_lock']); ?>)" style="color:#23b7e5; "><i class="fa fa-power-off m-right-xs"></i>解冻</a>-->
                                        <!--<?php else: ?>-->
                                        <!--<a href="javascript:memberLock(<?php echo ($v['userid']); ?>,<?php echo ($v['is_lock']); ?>)" style="color:#f03939;"><i class="fa fa-power-off m-right-xs"></i>冻结</a>-->
                                    <!--<?php endif; ?>-->
                                </td>
                            </tr><?php endforeach; endif; ?>
                        </tbody>
                    </table>
                    <div class="content text-right">
                        <ul class="pagination">
                            <?php echo ($page); ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo C('ADMIN_JS_PATH');?>/layui/layui.js"></script>
<script src="<?php echo C('ADMIN_JS_PATH');?>/time.js"></script>
<script src="<?php echo C('ADMIN_JS_PATH');?>/ajaxupload.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function(){
        //搜索功能
        $("#search").click(function () {
            var url = $(this).attr('url');
            var query = $('#search-form').find('.input-sm').serialize();

            if (url.indexOf('?') > 0) {
                url += '&' + query;
            } else {
                url += '?' + query;
            }
            window.location.href = url;
        });
        //导出功能
        $("#excel").click(function () {
            var url = $(this).attr('url');
            var query = $('#search-form').find('.input-sm').serialize();
            if (url.indexOf('?') > 0) {
                url += '&' + query;
            } else {
                url += '?' + query;
            }
            window.location.href = url;
        });
        $(".check-all").click(function(){
            $(".ids").prop("checked", this.checked);
        });
        $(".ids").click(function(){
            var option = $(".ids");
            option.each(function(i){
                if(!this.checked){
                    $(".check-all").prop("checked", false);
                    return false;
                }else{
                    $(".check-all").prop("checked", true);
                }
            });
        });

        // 创建一个上传参数
        var uploadOption = {
            // 提交目标
            action: "/util.php?m=Attachment&c=Index&a=excelUpload",
            // 服务端接收的名称
            name: "file",
            // 自动提交
            autoSubmit: true,
            // 选择文件之后…
            onChange: function (file, extension) {
                if (new RegExp(/(xls)|(xlsx)/i).test(extension)) {
                    $("#media_upload").removeClass("hide");
                    $("#filename").val(file);
                } else {
                    DMS.alert("只限上传xls文件，请重新选择！");
                    return;
                }
            },
            // 开始上传文件
            onSubmit: function (file, extension) {
                $("#media_upload").text("正在上传");
            },
            // 上传完成之后
            onComplete: function (file, response) {
                var response = JSON.parse(jQuery(response).text());
                console.log(response);
                if(response.status == 'success'){
                    $("#media_upload").text("上传完成");
                    $("#filename").val(response.path);
                }else{
                    $("#media_upload").addClass("hide");
                    DMS.alert("上传文件过大/上传失败！");
                    return false;
                }
            }
        };

        // 初始化图片上传框
        var oAjaxUpload = new AjaxUpload('#selector', uploadOption);
        // 给上传按钮增加上传动作
        $("#media_upload").click(function (){
            oAjaxUpload.submit();
        })
    })

    function manager(type){
        var userids='';
        $("input[name='userids[]']:checked").each(function(i, n){
            userids += $(n).val() + ',';
        });
        userids = userids.substring(0,userids.length-1);

        if(userids=='') {
            DMS.alert("请先选择员工")
            return false;
        }else{
            switch (type) {
                case 'delete':
                    var url = "/smartGarden/manager.php?s=/Member/membersDelete";
                    break;
                case 'changeManager':
                    var url = "/smartGarden/manager.php?s=/Member/membersManagerChange";
                    break;
            }
            if(url){
                if(type == 'changeManager'){
                    DMS.ajaxShow("批量切换养护经理",url+"/userids/"+userids);
                    return;
                }
                DMS.dialog("确定要执行当前操作吗?",function(){
                    DMS.ajaxPost(url,{userids:userids},function(ret){
                        if(ret.status==1){
                            DMS.success(ret.info,0,function(){
                                window.location.href = window.location.href;
                            });
                        }else{
                            DMS.error(ret.info,0);
                        }
                    })
                });
            }
        }
    }

    function fnImport(){
        var excel = $('#filename').val();
        if(!excel){
            DMS.error('请先上传文件');
            return;
        }
        DMS.ajaxPost("/smartGarden/manager.php?s=/Member/importFile",{"filename":excel},function(ret){
            if(ret.status==1){
                DMS.success(ret.info,1000,function(){
                    submitStatus = true;
                    if(ret.url){
                        window.location.href = ret.url;
                    }else{
                        window.location.href = window.location.href;
                    }
                })
            }else{
                DMS.error(''+ret.info+'',0,function(){
                    submitStatus = true;
                })
            }
        })
    }
    function fnImport(){
        var excel = $('#filename').val();
        if(!excel){
            DMS.error('请先上传文件');
            return;
        }
        DMS.ajaxPost("/smartGarden/manager.php?s=/Member/importFile",{"filename":excel},function(ret){
            if(ret.status==1){
                DMS.success(ret.info,1000,function(){
                    submitStatus = true;
                    if(ret.url){
                        window.location.href = ret.url;
                    }else{
                        window.location.href = window.location.href;
                    }
                })
            }else{
                DMS.error(''+ret.info+'',0,function(){
                    submitStatus = true;
                })
            }
        })
    }
    //日期选择
    $(document).ready(function () {
        layui.use('laydate', function () {
            var laydate = layui.laydate;
            var start = laydate.render({
                elem: '#start_time',
                type: 'datetime',
                theme: 'grid',
                festival: true, //显示节日
                calendar: true,
                max: 0,
                done: function (value, date) {
                    endMax = end.config.max;
                    end.config.min = date;
                    end.config.min.month = date.month - 1;
                }
            });

            var end = laydate.render({
                elem: '#end_time',
                type: 'datetime',
                theme: 'grid',
                festival: true, //显示节日
                calendar: true,
                max: 0,
                done: function (value, date) {
                    start.config.max = date;
                    start.config.max.month = date.month - 1;
                }
            });
        });
    });
    function showInfo(userid) {
        DMS.loadUrl("用户详情", "/smartGarden/manager.php?s=/Member/memberInfo/userid/" + userid);
    }
    function memberAdd() {
        DMS.ajaxShow("添加员工", "/smartGarden/manager.php?s=/Member/memberAdd");
    }
    function memberEdit(userid) {
        DMS.ajaxShow("养护工人编辑", "/smartGarden/manager.php?s=/Member/memberEdit/userid/" + userid);
    }
    function machineBind(userid) {
        DMS.ajaxShow("绑定设备", "/smartGarden/manager.php?s=/Member/machineBind/userid/" + userid);
    }
    // 解绑设备
    function machineUnbind(userid) {
        DMS.dialog("确定要解绑吗?", function () {
            DMS.ajaxPost("/smartGarden/manager.php?s=/Member/machineUnbind", {userid: userid}, function (ret) {
                if (ret.status == 1) {
                    layer.msg(ret.info, {icon: 1, time: 2000}, function () {
                        window.location.reload();
                    })
                } else {
                    layer.msg(ret.info, {icon: 0, time: 2000})
                }
            })
        });
    }
    // 删除
    function memberDelete(id) {
        DMS.dialog("确定要删除该用户吗?", function () {
            DMS.ajaxPost("/smartGarden/manager.php?s=/Member/memberDelete", {userid: id}, function (ret) {
                if (ret.status == 1) {
                    layer.msg(ret.info, {icon: 1, time: 2000}, function () {
                        window.location.reload();
                    })
                } else {
                    layer.msg(ret.info, {icon: 0, time: 2000})
                }
            })
        });
    }
    function memberLock(userid, is_lock) {
        // 解冻
        if (is_lock == '0') {
            DMS.dialog("确定要对此账户解冻吗?", function () {
                DMS.ajaxPost("/smartGarden/manager.php?s=/Member/memberLock", {userid: userid, is_lock: is_lock}, function (ret) {
                    if (ret.status == 1) {
                        DMS.success("操作成功", 0, function () {
                            if (ret.url) {
                                window.location.href = ret.url;
                            } else {
                                window.location.href = window.location.href;
                            }
                        });
                    } else {
                        DMS.error(ret.info, 0);
                    }
                })
            });
        } else if (is_lock == 1) {
            DMS.dialog("确定要冻结此账户吗?", function () {
                DMS.ajaxPost("/smartGarden/manager.php?s=/Member/memberLock", {userid: userid, is_lock: is_lock}, function (ret) {
                    if (ret.status == 1) {
                        DMS.success("操作成功", 0, function () {
                            if (ret.url) {
                                window.location.href = ret.url;
                            } else {
                                window.location.href = window.location.href;
                            }
                        });
                    } else {
                        DMS.error(ret.info, 0);
                    }
                })
            });
        } else {
            DMS.error("缺少参数");
        }
    }
    function changePosition(obj){
        var option = obj.value;
        var item = document.getElementById("member_manager");
        if(option>0){
            var url = '/smartGarden/manager.php?s=/Member/getMemberManager';
            $.post(url, {'parent_id':option}, function(ret){
                if(ret.status == 1){
//                    item.style.display = "inline-block";
                    item.querySelector("select").innerHTML = "";
                    item.querySelector("select").innerHTML= '<option value="">--全部--</option>';
                    for(i in ret.data){
                        item.querySelector("select").innerHTML += '<option value='+ret.data[i].userid+'<?php if($parent_id == '+ret.data[i].userid+'): ?>selected<?php endif; ?>>'+ret.data[i].realname+'</option>';
                    }
                } else{
//                    item.style.display = "none";
                    item.querySelector("select").innerHTML = "";
                    item.querySelector("select").innerHTML= '<option value="">--全部--</option>';
                }
            });
        }
//        else{
//            item.style.display = "none";
//        }
    }
</script>

            <!-- <footer class="footer">
                <img src="<?php echo C('ADMIN_IMAGE_PATH');?>/logo_common.png" />
                <p class="no-margin">
                    &copy; 2016 <strong>北京点萌科技有限公司</strong>. ALL Rights Reserved. <a href="http://www.dianm.cc" target="_blank">www.dianm.cc</a>
                </p>
            </footer> -->
            <!-- <a href="#" class="scroll-to-top hidden-print"><i class="fa fa-chevron-up fa-lg"></i></a> -->
            <!-- <a href="javascript:history.go(-1)" class="history-back" data-toggle="tooltip" data-placement="left" title="返回上一页"><i class="fa fa-chevron-left fa-lg"></i></a> -->
    </body>
</html>