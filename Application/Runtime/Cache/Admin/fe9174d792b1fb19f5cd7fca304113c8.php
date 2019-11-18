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
<div class="padding-md" id="ATTENDENCELIST">
  <!-- 面包屑导航 -->
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
            考勤统计
          </el-breadcrumb-item>
        </el-breadcrumb>
        <el-divider></el-divider>
        <el-form inline :model="queryCondition">
          <el-form-item label="工号">
            <el-input v-model="queryCondition.workNumber" style="width:100px;"></el-input>
          </el-form-item>
          <el-form-item label="姓名">
            <el-input v-model="queryCondition.name" style="width:100px;"></el-input>
          </el-form-item>
          <el-form-item label="时间">
            <el-date-picker v-model="queryCondition.time" type="daterange" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期">
            </el-date-picker>
          </el-form-item>
          <el-form-item label="考勤状态">
            <el-select v-model="queryCondition.status" style="width:120px;">
              <el-option v-for="(item, i) in [
                '全部',
                '上午正常上班',
                '上午迟到',
                '上午旷工',
                '上午正常下班',
                '上午早退',
                '上午异常',
                '下午正常上班',
                '下午迟到',
                '下午旷工',
                '下午正常下班',
                '下午早退',
                '下午异常',
                '异常'
              ]" :key="item" :label="item" :value="i">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="职位">
            <el-select v-model="queryCondition.position" style="width:120px;">
              <el-option :value="0" label="全部"></el-option>
              <?php if(is_array($positionList)): $i = 0; $__LIST__ = $positionList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sl): $mod = ($i % 2 );++$i;?><el-option value="<?php echo ($sl["id"]); ?>" label="<?php echo ($sl["name"]); ?>"></el-option><?php endforeach; endif; else: echo "" ;endif; ?>
            </el-select>
          </el-form-item>
          <el-form-item label="管理人员">
            <el-select v-model="queryCondition.position" style="width:120px;">
              <el-option :value="0" label="全部"></el-option>
              <?php if(is_array($managerList)): $i = 0; $__LIST__ = $managerList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sl): $mod = ($i % 2 );++$i;?><el-option value="<?php echo ($sl["userid"]); ?>" label="<?php echo ($sl["realname"]); ?>"></el-option><?php endforeach; endif; else: echo "" ;endif; ?>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button icon="el-icon-search" type="primary">
              查询
            </el-button>
          </el-form-item>
        </el-form>
        <el-table
          :data="tableData"
        >
          <template v-for="([prop, label], i) in tableColumns">
            <el-table-column :key="prop" :prop="prop" :label="label" v-if="prop === 'serialNumber'">
              <template #default="{ $index }">
                {{ $index + 1 }}
              </template>
            </el-table-column>
            <el-table-column :key="prop" :prop="prop" :label="label" v-else-if="prop === 'signStatus'">
              <template #default="{ row }">
                <el-tag v-if="row.signStatus === 1" type="primary">正常</el-tag>
                <el-tag v-else-if="row.signStatus === 2" type="danger">异常</el-tag>
                <el-tag v-if="row.signStatus === 3" type="info">暂无结果</el-tag>
              </template>
            </el-table-column>
            <el-table-column :key="prop" :prop="prop" :label="label" v-else-if="prop === 'rowOperations'">
              <template #default="{ row }">
                <el-tooltip effect="dark" placement="left">
                  <el-button type="text">详情</el-button>
                  <div slot="content">
                    <p>上午上班打卡时间：{{ row.morningSignTime }}</p>
                    <p>上午下班打卡时间：{{ row.morningLeaveTime }}</p>
                    <p>下午上班打卡时间：{{ row.afternoonSignTime }}</p>
                    <p>下午下班打卡时间：{{ row.afternoonLeaveTime }}</p>
                    <p>设备串号：{{ row.deviceNumber }}</p>
                    <p>员工信息：{{ row.employeeInfo }}</p>
                  </div>
                </el-tooltip>
              </template>
            </el-table-column>
            <el-table-column :key="prop" :prop="prop" :label="label" v-else></el-table-column>
          </template>
        </el-table>
        <div class="flex mt3 justify-between items-center">
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
<script>
  const attendenceList = new Vue({
    el: '#ATTENDENCELIST',
    data() {
      return {
        queryCondition: {
          workNumber: '',
          name: '',
          time: '',
          status: 0,
          position: 0,
          managerList: 0,
          page: 1
        },
        totalNumber: 1000,
        tableData: [{
          job_number: '938240',
          realname: '张飞',
          signStatus: 1,
          date: '2019-11-15',
          leaveTimes: 2,
          morningSignTime: '09:00',
          morningLeaveTime: '11:30',
          afternoonSignTime: '14:00',
          afternoonLeaveTime: '18:00',
          deviceNumber: '985345-23059-90',
          employeeInfo: 'XXXXXX'
        }, {
          job_number: '534351',
          realname: '赵云',
          signStatus: 2,
          date: '2019-11-15',
          leaveTimes: 1,
          morningSignTime: '09:00',
          morningLeaveTime: '11:30',
          afternoonSignTime: '14:00',
          afternoonLeaveTime: '18:00',
          deviceNumber: '985345-23059-90',
          employeeInfo: 'XXXXXX'
        }, {
          job_number: '2093480',
          realname: '曹操',
          signStatus: 3,
          date: '2019-11-15',
          leaveTimes: 1,
          morningSignTime: '09:00',
          morningLeaveTime: '11:30',
          afternoonSignTime: '14:00',
          afternoonLeaveTime: '18:00',
          deviceNumber: '985345-23059-90',
          employeeInfo: 'XXXXXX'
        }],
        tableColumns: [
          ['serialNumber', '序号'],
          ['job_number', '工号'],
          ['realname', '姓名'],
          ['signStatus', '打卡情况'],
          ['date', '日期'],
          ['leaveTimes', '离岗次数'],
          ['rowOperations', '操作']
        ]
      }
    },
    created(){
      // 在这里初始化获取第一页的数据
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