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
	      <li>排班管理</li>
	      <li>时间设置</li>
	      <li>时间添加</li>
	   </ul>
	</div>
	<div class="smart-widget" style="margin-bottom: 1px;">
        <div class="smart-widget-inner">
            <div class="smart-widget-body">
				<form class="form-horizontal no-margin" method="post" id="form">
					<div class="form-group">
						<label class="control-label col-md-3">名称</label>
						<div class="col-md-5">
							<input type="text" class="form-control input-sm" name="info[title]" required>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-3">上班时间</label>
						<div class="col-md-5">
							<input type="text" class="form-control input-sm" name="info[start_time]" id="start_time" placeholder="选择时间">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-3">下班时间</label>
						<div class="col-md-5">
							<input type="text" class="form-control input-sm" name="info[end_time]" id="end_time" placeholder="选择时间">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-3"></label>
						<div class="col-md-5">
							<div class="radio inline-block">
								<div class="custom-radio m-right-xs">
									<input type="radio" id="display1" name="info[is_show]" value="1" checked>
									<label for="display1"></label>
								</div>
								<div class="inline-block vertical-top">正常</div>
								<div class="custom-radio m-right-xs">
									<input type="radio" id="display2" name="info[is_show]" value="0" >
									<label for="display2"></label>
								</div>
								<div class="inline-block vertical-top">禁止</div>
							</div>
						</div>
					</div>
					<div class="text-center m-top-md">
						<button type="submit" class="btn btn-info">提交</button>
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
	//日期选择
    $(document).ready(function(){
       layui.use('laydate', function(){
            var laydate = layui.laydate;
            var start = laydate.render({
                elem: '#start_time',
                type: 'time',
                theme: 'grid',
            });

            var end = laydate.render({
                elem: '#end_time',
                type: 'time',
                theme: 'grid',
            });
        });
    });

	$(function(){
        $("#form").validate({
	        submitHandler:function(form){
	            confirm();
	        }
	    });
    })
	function confirm() {
		DMS.ajaxPost("/smartGarden/manager.php?s=/Schedules/timeAdd",$('#form').serialize(),function(ret){
			if(ret.status==1){
				layer.msg(ret.info,{icon: 1,time: 2000},function(){
					setTimeout(function(){
					    window.location.href = '/smartGarden/manager.php?s=/Schedules/timeList';
					}, 1000);
              	});
            }else{
            	layer.msg(ret.info,{icon: 2,time: 2000});
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