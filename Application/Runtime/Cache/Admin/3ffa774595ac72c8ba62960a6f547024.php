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

<style media="screen">
	.el-link+.el-link{
		margin-left: 8px;
	}
	input[type=file]{
		display: none;
	}
</style>
<div class="padding-md" id="MACHINELIST">
	<div class="smart-widget" style="margin-bottom: 1px;">
		<div class="smart-widget-inner">
			<div class="smart-widget-body">
				<el-breadcrumb separator="/">
          <el-breadcrumb-item>
            <a href="">首页</a>
          </el-breadcrumb-item>
          <el-breadcrumb-item>
            设备管理
          </el-breadcrumb-item>
          <el-breadcrumb-item>
            设备列表
          </el-breadcrumb-item>
        </el-breadcrumb>
				<el-divider></el-divider>
				<div class="flex px3 justify-between items-start">
					<el-button icon="el-icon-plus" type="primary" @click="create.createDviceDialog = true">
						添加设备
					</el-button>
					<el-form inline>
						<el-form-item label="设备状态">
							<el-select v-model="queryCondition.status" style="width:100px;">
								<el-option v-for="(l, i) in ['全部', '在线', '离线']" :label="l" :key="i" :value="i">
								</el-option>
							</el-select>
						</el-form-item>
						<el-form-item label="职位">
							<el-select v-model="queryCondition.position" style="width:100px;">
								<el-option label="全部" :value="0">
								</el-option>
								<?php if(is_array($positionList)): $i = 0; $__LIST__ = $positionList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pl): $mod = ($i % 2 );++$i;?><el-option label="<?php echo ($pl["name"]); ?>" value="<?php echo ($pl["id"]); ?>">
									</el-option><?php endforeach; endif; else: echo "" ;endif; ?>
							</el-select>
						</el-form-item>
						<el-form-item label="管理人员">
							<el-select v-model="queryCondition.manager" style="width:100px;">
								<el-option label="全部" :value="0">
								</el-option>
								<?php if(is_array($managerList)): $i = 0; $__LIST__ = $managerList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pl): $mod = ($i % 2 );++$i;?><el-option label="<?php echo ($pl["realname"]); ?>" value="<?php echo ($pl["userid"]); ?>">
									</el-option><?php endforeach; endif; else: echo "" ;endif; ?>
							</el-select>
						</el-form-item>
						<el-form-item label="关键字" prop="keyword">
							<el-input v-model="queryCondition.keyword"/>
						</el-form-item>
						<el-form-item>
							<el-button icon="el-icon-search" type="primary">
								查询
							</el-button>
						</el-form-item>
					</el-form>
				</div>
				<el-table :data="tableData" @selection-change="s => batchSelections = s">
					<el-table-column
					type="selection"
					width="55">
				</el-table-column>
				<template v-for="([prop, label], i) in tableColumns">
					<el-table-column :key="prop" :prop="prop" :label="label" v-if="prop === 'serialNumber'">
						<template #default="{ $index }">
							{{ $index + 1 }}
						</template>
					</el-table-column>
					<el-table-column :key="prop" :prop="prop" :label="label" v-else-if="prop === 'deviceStatus'">
						<template #default="{ row }">
							<el-tag v-if="row.deviceStatus === 1" type="primary">在线</el-tag>
							<el-tag v-else type="danger">离线</el-tag>
						</template>
					</el-table-column>
					<el-table-column :key="prop" :prop="prop" :label="label" v-else-if="prop === 'rowOperations'" :width="320">
						<template #default="{ row }">
							<el-link :underline="false" type="primary">
								设备位置
							</el-link>
							<el-link :underline="false" type="primary">
								设备轨迹
							</el-link>
							<el-link :underline="false" type="primary">
								划分区域
							</el-link>
							<el-link :underline="false" type="primary">
								所属区域
							</el-link>
							<el-link :underline="false" type="danger">
								解除区域
							</el-link>
							<el-link :underline="false" type="primary">
								设置班组
							</el-link>
							<el-link :underline="false" type="primary">
								绑定员工
							</el-link>
							<el-link :underline="false" type="primary">
								编辑
							</el-link>
							<el-link :underline="false" type="danger">
								删除
							</el-link>
						</template>
					</el-table-column>
					<el-table-column :key="prop" :prop="prop" :label="label" v-else></el-table-column>
				</template>
				</el-table>
					<div class="flex justify-between items-center mt4">
						<div class="">
							<el-button icon="el-icon-sort" type="primary" :disabled="!hasSelection" @click="batchChangeBindDialog = true">
								批量换绑或解除
							</el-button>
							<el-button type="warning" icon="el-icon-refresh-left" :disabled="!hasSelection">
								批量切换班组
							</el-button>
							<el-button type="danger" icon="el-icon-delete" :disabled="!hasSelection">
								批量删除设备
							</el-button>
						</div>
						<el-pagination
							background
							layout="prev, pager, next"
							:total="totalNumber"
							:current-page="queryCondition.page"
							@current-change="pageChange">
						</el-pagination>
				</div>
      </div>
		</div>
	</div>
	<el-dialog :visible.sync="batchChangeBindDialog" title="批量换绑或者解除区域" width="480px">
		<el-form label-width="100px">
			<el-form-item label="关键字查询">
				<el-input v-model="batchChangeBindDialogFormData.keyword"/>
				<el-button type="primary">
					查询
				</el-button>
			</el-form-item>
			<el-form-item label="解除区域">
				<el-select style="width:100%;" v-model="batchChangeBindDialogFormData.area">
					<el-option :label="`选项${n}`" v-for="n in 4" :key="n" :value="n"></el-option>
				</el-select>
			</el-form-item>
		</el-form>
		<div class="row justify-center" slot="footer">
			<el-button type="primary">
				提交
			</el-button>
		</div>
	</el-dialog>
	<el-dialog :visible.sync="create.createDviceDialog" title="添加设备" width="480px">
		<div class="flex justify-center">
			<el-radio-group v-model="create.type" style="margin-bottom: 30px;">
				<el-radio-button label="handy">手动添加</el-radio-button>
				<el-radio-button label="batch">批量添加</el-radio-button>
			</el-radio-group>
		</div>
		<el-form label-width="100px" v-if="create.type === 'handy'">
			<el-form-item label="设备名称">
				<el-input v-model="create.formData.deviceName"></el-input>
			</el-form-item>
			<el-form-item label="设备IMEI">
				<el-input v-model="create.formData.emei"></el-input>
			</el-form-item>
		</el-form>
		<div v-else class="column items-center">
			<el-link href="/smartGarden/Public/Admin/File/shebei.xlsx" type="primary" class="mb3">下载批量带入模板</el-link>
			<el-upload
			  drag
			  action="">
			  <i class="el-icon-upload"></i>
			  <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
			</el-upload>
		</div>
		<div class="row justify-center" slot="footer">
			<el-button type="primary">
				提交
			</el-button>
		</div>
	</el-dialog>
