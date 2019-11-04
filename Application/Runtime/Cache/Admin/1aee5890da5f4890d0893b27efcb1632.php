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
            <a href="javascript:adminAdd();" class="btn btn-sm btn-info">
                <i class="fa fa-plus"></i>增加管理员
            </a>
		</div>
		<div class="smart-widget-inner">
			<div class="smart-widget-body">
				<table class="table table-striped table-bordered" id="dataTable">
					<thead>
						<tr>
							<th>UID</th>
							<th>账号</th>
							<th>姓名</th>
							<th class="hidden-xs">用户组</th>
							<th class="hidden-xs">注册时间</th>
							<th class="hidden-xs">最后登录</th>
							<th class="hidden-xs">状态</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						<?php if(is_array($list)): foreach($list as $key=>$v): ?><tr id="data-<?php echo ($v['uid']); ?>">
							<td><?php echo ($v['uid']); ?></td>
							<td><?php if($v['avatar']): ?><img src="<?php echo ($v['avatar']); ?>" class="avatar" />&nbsp;<?php endif; echo ($v['username']); ?></td>
							<td><?php echo ($v['realname']); ?></td>
							<td class="hidden-xs"><?php echo ($v['group_name']); ?></td>
							<td class="hidden-xs"><?php echo (date("Y-m-d H:i:s",$v['reg_date'])); ?></td>
							<td class="hidden-xs"><?php echo (date("Y-m-d H:i:s",$v['last_date'])); ?></td>
							<td class="hidden-xs">
								<?php if($v['status'] == 1): ?><span class="label label-success">正常</span>
								<?php else: ?>
								<span class="label label-danger">禁止</span><?php endif; ?>
							</td>
							<td>
								<a href="javascript:showInfo(<?php echo ($v['uid']); ?>);" style="color: #edbc6c;"><i class="fa fa-info-circle m-right-xs"></i>详情</a>
								<a href="javascript:adminEdit(<?php echo ($v['uid']); ?>);" ><i class="fa fa-pencil-square-o m-right-xs"></i>编辑</a>
								<!-- <a href="/WFGarden/manager.php?s=/Admin/adminEdit/uid/1" class="btn btn-manager btn-info btn-xs" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-pencil"></i></a> -->
								<a href="javascript:adminDelete(<?php echo ($v['uid']); ?>);" style="color: #f03939;"><i class="fa fa-trash-o m-right-xs"></i>删除</a>
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
<script type="text/javascript">
	function adminAdd(){
		DMS.ajaxShow("新增管理员","/WFGarden/manager.php?s=/Admin/adminAdd");
	}
	function adminEdit(uid){
		DMS.ajaxShow("编辑管理员","/WFGarden/manager.php?s=/Admin/adminEdit/uid/"+uid);
	}
	function showInfo(uid){
		DMS.loadUrl("管理员详情","/WFGarden/manager.php?s=/Admin/adminInfo/uid/"+uid);
	}
	function adminDelete(uid){
		DMS.dialog("确定要删除吗?",function(){
			DMS.ajaxPost("/WFGarden/manager.php?s=/Admin/adminDelete",{uid:uid},function(ret,err){
				if(ret.status==1){
	            	DMS.success(ret.info,0,function(){
	            		$("#data-"+uid).remove();
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