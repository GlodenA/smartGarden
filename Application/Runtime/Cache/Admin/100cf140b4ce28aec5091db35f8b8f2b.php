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

<div id="LOADINGOVERLAY" v-loading.fullscreen.lock="globalLoading"></div>
<script type="text/javascript">
  window.LOADINGOVERLAY = new Vue({
    el: '#LOADINGOVERLAY',
    data(){
      return {
        globalLoading: false
      }
    },
    methods: {
      startLoading(){
        this.globalLoading = true
      },
      endLoading(){
        this.globalLoading = false
      }
    }
  })
</script>
<style>
.custom-btn{
	width: 48px;
	height: 48px;
	background-color: white;
	color: #333;
	cursor: pointer;
	border-radius: 50%;
	transition: all ease-in-out .2s;
	display: flex;
	align-items: center;
	justify-content: center;
}
.custom-btn.active{
	background-color: #33D792;
	color: #fff;
}
</style>
<div class="padding-md" id="AREALIST">
	<div class="smart-widget" style="margin-bottom: 1px;">
		<div class="smart-widget-inner">
			<div class="smart-widget-body" v-show="rendered">
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
					<el-button icon="el-icon-plus" type="primary" @click="addMap">
						区域添加
					</el-button>
					<div class="row items-center pr2">
						<el-input v-model="queryCondition.keyword" class="mr2">
						</el-input>
						<el-button type="primary" @click="keywordChange()">搜索</el-button>
					</div>
				</div>
				<el-table :data="tableData">
					<el-table-column prop="area_name" label="区域名称"></el-table-column>
					<el-table-column prop="add_time" label="添加时间"></el-table-column>
					<el-table-column prop="is_show" label="状态">
						<template #default="{ row }">
							<el-tag :type="row.is_show === '1' ? 'primary' : 'danger'">
								{{ row.is_show === '1' ? '显示' : '隐藏' }}
							</el-tag>
						</template>
					</el-table-column>
					<el-table-column label="操作">
						<template #default="{ row }">
							<el-link type="primary" @click="showBindDeviceDialog(row)">设备绑定列表</el-link>
							<el-link type="primary" class="mx2" @click="showDetailMap(row)">详情</el-link>
							<el-link type="primary" class="mx2" @click="editMap(row)">编辑</el-link>
							<el-link type="danger" @click="deleteMap(row)">删除</el-link>
						</template>
					</el-table-column>
				</el-table>
				<div class="row justify-end mt3">
					<el-pagination background layout="prev, pager, next"
								   :total="totalNumber"
								   :page-size="10"
								   :current-page="queryCondition.page"
								   @current-change="pageChange">
					</el-pagination>
				</div>
			</div>
		</div>
	</div>
	<el-dialog :visible.sync="create.dialog" :title="create.title">
		<div class="flex justify-center">
			<el-form inline :model="create.formData" :rules="create.formRules">
				<el-form-item label="区域名称" prop="area_name">
					<el-input v-model="create.formData.area_name"></el-input>
				</el-form-item>
				<el-form-item label="区域划分" prop="area">
					<el-button type="primary" @click="points = []">
						重新绘制
					</el-button>
				</el-form-item>
			</el-form>
		</div>
		<baidu-map style="width:100%;height:300px;" :center="create.mapData.center"
			:zoom="create.mapData.zoom" :scroll-wheel-zoom="true" :dragging="create.moveStatus"
			@click="({ point }) => {
				if(create.editStatus){
					points.push(point)
				}
			}"
			@ready="mapReady">
			<bm-control anchor="BMAP_ANCHOR_TOP_RIGHT" :offset="{
				width: 20,
				height: 20
			}" v-show="mapEditable">
				<div class="flex items-center">
					<el-tooltip content="画多边形">
						<div :class="['custom-btn', {'active': create.editStatus}]" @click="drawChange">
							<icon class="el-icon-crop"></icon>
						</div>
					</el-tooltip>
					<el-tooltip content="拖动" class="ml2">
						<div :class="['custom-btn', {'active': create.moveStatus}]" @click="moveChange">
							<icon class="el-icon-aim"></icon>
						</div>
					</el-tooltip>
				</div>
			</bm-control>
			<bm-polygon
					v-if="create.editStatus"
				:path="points"
				stroke-color="blue"
				:stroke-opacity="1"
				:stroke-weight="2"
				:editing="true"
				fillColor= "rgb(246,246,246)"
				@lineupdate="({ target }) => { points = target.getPath() }"
				></bm-polygon>
			<bm-polygon
					v-else
					:path="points"
					stroke-color="blue"
					:stroke-opacity="1"
					:stroke-weight="2"
					:editing="false"
					@lineupdate="({ target }) => { points = target.getPath() }"
			></bm-polygon>
		</baidu-map>
		<div slot="footer" class="row justify-center">
			<el-button type="primary" @click="submitMap">
				提交
			</el-button>
		</div>
	</el-dialog>

	<el-dialog :visible.sync="bindDevice.dialog" title="绑定设备" width="850px">
		<div>
			<el-button type="primary" icon="el-icon-plus" @click="bindDevice.add.dialog = true">
				添加设备
			</el-button>
		</div>
		<el-table :data="bindDevice.tableData">
			<el-table-column prop="deviceNumber" label="设备号" show-overflow-tooltip></el-table-column>
			<el-table-column prop="name" label="设备名称"></el-table-column>
			<el-table-column prop="centerNumber" label="中心号码"></el-table-column>
			<el-table-column prop="status" label="设备状态"></el-table-column>
			<el-table-column prop="job_number" label="工号"></el-table-column>
			<el-table-column prop="parent_name" label="负责人"></el-table-column>
			<el-table-column label="操作">
				<template #default="{ row }">
					<el-button type="danger" size="mini">
						解绑设备
					</el-button>
				</template>
			</el-table-column>
		</el-table>
	</el-dialog>

	<el-dialog :visible.sync="bindDevice.add.dialog" title="添加绑定设备" width="560px">
		<el-form label-width="100px">
			<el-form-item label="区域名称">
				<el-input v-model="bindDevice.targetArea.area_name" disabled></el-input>
			</el-form-item>
			<el-form-item label="设备IMEI">
				<el-input v-model="bindDevice.add.deviceIMEI">
					<el-button slot="suffix" type="primary" size="small">
						查询
					</el-button>
				</el-input>
			</el-form-item>
			<div class="">
				<el-divider content-position="left">设备情况</el-divider>
				<el-form-item label="设备名称">
					<el-input disabled v-model="bindDevice.add.name"></el-input>
				</el-form-item>
				<el-form-item label="状态">
					<el-input disabled v-model="bindDevice.add.status"></el-input>
				</el-form-item>
			</div>
		</el-form>
		<div class="flex justify-end" slot="footer">
			<el-button type="primary">
				绑定
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
				rendered: false,
				mapEditable: true,
				mlnglat:[],
				points: [],
				BMap: null,
				map: null,
				create: {
					dialog: false,
					operType:'',
					title:'',
					mapData: {
						// center: {
						// 	lng: 116.404,
						// 	lat: 39.915
						// },
						// zoom: 15
					},
					formRules: {

					},
					formData: {
						area_name: '',
						employee_num:'',
						id:'',
						area: '',
						coordinate:''
					},
					editStatus:false,
					moveStatus:false
				},
				totalNumber: 0,
				queryCondition: {
					page: 1,
					keyword: ''
				},
				tableData: [
				],
				bindDevice: {
					targetArea: {
						area_name: '',
						id: ''
					},
					add: {
						dialog: false,
						deviceIMEI: '',
						deviceInfo: {
							name: '设备1293',
							status: '该设备暂未绑定'
						}
					},
					dialog: false,
					tableData: [{
						deviceNumber: '092384209384092',
						name: '设备3982',
						centerNumber: '013120',
						status: 1,
						job_number: '9812',
						parent_name: '马喜忠'
					}]
				}
			}
		},
		created(){
			this.doQuery()
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
			drawChange(){
                  if(this.create.editStatus){
					  this.create.editStatus=false
				  }
                  else {
					  this.create.editStatus=true
				  }
			},
			moveChange(){
				this.create.moveStatus = !this.create.moveStatus
			},
			addMap(){
				this.create.title='区域添加'
				this.mapEditable = true
				this.points=[]
				this.create.moveStatus=false
				this.create.editStatus=false
				this.create.dialog = true
				this.create.operType=true
			},
			editMap(row){
				this.create.title='区域编辑'
				this.mapEditable = true
				this.create.moveStatus=false
				this.create.editStatus=false
				this.create.formData.area_name = row.area_name
				this.create.formData.id=row.id
				this.points = row.coordinate.map(({ lat, lon }) => ({
					lat,
					lng: lon
				}))
				this.create.operType=false
				this.create.dialog=true

			},
			showDetailMap(row){
				this.create.title='区域详情'
				this.mapEditable = false
				this.create.moveStatus=true
				this.create.editStatus=false
				this.create.formData.area_name = row.area_name
				this.points = row.coordinate.map(({ lat, lon }) => ({
					lat,
					lng: lon
				}))
				this.create.dialog=true
			},
			submitMap(){
				var path = this.points;
				for (var i = 0; i < path.length; i++) {
					this.mlnglat.push('{"lon":' + path[i].lng + ',"lat":' + path[i].lat+'}');
				}
                if(this.create.operType)
				{
                  //添加区域
					var coordinate = this.mlnglat.toString();

					var parm = {
						'area_name':this.create.formData.area_name,
						'coordinate':coordinate,
						'employee_num':'',
					}
					DMS.ajaxPost('/smartGarden/manager.php?s=/Map/mapFence',parm, ret=> {

						if (ret.status == 1) {
							this.$alert('添加成功！', '通知', {
								confirmButtonText: '确定',
								callback: action => {
									this.create.dialog = false
									window.location.reload()
								}
							})
						} else {
							this.$alert('添加失败！失败原因：'+res.info, '通知', {
								confirmButtonText: '确定',
								callback: action => {
									this.create.dialog = false
									window.location.reload()
								}
							})
						}
					})
				}
                else {

					//编辑区域
					if(this.mlnglat.length == 0){
						var type = 1;
					}else{
						var coordinate = this.mlnglat.toString();
						var type = 2;
					}
					var parm = {
						'area_name':this.create.formData.area_name,
						'employee_num':this.create.formData.employee_num,
						'coordinate':coordinate,
						'id':this.create.formData.id,
						'type':type,
					}
					DMS.ajaxPost('/smartGarden/manager.php?s=/Map/mapEdit',parm, ret=> {

						if (ret.status == 1) {
							this.$alert('编辑成功！', '通知', {
								confirmButtonText: '确定',
								callback: action => {
									this.create.dialog = false
									window.location.reload()
								}
							})
						} else {
							this.$alert('编辑失败！失败原因：'+res.info, '通知', {
								confirmButtonText: '确定',
								callback: action => {
									this.create.dialog = false
									window.location.reload()
								}
							})
						}
					})
				}

			},
			deleteMap(row){
				this.$confirm(`确认删除${ row.area_name }区域吗?`, '提示', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					// 执行删除逻辑
					DMS.ajaxPost("/smartGarden/manager.php?s=/Map/areaDelete",{
						id: row.id
					}, res => {
						if(res.status === 1){
							this.$message({
								type: 'success',
								message: '删除成功!',
								showClose: true

							})
							window.location.reload()
						}else{
							this.$message.error('删除失败！失败原因：'+res.info);

						}
					})

				}).catch(() => {})
			},
			doQuery(){
				DMS.ajaxPost('/smartGarden/manager.php?s=/Map/getAreaList',{
					page: this.queryCondition.page,
					keywords: this.queryCondition.keyword
				},res=>{
					this.tableData=res.areaList
					this.totalNumber = res.totalNumber*1
					this.create.mapData = res.mapData
				})
			},
			pageChange(p) {
				this.queryCondition.page = p
				this.doQuery()
			},
			keywordChange(){
				this.queryCondition.page = 1
				this.doQuery()
			},
			showBindDeviceDialog(row){
				Object.keys(this.bindDevice.targetArea).forEach(f => {
					this.bindDevice.targetArea[f] = row[f]
				})
				this.bindDevice.dialog = true
			}
		},
		mounted(){
			this.rendered = true
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