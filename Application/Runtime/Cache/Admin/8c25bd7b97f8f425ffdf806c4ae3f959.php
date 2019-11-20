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
  input[type=file]{
		display: none;
	}
</style>
<div class="padding-md" id="MEMBERLIST">
  <div class="smart-widget" style="margin-bottom: 1px;">
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
            员工列表
          </el-breadcrumb-item>
        </el-breadcrumb>
        <el-divider></el-divider>
        <div class="flex justify-between items-center">
          <el-button icon="el-icon-plus" type="primary" @click="create.dialog = true">
            员工添加
          </el-button>
          <el-form inline>
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
            <el-form-item label="员工状态">
							<el-select v-model="queryCondition.employeeStatus" style="width:100px;">
								<el-option v-for="(s, i) in ['全部', '在岗', '未上岗', '离岗', '未上岗但有考勤', '未上岗且无考勤']" :label="s" :value="i" :key="s">
								</el-option>
							</el-select>
						</el-form-item>
            <el-form-item label="关键字">
							<el-input v-model="queryCondition.keyword"></el-input>
						</el-form-item>
            <el-form-item>
              <el-button type="primary">查询</el-button>
            </el-form-item>
          </el-form>
        </div>
        <el-table :data="tableData" @selection-change="s => tableSelections = s">
          <el-table-column type="selection" width="55"></el-table-column>
          <template v-for="([label, prop], i) in tableColumns">
            <el-table-column :key="prop" :prop="prop" :label="label">
              <template v-if="prop === 'job_status'" #default="{ row }">
                <el-tag v-if="row[prop] === 1" type="primary">
                  在岗
                </el-tag>
                <el-tag v-else-if="row[prop] === 2" type="warning">
                  未上岗
                </el-tag>
                <el-tag v-else-if="row[prop] === 3" type="danger">
                  离岗
                </el-tag>
              </template>
            </el-table-column>
          </template>
          <el-table-column label="操作" width="380">
            <template #default="{ row }">
              <el-link :underline="false" type="primary">详情</el-link>
              <el-link :underline="false" type="primary">编辑</el-link>
              <el-link :underline="false" type="danger">删除</el-link>
              <el-link :underline="false" type="primary">绑定设备</el-link>
              <el-link :underline="false" type="primary">解绑设备</el-link>
              <el-link :underline="false" type="primary">员工轨迹</el-link>
              <el-link :underline="false" type="primary">所属区域</el-link>
            </template>
          </el-table-column>
        </el-table>
        <div class="flex justify-between items-center mt3">
          <div>
            <el-button :disabled="!hasSelection" type="danger" icon="el-icon-delete">
              批量删除
            </el-button>
            <el-button :disabled="!hasSelection" type="danger" icon="el-icon-sort" class="mx2">
              批量切换管理人员
            </el-button>
            <el-button icon="el-icon-download" @click="window.open('/smartGarden/manager.php?s=/Member/memberExcel')">
              导出
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
  <el-dialog :visible.sync="create.dialog" title="添加员工" width="480px">
    <div class="flex justify-center">
			<el-radio-group v-model="create.type" style="margin-bottom: 30px;">
				<el-radio-button label="handy">手动添加</el-radio-button>
				<el-radio-button label="batch">批量添加</el-radio-button>
			</el-radio-group>
		</div>
		<el-form label-width="100px" v-if="create.type === 'handy'" :model="create.formData" :rules="create.formRules">
			<el-form-item label="职位">
			  <el-select v-model="create.formData.position" style="width:100%;">
          <el-option v-for="p in positions" :label="p" :value="p" :key="p"></el-option>
        </el-select>
			</el-form-item>
			<el-form-item label="姓名">
				<el-input v-model="create.formData.realname"></el-input>
			</el-form-item>
      <el-form-item label="手机号">
				<el-input v-model="create.formData.mobile"></el-input>
			</el-form-item>
      <el-form-item label="员工号">
				<el-input v-model="create.formData.job_number"></el-input>
			</el-form-item>
      <el-form-item label="性别">
        <el-radio v-model="create.formData.gender" :label="0">男</el-radio>
         <el-radio v-model="create.formData.gender" :label="1">女</el-radio>
      </el-form-item>
		</el-form>
		<div v-else class="column items-center">
			<el-link href="/smartGarden/Public/Admin/File/01.xlsx" type="primary" class="mb3">下载批量带入模板</el-link>
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
  const MEMBERLIST = new Vue({
    el: '#MEMBERLIST',
    data(){
      return {
        positions: ['职位一', '职位二', '职位三'],
        tableSelections: [],
        tableData: [{
          realname: '孙吉祥',
          gender: '男',
          job_position: '工人',
          mobile: '17815638478',
          job_number: '9120',
          parent_name: '张德强',
          machine_imei: '29083402340',
          job_status: 1
        }, {
          realname: '赵佳琪',
          gender: '男',
          job_position: '工人',
          mobile: '17812638478',
          job_number: '2341',
          parent_name: '张德强',
          machine_imei: '2492344002',
          job_status: 2
        }, {
          realname: '孙立群',
          gender: '男',
          job_position: '工人',
          mobile: '17815648478',
          job_number: '3221',
          parent_name: '张德强',
          machine_imei: '23482349234',
          job_status: 3
        }],
        totalNumber: 1000,
        queryCondition: {
          position: 0,
          manager: 0,
          employeeStatus: 0,
          keyword: '',
          page: 1
        },
        tableColumns: [
          ['姓名', 'realname'],
          ['性别', 'gender'],
          ['职位', 'job_position'],
          ['手机号码', 'mobile'],
          ['员工号', 'job_number'],
          ['管理人员', 'parent_name'],
          ['设备IMEI', 'machine_imei'],
          ['状态', 'job_status']
        ],
        create: {
          dialog: false,
          type: 'handy',
          formRules: {

          },
          formData: {
            position: '',
            realname: '',
            mobile: '',
            job_number: '',
            gender: 0
          }
        }
      }
    },
    computed: {
      hasSelection(){
        return this.tableSelections.length > 0
      }
    },
    methods: {
      pageChange(p){
        this.queryCondition.page = p
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