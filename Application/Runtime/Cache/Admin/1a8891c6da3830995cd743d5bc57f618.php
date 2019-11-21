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
    },
    watch: {
      globalLoading(v){
        console.log('Loading change: ', v);
      }
    }
  })
</script>
<style media="screen">
	.text-indent {
		text-indent: 1em;
	}
</style>
<div class="padding-md" id="GROUPLIST">
	<div class="smart-widget widget-dark-blue">
		<div class="smart-widget-inner">
			<div class="smart-widget-body">
				<el-breadcrumb separator="/">
					<el-breadcrumb-item>
						<a href="">首页</a>
					</el-breadcrumb-item>
					<el-breadcrumb-item>
						员工管理
					</el-breadcrumb-item>
					<el-breadcrumb-item>
						员工职位
					</el-breadcrumb-item>
				</el-breadcrumb>
				<el-divider></el-divider>
				<div class="">
					<el-button icon="el-icon-plus" type="primary" @click="addDialog = true">
						添加管理职位
					</el-button>
				</div>
				<el-table :data="tableData">
					<el-table-column prop="id" label="ID"></el-table-column>
					<el-table-column prop="name" label="名称">
						<template #default="{ row }">
							<div :class="[{ 'text-indent': row.isChild }]">
								{{ `${row.isChild ? '|- ' : ''}${row.name}` }}
							</div>
						</template>
					</el-table-column>
					<el-table-column label="操作">
						<template #default="{ row }">
							<el-button type="primary" size="mini" v-if="!row.isChild" @click="addChild(row)">
								添加下级
							</el-button>
							<el-button type="primary" size="mini" @click="() => {
								if(row.isChild){
									editDialog = true
									editFormData.name = row.name
									editFormData.position = row.parentId
									return
								}
								editManageFormData.name = row.name
								editManagerDialog = true
							}">
								编辑
							</el-button>
							<el-button type="danger" size="mini" @click="askDel(row)">
								删除
							</el-button>
						</template>
					</el-table-column>
				</el-table>
			</div>
		</div>
	</div>

	<!-- 添加管理职位 -->
	<el-dialog title="添加管理职位" :visible.sync="addDialog" width="480px">
		<el-form :model="addFormData" label-width="100px"  :rules="nameRules">
			<el-form-item label="">
				<el-select v-model="addFormData.position" style="width:100%;" disabled>
					<el-option :value="0" label="作为管理人员"></el-option>
				</el-select>
			</el-form-item>
			<el-form-item prop="name" label="职位名称">
				<el-input v-model="addFormData.name" />
			</el-form-item>
		</el-form>
		<div slot="footer" class="flex justify-center">
			<el-button type="primary">
				提交
			</el-button>
		</div>
	</el-dialog>

	<!-- 添加下级 -->
	<el-dialog title="添加下级" :visible.sync="addChildDialog" width="480px">
		<el-form :model="addChildFormData" label-width="100px" :rules="nameRules">
			<el-form-item label="">
				<el-select v-model="addChildFormData.position" style="width:100%;" disabled>
					<?php if(is_array($addPositionList)): $i = 0; $__LIST__ = $addPositionList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pl): $mod = ($i % 2 );++$i;?><el-option label="<?php echo ($pl["name"]); ?>" value="<?php echo ($pl["id"]); ?>">
						</el-option><?php endforeach; endif; else: echo "" ;endif; ?>
				</el-select>
			</el-form-item>
			<el-form-item prop="name" label="名称">
				<el-input v-model="addChildFormData.name" />
			</el-form-item>
		</el-form>
		<div slot="footer" class="flex justify-center">
			<el-button type="primary">
				提交
			</el-button>
		</div>
	</el-dialog>


	<!-- 编辑管理人员信息 -->
	<el-dialog title="编辑管理人员信息" :visible.sync="editManagerDialog" width="480px">
		<el-form :model="editManageFormData" label-width="100px" :rules="nameRules">
			<el-form-item label="">
				<el-select v-model="editManageFormData.position" style="width:100%;" disabled>
					<el-option :value="0" label="作为管理人员"></el-option>
				</el-select>
			</el-form-item>
			<el-form-item prop="name" label="名称">
				<el-input v-model="editManageFormData.name" />
			</el-form-item>
		</el-form>
		<div slot="footer" class="flex justify-center">
			<el-button type="primary">
				提交
			</el-button>
		</div>
	</el-dialog>

	<!-- 编辑职位信息 -->
	<el-dialog title="编辑职位信息" :visible.sync="editDialog" width="480px">
		<el-form :model="editFormData" label-width="100px" :rules="nameRules">
			<el-form-item label="">
				<el-select v-model="editFormData.position" style="width:100%;">
					<el-option :value="0" label="作为管理人员"></el-option>
					<?php if(is_array($addPositionList)): $i = 0; $__LIST__ = $addPositionList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pl): $mod = ($i % 2 );++$i;?><el-option label="<?php echo ($pl["name"]); ?>" value="<?php echo ($pl["id"]); ?>">
						</el-option><?php endforeach; endif; else: echo "" ;endif; ?>
				</el-select>
			</el-form-item>
			<el-form-item prop="name" label="名称">
				<el-input v-model="editFormData.name" />
			</el-form-item>
		</el-form>
		<div slot="footer" class="flex justify-center">
			<el-button type="primary">
				提交
			</el-button>
		</div>
	</el-dialog>
</div>
<script>
	let tableData = []
</script>
<?php if(is_array($menuList)): foreach($menuList as $key=>$v): ?><script type="text/javascript">
		tableData.push({
			id: "<?php echo ($v['id']); ?>",
			name: "<?php echo ($v['name']); ?>",
		})
	</script>
	<?php if(is_array($v['children'])): foreach($v['children'] as $key=>$r): ?><script type="text/javascript">
			tableData.push({
				id: "<?php echo ($r['id']); ?>",
				name: "<?php echo ($r['name']); ?>",
				parentId: "<?php echo ($r[parent_id]); ?>",
				isChild: true
			})
		</script><?php endforeach; endif; endforeach; endif; ?>
<script type="text/javascript">
	const GROUPLIST = new Vue({
		el: '#GROUPLIST',
		data() {
			return {
				nameRules: {
					name: [{
						required: true,
						message: '请输入名称！',
						trigger: 'blur'
					}]
				},
				tableData,
				addChildDialog: false,
				addChildFormData: {
					name: '',
					position: ''
				},
				editDialog: false,
				editFormData: {
					name: ''
				},
				addDialog: false,
				addFormData: {
					name: '',
					position: 0
				},
				editManagerDialog: false,
				editManageFormData: {
				    name: '',
                    position: 0
                }
			}
		},
		mounted() {},
		methods: {
			addChild(row) {
			    this.addChildFormData.position = row.id
				this.addChildDialog = true
			},
			askDel(row) {
				this.$confirm('确认删除吗?', '提示', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					this.$message({
						type: 'success',
						showClose: true,
						message: '删除成功!'
					})
				}).catch(() => {})
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