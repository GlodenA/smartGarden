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
	#ATTENDENCESETTING .number-input {
		width: 100%;
	}
</style>
<div class="padding-md" id="ATTENDENCESETTING">
	<div class="smart-widget widget-dark-blue">
		<div class="smart-widget-inner padding-md">
			<div class="smart-widget-body">
				<el-breadcrumb separator="/">
					<el-breadcrumb-item>
						<a href="/">首页</a>
					</el-breadcrumb-item>
					<el-breadcrumb-item>
						考勤管理
					</el-breadcrumb-item>
					<el-breadcrumb-item>
						考勤设置
					</el-breadcrumb-item>
				</el-breadcrumb>
				<el-divider></el-divider>
				<el-row>
					<el-col :span="10" :offset="7">
						<el-form ref="mainForm" :model="info" label-width="320px" :rules="{
							error_time: [
				        { required: true, message: '请输入允许设备误差时间！', trigger: 'blur' },
				      ],
							still_time: [
				        { required: true, message: '请输入允许设备静止多长时间报警！', trigger: 'blur' },
				      ],
							late_time: [
								{ required: true, message: '请输入允许超过上班开始多长时间算是迟到！', trigger: 'blur' },
							],
							absenteeism_time: [
								{ required: true, message: '允许超过上班开始多长时间算是旷工！', trigger: 'blur' },
							],
							distance_time: [
								{ required: true, message: '允许设备远离区域多长时间报警！', trigger: 'blur' },
							],
							leave_time: [
								{ required: true, message: '允许设备远离区域多长时间算是早退！', trigger: 'blur' },
							],
						}">
							<el-form-item prop="error_time" label="允许设备误差时间(秒数)">
								<el-input-number v-model="info.error_time" class="number-input" />
							</el-form-item>
							<el-form-item prop="still_time" label="允许设备静止多长时间报警(秒数)">
								<el-input-number v-model="info.still_time" class="number-input" />
							</el-form-item>
							<el-form-item prop="late_time" label="允许超过上班开始多长时间算是迟到(秒数)">
								<el-input-number v-model="info.late_time" class="number-input" />
							</el-form-item>
							<el-form-item prop="absenteeism_time" label="允许超过上班开始多长时间算是旷工(秒数)">
								<el-input-number v-model="info.absenteeism_time" class="number-input" />
							</el-form-item>
							<el-form-item prop="distance_time" label="允许设备远离区域多长时间报警(秒数)">
								<el-input-number v-model="info.distance_time" class="number-input" />
							</el-form-item>
							<el-form-item prop="leave_time" label="允许设备远离区域多长时间算是早退(秒数)">
								<el-input-number v-model="info.leave_time" class="number-input" />
							</el-form-item>
						</el-form>
						<div class="row justify-center">
							<el-button type="primary">提交</el-button>
						</div>
					</el-col>
				</el-row>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	const attendanceSetting = new Vue({
		el: '#ATTENDENCESETTING',
		data() {
			return {
				info: {
					error_time: '<?php echo ($error_time); ?>',
					still_time: '<?php echo ($still_time); ?>',
					late_time: '<?php echo ($late_time); ?>',
					absenteeism_time: '<?php echo ($absenteeism_time); ?>',
					distance_time: '<?php echo ($distance_time); ?>',
					leave_time: '<?php echo ($leave_time); ?>'
				}
			}
		},
		methods: {
			submit() {
				this.$refs.mainForm.validate(r => {
					if (r) {
						DMS.ajaxPost("/smartGarden/manager.php?s=/Attendance/attendanceSetting", {
							info: this.info
						}, res => {
							if (res.status == 1) {
								this.$message({
				          showClose: true,
				          message: '提交成功！',
									type: 'success'
				        });
								window.loaction.reload()
							} else {
								DMS.error('' + ret.info + '', 0);
							}
						})
					}
				})
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