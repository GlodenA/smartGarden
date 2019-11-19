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

<div class="padding-md" id="WARNINGLIST">
  <div class="smart-widget" style="margin-bottom: 1px;">
    <div class="smart-widget-inner">
      <div class="smart-widget-body">
        <el-breadcrumb separator="/">
          <el-breadcrumb-item>
            <a href="">首页</a>
          </el-breadcrumb-item>
          <el-breadcrumb-item>
            考勤管理
          </el-breadcrumb-item>
          <el-breadcrumb-item>
            告警列表
          </el-breadcrumb-item>
        </el-breadcrumb>
        <el-divider></el-divider>
        <div class="flex justify-end">
          <el-form inline>
            <el-form-item label="关键字">
              <el-input v-model="queryCondition.keyword" style="width:120px;"></el-input>
            </el-form-item>
            <el-form-item label="时间">
              <el-date-picker
                      v-model="queryCondition.time"
                      type="daterange"
                      range-separator="至"
                      start-placeholder="开始日期"
                      end-placeholder="结束日期">
              </el-date-picker>
            </el-form-item>
            <el-form-item label="职位">
              <el-select v-model="queryCondition.position" style="width:120px;">
                <el-option :value="0" label="全部"></el-option>
                <?php if(is_array($positionList)): $i = 0; $__LIST__ = $positionList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sl): $mod = ($i % 2 );++$i;?><el-option value="<?php echo ($sl["id"]); ?>" label="<?php echo ($sl["name"]); ?>"></el-option><?php endforeach; endif; else: echo "" ;endif; ?>
              </el-select>
            </el-form-item>
            <el-form-item label="管理人员">
              <el-select v-model="queryCondition.manager" style="width:120px;">
                <el-option :value="0" label="全部"></el-option>
                <?php if(is_array($managerList)): $i = 0; $__LIST__ = $managerList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sl): $mod = ($i % 2 );++$i;?><el-option value="<?php echo ($sl["userid"]); ?>" label="<?php echo ($sl["realname"]); ?>"></el-option><?php endforeach; endif; else: echo "" ;endif; ?>
              </el-select>
            </el-form-item>
            <el-form-item label="报警类型">
              <el-select v-model="queryCondition.warningType" style="width:120px;">
                <el-option :value="0" label="全部"></el-option>
                <el-option v-for="(l, i) in [
                  '远离区域报警',
                  '低电量报警',
                  '迟到报警',
                  '旷工报警',
                  '早退报警',
                  '怠工超过设定时间告警'
                ]" :value="i + 2" :label="l"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-button type="primary">查询</el-button>
            </el-form-item>
          </el-form>
        </div>
        <el-table :data="tableData">
          <template v-for="([prop, label], i) in tableColumns">
            <el-table-column :key="prop" :prop="prop" :label="label" v-if="prop === 'warningType'">
              <template #default="{ row }">
                <el-link :underline="false" type="danger">{{ row[prop] }}</el-link>
              </template>
            </el-table-column>
            <el-table-column :key="prop" :prop="prop" :label="label" v-else-if="prop === 'serialNumber'">
              <template #default="{ $index }">
                {{ $index + 1 }}
              </template>
            </el-table-column>
            <el-table-column :key="prop" :prop="prop" :label="label" v-else></el-table-column>
          </template>
        </el-table>
        <div class="flex justify-between mt3">
          <el-button icon="el-icon-download">
            导出Excel
          </el-button>
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
</div>
<script type="text/javascript">
  const WARNINGLIST = new Vue({
    el: '#WARNINGLIST',
    data(){
      return {
        totalNumber: 1000,
        queryCondition: {
          keyword: '',
          time: '',
          position: 0,
          manager: 0,
          warningType: 0,
          page: 1
        },
        tableData: [{
          deviceName: 'cjl_0239429',
          deviceNumber: '092348023409809',
          bindEmployee: '孙继红',
          workNumber: '9239',
          position: '养护工人',
          manager: '王红艳',
          warningTime: '2019-11-05',
          warningType: '迟到报警'
        }, {
          deviceName: 'cjl_0239429',
          deviceNumber: '092348023409809',
          bindEmployee: '孙继红',
          workNumber: '9239',
          position: '养护工人',
          manager: '王红艳',
          warningTime: '2019-11-05',
          warningType: '怠工超过设定时间报警'
        }, {
          deviceName: 'cjl_0239429',
          deviceNumber: '092348023409809',
          bindEmployee: '孙继红',
          workNumber: '9239',
          position: '养护工人',
          manager: '王红艳',
          warningTime: '2019-11-05',
          warningType: '迟到报警'
        }],
        tableColumns: [
          ['serialNumber', '序号'],
          ['deviceName', '设备名称'],
          ['deviceNumber', '设备号'],
          ['bindEmployee', '绑定员工'],
          ['workNumber', '工号'],
          ['position', '职位'],
          ['manager', '管理人员'],
          ['warningTime', '告警时间'],
          ['warningType', '告警类型']
        ]
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