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

    </head>
    <body class="overflow-hidden">

<div class="padding-md">
	<div class="smart-widget widget-dark-blue">
		<div class="smart-widget-header">
            <a href="javascript:groupAdd()" class="btn btn-sm btn-info">
                <i class="fa fa-plus"></i>增加管理组
            </a>
		</div>
		<div class="smart-widget-inner">
			<div class="smart-widget-body">
				<table class="table table-striped table-bordered" id="dataTable">
					<thead>
						<tr>
							<th>ID</th>
							<th>名称</th>
							<th>描述</th>
							<th>状态</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						<?php if(is_array($list)): foreach($list as $key=>$v): ?><tr id="data-<?php echo ($v['id']); ?>">
							<td>#<?php echo ($v['id']); ?></td>
							<td><?php echo ($v['title']); ?></td>
							<td><?php echo ($v['description']); ?></td>
							<td>
								<?php if($v['status'] == 1): ?><span class="label label-success">正常</span>
								<?php else: ?>
								<span class="label label-danger">禁止</span><?php endif; ?>
							</td>
							<td>
								<a href="javascript:groupEdit(<?php echo ($v['id']); ?>,'<?php echo ($v['title']); ?>');"><i class="fa fa-pencil-square-o m-right-xs"></i>编辑</a>
								<!--<a href="/WFGarden/manager.php?s=/Group/groupSettingRule/groupId/<?php echo ($v['id']); ?>" style="color: #32c5cd;"><i class="fa fa-cog gear"></i>权限设置</a>-->
								<a href="/WFGarden/manager.php?s=/Group/groupSettingMenu/groupId/<?php echo ($v['id']); ?>" style="color:#00a65a;"><i class="fa fa-cog gear"></i>菜单设置</a>
								<a href="javascript:groupDelete(<?php echo ($v['id']); ?>);"style="color: #f03939;"><i class="fa fa-trash-o m-right-xs"></i>删除</a>
							</td>
						</tr><?php endforeach; endif; ?>
					</tbody>
				</table>
				<div class="content text-right">
					<ul class="pagination"><?php echo ($page); ?></ul>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function openPage(url){
		DMS.openPage(url);
	}
	function showInfo(uid){
		DMS.ajax("管理员详情","/WFGarden/manager.php?s=/Group/adminInfo/uid/"+uid);
	}
	function groupAdd(){
		DMS.ajaxShow("增加管理组","/WFGarden/manager.php?s=/Group/groupAdd");
	}
	function groupEdit(id,title){
		DMS.ajaxShow("编辑管理组 - "+title,"/WFGarden/manager.php?s=/Group/groupEdit/id/"+id);
	}
	function groupDelete(id){
		DMS.dialog("确定要删除当前管理组吗?",function(){
			DMS.ajaxPost("/WFGarden/manager.php?s=/Group/groupDelete",{id:id},function(ret){
				if(ret.status==1){
                	DMS.success(ret.info,0,function(){
                		$("#data-"+id).remove();
					});
                }else{
                	DMS.error(ret.info,0);
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