</div>
<script type="text/javascript">
	const MACHINELIST = new Vue({
		el: '#MACHINELIST',
		data(){
			return {
				create: {
					createDviceDialog: false,
					type: 'handy',
					formDataRules: {
						deviceName: [
						]
					},
					formData: {
						diviceName: '',
						imei: ''
					}
				},
				batchSelections: [],
				tableData: [{
					deviceNumber: '2983452-42930',
					deviceName: '设备1',
					deviceStatus: 1,
					battery: 78,
					workNumber: 290824,
					principal: '张晓晨',
					position: '维护工人',
					manager: '王大拿',
					area: '渭河西岸',
					group: '保安常白'
				}, {
					deviceNumber: '489423948-290398',
					deviceName: '设备2',
					deviceStatus: 2,
					battery: 90,
					workNumber: 23432,
					principal: '王安琪',
					position: '工人组长',
					manager: '刘璇',
					area: '渭河东岸',
					group: '保安常黑'
				}],
				queryCondition: {
					keyword: '',
					status: 0,
					position: 0,
					manager: 0,
					page: 1
				},
				// 分页总数据条数
				totalNumber: 1000,
				tableColumns: [
					['serialNumber', '序号'],
					['deviceNumber', '设备号'],
					['deviceName', '设备名称'],
					['deviceStatus', '设备状态'],
					['battery', '电量'],
					['workNumber', '工号'],
					['principal', '负责人'],
					['position', '职位'],
					['manager', '管理人员'],
					['area', '所属区域名称'],
					['group', '班组'],
					['rowOperations', '操作']
				],
				batchChangeBindDialog: false,
				batchChangeBindDialogFormData: {
					keyword: '',
					area: '',
				}
			}
		},
		created(){
			// 从接口获取数据
		},
		methods: {
			/**
			 * [pageChange 分页页数变化触发事件]
			 * @param  {[type]} p [需要查询数据的那一页]
			 * @return {[type]}   [无]
			 */
			pageChange(p){
				// 在这里用 p 向后台ajax获取分页数据
				console.log(`新的页码：${p}`)
			},

			/**
			 * [doQuery 查询触发事件]
			 * @return {[type]} [description]
			 */
			doQuery(){
				// 在这里将查询条件传后台获取查询结果
			}
		},
		computed: {
			hasSelection(){
				return this.batchSelections.length > 0
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