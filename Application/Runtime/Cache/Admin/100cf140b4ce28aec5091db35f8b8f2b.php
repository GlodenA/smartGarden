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

<div class="padding-md">
	<!-- 面包屑导航 -->
	<div>
		<ul class="breadcrumb">
			当前位置：
			<li><a href="/smartGarden/manager.php?s=/Index/main"> 主页</a></li>
			<li><a href="#">区域管理</a></li>
		</ul>
	</div>
	<div class="smart-widget" style="margin-bottom: 1px;">
		<div class="smart-widget-inner">
			<div class="smart-widget-body">
				<div class="header-text">
					区域管理
				</div>
				<div class="info-line">
					<a href="/smartGarden/manager.php?s=/Map/mapFence" class="btn btn-sm btn-info">
						<i class="fa fa-plus"></i>区域添加
					</a>
					<form class="searchTop-form form-inline pull-right p-l-10" id="search-form" action="/smartGarden/manager.php?s=/Map/areaList" method="post">
						<div class="form-group">
							<input type="text" name="keywords" placeholder="请输入区域名称..." class="form-control input" value="<?php echo ($keywords); ?>">
						</div>
						<a id="search" url="/smartGarden/manager.php?s=/Map/areaList" class="btn btn-sm btn-info">搜索</a>
					</form>
				</div>
				<div class="smart-widget-inner">
					<table class="table table-striped table-bordered" id="dataTable">
						<thead>
						<tr>
							<th>区域名称</th>
							<!--<th>设置员工数</th>-->
							<th>添加时间</th>
							<th>状态</th>
							<th>操作</th>
						</tr>
						</thead>
						<tbody>
						<?php if(is_array($areaList)): foreach($areaList as $key=>$v): ?><tr>
								<td><?php echo ($v['area_name']); ?></td>
								<!--<td><?php echo ($v['employee_num']); ?></td>-->
								<td><?php echo (date("Y-m-d H:i:s",$v['add_time'])); ?></td>
								<td>
									<?php if($v['is_show'] == 1): ?>显示
										<?php else: ?>
									隐藏<?php endif; ?>
								</td>
								<td class="manager">
									<a href="/smartGarden/manager.php?s=/Map/machineList/id/<?php echo ($v['id']); ?>" style="cursor: pointer"><i class="fa fa-pencil-square-o m-right-xs"></i>设备绑定列表</a>&nbsp;|&nbsp;
									<a href="/smartGarden/manager.php?s=/Map/areaInfo/id/<?php echo ($v['id']); ?>" class="text-success"><i class="fa fa-info-circle m-right-xs"></i>详情</a>&nbsp;|&nbsp;
									<a href="/smartGarden/manager.php?s=/Map/mapEdit/id/<?php echo ($v['id']); ?>" style="cursor: pointer"><i class="fa fa-pencil-square-o m-right-xs"></i>编辑</a>&nbsp;|&nbsp;
									<a style="cursor: pointer" onclick="areaDelete('<?php echo ($v["id"]); ?>')" class="text-danger"><i class="fa fa-trash-o m-right-xs"></i>删除</a>
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
<script src="<?php echo C('ADMIN_JS_PATH');?>/ajaxupload.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function(){
		//搜索功能
		$("#search").click(function(){
			var url = $(this).attr('url');
			var query  = $('form').find('.input').serialize();
			if( url.indexOf('?')>0 ){
				url += '&' + query;
			}else{
				url += '?' + query;
			}
			window.location.href = url;
		});
	})

	function memberBind(id){
		DMS.ajaxShow("员工绑定","/smartGarden/manager.php?s=/Map/memberBind/machine_id/"+id);
	}
	function areaDelete(id){
		DMS.dialog("确定要删除吗?",function(){
			DMS.ajaxPost("/smartGarden/manager.php?s=/Map/areaDelete",{id:id},function(ret){
				if(ret.status==1){
					layer.msg(ret.info, {icon: 1, time: 2000}, function () {
						window.location.href = window.location.href;
					});
				}else{
					layer.msg(ret.info, {icon: 0, time: 2000}, function () {
						window.location.href = window.location.href;
					});
				}
			})
		});
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