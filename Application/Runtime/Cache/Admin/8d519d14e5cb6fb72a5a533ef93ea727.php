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

<style type="text/css">
	#MAIN {
		color: #000;
	}

	#MAIN .panel-card {
		cursor: pointer;
		border-radius: 20px;
		background-color: #efefef;
	}

	#MAIN .panel-card:hover {
		background-color: #33D792;
		color: white;
	}

	#MAIN .panel-card .card-icon{
		color: #33D792;
		font-size: 32px;
	}

	#MAIN .panel-card:hover .card-icon{
		color: white;
	}

	#MAIN .panel-card p{
		color: #999;
	}

	#MAIN .panel-card:hover p{
		color: inherit;
	}

	#MAIN .real-time-monitor p{
		color: #999;
	}

	#MAIN .real-time-monitor h1{
		font-size: 56px;
	}

	#MAIN .real-time-monitor .add-btn{
		border: 1px dashed #ccc;
		border-radius: 3px;
		cursor: pointer;
	}

	#MAIN .real-time-monitor .add-btn:hover{
		border-color: #33D792;
	}

	#MAIN .real-time-monitor .add-btn i{
		font-size: 32px;
		color: #33D792;
	}

	#MAIN .real-time-selector{
		border: 1px solid #ddd;
		color: #999;
		padding: 12px 0;
		text-align: center;
		cursor: pointer;
		transition: all ease-in-out .2s;
		position: relative;
	}

	#MAIN .real-time-selector:hover:not(.selected),
	#MAIN .real-time-selector.selected{
		border-color: #33D792;
	}

	#MAIN .real-time-selector .checked{
		width: 20px;
		height: 18px;
		position: absolute;
		bottom: 0;
		right: 0;
		background-color: #33D792;
		color: white;
	}
</style>

<div id="MAIN" class="p3 bg-white">
	<div class="p2 bold h2 mt0">
  	常用菜单
  </div>
	<el-row :gutter="20" >
		<el-col :span="6" v-for="([icon, title, subtitle], i) in [
			['date', '考勤记录', '今日考勤记录'],
			['s-custom', '员工管理', '员工列表'],
			['map-location', '设备位置', '电子栅栏'],
			['mobile-phone', '设备管理', '设备列表']
		]" :key="i">
			<el-card class="panel-card" shadow="hover">
				<div class="row items-center justify-center">
					<i :class="[`el-icon-${icon}`, 'card-icon']"></i>
					<div class="ml3">
						<h4>{{ title }}</h4>
						<p>{{ subtitle }}</p>
					</div>
				</div>
			</el-card>
		</el-col>
	</el-row>
	<el-divider></el-divider>
	<div class="p2 bold h2">
  	实时监控
  </div>
	<el-row :gutter="20" class="px4 real-time-monitor flex items-stretch py2">
		<el-col :span="6" v-for="(n, i) in realTimeMonitorData" :key="i" :class="['column', 'items-center', realTimeMonitorColor[i]]">
			<p>{{ realTimeMonitorDataLabelArr[i] }}</p>
			<h1 class="m0">
				{{ n }}
			</h1>
		</el-col>
		<el-col :span="6" class="add-btn flex justify-center items-center" @click.native="realTimeDialog = true">
			<i class="el-icon-plus"></i>
		</el-col>
	</el-row>
	<el-divider></el-divider>
	<div class="flex justify-between items-center">
		<div class="p2 bold h2">
			异常警告
		</div>
		<el-button icon="el-icon-right" plain type="primary">
			查看更多
		</el-button>
	</div>
	<el-table :data="tableData" style="width: 100%">
		<template  v-for="({ prop, label }, i) in tableColumns">
			<el-table-column :key="i" :prop="prop" :label="label" v-if="prop !== 'warningType'"></el-table-column>
			<el-table-column :key="i" v-else :prop="prop" :label="label">
				<template #default="{ row}">
					<el-tag type="danger">
						{{ row.warningType }}
					</el-tag>
				</template>
			</el-table-column>
		</template>
 </el-table>
 <el-dialog
	title="实时监控"
	:visible.sync="realTimeDialog"
	width="50%">
	<el-row :gutter="20">
		<el-col :span="6" v-for="({ label, selected }, i) in realTimeMenusWithSelectedStates" class="mb2">
			<div :class="['real-time-selector', { 'selected': selected}]" @click="() => toggleRealTimeMenuSelectedState(label, selected)">
				{{ label }}
				<div class="checked" v-show="selected">
					<i class="el-icon-check"></i>
				</div>
			</div>
		</el-col>
	</el-row>
  <span slot="footer" class="flex justify-center">
		<el-button type="primary" @click="dialogVisible = false">确 定</el-button>
    <el-button @click="realTimeDialog = false">重置</el-button>
  </span>
</el-dialog>
</div>

<script type="text/javascript">
	const main = new Vue({
		el: '#MAIN',
		data() {
			return {
				selectedRealTimeMenus: [
					'当前在线设备数',
					'迟到',
					'旷工'
				],
				realTimeMenus: [
					'总设备数',
					'今日上线设备总数',
					'当前在线设备数',
					'员工总数',
					'在岗人数',
					'离岗人数',
					'未上岗人数',
					'迟到',
					'上午考勤统计',
					'下午考勤统计',
					'正常上班打卡',
					'正常下班打卡',
					'旷工',
					'早退',
					'异常情况'
				],
				realTimeDialog: false,
				tableData: Array(10).fill({
					deviceName: 'cyyl_05007',
					deviceNumber: '2783492374893278',
					bindEmployee: '王建国',
					workNumber: '1197',
					position: '养护工人',
					manager: '李香社（金晶大道）',
					warningTime: '2019-11-04 13:53:42',
					warningType: '怠工超过设定时间警告',
				}),
				tableColumns: [
					['设备名称', 'deviceName'],
					['设备号', 'deviceNumber'],
					['绑定员工', 'bindEmployee'],
					['工号', 'workNumber'],
					['职位', 'position'],
					['管理人员', 'manager'],
					['告警时间', 'warningTime'],
					['告警类型', 'warningType'],
				].map(([ label, prop ]) => ({
					label,
					prop
				})),
				realTimeMonitorData: [259, 13, 31],
				realTimeMonitorColor: ['primary', 'warning', 'danger'],
				realTimeMonitorDataLabelArr: ['当前设备在线数', '迟到', '旷工']
			}
		},
		computed: {
			realTimeMenusWithSelectedStates(){
				return this.realTimeMenus.map(label => ({
					label,
					selected: this.selectedRealTimeMenus.some(v => v === label)
				}))
			}
		},
		methods: {
			toggleRealTimeMenuSelectedState(label, state){
				if(state){
					this.selectedRealTimeMenus.splice(this.selectedRealTimeMenus.findIndex(v => v === label), 1)
					return
				}
				this.selectedRealTimeMenus.push(label)
			}
		}
	})
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