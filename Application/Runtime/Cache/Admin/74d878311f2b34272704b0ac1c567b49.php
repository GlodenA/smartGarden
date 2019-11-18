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

<div class="padding-md" id="TIMELIST">
  <!-- 面包屑导航 -->
  <div class="smart-widget" style="margin-bottom: 1px;">
    <div class="smart-widget-inner">
      <div class="smart-widget-body">
        <div class="px2 pt2">
          <el-breadcrumb separator="/">
            <el-breadcrumb-item>
              <a href="/">首页</a>
            </el-breadcrumb-item>
            <el-breadcrumb-item>
              班组管理
            </el-breadcrumb-item>
            <el-breadcrumb-item>
              时间设置
            </el-breadcrumb-item>
          </el-breadcrumb>
        </div>
        <el-divider></el-divider>
        <div class="info-line padding-md">
          <el-button type="primary" icon="el-icon-plus" @click="createDialog = true">
            添加时间段
          </el-button>
        </div>
        <div class="smart-widget-inner">
          <el-table :data="tableData">
            <el-table-column label="名称" prop="title">
            </el-table-column>
            <el-table-column label="上班时间" prop="start_time">
            </el-table-column>
            <el-table-column label="下班时间" prop="end_time">
            </el-table-column>
            <el-table-column label="操作" prop="id">
              <template #default="{ row }">
                <el-button type="text" @click="() => { window.location.href = `/smartGarden/manager.php?s=/Schedules/timeEdit/id/{row.id}` }">编辑</el-button>
              </template>
            </el-table-column>
          </el-table>
          <div class="row justify-end mt2">
            <el-pagination
              background
              layout="prev, pager, next"
              :total="<?php echo ($total); ?>"
              :current-page="<?php echo ($currentPage); ?>"
              @current-change="pageChange">
            </el-pagination>
          </div>
        </div>
      </div>
    </div>
  </div>
  <el-dialog :visible.sync="createDialog" title="添加时间段" width="30%">
    <el-form ref="createForm" :model="newData" label-width="80px" :rules="{
      title: [
        { required: true, message: '请输入名称！', trigger: 'blur' },
      ],
      start_time: [
        { required: true, message: '请选择上班时间！', trigger: 'blur' },
      ],
      end_time: [
        { required: true, message: '请选择下班时间！', trigger: 'blur' },
      ]
    }">
      <el-form-item label="名称" prop="title">
        <el-input v-model="newData.title"></el-input>
      </el-form-item>
      <el-form-item label="上班时间" prop="start_time">
        <el-time-picker v-model="newData.start_time" style="width:100%;" value-format="HH:mm:ss">
        </el-time-picker>
      </el-form-item>
      <el-form-item label="下班时间" prop="end_time">
        <el-time-picker v-model="newData.end_time" style="width:100%;" value-format="HH:mm:ss">
        </el-time-picker>
      </el-form-item>
      <el-form-item label="状态" prop="state">
        <el-radio v-model="newData.state" label="1">正常</el-radio>
        <el-radio v-model="newData.state" label="2">禁止</el-radio>
      </el-form-item>
    </el-form>
    <div slot="footer" class="row justify-center">
      <el-button type="primary" plain @click="createDialog = false">取消</el-button>
      <el-button type="primary" @click="doCreate">提交</el-button>
    </div>
  </el-dialog>
</div>
<script type="text/javascript">
  let tableData = []
</script>
<?php if(is_array($list)): $k = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($k % 2 );++$k;?><script type="text/javascript">
  tableData.push({
    id: "<?php echo ($v['id']); ?>",
    title: "<?php echo ($v['title']); ?>",
    start_time: "<?php echo ($v['start_time']); ?>",
    end_time: "<?php echo ($v['end_time']); ?>"
  })
</script><?php endforeach; endif; else: echo "" ;endif; ?>
<script type="text/javascript">
  const timeList = new Vue({
    el: '#TIMELIST',
    data(){
      return {
        tableData,
        createDialog: false,
        newData: {
          title: '',
          start_time: '',
          end_time: '',
          state: '1'
        }
      }
    },
    methods: {
      pageChange(p){
        window.location.href = `/smartGarden/manager.php?s=/Schedules/timeList/p/${p}.html`
      },
      doCreate(){
        this.$refs.createForm.validate(r => {
          if(r){
            DMS.ajaxPost("/smartGarden/manager.php?s=/Schedules/timeAdd",{
              info: this.newData
            }, res => {
              if(res.status === 1){
                this.$alert('添加成功！', '通知', {
                   confirmButtonText: '确定',
                   callback: action => {
                     this.createDialog = false
                     window.location.reload()
                   }
                 })
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