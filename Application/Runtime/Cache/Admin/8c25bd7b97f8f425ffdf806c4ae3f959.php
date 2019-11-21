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
    }
  })
</script>
<style media="screen">
  input[type=file] {
    display: none;
  }

  .detail-value,
  .detail-label {
    font-size: 16px;
  }

  .detail-label {
    width: 120px;
    text-align: right;
    font-weight: bold;
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
              <el-select v-model="queryCondition.position" style="width:100px;" @change="changePosition">
                <el-option label="全部" :value="0">
                </el-option>
                <?php if(is_array($positionList)): $i = 0; $__LIST__ = $positionList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pl): $mod = ($i % 2 );++$i;?><el-option label="<?php echo ($pl["name"]); ?>" value="<?php echo ($pl["id"]); ?>">
                  </el-option><?php endforeach; endif; else: echo "" ;endif; ?>
              </el-select>
            </el-form-item>
            <el-form-item label="管理人员">
              <el-select v-model="queryCondition.manager" style="width:100px;" @change="changeManager">
                <el-option label="全部" :value="0">
                </el-option>
                <?php if(is_array($managerList)): $i = 0; $__LIST__ = $managerList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pl): $mod = ($i % 2 );++$i;?><el-option label="<?php echo ($pl["realname"]); ?>" value="<?php echo ($pl["userid"]); ?>">
                  </el-option><?php endforeach; endif; else: echo "" ;endif; ?>
              </el-select>
            </el-form-item>
            <el-form-item label="员工状态">
              <el-select v-model="queryCondition.status" style="width:100px;" @change="changeStatus">
                <el-option v-for="(s, i) in ['全部', '在岗', '未上岗', '离岗','离职']" :label="s" :value="i" :key="s">
                </el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="关键字">
              <el-input v-model="queryCondition.keywords"></el-input>
            </el-form-item>
            <el-form-item>
              <el-button icon="el-icon-search" type="primary" @click="doQuery">查询</el-button>
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
                <el-tag v-else-if="row[prop] === 0" type="danger">
                  离岗
                </el-tag>
                <el-tag v-else-if="row[prop] === 4" type="danger">
                  离职
                </el-tag>
              </template>
              <template v-else-if="prop === 'sex'" #default="{ row }">
                {{ row[prop] * 1 === 1 ? '男' : '女' }}
              </template>
              <template #default="{ row }" v-else-if="prop === 'position'">
                {{ getPositionLabel(row[prop]) }}
              </template>
            </el-table-column>
          </template>
          <el-table-column prop="job_status" label="状态">
            <template #default="{ row }">
              {{
                            new Map([
                            ['1', '在岗'],
                            ['0', '离岗'],
                            ['2', '未上岗'],
                            ['4', '离职'],
                            ]).get(row.job_status)
                            }}
            </template>
          </el-table-column>
          <el-table-column label="操作" width="380">
            <template #default="{ row }">
              <el-link :underline="false" type="primary" @click="() => {
                                detailRow =  [
                                  ['姓名', 'realname'],
                                  ['员工号', 'job_number'],
                                  ['手机号码', 'mobile'],
                                  ['职位', 'position'],
                                  ['性别', 'sex', v => v === '1' ? '男' : '女'],
                                  ['状态', 'job_status'],
                                ].map(([label, field, formatter = v => v]) => ({
                                  label,
                                  field,
                                  value: row[field],
                                  formatter
                                }))
                                detailDialog = true
                              }">详情</el-link>
              <el-link :underline="false" type="primary" @click="() => {
                                Object.keys(editFormData).forEach(f => {
                                  editFormData[f] = row[f]
                                })
                                editDialog = true
                              }">编辑</el-link>
              <el-link :underline="false" type="danger" @click="deleteMember(row)">删除</el-link>
              <el-link :underline="false" type="primary" @click="() => {
                                Object.keys(bindDeviceFormData).forEach(f => {
                                  if(f in row){
                                    bindDeviceFormData[f] = row[f]
                                  }
                                })
                                bindDeviceDialog = true
                              }">绑定设备</el-link>
              <el-link :underline="false" type="primary">解绑设备</el-link>
              <el-link :underline="false" type="primary">员工轨迹</el-link>
              <el-link :underline="false" type="primary">所属区域</el-link>
            </template>
          </el-table-column>
        </el-table>
        <div class="flex justify-between items-center mt3">
          <div>
            <el-button :disabled="!hasSelection" type="danger" icon="el-icon-delete" @click="membersDelete">
              批量删除
            </el-button>
            <el-button :disabled="!hasSelection" type="danger" icon="el-icon-sort" class="mx2" @click="exchangeManagerBatchDialog = true">
              批量切换管理人员
            </el-button>
            <el-button icon="el-icon-download" @click="window.open('/smartGarden/manager.php?s=/Member/memberExcel')">
              导出
            </el-button>
          </div>
          <el-pagination background layout="prev, pager, next" :total="totalNumber" :current-page="queryCondition.page" @current-change="pageChange">
          </el-pagination>
        </div>
      </div>
    </div>
  </div>

  <!-- 添加 -->
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
          <?php if(is_array($positionList)): $i = 0; $__LIST__ = $positionList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pl): $mod = ($i % 2 );++$i;?><el-option label="<?php echo ($pl["name"]); ?>" value="<?php echo ($pl["id"]); ?>">
            </el-option><?php endforeach; endif; else: echo "" ;endif; ?>
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
        <el-radio v-model="create.formData.sex" :label="1">男</el-radio>
        <el-radio v-model="create.formData.sex" :label="2">女</el-radio>
      </el-form-item>
    </el-form>
    <div v-else class="column items-center">
      <el-link href="/smartGarden/Public/Admin/File/01.xlsx" type="primary" class="mb3">下载批量带入模板</el-link>
      <el-upload drag action="" :before-upload="beforeUpload">
        <i class="el-icon-upload"></i>
        <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
      </el-upload>
    </div>
    <div class="row justify-center" slot="footer">
      <el-button type="primary" @click="addMember">
        提交
      </el-button>
    </div>
  </el-dialog>

  <!-- 详情 -->
  <el-dialog :visible.sync="detailDialog" title="员工详情" width="480px">
    <el-table border :show-header="false" :data="detailRow">
      <el-table-column prop="label"></el-table-column>
      <el-table-column prop="value">
        <template #default="{ row }">
          <template v-if="row.field === 'job_status'">
            <el-tag :type="new Map([['1', 'primary'], ['0', 'danger'], ['2', 'warning'], ['4', 'info']]).get(row.value)">
              {{ statusMap.get(row.value) }}
            </el-tag>
          </template>
          <template v-else>
            {{ row.formatter(row.value) }}
          </template>
        </template>
      </el-table-column>
    </el-table>
  </el-dialog>

  <!-- 编辑 -->
  <el-dialog :visible.sync="editDialog" title="编辑员工信息" width="480px">
    <el-form :model="editFormData" rules="create.formRules" label-width="100px">
      <el-form-item label="职位">
        <el-select v-model="editFormData.position" style="width:100%;">
          <?php if(is_array($positionList)): $i = 0; $__LIST__ = $positionList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pl): $mod = ($i % 2 );++$i;?><el-option label="<?php echo ($pl["name"]); ?>" value="<?php echo ($pl["id"]); ?>">
            </el-option><?php endforeach; endif; else: echo "" ;endif; ?>
        </el-select>
      </el-form-item>
      <el-form-item label="管理人员">
        <el-select v-model="editFormData.parent_name" style="width:100%;">
          <?php if(is_array($managerList)): $i = 0; $__LIST__ = $managerList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pl): $mod = ($i % 2 );++$i;?><el-option label="<?php echo ($pl["realname"]); ?>" value="<?php echo ($pl["userid"]); ?>">
            </el-option><?php endforeach; endif; else: echo "" ;endif; ?>
        </el-select>
      </el-form-item>
      <el-form-item label="姓名">
        <el-input v-model="editFormData.realname"></el-input>
      </el-form-item>
      <el-form-item label="手机号">
        <el-input v-model="editFormData.mobile"></el-input>
      </el-form-item>
      <el-form-item label="员工号">
        <el-input v-model="editFormData.job_number"></el-input>
      </el-form-item>
      <el-form-item label="性别">
        <el-radio v-model="editFormData.sex" label="1">男</el-radio>
        <el-radio v-model="editFormData.sex" label="2">女</el-radio>
      </el-form-item>
    </el-form>
    <div slot="footer" class="flex justify-center">
      <el-button type="primary">
        提交
      </el-button>
    </div>
  </el-dialog>

  <!-- 绑定设备 -->
  <el-dialog :visible.sync="bindDeviceDialog" title="绑定设备" width="480px">
    <el-form :model="bindDeviceFormData" label-width="100px">
      <el-divider content-position="left">用户信息</el-divider>
      <el-form-item :label="l" :key="f" v-for="([f, l]) in [
        ['realname', '姓名'],
        ['job_number', '员工号'],
        ['mobile', '手机号']
      ]">
        <el-input v-model="bindDeviceFormData[f]" disabled></el-input>
      </el-form-item>
      <el-divider content-position="left">设备IMEI</el-divider>
        <el-row :gutter="10">
          <el-col :span="18">
            <el-form-item label="设备IMEI">
              <el-input v-model="bindDeviceFormData.imei"></el-input>
            </el-form-item>
          </el-col>
          <el-con :span="6">
            <el-button type="primary" icon="el-icon-search">
              查询
            </el-button>
          </el-con>
        </el-row>
    </el-form>
    <div slot="footer" class="flex justify-center">
      <el-button type="primary">
        提交
      </el-button>
    </div>
  </el-dialog>

  <!-- 批量切换管理人员 -->
  <el-dialog :visible.sync="exchangeManagerBatchDialog" title="批量解除管理人员" width="480px">
    <el-form label-width="100px">
      <el-form-item label="经理选择">
        <el-select v-model="exchangeManagerBatchFormData.id">
          <el-option label="解除管理人员" :value="-1"></el-option>
          <?php if(is_array($managerList)): $i = 0; $__LIST__ = $managerList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pl): $mod = ($i % 2 );++$i;?><el-option label="<?php echo ($pl["realname"]); ?>" value="<?php echo ($pl["userid"]); ?>"></el-option><?php endforeach; endif; else: echo "" ;endif; ?>
        </el-select>
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
  let positionList = []
