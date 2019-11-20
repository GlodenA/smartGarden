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
      <link rel="stylesheet" href="/Public/Admin/Css//util/flex.css">
      <link href="https://unpkg.com/basscss@8.0.2/css/basscss.min.css" rel="stylesheet">
      <script src="https://cdn.jsdelivr.net/npm/vue@2.6.0"></script>
      <link rel="stylesheet" href="/Public/Admin/element/index.css">
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
                      format="yyyy-MM-dd"
                      value-format="yyyy-MM-dd"
                      type="daterange"
                      range-separator="至"
                      start-placeholder="开始日期"
                      end-placeholder="结束日期">
              </el-date-picker>
            </el-form-item>
            <el-form-item label="职位">
              <el-select v-model="queryCondition.position" style="width:120px;" @change="changePosition">
                <el-option :value="0" label="全部"></el-option>
                <?php if(is_array($positionList)): $i = 0; $__LIST__ = $positionList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sl): $mod = ($i % 2 );++$i;?><el-option value="<?php echo ($sl["id"]); ?>" label="<?php echo ($sl["name"]); ?>"></el-option><?php endforeach; endif; else: echo "" ;endif; ?>
              </el-select>
            </el-form-item>
            <el-form-item label="管理人员">
              <el-select v-model="queryCondition.manager" style="width:120px;" @change="changeManager">
                <el-option :value="0" label="全部"></el-option>
                <?php if(is_array($managerList)): $i = 0; $__LIST__ = $managerList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sl): $mod = ($i % 2 );++$i;?><el-option value="<?php echo ($sl["userid"]); ?>" label="<?php echo ($sl["realname"]); ?>"></el-option><?php endforeach; endif; else: echo "" ;endif; ?>
              </el-select>
            </el-form-item>
            <el-form-item label="报警类型">
              <el-select v-model="queryCondition.warningType" style="width:120px;" @change="changeWarningType">
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
              <el-button type="primary" @click="doQuery">查询</el-button>
            </el-form-item>
          </el-form>
        </div>
        <el-table :data="tableData">
          <template v-for="([prop, label], i) in tableColumns">
<!--            <el-table-column :key="prop" :prop="prop" :label="label" v-if="prop === 'type'">-->
<!--              <template #default="{ row }">-->
<!--                <el-link :underline="false" type="danger">{{ row[prop] }}</el-link>-->
<!--              </template>-->
<!--            </el-table-column>-->
            <el-table-column :key="prop" :prop="prop" :label="label" v-if="prop === 'serialNumber'">
              <template #default="{ $index }">
                {{ $index + 1 }}
              </template>
            </el-table-column>
            <el-table-column :key="prop" :prop="prop" :label="label" v-else-if="prop === 'add_time'">
              <template #default="{ row }">
                  {{ formatedDateYmdhms(row.add_time) }}
              </template>
            </el-table-column>
            <el-table-column :key="prop" :prop="prop" :label="label" v-else-if="prop === 'realname'">
              <template #default="{ row }">
                <el-button  type="text" @click="userInfo(row)" >{{ row[prop] }}</el-button>
              </template>
            </el-table-column>
            <el-table-column :key="prop" :prop="prop" :label="label" v-else-if="prop === 'type'">
                <template #default="{ row }">
                  <el-link type="danger"
                                     :underline="false"
                                    @click.native="window.open(`/manager.php?s=/Machine/machineOrbit/machine_id/${row.machine_imei}/searchTime/${formatedDateYmd(row.add_time)}`, '_self')"
                   >
                              {{
                                new Map([
                                  ['1', '远离工作区域'],
                                  ['2', '远离区域报警'],
                                  ['3', '低电量报警'],
                                  ['4', '迟到报警'],
                                  ['5', '旷工报警'],
                                  ['6', '早退报警'],
                                  ['7', '怠工超过设定时间告警']
                                ]).get(row.type)
                              }}
                            </el-link>
                          </template>
                        </el-table-column>
            <el-table-column :key="prop" :prop="prop" :label="label" v-else></el-table-column>
          </template>
        </el-table>
        <div class="flex justify-between mt3">
          <el-button icon="el-icon-download" @click="exportExcel">
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
        totalNumber: 0,
        queryCondition: {
          keyword: '',
          time: [],
          position: 0,
          manager: 0,
          warningType: 0,
          page: 1
        },
        tableData: [
        ],
        tableColumns: [
          ['serialNumber', '序号'],
          ['machine_name', '设备名称'],
          ['machine_imei', '设备号'],
          ['realname', '绑定员工'],
          ['job_number', '工号'],
          // ['position', '职位id'],
          ['position_name', '职位'],
          // ['parent_id', '管理人员id'],
          ['parent_name', '管理人员'],
          ['add_time', '告警时间'],
          ['type', '告警类型']
        ]
      }
    },


   //页面加载时候，在mounted中进行赋值
    mounted() {
      // 初始化查询，默认为今天
      this.queryCondition.time = [this.timeDefault,this.timeDefault];
    },
    computed: {
      timeDefault() {
         var date = new Date();
         var s1 = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
         return s1;
      },
    },
    created(){
      this.doQuery();
    },
    methods: {
      formatedDateYmdhms(timeStamp){
        let d = new Date(timeStamp * 1000)
        return `${d.getFullYear()}-${d.getMonth() + 1}-${d.getDate()} ${d.getHours()}:${d.getMinutes()}:${d.getSeconds()}`
      },
      formatedDateYmd(timeStamp){
        let d = new Date(timeStamp * 1000)
        return `${d.getFullYear()}-${d.getMonth() + 1}-${d.getDate()}`
      },
      doQuery(){
        param = {
          "keyword":this.queryCondition.keyword,
          "time":this.queryCondition.time,
          "position":this.queryCondition.position,
          "parent_id":this.queryCondition.manager,
          "type":this.queryCondition.warningType,
          "page":this.queryCondition.page,
        }
        DMS.ajaxPost('/manager.php?s=/WarningMessage/getWarningMessageList',
                param, res => {
                  this.tableData=res.WARNINGLIST;
                  this.totalNumber=res.totalNumber * 1;
                })
      },
      pageChange(p){
        this.queryCondition.page = p;
        this.doQuery()
      },
      changePosition(e){
        this.doQuery()
      },
      changeWarningType(e){
        this.doQuery()
      },
      changeManager(e){
        this.doQuery()
      },
      userInfo(row){
        layer.open({
          type: 2,
          title:"员工详情",
          shadeClose: true,
          shade: 0.2,
          scrollbar: false,
          area: ['800px', '400px'],
          content: "/manager.php?s=/Member/memberInfo/userid/"+row.userid
        });
      },
      exportExcel(){
        param = {
          "keyword":this.queryCondition.keyword,
          "time":this.queryCondition.time,
          "position":this.queryCondition.position,
          "parent_id":this.queryCondition.manager,
          "type":this.queryCondition.warningType,
          "page":this.queryCondition.page,
        }

        window.open('/manager.php?s=/WarningMessage/warningExcel/keywords/'+param["keyword"]+'/type/'+param["type"]+'/time/'+param["time"]+'/position/'+param["position"]+'/parent_id/'+param["parent_id"]
                ,"_self");
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