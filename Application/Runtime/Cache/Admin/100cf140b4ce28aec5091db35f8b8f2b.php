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
      <link rel="stylesheet" href="/Public/Admin/Css//util/flex.css">
      <link href="https://unpkg.com/basscss@8.0.2/css/basscss.min.css" rel="stylesheet">
      <script src="https://cdn.jsdelivr.net/npm/vue@2.6.0"></script>
      <link rel="stylesheet" href="/Public/Admin/element/index.css">
      <script src="https://unpkg.com/element-ui/lib/index.js"></script>
      <!-- Vue, element, 间距工具类 相关 -->
    </head>
    <body class="overflow-hidden">

<div class="padding-md" id="AREALIST">
	<div class="smart-widget" style="margin-bottom: 1px;">
		<div class="smart-widget-inner">
			<div class="smart-widget-body">
				<el-breadcrumb separator="/">
					<el-breadcrumb-item>
						<a href="">首页</a>
					</el-breadcrumb-item>
					<el-breadcrumb-item>
						区域管理
					</el-breadcrumb-item>
					<el-breadcrumb-item>
						区域列表
					</el-breadcrumb-item>
				</el-breadcrumb>
				<el-divider></el-divider>
				<div class="flex justify-between mb3">
					<el-button icon="el-icon-plus" type="primary" @click="create.dialog = true">
						区域添加
					</el-button>
					<div class="row items-center pr2">
						<el-input v-model="queryCondition.keyword" class="mr2">
						</el-input>
						<el-button type="primary">搜索</el-button>
					</div>
				</div>
				<el-table :data="tableData">
					<el-table-column prop="name" label="区域名称"></el-table-column>
					<el-table-column prop="addTime" label="添加时间"></el-table-column>
					<el-table-column prop="status" label="状态">
						<template #default="{ row }">
							<el-tag :type="row.status === 1 ? 'primary' : 'danger'">
								{{ row.status === 1 ? '显示' : '隐藏' }}
							</el-tag>
						</template>
					</el-table-column>
					<el-table-column label="操作">
						<template #default="{ row }">
							<el-link type="primary">设备绑定列表</el-link>
							<el-link type="primary" class="mx2">详情</el-link>
							<el-link type="danger">删除</el-link>
						</template>
					</el-table-column>
				</el-table>
				<div class="row justify-end mt3">
					<el-pagination background layout="prev, pager, next" :total="totalNumber" :current-page="queryCondition.page" @current-change="pageChange">
					</el-pagination>
				</div>
			</div>
		</div>
	</div>
	<el-dialog :visible.sync="create.dialog" title="区域添加">
		<div class="flex justify-center">
			<el-form inline :model="create.formData" :rules="create.formRules">
				<el-form-item label="区域名称" prop="name">
					<el-input v-model="create.formData.name"></el-input>
				</el-form-item>
				<el-form-item label="区域划分" prop="area">
					<el-button type="primary" @click="points = []">
						重新绘制
					</el-button>
				</el-form-item>
			</el-form>
		</div>
		<baidu-map style="width:100%;height:300px;" :center="create.mapData.center"
			:zoom="create.mapData.zoom"
			@click="({ point }) => { points.push(point) }"
			@ready="mapReady">
			<bm-control anchor="BMAP_ANCHOR_TOP_RIGHT" :offset="{
				width: 20,
				height: 20
			}">
				<el-button icon="el-icon-crop" round>

				</el-button>
				<el-button icon="el-icon-aim" round>

				</el-button>
			</bm-control>
			<bm-polygon
				:path="points"
				stroke-color="blue"
				:stroke-opacity="1"
				:stroke-weight="2"
				:editing="true"
				@lineupdate="({ target }) => { points = target.getPath() }"
				></bm-polygon>
		</baidu-map>
		<div slot="footer" class="row justify-center">
			<el-button type="primary">
				提交
			</el-button>
		</div>
	</el-dialog>
</div>
<script src="https://unpkg.com/vue-baidu-map"></script>
<script type="text/javascript">
	Vue.use(VueBaiduMap.default, {
		ak: 'NENpvHSwTNZ6ftZOKdfiiPDxGKKPHjtg'
	})
	const AREALIST = new Vue({
		el: '#AREALIST',
		data() {
			return {
				points: [],
				BMap: null,
				map: null,
				create: {
					dialog: false,
					mapData: {
						center: {
							lng: 116.404,
							lat: 39.915
						},
						zoom: 15
					},
					formRules: {

					},
					formData: {
						name: '',
						area: ''
					}
				},
				totalNumber: 1000,
				queryCondition: {
					page: 1,
					keyword: ''
				},
				tableData: [{
					name: '马喜忠负责区域',
					addTime: '2019-11-18 14:42:23',
					status: 1,
				}, {
					name: '马喜忠负责区域',
					addTime: '2019-11-18 14:42:23',
					status: 2,
				}, {
					name: '马喜忠负责区域',
					addTime: '2019-11-18 14:42:23',
					status: 1
				}],
			}
		},
		methods: {
			mapReady({
				BMap,
				map
			}) {
				[
					this.BMap,
					this.map
				] = [
					BMap,
					map
				]
			},
			pageChange(p) {
				this.queryCondition.page = p
			}
		},
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