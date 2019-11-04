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
	<!-- 面包屑导航 -->
	<div>
	   <ul class="breadcrumb">
	      当前位置：
		   <li><a href="/WFGarden/manager.php?s=/Index/main">主页</a></li>
		   <li>班组管理</li>
	      <!-- <li><a href="javascript:void(0);" onclick="openItem(0,'/manager.php?s=')"> 排班管理</a></li>
	      <li><a href="javascript:void(0);" onclick="openItem(26,27,'/manager.php?s=/Schedules/schedulesList')">排班设置</a></li> -->
	   </ul>
	</div>
	<div class="smart-widget" style="margin-bottom: 1px;">
        <div class="smart-widget-inner">
            <div class="smart-widget-body">
            	<div class="header-text">
					班组管理
				</div>
				<div class="info-line padding-md">
					<a href="javascript:schedulesAdd();" class="btn btn-sm btn-info">
		                <i class="fa fa-plus"></i>增加班组
		            </a>
		            <div class="searchTop-form form-inline pull-right p-l-10" id="search-form">
	                    <div class="form-group">
	                    	<input type="text" class="form-control input-sm" id="keywords" name="keywords" value="<?php echo ($keywords); ?>" placeholder="根据名称查询">
	                    </div>
						<a id="search" url="/WFGarden/manager.php?s=/Schedules/schedulesList" class="btn btn-sm btn-info">
			                查询
			            </a>
	                </div>
	            </div>
	            <div class="smart-widget-inner">
					<table class="table table-striped table-bordered" id="dataTable">
						<thead>
							<tr>
								<th>班组名称</th>
								<th>考勤时间</th>
								<th>状态</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
							<?php if(is_array($list)): $k = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($k % 2 );++$k;?><tr id="data-<?php echo ($v['schedules_id']); ?>">
									<td><?php echo ($v['schedules_name']); ?></td>
									<td><?php echo ($v['work_day']); ?>&nbsp;&nbsp;|&nbsp;&nbsp;
										<?php if(is_array($v['timeList'])): $kk = 0; $__LIST__ = $v['timeList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($kk % 2 );++$kk; if($vv): echo ($vv['start_time']); ?>-<?php echo ($vv['end_time']); ?>&nbsp;&nbsp;<?php endif; endforeach; endif; else: echo "" ;endif; ?>
									</td>
									<td>
										<?php if($v['is_show'] == 1): ?><span class="label label-success">显示</span>
										<?php else: ?>
											<span class="label label-danger">隐藏</span><?php endif; ?>
									</td>
									<td>
										<a href="javascript:schedulesEdit(<?php echo ($v['schedules_id']); ?>);"><i class="fa fa-pencil-square-o m-right-xs"></i>编辑</a> &nbsp;|&nbsp;
										<a href="javascript:schedulesDelete(<?php echo ($v['schedules_id']); ?>);" style="color: #f03939;"><i class="fa fa-trash-o m-right-xs"></i>删除</a>
									</td>
								</tr><?php endforeach; endif; else: echo "" ;endif; ?>
						</tbody>
					</table>
					<div class="content text-right">
						<ul class="pagination">
	 					</ul>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	//搜索功能
	$("#search").click(function(){
		var url = $(this).attr('url');
        var query  = $('#search-form').find('.input-sm').serialize();
        
        if( url.indexOf('?')>0 ){
            url += '&' + query;
        }else{
            url += '?' + query;
        }
		window.location.href = url;
	});

	function schedulesAdd(){
		DMS.ajaxShow("增加班组","/WFGarden/manager.php?s=/Schedules/schedulesAdd");
	}
	function schedulesEdit(schedules_id){
		DMS.ajaxShow("编辑班组","/WFGarden/manager.php?s=/Schedules/schedulesEdit/schedules_id/"+schedules_id);
	}
	function schedulesDelete(schedules_id){
		DMS.dialog("确定要删除当前班组吗?",function(){
			DMS.ajaxPost("/WFGarden/manager.php?s=/Schedules/schedulesDelete",{schedules_id:schedules_id},function(ret){
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