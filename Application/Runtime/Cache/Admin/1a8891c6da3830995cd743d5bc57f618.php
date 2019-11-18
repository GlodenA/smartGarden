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
	<div class="smart-widget widget-dark-blue">
		<div class="smart-widget-header">
			<a href="javascript:menuAdd()" class="btn btn-sm btn-info"><i class="fa fa-plus"></i>添加职位</a>
		</div>
		<div class="smart-widget-inner">
			<div class="smart-widget-body">
				<table class="table table-striped table-bordered" id="dataTable">
					<thead>
					<tr>
						<th>ID</th>
						<th>名称</th>
						<th>操作</th>
					</tr>
					</thead>
					<tbody>
					<?php if(is_array($menuList)): foreach($menuList as $key=>$v): ?><tr id="data-<?php echo ($v['id']); ?>">
							<td><?php echo ($v['id']); ?></td>
							<td><?php echo ($v['name']); ?></td>
							<td>
								<a href="javascript:menuAdd(<?php echo ($v['id']); ?>)" class="btn btn-manager btn-warning btn-xs" data-toggle="tooltip" data-placement="top" title="添加下级" data-original-title="添加下级"><i class="fa fa-plus"></i></a>
								<a href="javascript:menuEdit(<?php echo ($v['id']); ?>)" class="btn btn-manager btn-info btn-xs" data-toggle="tooltip" data-placement="top" title="修改"><i class="fa fa-pencil"></i></a>
								<a href="javascript:menuDelete(<?php echo ($v['id']); ?>)" class="btn btn-manager btn-danger btn-xs" data-toggle="tooltip" data-placement="top" title="删除"><i class="fa fa-trash-o"></i></a>
							</td>
						</tr>
						<?php if($v['children']){ ?>
						<?php if(is_array($v['children'])): foreach($v['children'] as $key=>$r): ?><tr id="data-<?php echo ($r['id']); ?>">
								<td><?php echo ($r['id']); ?></td>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;&nbsp;<?php echo ($r['name']); ?></td>
								<td>
									<a href="javascript:menuEdit(<?php echo ($r['id']); ?>)" class="btn btn-manager btn-info btn-xs" data-toggle="tooltip" data-placement="top" title="修改"><i class="fa fa-pencil"></i></a>
									<a href="javascript:menuDelete(<?php echo ($r['id']); ?>)" class="btn btn-manager btn-danger btn-xs" data-toggle="tooltip" data-placement="top" title="删除"><i class="fa fa-trash-o"></i></a>
								</td>
							</tr><?php endforeach; endif; ?>
						<?php } endforeach; endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function menuAdd(pid){
		if(pid){
			DMS.ajaxShow("新增职位","/smartGarden/manager.php?s=/Member/menuAdd/pid/"+pid);
		}else{
			DMS.ajaxShow("新增职位","/smartGarden/manager.php?s=/Member/menuAdd");
		}
	}
	function menuEdit(id){
		DMS.ajaxShow("职位编辑","/smartGarden/manager.php?s=/Member/menuEdit/id/"+id);
	}
	function menuDelete(id){
		DMS.dialog("确定要删除吗?",function(){
			DMS.ajaxPost("/smartGarden/manager.php?s=/Member/menuDelete",{id:id},function(ret){
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