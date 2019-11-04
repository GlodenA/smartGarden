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
            管理组菜单设置
		</div>
		<div class="smart-widget-inner">
			<div class="smart-widget-body">
				<div class="alert alert-success alert-custom alert-dismissible" role="alert">
			    	当前管理组:<strong class="text-danger"><?php echo ($title); ?></strong>
			    </div>
				<a class="btn btn-success marginTB-xs" onclick="confirm()">提交</a>
			    <?php if(is_array($menuList)): foreach($menuList as $key=>$v): ?><div class="menu-check">
				    	<div class="m-top-md">
					    	<div class="custom-checkbox m-right-xs">
								<input class="auth-menus menus-row" type="checkbox" id="id-<?php echo ($v['id']); ?>" name="ids[]" value="<?php echo ($v['id']); ?>" <?php if(in_array($v['id'], $menu_ids)){ ?>checked<?php } ?>>
								<label for="id-<?php echo ($v['id']); ?>"></label>
							</div>
							<div class="inline-block vertical-top text-danger"><?php echo ($v['name']); ?></div>
						</div>
						<div class="child-row">
				    	<?php if(is_array($v['children'])): foreach($v['children'] as $key=>$r): ?><div class="custom-checkbox m-right-xs m-top-md">
								<input class="auth-menus menus-list" type="checkbox" id="id-<?php echo ($r['id']); ?>" name="ids[]" value="<?php echo ($r['id']); ?>" parent-id="<?php echo ($v['id']); ?>" <?php if(in_array($r['id'], $menu_ids)){ ?>checked<?php } ?> >
								<label for="id-<?php echo ($r['id']); ?>" parent-id="<?php echo ($v['id']); ?>" class="checkboxs"></label>
							</div>
							<div class="inline-block vertical-top text-primary m-top-md"><?php echo ($r['name']); ?></div>&nbsp;&nbsp;&nbsp;&nbsp;<?php endforeach; endif; ?>
				    	</div>
			    	</div><?php endforeach; endif; ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
    $('.menus-row').on('change',function(){
        $(this).closest('.menu-check').find('.child-row').find('input').prop('checked',this.checked);
    });
    $('.menus-list').on('change',function(){
    	var parentId = $(this).attr("parent-id");
    	if($("input[parent-id='"+parentId+"']:checked").length){
    		$("#id-"+parentId).prop("checked",true);
    	}else{
    		$("#id-"+parentId).prop("checked",false);
    	}
    });

})
function confirm(){
	var ids='';
	$("input[name='ids[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	ids = ids.substring(0,ids.length-1);
	if(ids=='') {
		DMS.alert("请至少选择一个规则");
		return false;
	}else{
		DMS.ajaxPost("/WFGarden/manager.php?s=/Group/groupSettingMenu",{groupId:'<?php echo ($id); ?>',ids:''+ids+''},function(ret){
			if(ret.status==1){
            	DMS.success("提交成功",0);
            }else{
            	DMS.error("提交失败",0);
            }
		})
	}
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