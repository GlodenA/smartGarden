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
            <li><a href="#">主页</a></li>
            <li><a href="/smartGarden/manager.php?s=/Attendance/leaveList">请假列表</a></li>
            <li><a href="#">请假申请</a></li>
        </ul>
    </div>
    <div class="smart-widget widget-dark-blue">
        <div class="smart-widget-inner padding-md">
            <div class="smart-widget-body">
                <div class="header-text">
                    请假申请
                </div>
                <form class="form-horizontal no-margin" id="search_form">
                    <div class="form-group">
                        <label class="control-label col-md-2">员工搜索</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="keyword" placeholder="请输入员工号/手机号">
                        </div>
                        <a href="javascript:getMemberInfo();" class="btn btn-sm btn-success">查询</a>
                    </div>
                </form>
                <form class="form-horizontal no-margin hide" method="post" id="form">
                    <input type="hidden" class="form-control" name="info[userid]" id="userid">
                    <input type="hidden" class="form-control" name="info[machine_id]" id="machine_id">
                    <div class="form-group">
                        <label class="control-label col-md-2">员工号</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="info[job_number]" id="job_number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">姓名</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="info[realname]" id="realname">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">请假开始时间</label>
                        <div class="col-md-6">
                        <input type="text" class="form-control time-input" name="info[start_time]" id="start_time" placeholder="起始时间">
                        </div>
                        </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">请假结束时间</label>
                        <div class="col-md-6">
                        <input type="text" class="form-control time-input" name="info[end_time]" id="end_time" placeholder="结束时间">
                        </div>
                        </div>
                    <div class="text-center m-top-md">
                        <button type="submit" class="btn btn-info" id="sub">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo C('ADMIN_JS_PATH');?>/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo C('ADMIN_JS_PATH');?>/layui/layui.js"></script>
<script src="<?php echo C('ADMIN_JS_PATH');?>/time.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        layui.use('laydate', function(){
            var laydate = layui.laydate;
            var start = laydate.render({
                elem: '#start_time',
                type: 'datetime',
                theme: 'grid',
                festival: true, //显示节日
                calendar: true,
                min:1,
                max:7,
                done: function(value, date){
                    end.config.min = start.config.dateTime;
                    end.config.max.year = date.year;
                    end.config.max.month = date.month-1;
                    end.config.max.date = date.date;
                    end.config.max.hours = 23;
                    end.config.max.minutes = 59;
                    end.config.max.seconds = 59;
                }
            });
            var end = laydate.render({
                elem: '#end_time',
                type: 'datetime',
                theme: 'grid',
                festival: true, //显示节日
                calendar: true,
                min:1,
                max:7,
                done: function(value, date){
                    start.config.max = end.config.dateTime;
                    start.config.min.year = date.year;
                    start.config.min.month = date.month-1;
                    start.config.min.date = date.date;
                    start.config.min.hours = 00;
                    start.config.min.minutes = 00;
                    start.config.min.seconds = 00;
                }
            });
        });
    });
    // 查询操作
    function getMemberInfo(){
        var keywords = $('#keyword').val();
        if(!keywords){
            $("#form").addClass("hide");
            layer.msg("请输入员工号/手机号");
            return;
        }
        DMS.ajaxPost("/smartGarden/manager.php?s=/Attendance/getMemberInfo",{keywords:keywords},function(ret){
            if(ret.status==1){
                if(ret.data.machine_id > 0){
                    $("#form").removeClass("hide");
                    $("#job_number").val(ret.data.job_number);
                    $("#realname").val(ret.data.realname);
                    $("#userid").val(ret.data.userid);
                    $("#machine_id").val(ret.data.machine_id);
                }else{
                    $("#form").addClass("hide");
                    layer.msg("请先给员工绑定设备", {icon: 0});
                    return;
                }
            }else{
                $("#form").addClass("hide");
                layer.msg("无此员工信息", {icon: 0});
            }
        })
    }
    $(function(){
        $("#form").validate({
            submitHandler:function(form){
                save();
            }
        });
    })
    function save() {
        DMS.ajaxPost("/smartGarden/manager.php?s=/Attendance/leaveApply",$('#form').serialize(),function(ret,err){
            if(ret.status==1){
                DMS.success(ret.info,1000,function(){
                    if(ret.url){
                        window.location.href = ret.url;
                    }else{
                        window.location.href = window.location.href;
                    }
                });
            }else{
                layer.msg(ret.info, {icon: 0});
            }
        })
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