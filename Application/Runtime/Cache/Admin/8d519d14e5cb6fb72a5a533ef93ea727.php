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

	<style type="text/css">
	/* info信息条 */
	.info{
   	    display: table;
	    width: 100%;
	}
	.info-line-index{
		padding: 10px 16px;
		display: table;
		width: 100%;
	}
	.info-tx{
		width: 70px;
	    height: 70px;
        display: table-cell;
	    vertical-align: middle;
	    line-height: 36px;
	}
	.info-img{
		width: 60px;
		height: 60px;
		border-radius: 50px;
		padding:5px;
	}
	.home-nav-title{
		/* display: block;
	    margin: 6px 0 4px;
	    font-size: 16px;
	    color: #000;
	    overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap; */
		margin: 6px 0 4px;
	    font-size: 16px;
	    color: #000;
	    display: -webkit-box;
	    overflow: hidden;
	    text-overflow: ellipsis;
	    word-wrap: break-word;
	    word-break: break-all;
	    white-space: normal !important;
	    -webkit-line-clamp: 1;
	    -webkit-box-orient: vertical;
	}
	.p-r-15{
		padding-right: 15px;
	}
	.home-sub-title{
		font-size: 14px;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
	.box-home-nav{
		/*padding-left: 10px;*/
	}
	.home-nav-item{
		padding: 20px 16px;
		border-right: 1px solid #f0f0f0;
	}
	.home-nav-item:hover{
	    -webkit-box-shadow: 0 8px 10px 0 rgba(0, 0, 0, .16);
	    box-shadow: 0 8px 10px 0 rgba(0, 0, 0, .16);
	}
	.panel-box{
		padding: 10px;
		border-bottom:1px solid #f0f0f0;
		line-height: 30px;
	}
	.panel-img{
		width: 30px;
		height: 30px;
		border-radius: 50px;
	}
	.panel-tit{
		font-size: 16px;
		line-height: 28px;
		color: #000;
	}

	.hearder-account{
		font-size: 20px;
		padding-left: 7px;
		display: table-cell;
       	vertical-align: middle;
	}
	.hearder-account-sub{
		font-size: 14px;
		padding-top:5px;
		color: #999;
	}
	.main-today-part{
		width:14.2%;
		height: 100px;
		padding: 20px;
		list-style-type:none;
	}
	.main-today-part:hover{
		background: #f5f5f5;
	}
	.part-tit{
		font-size: 10px;
		color: #999;
	}
	.part-num{
		font-size:20px;
		color: #000;
		padding-top: 15px;
	}
	.ture-btn{
	    color: #fff;
	    font-size: 14px;
	    background-color: #019c8a;
	    padding: 5px 10px;
	    margin-left: 10px;
	    border-radius: 5px;
	}
	.ture-btn:hover{
	    color: #fff;
	}
	.smart-widget {
	    background-color: #f5f5f5;
	}
	.smart-widget-inner{
		background-color: #fff;
	}
	.p-b-15{
		padding-bottom: 15px;
	}
	</style>
<div class="padding-md">
	<div class="row m-0">
		<div>
			<ul class="breadcrumb">
				当前位置：
				<li><a href="#"> 主页</a></li>
			</ul>
		</div>
		<div class="col-md-12 p-0 overflow-hidden">
			<div class="smart-widget p-b-15 overflow-hidden">
		        <div class="smart-widget-inner overflow-hidden">
		            <div class="overflow-hidden">
						<div class="col-lg-3 col-md-3 home-nav-item clearfix">
							<div class="pull-left">
								<img src="<?php echo C('ADMIN_IMAGE_PATH');?>/icon/kaoqin.png" class="info-img">
							</div>
							<div class="box-home-nav pull-left">
								<a class="home-nav-title" href="/WFGarden/manager.php?s=/Attendance/attendanceList">考勤记录</a>
								<a class="home-sub-title" href="/WFGarden/manager.php?s=/Attendance/attendanceList/start_time/<?php echo ($newTime); ?>/end_time/<?php echo ($newTime); ?>">今日考勤记录</a>
							</div>
						</div>
						<div class="col-lg-3 col-md-3 home-nav-item clearfix">
							<div class="pull-left">
								<img src="<?php echo C('ADMIN_IMAGE_PATH');?>/icon/yuangong.png" class="info-img">
							</div>
							<div class="box-home-nav pull-left">
								<a class="home-nav-title" href="/WFGarden/manager.php?s=/Member/memberList">员工管理</a>
								<a class="home-sub-title" href="/WFGarden/manager.php?s=/Member/memberAdd">增加新员工</a>
							</div>
						</div>
						<div class="col-lg-3 col-md-3 home-nav-item clearfix">
							<div class="pull-left">
								<img src="<?php echo C('ADMIN_IMAGE_PATH');?>/icon/map.png" class="info-img">
							</div>
							<div class="box-home-nav pull-left">
								<a href="/WFGarden/manager.php?s=/Map/mapMachine" class="home-nav-title">设备位置</a>
								<a href="/WFGarden/manager.php?s=/Map/mapAllFence" class="home-sub-title">电子栅栏</a>
							</div>
						</div>
						<div class="col-lg-3 col-md-3 clearfix home-nav-item">
							<div class="pull-left">
								<img src="<?php echo C('ADMIN_IMAGE_PATH');?>/icon/shebei.png" class="info-img">
							</div>
							<div class="box-home-nav pull-left">
								<a class="home-nav-title" href="/WFGarden/manager.php?s=/Machine/machineList">设备管理</a>
								<a class="home-sub-title" href="/WFGarden/manager.php?s=/Machine/machineList">设备列表</a>
							</div>
						</div>
			        </div>
		        </div>
		    </div>

			<div class="smart-widget p-b-15 overflow-hidden">
		        <div class="smart-widget-inner overflow-hidden">
		            <div class="overflow-hidden">
				        <div class="smart-widget-body">
				        	<div class="header-text">
								今日统计数
							</div>
							<div class="smart-widget-body row">
								<a href="/WFGarden/manager.php?s=/Machine/machineList">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											总设备数
										</div>
										<div class="part-num"><?php echo ((isset($mahcine_count) && ($mahcine_count !== ""))?($mahcine_count):0); ?></div>
									</div>
								</a>
								<a href="/WFGarden/manager.php?s=/Machine/machineOnLineList">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											今日在线设备总数
										</div>
										<div class="part-num"><?php echo ($today_mount_guard_count); ?></div>
									</div>
								</a>
								<a href="/WFGarden/manager.php?s=/Machine/machineList/machine_status/1">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											当前在线设备数
										</div>
										<div class="part-num"><?php echo ((isset($mount_guard_count) && ($mount_guard_count !== ""))?($mount_guard_count):0); ?></div>
									</div>
								</a>
								<a href="/WFGarden/manager.php?s=/Member/memberList">
								<div class="main-today-part col-sm-2">
									<div class="part-tit">
										总员工数
									</div>
									<div class="part-num"><?php echo ((isset($count_member) && ($count_member !== ""))?($count_member):0); ?></div>
								</div>
								</a>
								<a href="/WFGarden/manager.php?s=/Member/memberList/status/1">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											在岗人数
										</div>
										<div class="part-num"><?php echo ((isset($on_guard_count) && ($on_guard_count !== ""))?($on_guard_count):0); ?></div>
									</div>
								</a>
								<a href="/WFGarden/manager.php?s=/Member/memberList/status/3">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											离岗人数
										</div>
										<div class="part-num"><?php echo ((isset($out_guard_count) && ($out_guard_count !== ""))?($out_guard_count):0); ?></div>
									</div>
								</a>
								<a href="/WFGarden/manager.php?s=/Member/memberList/status/2">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											未上岗人数
										</div>
										<div class="part-num"><?php echo ((isset($no_guard_count) && ($no_guard_count !== ""))?($no_guard_count):0); ?></div>
									</div>
								</a>
							</div>
							<div class="smart-widget-body row">
								<div class="main-today-part col-sm-2">
									<div class="part-tit">
										上午考勤统计
									</div>
								</div>
								<a href="/WFGarden/manager.php?s=/Attendance/attendanceList/status/1">
								<div class="main-today-part col-sm-2">
									<div class="part-tit">
										正常上班打卡
									</div>
									<div class="part-num"><?php echo ((isset($am_t_normal_count) && ($am_t_normal_count !== ""))?($am_t_normal_count):0); ?></div>
								</div>
									</a>
								<a href="/WFGarden/manager.php?s=/Attendance/attendanceList/status/2">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											迟到
										</div>
										<div class="part-num"><?php echo ((isset($am_late_count) && ($am_late_count !== ""))?($am_late_count):0); ?></div>
									</div>
								</a>
								<a href="/WFGarden/manager.php?s=/Attendance/attendanceList/status/3">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											旷工
										</div>
										<div class="part-num"><?php echo ((isset($am_absenteeism_count) && ($am_absenteeism_count !== ""))?($am_absenteeism_count):0); ?></div>
									</div>
								</a>
								<a href="/WFGarden/manager.php?s=/Attendance/attendanceList/status/4">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											早退
										</div>
										<div class="part-num"><?php echo ((isset($am_leave_count) && ($am_leave_count !== ""))?($am_leave_count):0); ?></div>
									</div>
								</a>
								<a href="/WFGarden/manager.php?s=/Attendance/attendanceList/status/5">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											正常下班打卡
										</div>
										<div class="part-num"><?php echo ((isset($am_d_normal_count) && ($am_d_normal_count !== ""))?($am_d_normal_count):0); ?></div>
									</div>
								</a>
								<a href="/WFGarden/manager.php?s=/Attendance/attendanceList/status/12">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											异常情况
										</div>
										<div class="part-num"><?php echo ((isset($am_abnormal_count) && ($am_abnormal_count !== ""))?($am_abnormal_count):0); ?></div>
									</div>
								</a>
								<!--<div class="main-today-part col-sm-2">-->
									<!--<div class="part-tit">-->
										<!--未上岗人数-->
									<!--</div>-->
									<!--<div class="part-num"><?php echo ((isset($no_guard_count) && ($no_guard_count !== ""))?($no_guard_count):0); ?></div>-->
								<!--</div>-->
							</div>
							<div class="smart-widget-body row">
								<div class="main-today-part col-sm-2">
									<div class="part-tit">
										下午考勤统计
									</div>
								</div>
								<a href="/WFGarden/manager.php?s=/Attendance/attendanceList/status/6">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											正常上班打卡
										</div>
										<div class="part-num"><?php echo ((isset($pm_t_normal_count) && ($pm_t_normal_count !== ""))?($pm_t_normal_count):0); ?></div>
									</div>
								</a>
								<a href="/WFGarden/manager.php?s=/Attendance/attendanceList/status/7">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											迟到
										</div>
										<div class="part-num"><?php echo ((isset($pm_late_count) && ($pm_late_count !== ""))?($pm_late_count):0); ?></div>
									</div>
								</a>
								<a href="/WFGarden/manager.php?s=/Attendance/attendanceList/status/8">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											旷工
										</div>
										<div class="part-num"><?php echo ((isset($pm_absenteeism_count) && ($pm_absenteeism_count !== ""))?($pm_absenteeism_count):0); ?></div>
									</div>
								</a>
								<a href="/WFGarden/manager.php?s=/Attendance/attendanceList/status/9">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											早退
										</div>
										<div class="part-num"><?php echo ((isset($pm_leave_count) && ($pm_leave_count !== ""))?($pm_leave_count):0); ?></div>
									</div>
								</a>
								<a href="/WFGarden/manager.php?s=/Attendance/attendanceList/status/10">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											正常下班打卡
										</div>
										<div class="part-num"><?php echo ((isset($pm_d_normal_count) && ($pm_d_normal_count !== ""))?($pm_d_normal_count):0); ?></div>
									</div>
								</a>
								<a href="/WFGarden/manager.php?s=/Attendance/attendanceList/status/13">
									<div class="main-today-part col-sm-2">
										<div class="part-tit">
											异常情况
										</div>
										<div class="part-num"><?php echo ((isset($pm_abnormal_count) && ($pm_abnormal_count !== ""))?($pm_abnormal_count):0); ?></div>
									</div>
								</a>
								<!--<div class="main-today-part col-sm-2">-->
								<!--<div class="part-tit">-->
								<!--未上岗人数-->
								<!--</div>-->
								<!--<div class="part-num"><?php echo ((isset($no_guard_count) && ($no_guard_count !== ""))?($no_guard_count):0); ?></div>-->
								<!--</div>-->
							</div>
						</div>
					</div>
		        </div>
		    </div>
	    </div>
	    <!--<div class="col-md-4 p-r-0">-->
			<!--<div class="smart-widget m-b-15">-->
		        <!--<div class="smart-widget-inner">-->
		            <!--<div class="smart-widget-body">-->
		            	<!--<div class="header-text">-->
							<!--官方通知-->
							<!--<a href="/WFGarden/manager.php?s=/Ceshi/notice_list">-->
								<!--<span class="nav-btn pull-right">更多</span>-->
							<!--</a>-->
						<!--</div>-->
						<!--<div class="notice-list">-->
							<!--<ul>-->
								<!--<li class="notice-li">-->
									<!--<a href="/WFGarden/manager.php?s=/Ceshi/notice_detail">-->
										<!--<div class="color999">2018-11-22 18:47</div>-->
										<!--<div class="ellipsis-1 p-t-5">产品升级公告(小单位报价、发票优化)</div>-->
									<!--</a>-->
								<!--</li>-->
								<!--<li class="notice-li">-->
									<!--<a href="/WFGarden/manager.php?s=/Ceshi/notice_detail">-->
										<!--<div class="color999">2018-11-22 18:47</div>-->
										<!--<div class="ellipsis-1 p-t-5">产品升级公告(小单位报价、发票优化)</div>-->
									<!--</a>-->
								<!--</li>-->
								<!--<li class="notice-li">-->
									<!--<a href="/WFGarden/manager.php?s=/Ceshi/notice_detail">-->
										<!--<div class="color999">2018-11-22 18:47</div>-->
										<!--<div class="ellipsis-1 p-t-5">产品升级公告(小单位报价、发票优化)</div>-->
									<!--</a>-->
								<!--</li>-->
								<!--<li class="notice-li">-->
									<!--<a href="/WFGarden/manager.php?s=/Ceshi/notice_detail">-->
										<!--<div class="color999">2018-11-22 18:47</div>-->
										<!--<div class="ellipsis-1 p-t-5">产品升级公告(小单位报价、发票优化)</div>-->
									<!--</a>-->
								<!--</li>-->

							<!--</ul>-->
						<!--</div>-->
			        <!--</div>-->
		        <!--</div>-->
		    <!--</div>-->
		<!--</div>-->
	<!--</div>-->
	<div class="col-md-12 p-0 overflow-hidden">
		<div class="smart-widget p-b-15">
	        <div class="smart-widget-inner">
	            <div class="smart-widget-body">
	            	<div class="header-text">
						<a href="/WFGarden/manager.php?s=/WarningMessage/warningMessageList">告警报表</a>
					</div>
					<div class="smart-widget-inner">
						<div class="smart-widget-hidden-section">
							<ul class="widget-color-list clearfix">
								<li style="background-color:#20232b;" data-color="widget-dark"></li>
								<li style="background-color:#4c5f70;" data-color="widget-dark-blue"></li>
								<li style="background-color:#23b7e5;" data-color="widget-blue"></li>
								<li style="background-color:#2baab1;" data-color="widget-green"></li>
								<li style="background-color:#edbc6c;" data-color="widget-yellow"></li>
								<li style="background-color:#fbc852;" data-color="widget-orange"></li>
								<li style="background-color:#e36159;" data-color="widget-red"></li>
								<li style="background-color:#7266ba;" data-color="widget-purple"></li>
								<li style="background-color:#f5f5f5;" data-color="widget-light-grey"></li>
								<li style="background-color:#fff;" data-color="reset"></li>
							</ul>
						</div>
						<div class="widget-tab clearfix">
							<ul class="tab-bar">
								<li class="active"><a href="#style3Tab1" data-toggle="tab"><i class="fa  fa-bar-chart-o"></i> 今日告警报表</a></li>
								<li class=""><a href="#style3Tab2" data-toggle="tab"><i class="fa  fa-bar-chart-o"></i> 月份告警报表</a></li>
								<li class=""><a href="#style3Tab3" data-toggle="tab"><i class="fa  fa-bar-chart-o"></i> 年份告警报表</a></li>
							</ul>
						</div>
						<!-- <div class="smart-widget-body"> -->
							<div class="tab-content">
								<div class="tab-pane active" id="style3Tab1">
									<!-- <div class="info-line m-t-15">
							            <div class="searchTop-form form-inline pull-right p-l-10" id="search-form">
						                    <div class="form-group">
						                    	<input type="text" name="keywords" placeholder="请输入..." class="form-control input" value="">
						                    </div>
											<a href="javascript:adminAdd();" class="btn btn-sm btn-info">
								                查询
								            </a>
						                </div>
						            </div> -->
									<div class="smart-widget-inner  m-t-15">
										<table class="table table-striped table-bordered" id="dataTable1">
											<thead>
												<tr>
													<th>设备名称</th>
													<th>设备号</th>
													<th>绑定员工姓名</th>
													<th>工号</th>
													<th>职位</th>
													<th>管理人员</th>
													<th>告警时间</th>
													<th>告警类型</th>
												</tr>
											</thead>
											<tbody>
												<?php if($list_today): if(is_array($list_today)): foreach($list_today as $key=>$v): ?><tr >
															<td><?php echo ($v['machine_name']); ?></td>
															<td><?php echo ($v['machine_imei']); ?></td>
															<td><a href="javascript:userInfo('<?php echo ($v["uid"]); ?>');"><?php echo ($v['realname']); ?></a></td>
															<td><?php echo ($v['job_number']); ?></td>
															<td><?php echo (getPositionName($v['position'])); ?></td>
															<td><?php echo (getMemberManager($v['parent_id'])); ?></td>
															<td>
																<?php echo (date("Y-m-d H:i:s",$v['add_time'])); ?>
															</td>
															<td>
																<?php if($v['type'] == 1 ): ?>远离工作区域
																	<?php elseif($v['type'] == 2): ?>
																	<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">远离区域报警</a>
																	<?php elseif($v['type'] == 3): ?>
																	低电量报警
																	<?php elseif($v['type'] == 4): ?>
																	<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">迟到报警</a>
																	<?php elseif($v['type'] == 5): ?>
																	<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">旷工报警</a>
																	<?php elseif($v['type'] == 6): ?>
																	<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">早退报警</a>
																	<?php elseif($v['type'] == 7): ?>
																	<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">静止报警</a><?php endif; ?>
															</td>
														</tr><?php endforeach; endif; ?>
													 <?php else: ?>
								                    <tr>
								                    	<td colspan="8" style="text-align: center;">暂无告警信息</td>
								                    </tr><?php endif; ?>		
											</tbody>
										</table>
										<div class="content text-right">
											<ul class="pagination">
						 					</ul>
										</div>
									</div>
								</div><!-- ./tab-pane -->
								<div class="tab-pane fade" id="style3Tab2">
									<div class="smart-widget-inner  m-t-15">
										<table class="table table-striped table-bordered" id="dataTable2">
											<thead>
												<tr>
													<th>设备名称</th>
													<th>设备号</th>
													<th>负责人</th>
													<th>工号</th>
													<th>职位</th>
													<th>管理人员</th>
													<th>告警时间</th>
													<th>告警类型</th>
												</tr>
											</thead>
											<tbody>
												<?php if($list_thismonth): if(is_array($list_thismonth)): foreach($list_thismonth as $key=>$v): ?><tr >
															<td><?php echo ($v['machine_name']); ?></td>
															<td><?php echo ($v['machine_imei']); ?></td>
															<td><a href="javascript:userInfo('<?php echo ($v["uid"]); ?>');"><?php echo ($v['realname']); ?></a></td>
															<td><?php echo ($v['job_number']); ?></td>
															<td><?php echo (getPositionName($v['position'])); ?></td>
															<td><?php echo (getMemberManager($v['parent_id'])); ?></td>
															<td>
																<?php echo (date("Y-m-d H:i:s",$v['add_time'])); ?>
																
															</td>
															<td>
																<?php if($v['type'] == 1 ): ?>远离工作区域
																	<?php elseif($v['type'] == 2): ?>
																	<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">远离区域报警</a>
																	<?php elseif($v['type'] == 3): ?>
																	低电量报警
																	<?php elseif($v['type'] == 4): ?>
																	<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">迟到报警</a>
																	<?php elseif($v['type'] == 5): ?>
																	<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">旷工报警</a>
																	<?php elseif($v['type'] == 6): ?>
																	<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">早退报警</a>
																	<?php elseif($v['type'] == 7): ?>
																	<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">静止报警</a><?php endif; ?>
															</td>
															
															<!-- <td>20185645559</td>
															<td>李明明</td> -->
															
														</tr><?php endforeach; endif; ?>
													 <?php else: ?>
								                    <tr>
								                    	<td colspan="8" style="text-align: center;">暂无告警信息</td>
								                    </tr><?php endif; ?>		
											</tbody>
										</table>
										<div class="content text-right">
											<ul class="pagination">
						 					</ul>
										</div>
									</div>
								</div><!-- ./tab-pane -->
								<div class="tab-pane fade" id="style3Tab3">
									<div class="smart-widget-inner  m-t-15">
										<table class="table table-striped table-bordered" id="dataTable3">
											<thead>
												<tr>
													<th>设备名称</th>
													<th>设备号</th>
													<th>负责人</th>
													<th>工号</th>
													<th>职位</th>
													<th>管理人员</th>
													<th>告警时间</th>
													<th>告警类型</th>
												</tr>
											</thead>
											<tbody>
												<?php if($list_thisyear): if(is_array($list_thisyear)): foreach($list_thisyear as $key=>$v): ?><tr >
															<td><?php echo ($v['machine_name']); ?></td>
															<td><?php echo ($v['machine_imei']); ?></td>
															<td><a href="javascript:userInfo('<?php echo ($v["uid"]); ?>');"><?php echo ($v['realname']); ?></a></td>
															<td><?php echo ($v['job_number']); ?></td>
															<td><?php echo (getPositionName($v['position'])); ?></td>
															<td><?php echo (getMemberManager($v['parent_id'])); ?></td>
															<td>
																<?php echo (date("Y-m-d H:i:s",$v['add_time'])); ?>
															</td>
															<td>
																<?php if($v['type'] == 1 ): ?>远离工作区域
																	<?php elseif($v['type'] == 2): ?>
																	<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">远离区域报警</a>
																	<?php elseif($v['type'] == 3): ?>
																	低电量报警
																	<?php elseif($v['type'] == 4): ?>
																	<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">迟到报警</a>
																	<?php elseif($v['type'] == 5): ?>
																	<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">旷工报警</a>
																	<?php elseif($v['type'] == 6): ?>
																	<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">早退报警</a>
																	<?php elseif($v['type'] == 7): ?>
																	<a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">静止报警</a><?php endif; ?>
															</td>
															
															<!-- <td>20185645559</td>
															<td>李明明</td> -->
															
														</tr><?php endforeach; endif; ?>
													 <?php else: ?>
								                    <tr>
								                    	<td colspan="8" style="text-align: center;">暂无告警信息</td>
								                    </tr><?php endif; ?>	
											</tbody>
										</table>
										<div class="content text-right">
											<ul class="pagination">
						 					</ul>
										</div>
									</div>
								</div><!-- ./tab-pane -->
							</div><!-- ./tab-content -->
						</div>
					</div>

	            </div>

	        </div>
	    </div>
   	</div>
	<div class="col-md-12 p-0 overflow-hidden">
		<div class="smart-widget p-b-15 overflow-hidden">
			<div class="smart-widget-inner overflow-hidden">
				<div class="overflow-hidden">
					<div class="smart-widget-body">
						<div class="header-text">
							报表统计
						</div>
						<div class="smart-widget-body row">
							<div class="row">
								<div class="col-sm-6">
									<div style="min-height: 450px">
										<div style="width:100%;height:500px;" id="list">

										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div style="min-height: 450px">
										<div style="width:100%;height:500px;" id="list1">

										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
	<script src="<?php echo C('ADMIN_JS_PATH');?>/echarts.js"></script>
<script type="text/javascript">
	var scrollStatus = true;
	$(window).scroll(function(){
		//scrollTop是浏览器滚动条的top位置，  
		var scrollTop=document.body.scrollTop||document.documentElement.scrollTop;
		//通过判断滚动条的top位置与可视网页之和与整个网页的高度是否相等来决定是否加载内容；  
		if(scrollTop>300 && scrollStatus){
			scrollStatus = false;
			getLineCount();
			getTodayLineCount();
		}
	})
	function getLineCount(){
		$.post("/WFGarden/manager.php?s=/Index/getLineCount",{},function(ret){
			if(ret.status == 1){
				var myChart = echarts.init(document.getElementById('list'));
				myChart.innerHtml = "";
				option = {
					title: {
						text: '本周考勤人数变化',
					},
					tooltip: {
						trigger: 'axis'
					},
					legend: {
						data:['上岗人数','未上岗人数']
					},
					toolbox: {
						show: true,
						feature: {
							dataZoom: {
								yAxisIndex: 'none'
							},
							dataView: {readOnly: false},
							magicType: {type: ['line', 'bar']},
							restore: {},
							saveAsImage: {}
						}
					},
					color:['#45b97c','#c23531'],
					xAxis:  {
						type: 'category',
						boundaryGap: false,
						data: ['周一','周二','周三','周四','周五','周六','周日']
					},
					yAxis: {
						type: 'value',
						minInterval: 1,
						axisLabel: {
							formatter: '{value} 人'
						}
					},
					series: [
						{
							name:'上岗人数',
							type:'line',
							data:ret.yes_count,
							markPoint: {
								data: [
									{type: 'max', name: '最大值'},
									{type: 'min', name: '最小值'}
								]
							}
						},
						{
							name:'未上岗人数',
							type:'line',
							data:ret.no_count,
							markPoint: {
								data: [
									{type: 'max', name: '最大值'},
									{type: 'min', name: '最小值'}
								]
							},
						}
					]
				};
				myChart.setOption(option);
			}
		})
	}
	function getTodayLineCount(){
		$.post("/WFGarden/manager.php?s=/Index/getTodayLineCount",{},function(ret){
			if(ret.status == 1){
				var myChart1 = echarts.init(document.getElementById('list1'));
				option1 = {
					title : {
						text: '今日上岗情况分析',
						x:'center'
					},
					tooltip : {
						trigger: 'item',
						formatter: "{a} <br/>{b} : {c} ({d}%)"
					},
					legend: {
						orient: 'vertical',
						left: 'left',
						data: ['在岗人数','离岗人数','未上岗人数']
					},
					color:['#45b97c','#ffd400','#c23531'],
					series : [
						{
							name: '人数统计',
							type: 'pie',
							radius : '55%',
							center: ['50%', '60%'],
							data:[
								{value:ret.yes_count, name:'在岗人数'},
								{value:ret.off_count, name:'离岗人数'},
								{value:ret.no_count, name:'未上岗人数'},
							],
							itemStyle: {
								emphasis: {
									shadowBlur: 10,
									shadowOffsetX: 0,
									shadowColor: 'rgba(0, 0, 0, 0.5)'
								}
							}
						}
					]
				};
				myChart1.setOption(option1);
			}
		})
	}
	function userInfo(userid){
        layer.open({
            type: 2,
            title:"员工详情",
            shadeClose: true,
            shade: 0.2,
            scrollbar: false,
            area: ['800px', '400px'],
            content: "/WFGarden/manager.php?s=/Member/memberInfo/userid/"+userid
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