</script>
<?php if(is_array($positionList)): $i = 0; $__LIST__ = $positionList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pl): $mod = ($i % 2 );++$i;?><script>
    positionList.push({
      label: '<?php echo ($pl["name"]); ?>',
      value: '<?php echo ($pl["id"]); ?>'
    })
  </script><?php endforeach; endif; else: echo "" ;endif; ?>
<script type="text/javascript">
  const MEMBERLIST = new Vue({
    el: '#MEMBERLIST',
    data() {
      return {
        statusMap: new Map([
          ['1', '在岗'],
          ['0', '离岗'],
          ['2', '未上岗'],
          ['4', '离职'],
        ]),
        positionList,
        tableSelections: [],
        tableData: [{
          userid: '',
          realname: '',
          sxe: 0,
          position: '',
          mobile: '',
          job_number: '',
          parent_name: '',
          machine_imei: '',
          job_status: 0
        }],
        totalNumber: 1000,
        queryCondition: {
          position: 0,
          manager: 0,
          status: 0,
          keywords: '',
          parent_id: '',
          page: 1
        },
        tableColumns: [
          ['姓名', 'realname'],
          ['性别', 'sex'],
          ['职位', 'position'],
          ['手机号码', 'mobile'],
          ['员工号', 'job_number'],
          ['管理人员', 'parent_name'],
          ['设备IMEI', 'machine_imei'],
        ],
        create: {
          dialog: false,
          type: 'handy',
          formRules: {},
          formData: {
            file: null,
            position: '',
            realname: '',
            mobile: '',
            job_number: '',
            sex: 0
          }
        },
        detailRow: [],
        detailDialog: false,
        editDialog: false,
        editFormData: {
          position: '',
          parent_name: '',
          realname: '',
          mobile: '',
          job_number: '',
          sex: ''
        },
        bindDeviceDialog: false,
        bindDeviceFormData: {
          realname: '',
          mobile: '',
          job_number: '',
          imei: ''
        },
        exchangeManagerBatchDialog: false,
        exchangeManagerBatchFormData: {
          id: -1
        }
      }
    },
    computed: {
      hasSelection() {
        return this.tableSelections.length > 0
      }
    },
    created() {
      this.doQuery();
    },
    computed: {
      hasSelection() {
        return this.tableSelections.length > 0
      }
    },
    methods: {
      membersDelete() {
        console.log(this.tableSelections.map(row => row.userid).join(','));
        DMS.ajaxPost('/smartGarden/manager.php?s=/Member/membersDelete', {
          "userid": this.tableSelections.map(row => row.userid).join(',')
        }, res => {
          if (res.status === 1) {
            this.$message({
              message: res.info,
              type: 'success'
            });
            window.location.reload();
          } else {
            this.$message({
              message: res.info,
              type: 'error'
            });
          }
        })
      },
      beforeUpload(f) {
        if (!new RegExp(/(xls)|(xlsx)/i).test(f.name)) {
          this.$alert('只能上传xlsx文件')
          return false
        }
        this.create.formData.file = f
        return false
      },
      showLeaveDialog(row) {
        // 从接口查询离岗详情并赋值给this.edit.leaveFormData
        DMS.ajaxPost("/smartGarden/manager.php?s=/Member/leaveInfo", {
          userid: row.userid,
          add_time: row.add_time,
        }, ret => {
          this.testArr = ret.leaveInfo
        })
        this.edit.leaveDialog = true
      },
      getPositionLabel(v) {
        let p = positionList.find(p => p.value === v)
        if (!p) {
          return '管理人员'
        }
        return p.label
      },
      pageChange(p) {
        this.queryCondition.page = p;
        this.doQuery();
      },
      doQuery() {
        // 在这里将查询条件传后台获取查询结果

        param = {
          "keywords": this.queryCondition.keywords,
          "status": this.queryCondition.status,
          "position": this.queryCondition.position,
          "parent_id": this.queryCondition.manager,
          "page": this.queryCondition.page,
        }
        DMS.ajaxPost('/smartGarden/manager.php?s=/Member/getMemberList',
          param, res => {
            this.tableData = res.MEMBERLIST;
            this.totalNumber = res.totalNumber * 1;
          })
      },
      addMember() {
        // 在这里将查询条件传后台获取查询结果
        if (this.create.type === 'batch') {
          let fd = new FormData()
          fd.append('file', this.create.formData.file)
          $.ajax({
            url: '/util.php?m=Attachment&c=Index&a=excelUpload',
            data: fd,
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: res => {
              console.log(res.file.savename)
              DMS.ajaxPost("/smartGarden/manager.php?s=/Member/importFile", {
                "filename": res.file.savename
              }, function(ret) {
                if (ret.status == 1) {
                  DMS.success(ret.info, 1000, function() {
                    submitStatus = true;
                    if (ret.url) {
                      window.location.reload();
                    } else {
                      window.location.reload();
                    }
                  })
                } else {
                  DMS.error('' + ret.info + '', 0, function() {
                    submitStatus = true;
                    window.location.reload();
                  })
                }
              })
            }
          })
          return
        }
        param = {
          "position": this.create.formData.position,
          "realname": this.create.formData.realname,
          "mobile": this.create.formData.mobile,
          "job_number": this.create.formData.job_number,
          "sex": this.create.formData.sex
        }
        console.log(param);
        DMS.ajaxPost('/smartGarden/manager.php?s=/Member/memberAdd',
          param, res => {
            if (res) {
              this.$message({
                message: '员工添加成功',
                type: 'success'
              });
              window.location.reload();
            } else {
              this.$message({
                message: '员工添加失败，请检查信息',
                type: 'error'
              });
              window.location.reload();
            }
          })
      },
      deleteMember(row) {
        console.log(row.userid);
        DMS.ajaxPost('/smartGarden/manager.php?s=/Member/memberDelete', {
          "usersid": row.userid
        }, res => {
          if (res) {
            this.$message({
              message: '员工删除成功',
              type: 'success'
            });
            window.location.reload();
          } else {
            this.$message({
              message: '员工删除失败',
              type: 'error'
            });
            window.location.reload();
          }
        })
      },
      changePosition(e) {
        this.doQuery()
      },
      changeStatus(e) {
        this.doQuery()
      },
      changeManager(e) {
        this.doQuery()
      },
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