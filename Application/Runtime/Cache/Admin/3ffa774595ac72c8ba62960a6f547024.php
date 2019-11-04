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
			<li><a href="/WFGarden/manager.php?s=/Index/main"> 主页</a></li>
			<li><a href="/WFGarden/manager.php?s=/Machine/machineList">设备管理</a></li>
		</ul>
	</div>
	<div class="smart-widget" style="margin-bottom: 1px;">
		<div class="smart-widget-inner">
			<div class="smart-widget-body">
				<div class="header-text">
					设备管理
				</div>
				<div class="info-line">
					<a href="javascript:machineAdd();" class="btn btn-sm btn-info">
						<i class="fa fa-plus"></i>增加设备
					</a>
					<a href="javascript:areaBinds()" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="bottom" title="批量换绑或解除区域" data-original-title="批量换绑或解除区域">换绑或解除区域</a>
					<a href="javascript:manager('changeSchedules')" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="bottom" title="批量切换班组" data-original-title="批量切换班组">切换班组</a>
					<a href="javascript:changeSchedules()" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="bottom" title="一键切换班组" data-original-title="一键切换班组">切换班组</a>
					<a href="javascript:manager('delMachine')" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="bottom" title="批量删除设备" data-original-title="批量删除设备">删除设备</a>
					<form class="searchTop-form form-inline pull-right p-l-10" id="search-form" action="/WFGarden/manager.php?s=/Machine/machineList" method="post">
						<div class="form-group">
							<label for="machine_status">设备状态</label>
							<select class="form-control searchbody input-sm" name="machine_status" id="machine_status">
								<option value="0" <?php if($machine_status == 0): ?>selected<?php endif; ?>>--全部--</option>
								<option value="1" <?php if($machine_status == 1): ?>selected<?php endif; ?>>在线</option>
								<option value="2" <?php if($machine_status == 2): ?>selected<?php endif; ?>>离线</option>
							</select>
						</div>
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
							<label for="keywords">关键字：</label>
							<input type="text" class="form-control input-sm" id="keywords" name="keywords" value="<?php echo ($keywords); ?>" placeholder="根据关键字查询">
						</div>
						<a id="search" url="/WFGarden/manager.php?s=/Machine/machineList" class="btn btn-sm btn-success">搜索</a>
					</form>
					<div class="searchTop-form form-inline pull-right">
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
						<a href="/WFGarden/Public/Admin/File/shebei.xlsx" class="btn btn-sm btn-info form-inline">
							下载模板
						</a>
					</div>
				</div>
				<div class="smart-widget-inner">
					<table class="table table-striped table-bordered" id="dataTable">
						<thead>
						<tr>
							<th>
								<div class="custom-checkbox">
									<input type="checkbox" id="checkall" class="check-all" >
									<label for="checkall"></label>
								</div>
							</th>
							<th>#</th>
							<th>设备号</th>
							<th>设备名称</th>
							<th>设备状态</th>
							<th>电量</th>
							<th>工号</th>
							<th>负责人</th>
							<th>职位</th>
							<th>管理人员</th>
							<th>所属区域名称</th>
							<th>班组</th>
							<!-- <th>添加时间</th> -->
							<th>操作</th>
						</tr>
						</thead>
						<tbody>
						<?php if(is_array($machineList)): foreach($machineList as $k=>$v): ?><tr id="data-<?php echo ($v['machine_id']); ?>">
								<td>
									<div class="custom-checkbox">
										<input type="checkbox" value="<?php echo ($v['machine_id']); ?>" name="machineids[]" id="machineid-<?php echo ($v['machine_id']); ?>" class="ids" >
										<label for="machineid-<?php echo ($v['machine_id']); ?>"></label>
									</div>
								</td>
								<td><?php echo ($k +1 + $number); ?></td>
								<td><?php echo ($v['machine_imei']); ?></td>
								<td><?php echo ($v['machine_name']); ?></td>
								<td><?php echo ($v['machine_status']); ?></td>
								<td><?php echo ($v['electricity']); ?></td>
								<td><?php echo ($v['job_number']); ?></td>
								<td><?php echo ($v['realname']); ?></td>
								<td>
									<?php echo ($v['position_name']); ?>
								</td>
								<td>
									<?php echo ($v['parent_name']); ?>
								</td>
								<td><?php echo ($v['area_name']); ?></td>
								<td>
									<?php echo ($v['schedulesInfo']['schedules_name']); ?>
									<!--&nbsp;&nbsp;-->
									<!--<?php if($v['schedulesInfo']['work_day']): ?>-->
										<!--【<?php echo ($v['schedulesInfo']['work_day']); ?>-->
										<!--<?php if($v['schedulesInfo']['timeList']): ?>-->
											<!--(<?php if(is_array($v["schedulesInfo"]["timeList"])): $kk = 0; $__LIST__ = $v["schedulesInfo"]["timeList"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($kk % 2 );++$kk;?>&nbsp;<?php echo ($vv['start_time']); ?>~<?php echo ($vv['end_time']); ?>&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>)-->
										<!--<?php endif; ?>-->
										<!--】-->
									<!--<?php endif; ?>-->
								</td>
								<!-- <td><?php echo (date("Y-m-d H:i:s",$v['add_time'])); ?></td> -->
								<td class="manager">
									<a href="/WFGarden/manager.php?s=/Machine/machineMap/machine_id/<?php echo ($v['machine_id']); ?>" class="text-success"><i class="fa fa-info-circle m-right-xs"></i>设备位置</a>&nbsp;|&nbsp;
									<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>" class="text-success"><i class="fa fa-info-circle m-right-xs"></i>设备轨迹</a>&nbsp;|&nbsp;
									<a href="/WFGarden/manager.php?s=/Machine/machineArea/machine_id/<?php echo ($v['machine_id']); ?>" class="text-success"><i class="fa fa-info-circle m-right-xs"></i>划分区域</a>&nbsp;|&nbsp;
									<?php if($v['area_id']): ?><a href="/WFGarden/manager.php?s=/Map/areaInfo/id/<?php echo ($v['area_id']); ?>" class="text-success"><i class="fa fa-info-circle m-right-xs"></i>所属区域</a>&nbsp;|&nbsp;
										<a href="javascript:areaUnbind('<?php echo ($v["machine_id"]); ?>');" class="text-danger"><i class="fa fa fa-trash-o m-right-xs"></i>解除区域</a>&nbsp;|&nbsp;
										<?php else: ?>
										<a href="javascript:areaBind('<?php echo ($v["machine_id"]); ?>');" style="cursor: pointer"><i class="fa fa-pencil-square-o m-right-xs"></i>绑定区域</a>&nbsp;|&nbsp;<?php endif; ?>
									<a href="javascript:schedulesBind(<?php echo ($v['machine_id']); ?>);" style="cursor: pointer"><i class="fa fa-pencil-square-o m-right-xs"></i>设置班组</a>&nbsp;|&nbsp;
									<a href="javascript:memberBind(<?php echo ($v['machine_id']); ?>);" style="cursor: pointer"><i class="fa fa-pencil-square-o m-right-xs"></i>绑定员工</a>&nbsp;|&nbsp;
									<!--<a href="/WFGarden/manager.php?s=/Machine/machineInfo/machine_imei/<?php echo ($v['machine_imei']); ?>" class="text-success"><i class="fa fa-info-circle m-right-xs"></i>详情</a>&nbsp;|&nbsp;-->
									<a href="javascript:machineEdit(<?php echo ($v['machine_id']); ?>);" style="cursor: pointer"><i class="fa fa-pencil-square-o m-right-xs"></i>编辑</a>&nbsp;|&nbsp;
									<a style="cursor: pointer" onclick="machineDelete('<?php echo ($v["machine_id"]); ?>')" class="text-danger"><i class="fa fa-trash-o m-right-xs"></i>删除</a>
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
		var machineids='';
		$("input[name='machineids[]']:checked").each(function(i, n){
			machineids += $(n).val() + ',';
		});
		machineids = machineids.substring(0,machineids.length-1);

		if(machineids=='') {
			DMS.alert("请先选择设备")
			return false;
		}else{
			switch (type) {
				case 'unArea':
					var url = "/WFGarden/manager.php?s=/Machine/areaDels";
					break;
				case 'delMachine':
					var url = "/WFGarden/manager.php?s=/Machine/machineDels";
					break;
				case 'changeSchedules':
					var url = "/WFGarden/manager.php?s=/Machine/changeSchedules";
					break;
			}
			if(url){
				if(type == 'changeSchedules'){
					DMS.ajaxShow("批量切换班组",url+"/machineids/"+machineids);
					return;
				}
				DMS.dialog("确定要执行当前操作吗?",function(){
					DMS.ajaxPost(url,{machineids:machineids},function(ret){
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
		DMS.ajaxPost("/WFGarden/manager.php?s=/Machine/importFile",{"filename":excel},function(ret){
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
	function machineAdd(){
		DMS.ajaxShow("新增设备","/WFGarden/manager.php?s=/Machine/machineAdd");
	}
	function machineEdit(id){
		DMS.ajaxShow("设备编辑","/WFGarden/manager.php?s=/Machine/machineEdit/machine_id/"+id);
	}
//	function fnEdit(id){
//		DMS.ajaxShow("设备编辑","/WFGarden/manager.php?s=/Machine/machineEdit/machine_id/"+id);
//	}
	function memberBind(id){
		DMS.ajaxShow("员工绑定","/WFGarden/manager.php?s=/Machine/memberBind/machine_id/"+id);
	}

	function changeSchedules(){
		DMS.ajaxShow("一键切换班组","/WFGarden/manager.php?s=/Machine/changeSchedules");
	}

	function areaBinds(){
		var machineids='';
		$("input[name='machineids[]']:checked").each(function(i, n){
			machineids += $(n).val() + ',';
		});
		machineids = machineids.substring(0,machineids.length-1);
		if(machineids=='') {
			DMS.alert("请先选择设备");
			return false;
		}else{
			DMS.ajaxShow("批量换绑或解除区域","/WFGarden/manager.php?s=/Machine/areaBinds/machineids/"+machineids);
		}

	}

	function schedulesBind(id){
		DMS.ajaxShow("班组设置","/WFGarden/manager.php?s=/Machine/schedulesBind/machine_id/"+id);
	}
	function machineDelete(id){
		DMS.dialog("确定要删除吗?",function(){
			DMS.ajaxPost("/WFGarden/manager.php?s=/Machine/machineDelete",{machine_id:id},function(ret){
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
	function areaUnbind(machine_id){
		DMS.dialog("确定要解除区域吗?",function(){
			DMS.ajaxPost("/WFGarden/manager.php?s=/Machine/areaUnbind",{machine_id:machine_id},function(ret){
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
	function machineLocation(machine_id){
		DMS.dialog("确定要发送定位命令吗?",function(){
			DMS.ajaxPost("/WFGarden/manager.php?s=/Machine/machineLocation",{machine_id:machine_id},function(ret){
				console.log(JSON.stringify(ret));
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
	//绑定区域
	function areaBind(machine_id){
		DMS.ajaxShow("区域绑定","/WFGarden/manager.php?s=/Machine/areaBind/machine_id/"+machine_id);
	}
	//切换班组
	function changeTime(num){
		DMS.dialog("确定要切换班组吗?",function(){
			DMS.ajaxPost("/WFGarden/manager.php?s=/Machine/changeTime",{num:num},function(ret){
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
	function changePosition(obj){
		var option = obj.value;
		var item = document.getElementById("member_manager");
		if(option>0){
			var url = '/WFGarden/manager.php?s=/Member/getMemberManager';
			$.post(url, {'parent_id':option}, function(ret){
				if(ret.status == 1){
//                    item.style.display = "inline-block";
					item.querySelector("select").innerHTML = "";
					item.querySelector("select").innerHTML= '<option value="0">--全部--</option>';
					for(i in ret.data){
						item.querySelector("select").innerHTML += '<option value='+ret.data[i].userid+'<?php if($parent_id == '+ret.data[i].userid+'): ?>selected<?php endif; ?>>'+ret.data[i].realname+'</option>';
					}
				} else{
//                    item.style.display = "none";
					item.querySelector("select").innerHTML = "";
					item.querySelector("select").innerHTML= '<option value="0">--全部--</option>';
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