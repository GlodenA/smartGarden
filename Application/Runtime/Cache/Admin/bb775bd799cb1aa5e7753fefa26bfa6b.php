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

<div class="padding-md" id="GROUPMANAGE">
	<div class="smart-widget" style="margin-bottom: 1px;">
		<div class="smart-widget-inner">
			<div class="smart-widget-body">
				<el-breadcrumb separator="/">
					<el-breadcrumb-item>
						<a href="">首页</a>
					</el-breadcrumb-item>
					<el-breadcrumb-item>
						班组管理
					</el-breadcrumb-item>
					<el-breadcrumb-item>
						班组设置
					</el-breadcrumb-item>
				</el-breadcrumb>
				<el-divider></el-divider>
				<div class="flex justify-between items-center px2 mb2">
					<el-button icon="el-icon-plus" type="primary" @click="create.dialog = true">
						增加班组
					</el-button>
					<div class="row items-center">
						<el-input placeholder="请输入区域名称" class="mr2"></el-input>
						<el-button type="primary">
							查询
						</el-button>
					</div>
				</div>
				<el-table :data="tableData">
					<el-table-column prop="areaName" label="区域名称"></el-table-column>
					<el-table-column prop="addTime" label="添加时间">
						<template #default="{ row }">
							{{ row.addTime.weekArr.join(' ') }} | {{ row.addTime.timeDuration.map(({ start, end }) => `${start}-${end}`).join(' ') }}
						</template>
					</el-table-column>
					<el-table-column prop="status" label="状态">
						<template #default="{ row }">
							<el-tag :type="row.status === 1 ? 'primary' : 'danger'">
								{{ row.status === 1 ? '显示' : '隐藏' }}
							</el-tag>
						</template>
					</el-table-column>
					<el-table-column prop="rowOperations" label="操作">
						<template #default="{ row }">
							<el-button type="primary" plain size="mini" @click="() => {
								edit.formData = {
									name: row.areaName,
									weeks: row.addTime.weekArr,
									timeFields: [],
									status: row.status
								}
								edit.dialog = true
							}">
								编辑
							</el-button>
							<el-button type="danger" plain size="mini" @click="showDelDialog(row)">
								删除
							</el-button>
						</template>
					</el-table-column>
				</el-table>
				<div class="flex justify-end mt4">
					<el-pagination background layout="prev, pager, next" :total="totalNumber" :current-page="queryCondition.page" @current-change="pageChange">
					</el-pagination>
				</div>
			</div>
		</div>
	</div>
	<el-dialog :visible.sync="create.dialog" title="添加班组" width="660px">
		<el-form label-width="120px" :model="create.formData" :rules="create.formDataRules" ref="createForm">
			<el-form-item label="班组名称" prop="name">
				<el-input v-model="create.formData.name"></el-input>
			</el-form-item>
			<el-form-item prop="weeks" label="工作日设置">
				<el-checkbox-group v-model="create.formData.weeks" size="small">
					<el-checkbox-button v-for="w in create.weeks" :label="w + 1" :key="">
						{{ w }}
					</el-checkbox-button>
				</el-checkbox-group>
			</el-form-item>
			<el-form-item label="时间段设置" prop="timeFields">
				<el-checkbox-group v-model="create.formData.timeFields">
					<el-checkbox v-for="(t, i) in create.times" :label="i" :key="t">
						{{ t }}
					</el-checkbox>
				</el-checkbox-group>
			</el-form-item>
			<el-form-item label="状态" prop="status">
				<el-radio v-model="create.formData.status" :label="1">显示</el-radio>
				<el-radio v-model="create.formData.status" :label="2">隐藏</el-radio>
			</el-form-item>
		</el-form>
		<div slot="footer" class=" flex justify-center">
			<el-button @click="() => {
				let f = $refs.createForm
				if(f){
					f.resetFields()
				}
			}">
				重置
			</el-button>
			<el-button type="primary" class="ml2" @click="submitCreate">
				提交
			</el-button>
		</div>
	</el-dialog>
	<el-dialog :visible.sync="edit.dialog" title="编辑班组信息" width="660px">
		<el-form label-width="120px" :model="create.formData" :rules="edit.formDataRules" ref="editForm">
			<el-form-item label="班组名称" prop="name">
				<el-input v-model="edit.formData.name"></el-input>
			</el-form-item>
			<el-form-item prop="weeks" label="工作日设置">
				<el-checkbox-group v-model="edit.formData.weeks" size="small">
					<el-checkbox-button v-for="w in create.weeks" :label="w" :key="">
						{{ w }}
					</el-checkbox-button>
				</el-checkbox-group>
			</el-form-item>
			<el-form-item label="时间段设置" prop="timeFields">
				<el-checkbox-group v-model="edit.formData.timeFields">
					<el-checkbox v-for="(t, i) in create.times" :label="i" :key="t">
						{{ t }}
					</el-checkbox>
				</el-checkbox-group>
			</el-form-item>
			<el-form-item label="状态" prop="status">
				<el-radio v-model="edit.formData.status" :label="1">显示</el-radio>
				<el-radio v-model="edit.formData.status" :label="2">隐藏</el-radio>
			</el-form-item>
		</el-form>
		<div slot="footer" class=" flex justify-center">
			<el-button @click="() => {
				let f = $refs.editForm
				if(f){
					f.resetFields()
				}
			}">
				重置
			</el-button>
			<el-button type="primary" class="ml2" @click="submitCreate">
				提交
			</el-button>
		</div>
	</el-dialog>
</div>
<script>
	const GROUPMANAGE = new Vue({
		el: '#GROUPMANAGE',
		data() {
			return {
				edit: {
					dialog: false,
					formData: {
						name: '',
						weeks: [],
						timeFields: [],
						status: 1
					},
				},
				create: {
					weeks: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
					times: [
						'冬季上午班(07:00:00-11:00:00)',
						'冬季下午班(13:00:00-17:00:00)'
					],
					dialog: false,
					formDataRules: {

					},
					formData: {
						name: '',
						weeks: [],
						timeFields: [],
						status: 1
					}
				},
				totalNumber: 1000,
				queryCondition: {
					keyword: '',
					page: 1
				},
				tableData: [{
					areaName: '北海长夜',
					addTime: {
						weekArr: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
						timeDuration: [{
							start: '22:00:00',
							end: '07:00:00'
						}]
					},
					status: 1
				}, {
					areaName: '保安中班',
					addTime: {
						weekArr: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
						timeDuration: [{
							start: '07:30:00',
							end: '11:30:00'
						}, {
							start: '13:30:00',
							end: '17:30:00'
						}]
					},
					status: 2
				}, {
					areaName: '秋季班',
					addTime: {
						weekArr: ['周一', '周二', '周三', '周四', '周五', '周六'],
						timeDuration: [{
							start: '07:00:00',
							end: '11:00:00'
						}, {
							start: '14:00:00',
							end: '18:00:00'
						}]
					},
					status: 1
				}]
			}
		},
		methods: {
			// 展示删除弹窗
			showDelDialog(row) {
				this.$confirm(`确认删除${ row.areaName }吗?`, '提示', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					// 执行删除逻辑
					this.$message({
						type: 'success',
						message: '删除成功!',
						showClose: true
					})
				}).catch(() => {})
			},
			// 提交创建弹窗的数据
			submitCreate() {

			},
			pageChange(p) {
				this.queryCondition.page = p
				// 这里进行分页查询
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