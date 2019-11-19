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

<link href="<?php echo C('ADMIN_JS_PATH');?>/layui/css/layui.css" rel="stylesheet">
<div class="padding-md" id="APPLYEXCHANGE">
  <div class="smart-widget" style="margin-bottom: 1px;">
    <div class="smart-widget-inner">
      <div class="smart-widget-body">
        <el-breadcrumb separator="/">
          <el-breadcrumb-item>
            <a href="">首页</a>
          </el-breadcrumb-item>
          <el-breadcrumb-item>
            请假管理
          </el-breadcrumb-item>
          <el-breadcrumb-item>
            换班申请
          </el-breadcrumb-item>
        </el-breadcrumb>
      </div>
      <el-divider></el-divider>
      <el-row>
        <el-col :offset="8" :span="8">
          <el-form :model="applyInfo" label-width="100px" ref="mainForm" :rules="{
            date: [
              { required: true, message: '请选择换班日期！', trigger: 'blur' },
            ],
            applicant: [
              { required: true, message: '请输入申请人！', trigger: 'blur' },
            ],
            targetEmployee: [
              { required: true, message: '请输入换班人！', trigger: 'blur' },
            ],
            time: [
              { required: true, message: '请选择时间！', trigger: 'blur' },
            ],
            remark: [
              { required: true, message: '请输入备注！', trigger: 'blur' },
            ],
          }">
            <el-form-item label="员工工号">
              <el-input v-model="applyInfo.job_number">
                <el-button slot="suffix" size="small" icon="el-icon-search" type="primary" @click="queryEmployee">
                  查询
                </el-button>
              </el-input>
            </el-form-item>
            <el-form-item prop="date" label="换班日期">
              <el-date-picker
                style="width:100%;"
                v-model="applyInfo.date"
                type="datetimerange"
                range-separator="至"
                start-placeholder="开始日期"
                end-placeholder="结束日期"
                align="right">
              </el-date-picker>
            </el-form-item>
            <el-form-item prop="applicant" label="申请人">
              <el-input v-model="applyInfo.applicant"/>
            </el-form-item>
            <el-form-item prop="targetEmployee" label="换班人">
              <el-input v-model="applyInfo.targetEmployee"/>
            </el-form-item>
            <el-form-item prop="remark" label="备注">
              <el-input
                type="textarea"
                :rows="6"
                v-model="applyInfo.remark">
              </el-input>
            </el-form-item>
          </el-form>
          <div class="row justify-center my4">
            <el-button @click="$refs.mainForm.resetFields()">重置</el-button>
            <el-button type="primary" class="ml4" @click="submit">提交</el-button>
          </div>
        </el-col>
      </el-row>
    </div>
  </div>
</div>
<script type="text/javascript">
  const APPLYEXCHANGE = new Vue({
    el: '#APPLYEXCHANGE',
    data(){
      return {
        applyInfo: {
          job_number: '',
          date: '',
          applicant: '',
          targetEmployee: '',
          remark: ''
        }
      }
    },
    methods: {
      submit(){
        this.$refs.mainForm.validate(r => {
          if(r){
            // 这里代表表单验证通过，即将提交数据到后台
            console.log('提交数据至后台')
          }
        })
      },
      // 查询员工信息
      queryEmployee(){

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